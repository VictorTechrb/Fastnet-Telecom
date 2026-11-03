<?php
/**
 * API REST do atendimento Fastnet.
 */
if (!defined('ABSPATH')) { exit; }

function fastnet_chat_authorize_request($request)
{
    $session_id = (string) $request['session_id'];
    $token = (string) $request->get_header('x-fastnet-chat-token');
    return (bool) fastnet_chat_authenticate_session($session_id, $token);
}

function fastnet_chat_register_routes()
{
    register_rest_route('fastnet-chat/v1', '/session', array(
        'methods' => WP_REST_Server::CREATABLE,
        'callback' => 'fastnet_chat_create_session_endpoint',
        'permission_callback' => '__return_true',
    ));

    register_rest_route('fastnet-chat/v1', '/session/(?P<session_id>[a-f0-9-]{36})/messages', array(
        array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => 'fastnet_chat_list_messages_endpoint',
            'permission_callback' => 'fastnet_chat_authorize_request',
            'args' => array('after_id' => array('default' => 0, 'sanitize_callback' => 'absint')),
        ),
        array(
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => 'fastnet_chat_send_message_endpoint',
            'permission_callback' => 'fastnet_chat_authorize_request',
        ),
    ));

    register_rest_route('fastnet-chat/v1', '/session/(?P<session_id>[a-f0-9-]{36})/media/(?P<message_id>\\d+)', array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => 'fastnet_chat_get_private_media_endpoint',
        'permission_callback' => 'fastnet_chat_authorize_request',
    ));

    register_rest_route('fastnet-chat/v1', '/webhook', array(
        'methods' => WP_REST_Server::CREATABLE,
        'callback' => 'fastnet_chat_receive_opa_webhook',
        'permission_callback' => '__return_true',
    ));
}
add_action('rest_api_init', 'fastnet_chat_register_routes');

function fastnet_chat_create_session_endpoint($request)
{
    if (empty(fastnet_chat_config()['ready'])) {
        return new WP_Error('chat_not_configured', 'Atendimento indisponível no momento.', array('status' => 503));
    }

    $ip = isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : 'unknown';
    if (!fastnet_chat_rate_limit('session', 60, 600, $ip)) {
        return new WP_Error('chat_rate_limited', 'Muitas tentativas. Aguarde um pouco e tente novamente.', array('status' => 429));
    }

    $session = fastnet_chat_create_session();
    if (is_wp_error($session)) { return $session; }
    return new WP_REST_Response(array('session_id' => $session['session_id'], 'token' => $session['token']), 201);
}

function fastnet_chat_list_messages_endpoint($request)
{
    global $wpdb;
    $session_id = (string) $request['session_id'];
    $after_id = absint($request->get_param('after_id'));
    $table = fastnet_chat_messages_table();

    if (0 === $after_id) {
        $rows = $wpdb->get_results(
            $wpdb->prepare("SELECT id,direction,message_type,message_text,media_url,media_name,media_type,(media_data IS NOT NULL AND media_data <> '') AS has_private_media,created_at FROM {$table} WHERE session_id = %s AND delivery_status = 'sent' ORDER BY id DESC LIMIT 100", $session_id),
            ARRAY_A
        );
        $rows = array_reverse(is_array($rows) ? $rows : array());
    } else {
        $rows = $wpdb->get_results(
            $wpdb->prepare("SELECT id,direction,message_type,message_text,media_url,media_name,media_type,(media_data IS NOT NULL AND media_data <> '') AS has_private_media,created_at FROM {$table} WHERE session_id = %s AND delivery_status = 'sent' AND id > %d ORDER BY id ASC LIMIT 100", $session_id, $after_id),
            ARRAY_A
        );
    }

    $messages = array();
    $last_id = $after_id;
    foreach ((array) $rows as $row) {
        $id = (int) $row['id'];
        $last_id = max($last_id, $id);
        $has_private_media = !empty($row['has_private_media']);
        $media_url = (string) $row['media_url'];
        if ($has_private_media) {
            $media_url = rest_url(
                'fastnet-chat/v1/session/' . rawurlencode($session_id) . '/media/' . $id
            );
        }

        $messages[] = array(
            'id' => $id,
            'direction' => $row['direction'],
            'type' => $row['message_type'],
            'text' => (string) $row['message_text'],
            'media_url' => $media_url,
            'media_private' => $has_private_media,
            'media_name' => (string) $row['media_name'],
            'media_type' => (string) $row['media_type'],
            'created_at' => $row['created_at'],
        );
    }

    fastnet_chat_touch_session($session_id);
    return new WP_REST_Response(array('messages' => $messages, 'last_id' => $last_id), 200);
}


