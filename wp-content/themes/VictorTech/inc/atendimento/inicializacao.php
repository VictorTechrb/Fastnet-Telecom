<?php
/**
 * Inicializa o atendimento oficial da Fastnet Telecom.
 *
 * O Opa! Suite administra a janela, a sessão e a entrega das mensagens.
 * Este arquivo carrega o Webchat e aplica a identidade visual da Fastnet.
 */
if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/configuracao.php';

/**
 * Define o título da página de atendimento nos resultados de busca.
 */
function fastnet_definir_titulo_seo_atendimento($titulo_atual)
{
    if (!function_exists('victortech_is_atendimento_request') || !victortech_is_atendimento_request()) {
        return $titulo_atual;
    }

    return 'Atendimento Fastnet Telecom | Internet Fibra Óptica em Sergipe';
}
add_filter('pre_get_document_title', 'fastnet_definir_titulo_seo_atendimento', 20);

/**
 * Adiciona uma descrição para os buscadores quando não há extensão de SEO ativa.
 */
function fastnet_adicionar_descricao_seo_atendimento()
{
    if (!function_exists('victortech_is_atendimento_request') || !victortech_is_atendimento_request()) {
        return;
    }

    // Evita criar descrições duplicadas quando uma extensão de SEO já administra a página.
    $extensao_seo_ativa = defined('WPSEO_VERSION')
        || defined('RANK_MATH_VERSION')
        || defined('SEOPRESS_VERSION')
        || defined('AIOSEO_VERSION')
        || class_exists('The_SEO_Framework\\Load');

    if ($extensao_seo_ativa) {
        return;
    }

    $descricao = 'Fale com a Fastnet Telecom em Sergipe. Tire dúvidas sobre internet fibra óptica, suporte técnico e planos residenciais e empresariais.';
    echo '<meta name="description" content="' . esc_attr($descricao) . '">' . "\n";
}
add_action('wp_head', 'fastnet_adicionar_descricao_seo_atendimento', 1);

/**
 * Carrega o Webchat oficial do Opa! Suite.
 */
function fastnet_carregar_webchat_oficial()
{
    $pasta_tema = get_template_directory();
    $endereco_tema = get_template_directory_uri();
    $arquivo_estilo = $pasta_tema . '/assets/atendimento/identidade-visual.css';
    $arquivo_comportamento = $pasta_tema . '/assets/atendimento/identidade-visual.js';

    // O conteúdo e a identidade da página continuam disponíveis mesmo sem o Webchat.
    if (file_exists($arquivo_estilo)) {
        wp_enqueue_style(
            'fastnet-identidade-atendimento',
            $endereco_tema . '/assets/atendimento/identidade-visual.css',
            array('victortech-style'),
            (string) filemtime($arquivo_estilo)
        );
    }

    $configuracao = fastnet_obter_configuracao_webchat();

    if (empty($configuracao['pronto'])) {
        return;
    }

    $endereco_script = trailingslashit($configuracao['endereco']) . 'js/opa.js';

    wp_enqueue_script(
        'fastnet-webchat-oficial',
        esc_url($endereco_script),
        array(),
        null,
        true
    );

    // Mantém os nomes das opções do código oficial de instalação do OPA.
    $opcoes_webchat = array(
        'permitir_login_anonimo' => 'on',
        'facebook_appid' => '',
        'google_credential' => '',
        'google_oauth' => '',
    );

    // O terceiro parâmetro do OPA é uma sequência JSON; os nomes são exigidos pelo fornecedor.
    $codigo_inicializacao = 'if (typeof opa !== "undefined" && opa && typeof opa.init === "function") {' .
        'opa.init(' .
        wp_json_encode($configuracao['endereco']) . ',' .
        wp_json_encode($configuracao['identificador']) . ',' .
        wp_json_encode(wp_json_encode($opcoes_webchat)) .
        ');' .
        '}';

    wp_add_inline_script('fastnet-webchat-oficial', $codigo_inicializacao, 'after');

    if (file_exists($arquivo_comportamento)) {
        wp_enqueue_script(
            'fastnet-identidade-atendimento',
            $endereco_tema . '/assets/atendimento/identidade-visual.js',
            array('fastnet-webchat-oficial'),
            (string) filemtime($arquivo_comportamento),
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'fastnet_carregar_webchat_oficial', 30);
