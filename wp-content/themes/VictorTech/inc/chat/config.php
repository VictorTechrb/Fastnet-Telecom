<?php
/**
 * Configuração do Webchat oficial do Opa! Suite.
 *
 * Este modo usa somente a URL da instalação e o identificador público do canal
 * fornecido no script opa.init(). Não é a integração de API privada.
 */
if (!defined('ABSPATH')) { exit; }

function fastnet_chat_private_settings()
{
    static $settings = null;
    if (null !== $settings) { return $settings; }

    $settings = get_option('fastnet_chat_settings', array());
    if (!is_array($settings)) { $settings = array(); }

    $path = getenv('FASTNET_CHAT_PRIVATE_CONFIG');
    if (is_string($path) && '' !== trim($path) && is_readable($path) && is_file($path)) {
        $loaded = require $path;
        if (is_array($loaded)) {
            $settings = array_merge($settings, $loaded);
        }
    }

    return $settings;
}

function fastnet_chat_secret($key, $environment, $fallback_environment = '')
{
    $value = getenv($environment);
    if (false !== $value && '' !== trim($value)) {
        return trim($value);
    }

    if ('' !== $fallback_environment) {
        $value = getenv($fallback_environment);
        if (false !== $value && '' !== trim($value)) {
            return trim($value);
        }
    }

    $settings = fastnet_chat_private_settings();
    return isset($settings[$key]) && is_scalar($settings[$key])
        ? trim((string) $settings[$key])
        : '';
}

function fastnet_chat_settings_sanitize($input)
{
    $input = is_array($input) ? $input : array();
    $current = get_option('fastnet_chat_settings', array());
    if (!is_array($current)) { $current = array(); }

    $api_url = isset($input['api_url'])
        ? esc_url_raw(trim((string) $input['api_url']))
        : '';
    $token = isset($input['legacy_key'])
        ? trim(sanitize_text_field((string) $input['legacy_key']))
        : '';

    $parts = wp_parse_url($api_url);
    $valid_url = is_array($parts)
        && isset($parts['scheme'], $parts['host'])
        && 'https' === strtolower($parts['scheme'])
        && empty($parts['user'])
        && empty($parts['pass'])
        && empty($parts['query'])
        && empty($parts['fragment'])
        && (!isset($parts['path']) || '' === $parts['path'] || '/' === $parts['path']);

    if (!$valid_url) {
        $api_url = isset($current['api_url']) ? (string) $current['api_url'] : '';
    }

    // Migra automaticamente o ID do canal que já era salvo pela tela anterior.
    $saved_token = isset($current['legacy_key'])
        ? (string) $current['legacy_key']
        : (isset($current['channel_id']) ? (string) $current['channel_id'] : '');

    if ('' === $token) {
        // Campo vazio mantém o identificador já salvo.
        $token = $saved_token;
    }

    if ('' !== $token && !preg_match('/^[a-zA-Z0-9_-]{1,128}$/', $token)) {
        $token = $saved_token;
    }

    return array(
        'api_url' => untrailingslashit($api_url),
        'legacy_key' => $token,
    );
}

function fastnet_chat_register_settings()
{
    register_setting(
        'fastnet_chat_settings',
        'fastnet_chat_settings',
        array('sanitize_callback' => 'fastnet_chat_settings_sanitize')
    );
}
add_action('admin_init', 'fastnet_chat_register_settings');

function fastnet_chat_settings_page()
{
    if (!current_user_can('manage_options')) { return; }

    $settings = get_option('fastnet_chat_settings', array());
    if (!is_array($settings)) { $settings = array(); }
    $has_saved_token = !empty($settings['legacy_key']) || !empty($settings['channel_id']);
    ?>
    <div class="wrap">
        <h1>Atendimento Fastnet</h1>
        <p>Configure o Webchat oficial do Opa! Suite com a URL da instalação e o identificador do canal copiado do script gerado pelo OPA.</p>
        <p><strong>Importante:</strong> este identificador é público no Webchat incorporado. Não informe aqui o token de autenticação da API ou a senha de um usuário OPA.</p>
        <form method="post" action="options.php">
            <?php settings_fields('fastnet_chat_settings'); ?>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label for="fastnet-chat-api-url">URL do Opa! Suite</label></th>
                    <td>
                        <input
                            name="fastnet_chat_settings[api_url]"
                            id="fastnet-chat-api-url"
                            type="url"
                            class="regular-text"
                            value="<?php echo esc_attr(isset($settings['api_url']) ? $settings['api_url'] : 'https://lara.fastnettelecom.com'); ?>"
                            placeholder="https://lara.fastnettelecom.com"
                            required
                        >
                        <p class="description">Informe somente o domínio, sem /js/opa.js e sem caminhos adicionais.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="fastnet-chat-token">Token / ID do canal</label></th>
                    <td>
                        <input
                            name="fastnet_chat_settings[legacy_key]"
                            id="fastnet-chat-token"
                            type="password"
                            class="regular-text"
                            value=""
                            autocomplete="new-password"
                            placeholder="<?php echo $has_saved_token ? esc_attr__('Já configurado — deixe vazio para manter', 'victortech') : esc_attr__('Cole o segundo parâmetro de opa.init()', 'victortech'); ?>"
                        >
                        <p class="description">No script enviado, é o valor que aparece como segundo argumento de opa.init(), por exemplo o identificador iniciado por 6ad1. Deixe vazio para manter o valor atual.</p>
                    </td>
                </tr>
            </table>
            <?php submit_button('Salvar configuração'); ?>
        </form>
    </div>
    <?php
}

function fastnet_chat_add_settings_page()
{
    add_options_page(
        'Atendimento Fastnet',
        'Atendimento Fastnet',
        'manage_options',
        'fastnet-chat-settings',
        'fastnet_chat_settings_page'
    );
}
add_action('admin_menu', 'fastnet_chat_add_settings_page');

function fastnet_chat_config()
{
    // Preferir variáveis próprias do Webchat evita confundir a URL pública
    // do widget com a URL /api/v1/webchat do modo API.
    $api_url = fastnet_chat_secret('api_url', 'FASTNET_OPA_WEBCHAT_URL');
    $token = fastnet_chat_secret('legacy_key', 'FASTNET_OPA_WEBCHAT_TOKEN', 'FASTNET_OPA_LEGACY_KEY');
    if ('' === $token) {
        $settings = fastnet_chat_private_settings();
        if (isset($settings['channel_id']) && is_scalar($settings['channel_id'])) {
            $token = trim((string) $settings['channel_id']);
        }
    }

    $parts = wp_parse_url($api_url);
    $valid_url = is_array($parts)
        && isset($parts['scheme'], $parts['host'])
        && 'https' === strtolower($parts['scheme'])
        && empty($parts['user'])
        && empty($parts['pass'])
        && empty($parts['query'])
        && empty($parts['fragment'])
        && (!isset($parts['path']) || '' === $parts['path'] || '/' === $parts['path']);
    $valid_token = (bool) preg_match('/^[a-zA-Z0-9_-]{1,128}$/', $token);

    return array(
        'url' => $valid_url ? untrailingslashit(esc_url_raw($api_url)) : '',
        'token' => $valid_token ? $token : '',
        'ready' => $valid_url && $valid_token,
    );
}
