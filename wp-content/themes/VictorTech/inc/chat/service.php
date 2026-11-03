<?php
/**
 * Adaptador HTTP Opa! Suite e validação dos conteúdos.
 */
if (!defined('ABSPATH')) { exit; }

function fastnet_chat_rate_limit($scope, $limit, $seconds, $identity)
{
    $key = 'fastnet_chat_' . sanitize_key($scope) . '_' . hash_hmac('sha256', (string) $identity, wp_salt('auth'));
    $count = (int) get_transient($key);
    if ($count >= (int) $limit) { return false; }
    set_transient($key, $count + 1, (int) $seconds);
    return true;
}

function fastnet_chat_opa_endpoint()
{
    $config = fastnet_chat_config();
    if (empty($config['ready'])) { return ''; }
    return $config['api_url'] . '/' . rawurlencode($config['channel_id']) . '/message';
}

function fastnet_chat_send_opa($session, $message)
{
    $config = fastnet_chat_config();
    $endpoint = fastnet_chat_opa_endpoint();
    if (empty($config['ready']) || '' === $endpoint) {
        return new WP_Error('chat_not_configured', 'Atendimento indisponível no momento.', array('status' => 503));
    }

    $response = wp_remote_post(
        $endpoint,
        array(
            'timeout' => 20,
            'redirection' => 0,
            'headers' => array(
                'Authorization' => 'Bearer ' . $config['token'],
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ),
            'body' => wp_json_encode(array(
                'user' => array('id' => $session['user_id'], 'username' => 'Visitante Fastnet'),
                'message' => $message,
            )),
        )
    );

    if (is_wp_error($response)) {
        return new WP_Error('chat_upstream_error', 'Não foi possível conectar ao atendimento. Tente novamente.', array('status' => 502));
    }

    $status = (int) wp_remote_retrieve_response_code($response);
    $body = json_decode(wp_remote_retrieve_body($response), true);
    // O Opa! Suite também devolve erros de validação em HTTP 200.
    if ($status < 200 || $status >= 300 || !is_array($body) || empty($body['success']) || true !== $body['success']) {
        return new WP_Error('chat_upstream_rejected', 'O atendimento não confirmou o recebimento da mensagem. Tente novamente.', array('status' => 502));
    }

    return true;
}

function fastnet_chat_normalize_message($input)
{
    if (!is_array($input) || empty($input['type'])) {
        return new WP_Error('chat_invalid_message', 'Mensagem inválida.', array('status' => 400));
    }

    $type = sanitize_key($input['type']);
    if ('text' === $type) {
        $text = isset($input['text']) ? trim(sanitize_textarea_field($input['text'])) : '';
        if ('' === $text || strlen($text) > 2000) {
            return new WP_Error('chat_invalid_text', 'A mensagem deve conter entre 1 e 2.000 caracteres.', array('status' => 400));
        }

        return array(
            'opa' => array('type' => 'text', 'text' => $text),
            'stored' => array('message_type' => 'text', 'message_text' => $text, 'media_url' => null, 'media_name' => null, 'media_type' => null),
        );
    }

    if ('media' !== $type) {
        return new WP_Error('chat_invalid_type', 'Formato de mensagem não suportado.', array('status' => 400));
    }

    $mime = isset($input['mediaType']) ? strtolower(trim(explode(';', sanitize_text_field($input['mediaType']))[0])) : '';
    $filename = isset($input['filename']) ? sanitize_file_name($input['filename']) : '';
    $encoded = isset($input['data']) ? (string) $input['data'] : '';
    $allowed = array(
        'image/jpeg', 'image/png', 'image/gif', 'image/webp',
        'application/pdf', 'text/plain', 'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'audio/webm', 'audio/ogg', 'audio/mpeg', 'audio/mp4', 'audio/wav', 'audio/x-wav',
    );

    if (!in_array($mime, $allowed, true) || '' === $filename || '' === $encoded) {
        return new WP_Error('chat_invalid_media', 'Tipo de arquivo não permitido.', array('status' => 400));
    }

    $max_bytes = (int) wp_max_upload_size();
    if ($max_bytes < 1) { $max_bytes = 1024 * 1024; }
    $max_bytes = min($max_bytes, 10 * 1024 * 1024);
    if (strlen($encoded) > (int) ceil($max_bytes * 1.4)) {
        return new WP_Error('chat_media_too_large', 'O arquivo excede o limite permitido pelo servidor.', array('status' => 413));
    }

    $decoded = base64_decode($encoded, true);
    if (false === $decoded || strlen($decoded) > $max_bytes || 0 === strlen($decoded)) {
        return new WP_Error('chat_invalid_media_data', 'Não foi possível validar o arquivo enviado.', array('status' => 400));
    }

    return array(
        'opa' => array(
            'type' => 'media',
            'media' => array('mediaType' => $mime, 'data' => $encoded, 'filename' => $filename),
        ),
        'stored' => array('message_type' => 'media', 'message_text' => '', 'media_url' => null, 'media_name' => $filename, 'media_type' => $mime),
    );
}