function fastnet_chat_get_private_media_endpoint($request)
{
    global $wpdb;

    $session_id = (string) $request['session_id'];
    $message_id = absint($request['message_id']);
    $table = fastnet_chat_messages_table();

    $row = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT media_data,media_type FROM {$table} WHERE id = %d AND session_id = %s AND message_type = 'media' LIMIT 1",
            $message_id,
            $session_id
        ),
        ARRAY_A
    );

    if (!$row || empty($row['media_data'])) {
        return new WP_Error('chat_media_not_found', 'Anexo não encontrado nesta conversa.', array('status' => 404));
    }

    $mime = sanitize_text_field((string) $row['media_type']);
    $allowed_mime_types = array(
        'image/jpeg', 'image/png', 'image/gif', 'image/webp',
        'application/pdf', 'text/plain', 'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'audio/webm', 'audio/ogg', 'audio/mpeg', 'audio/mp4', 'audio/wav', 'audio/x-wav',
    );

    if (!in_array($mime, $allowed_mime_types, true)) {
        return new WP_Error('chat_media_invalid_type', 'O tipo do anexo armazenado não é permitido.', array('status' => 415));
    }

    $decoded = base64_decode((string) $row['media_data'], true);
    if (false === $decoded || 0 === strlen($decoded)) {
        return new WP_Error('chat_media_corrupt', 'Não foi possível recuperar este anexo.', array('status' => 500));
    }

    $response = new WP_REST_Response(
        array('data_url' => 'data:' . $mime . ';base64,' . base64_encode($decoded)),
        200
    );
    $response->header('Cache-Control', 'private, no-store, max-age=0');
    return $response;
}

function fastnet_chat_send_message_endpoint($request)
{
    $session_id = (string) $request['session_id'];
    $session = fastnet_chat_authenticate_session($session_id, (string) $request->get_header('x-fastnet-chat-token'));
    if (!$session) {
        return new WP_Error('chat_unauthorized', 'Sessão inválida. Atualize a página e tente novamente.', array('status' => 401));
    }

    if (!fastnet_chat_rate_limit('message', 30, 60, $session_id)) {
        return new WP_Error('chat_rate_limited', 'Você enviou mensagens muito rapidamente. Aguarde um instante.', array('status' => 429));
    }

    $body = $request->get_json_params();
    $normalized = fastnet_chat_normalize_message(isset($body['message']) ? $body['message'] : null);
    if (is_wp_error($normalized)) { return $normalized; }

    $sent = fastnet_chat_send_opa($session, $normalized['opa']);
    if (is_wp_error($sent)) { return $sent; }

    $saved = fastnet_chat_store_message(array_merge($normalized['stored'], array(
        'session_id' => $session_id,
        'remote_id' => null,
        'direction' => 'visitor',
        'delivery_status' => 'sent',
        'created_at' => current_time('mysql', true),
    )));

    if (!$saved) {
        // A OPA já aceitou a mensagem. Retorne sucesso para evitar reenvio automático.
        error_log('Fastnet Chat: mensagem aceita pela OPA, mas o histórico local não foi gravado.');
        return new WP_REST_Response(array('success' => true, 'history_warning' => true), 200);
    }

    return new WP_REST_Response(array('success' => true, 'message_id' => $saved), 200);
}
