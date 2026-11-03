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

    $settings = get_option('fastnet_chat_settings', array());
    if (!is_array($settings)) { $settings = array(); }
    $path = getenv('FASTNET_CHAT_PRIVATE_CONFIG');
    if (is_string($path) && '' !== trim($path) && is_readable($path) && is_file($path)) {
        $loaded = require $path;
        if (is_array($loaded)) { $settings = $loaded; }
    }
    return $settings;
}

function fastnet_chat_settings_sanitize($input)
{
    $input = is_array($input) ? $input : array();
    $current = get_option('fastnet_chat_settings', array());
    if (!is_array($current)) { $current = array(); }

    $api_url = isset($input['api_url']) ? esc_url_raw(trim((string) $input['api_url'])) : '';
    $legacy_key = isset($input['legacy_key']) ? trim((string) $input['legacy_key']) : '';

    return array(
        'api_url' => $api_url,
        'legacy_key' => '' !== $legacy_key ? $legacy_key : (isset($current['legacy_key']) ? $current['legacy_key'] : ''),
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
    ?>
    <div class="wrap">
        <h1>Atendimento Fastnet</h1>
        <p>As credenciais ficam armazenadas nas opções privadas do WordPress e não são enviadas ao navegador.</p>
        <form method="post" action="options.php">
            <?php
            settings_fields('fastnet_chat_settings');
            $settings = get_option('fastnet_chat_settings', array());
            ?>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label for="fastnet-chat-api-url">URL do atendimento</label></th>
                    <td><input name="fastnet_chat_settings[api_url]" id="fastnet-chat-api-url" type="url" class="regular-text" value="<?php echo esc_attr(isset($settings['api_url']) ? $settings['api_url'] : ''); ?>" required></td>
                </tr>
                <tr>
                    <th scope="row"><label for="fastnet-chat-legacy-key">Código do atendimento</label></th>
                    <td><input name="fastnet_chat_settings[legacy_key]" id="fastnet-chat-legacy-key" type="password" class="regular-text" value="" autocomplete="new-password"><p class="description">Deixe vazio para manter o código atual.</p></td>
                </tr>
            </table>
            <?php submit_button('Salvar configuração'); ?>
        </form>
        <p><strong>Webhook:</strong> <?php echo esc_html(rest_url('fastnet-chat/v1/webhook')); ?></p>
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
    $legacy_key = fastnet_chat_secret('legacy_key', 'FASTNET_OPA_LEGACY_KEY');
    $channel = fastnet_chat_secret('channel_id', 'FASTNET_OPA_CHANNEL_ID');
    $token = fastnet_chat_secret('token', 'FASTNET_OPA_TOKEN');
    $signature = fastnet_chat_secret('webhook_secret', 'FASTNET_OPA_WEBHOOK_SECRET');
    if ('' !== $legacy_key) {
        $channel = $legacy_key;
        $token = $legacy_key;
        $signature = $legacy_key;
    }
    $parts = wp_parse_url($api_url);
    $valid_channel = (bool) preg_match('/^[a-zA-Z0-9_-]{1,100}$/', $channel);
    $valid_url = is_array($parts)
        && isset($parts['scheme'], $parts['host'])
        && 'https' === strtolower($parts['scheme'])
        && empty($parts['query'])
        && empty($parts['fragment']);

    return array(
        'api_url' => $valid_url ? untrailingslashit(esc_url_raw($api_url)) : '',
        'channel_id' => $valid_channel ? $channel : '',
        'token' => $token,
        'webhook_secret' => $signature,
        'legacy' => '' !== $legacy_key,
        'ready' => $valid_url && $valid_channel && '' !== $token && '' !== $signature,
    );
}

/**
 * Calcula o limite de anexo levando em conta o upload original e o base64
 * dentro do JSON (que também consome post_max_size).
 */
function fastnet_chat_max_upload_bytes()
{
    $max_bytes = (int) wp_max_upload_size();
    if ($max_bytes < 1) { return 0; }

    $post_max = function_exists('wp_convert_hr_to_bytes')
        ? (int) wp_convert_hr_to_bytes(ini_get('post_max_size'))
        : 0;

    if ($post_max > 0) {
        // Reserva espaço para JSON/cabeçalhos e considera a expansão do base64.
        $safe_base64_limit = (int) floor(max(0, $post_max - (256 * 1024)) / 1.4);
        $max_bytes = min($max_bytes, $safe_base64_limit);
    }

    return max(0, min($max_bytes, 10 * 1024 * 1024));
}

/** Apenas dados não secretos são disponibilizados ao navegador. */
function fastnet_chat_browser_config()
{
    return array(
        'restUrl' => esc_url_raw(rest_url('fastnet-chat/v1')),
        'maxUploadBytes' => fastnet_chat_max_upload_bytes(),
        'fallbackUrl' => esc_url_raw(get_theme_mod('victortech_central_url', '')),
    );
}