function fastnet_chat_receive_opa_webhook($request)
{
    $config = fastnet_chat_config();
    $signature = (string) $request->get_header('x-signature');
    if (empty($config['webhook_secret']) || '' === $signature || !hash_equals($config['webhook_secret'], $signature)) {
        return new WP_REST_Response(array('success' => false), 403);
    }

    $payload = $request->get_json_params();
    if (!is_array($payload) || empty($payload['customerService']['canal_cliente']) || !isset($payload['message']) || !is_array($payload['message'])) {
        return new WP_REST_Response(array('success' => false), 400);
    }

    $external_id = sanitize_text_field($payload['customerService']['canal_cliente']);
    if (0 !== strpos($external_id, 'fastnet-')) { return new WP_REST_Response(array('success' => true), 200); }

    $session_id = substr($external_id, 8);
    $session = fastnet_chat_get_session($session_id);
    if (!$session || $session['user_id'] !== $external_id) { return new WP_REST_Response(array('success' => true), 200); }

    $remote = $payload['message'];
    $remote_id = isset($remote['_id']) ? sanitize_text_field($remote['_id']) : '';
    if (fastnet_chat_remote_message_exists($remote_id)) { return new WP_REST_Response(array('success' => true), 200); }

    $text = isset($remote['mensagem']) && is_scalar($remote['mensagem']) ? sanitize_textarea_field((string) $remote['mensagem']) : '';
    $object = isset($remote['objeto']) && is_array($remote['objeto']) ? $remote['objeto'] : array();
    $media_url = '';
    if (!empty($object['url']) && is_string($object['url'])) {
        $candidate = esc_url_raw($object['url']);
        $parts = wp_parse_url($candidate);
        if (is_array($parts) && isset($parts['scheme']) && 'https' === strtolower($parts['scheme'])) { $media_url = $candidate; }
    }

    if ('' === $text && '' === $media_url) { return new WP_REST_Response(array('success' => true), 200); }

    $created = current_time('mysql', true);
    if (!empty($remote['data']) && is_string($remote['data'])) {
        $timestamp = strtotime($remote['data']);
        if (false !== $timestamp) { $created = gmdate('Y-m-d H:i:s', $timestamp); }
    }

    $saved = fastnet_chat_store_message(array(
        'session_id' => $session_id,
        'remote_id' => '' !== $remote_id ? $remote_id : null,
        'direction' => 'agent',
        'message_type' => '' !== $media_url ? 'media' : 'text',
        'message_text' => $text,
        'media_url' => '' !== $media_url ? $media_url : null,
        'media_name' => isset($object['nome']) ? sanitize_file_name($object['nome']) : null,
        'media_type' => isset($object['tipo']) ? sanitize_text_field($object['tipo']) : null,
        'delivery_status' => 'sent',
        'created_at' => $created,
    ));

    if (!$saved) { return new WP_REST_Response(array('success' => false), 500); }
    return new WP_REST_Response(array('success' => true), 200);
}
