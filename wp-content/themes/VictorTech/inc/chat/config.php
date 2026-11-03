<?php
/**
 * Configuração privada do atendimento Fastnet.
 * Credenciais ficam no ambiente ou em arquivo fora do repositório e da pasta pública.
 */
if (!defined('ABSPATH')) { exit; }

function fastnet_chat_private_settings()
{
    static $settings = null;
    if (null !== $settings) { return $settings; }

    $settings = array();
    $path = getenv('FASTNET_CHAT_PRIVATE_CONFIG');
    if (is_string($path) && '' !== trim($path) && is_readable($path) && is_file($path)) {
        $loaded = require $path;
        if (is_array($loaded)) { $settings = $loaded; }
    }
    return $settings;
}

function fastnet_chat_secret($key, $environment)
{
    $value = getenv($environment);
    if (false !== $value && '' !== trim($value)) { return trim($value); }

    $settings = fastnet_chat_private_settings();
    return isset($settings[$key]) && is_scalar($settings[$key]) ? trim((string) $settings[$key]) : '';
}

function fastnet_chat_config()
{
    $api_url = fastnet_chat_secret('api_url', 'FASTNET_OPA_API_URL');
    $channel = fastnet_chat_secret('channel_id', 'FASTNET_OPA_CHANNEL_ID');
    $token = fastnet_chat_secret('token', 'FASTNET_OPA_TOKEN');
    $signature = fastnet_chat_secret('webhook_secret', 'FASTNET_OPA_WEBHOOK_SECRET');
    $parts = wp_parse_url($api_url);
    $valid_url = is_array($parts)
        && isset($parts['scheme'], $parts['host'])
        && 'https' === strtolower($parts['scheme'])
        && empty($parts['query'])
        && empty($parts['fragment']);

    return array(
        'api_url' => $valid_url ? untrailingslashit(esc_url_raw($api_url)) : '',
        'channel_id' => sanitize_text_field($channel),
        'token' => $token,
        'webhook_secret' => $signature,
        'ready' => $valid_url && '' !== $channel && '' !== $token && '' !== $signature,
    );
}

/** Apenas dados não secretos são disponibilizados ao navegador. */
function fastnet_chat_browser_config()
{
    $max_bytes = (int) wp_max_upload_size();
    if ($max_bytes < 1) { $max_bytes = 1024 * 1024; }

    return array(
        'restUrl' => esc_url_raw(rest_url('fastnet-chat/v1')),
        'maxUploadBytes' => min($max_bytes, 10 * 1024 * 1024),
        'fallbackUrl' => esc_url_raw(get_theme_mod('victortech_central_url', '')),
    );
}
