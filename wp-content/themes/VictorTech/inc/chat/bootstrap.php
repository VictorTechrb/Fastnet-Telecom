<?php
/**
 * Carrega o Webchat oficial do Opa! Suite.
 *
 * O OPA gerencia a interface, a sessão e o transporte das conversas.
 * Não são registrados endpoints REST próprios nem chamadas do modo API.
 */
if (!defined('ABSPATH')) { exit; }

require_once __DIR__ . '/config.php';

function fastnet_chat_enqueue_opa_webchat()
{
    $config = fastnet_chat_config();
    if (empty($config['ready'])) {
        return;
    }

    $script_url = trailingslashit($config['url']) . 'js/opa.js';
    wp_enqueue_script(
        'fastnet-opa-webchat',
        esc_url($script_url),
        array(),
        null,
        false
    );

    // Mantém as mesmas opções de login anônimo do script fornecido pela Fastnet.
    $widget_options = array(
        'permitir_login_anonimo' => 'on',
        'facebook_appid' => '',
        'google_credential' => '',
        'google_oauth' => '',
    );

    // O terceiro argumento de opa.init() é uma string JSON, como no código oficial.
    // A biblioteca é carregada antes deste inline script, evitando corrida de inicialização.
    $inline_script = 'if (typeof opa !== "undefined" && opa && typeof opa.init === "function") {' .
        'opa.init(' .
        wp_json_encode($config['url']) . ',' .
        wp_json_encode($config['token']) . ',' .
        wp_json_encode(wp_json_encode($widget_options)) .
        ');' .
        '}';

    wp_add_inline_script('fastnet-opa-webchat', $inline_script, 'after');
}
add_action('wp_enqueue_scripts', 'fastnet_chat_enqueue_opa_webchat', 30);
