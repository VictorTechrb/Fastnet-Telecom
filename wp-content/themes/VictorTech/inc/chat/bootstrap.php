<?php
/**
 * Carrega o Webchat oficial do Opa! Suite e a identidade visual da Fastnet.
 *
 * O OPA continua responsável por receber e encaminhar as mensagens.
 * Este módulo personaliza somente o acionador que aparece no site.
 */
if (!defined('ABSPATH')) { exit; }

require_once __DIR__ . '/config.php';

function fastnet_carregar_webchat_opa()
{
    $configuracao = fastnet_chat_config();

    if (empty($configuracao['ready'])) {
        return;
    }

    $endereco_script = trailingslashit($configuracao['url']) . 'js/opa.js';

    wp_enqueue_script(
        'fastnet-webchat-opa',
        esc_url($endereco_script),
        array(),
        null,
        true
    );

    // Mantém as opções de acesso sem login do script oficial da Fastnet.
    $opcoes_webchat = array(
        'permitir_login_anonimo' => 'on',
        'facebook_appid' => '',
        'google_credential' => '',
        'google_oauth' => '',
    );

    // O terceiro parâmetro do OPA deve continuar sendo uma sequência JSON.
    // A inicialização acontece depois que o arquivo oficial é carregado.
    $codigo_inicializacao = 'if (typeof opa !== "undefined" && opa && typeof opa.init === "function") {' .
        'opa.init(' .
        wp_json_encode($configuracao['url']) . ',' .
        wp_json_encode($configuracao['token']) . ',' .
        wp_json_encode(wp_json_encode($opcoes_webchat)) .
        ');' .
        '}';

    wp_add_inline_script('fastnet-webchat-opa', $codigo_inicializacao, 'after');

    // Carrega a personalização visual depois do Webchat oficial.
    $pasta_tema = get_template_directory();
    $endereco_tema = get_template_directory_uri();
    $arquivo_estilo = $pasta_tema . '/assets/chat/identidade-atendimento.css';
    $arquivo_comportamento = $pasta_tema . '/assets/chat/identidade-atendimento.js';

    if (file_exists($arquivo_estilo)) {
        wp_enqueue_style(
            'fastnet-identidade-atendimento',
            $endereco_tema . '/assets/chat/identidade-atendimento.css',
            array('victortech-style'),
            (string) filemtime($arquivo_estilo)
        );
    }

    if (file_exists($arquivo_comportamento)) {
        wp_enqueue_script(
            'fastnet-identidade-atendimento',
            $endereco_tema . '/assets/chat/identidade-atendimento.js',
            array('fastnet-webchat-opa'),
            (string) filemtime($arquivo_comportamento),
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'fastnet_carregar_webchat_opa', 30);
