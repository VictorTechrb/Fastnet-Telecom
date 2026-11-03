<?php
/**
 * Configura o atendimento online oficial da Fastnet Telecom.
 * Mantém a compatibilidade com os dados gravados pelas versões anteriores.
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Recupera as configurações privadas e os dados já gravados no WordPress.
 */
function fastnet_obter_configuracoes_privadas_atendimento()
{
    static $configuracoes = null;

    if (null !== $configuracoes) {
        return $configuracoes;
    }

    $configuracoes = get_option('fastnet_chat_settings', array());

    if (!is_array($configuracoes)) {
        $configuracoes = array();
    }

    $arquivo_privado = getenv('FASTNET_CHAT_PRIVATE_CONFIG');

    if (
        is_string($arquivo_privado)
        && '' !== trim($arquivo_privado)
        && is_readable($arquivo_privado)
        && is_file($arquivo_privado)
    ) {
        $configuracoes_arquivo = require $arquivo_privado;

        if (is_array($configuracoes_arquivo)) {
            $configuracoes = array_merge($configuracoes, $configuracoes_arquivo);
        }
    }

    return $configuracoes;
}

/**
 * Obtém uma configuração usando primeiro as variáveis do servidor.
 */
function fastnet_obter_valor_atendimento($chave, $variaveis_servidor = array(), $chaves_antigas = array())
{
    foreach ($variaveis_servidor as $variavel) {
        $valor = getenv($variavel);

        if (false !== $valor && '' !== trim($valor)) {
            return trim($valor);
        }
    }

    $configuracoes = fastnet_obter_configuracoes_privadas_atendimento();

    if (isset($configuracoes[$chave]) && is_scalar($configuracoes[$chave])) {
        $valor = trim((string) $configuracoes[$chave]);

        if ('' !== $valor) {
            return $valor;
        }
    }

    foreach ($chaves_antigas as $chave_antiga) {
        if (isset($configuracoes[$chave_antiga]) && is_scalar($configuracoes[$chave_antiga])) {
            $valor = trim((string) $configuracoes[$chave_antiga]);

            if ('' !== $valor) {
                return $valor;
            }
        }
    }

    return '';
}

/**
 * Valida os campos e preserva os dados que já estavam salvos.
 */
function fastnet_validar_configuracoes_atendimento($entrada)
{
    $entrada = is_array($entrada) ? $entrada : array();
    $atuais = get_option('fastnet_chat_settings', array());

    if (!is_array($atuais)) {
        $atuais = array();
    }

    $endereco_atual = isset($atuais['url_opa'])
        ? (string) $atuais['url_opa']
        : (isset($atuais['api_url']) ? (string) $atuais['api_url'] : '');

    $identificador_atual = isset($atuais['identificador_canal'])
        ? (string) $atuais['identificador_canal']
        : (isset($atuais['legacy_key'])
            ? (string) $atuais['legacy_key']
            : (isset($atuais['channel_id']) ? (string) $atuais['channel_id'] : ''));

    $endereco_opa = isset($entrada['url_opa'])
        ? esc_url_raw(trim((string) $entrada['url_opa']))
        : $endereco_atual;

    $identificador_canal = isset($entrada['identificador_canal'])
        ? trim(sanitize_text_field((string) $entrada['identificador_canal']))
        : '';

    $partes_endereco = wp_parse_url($endereco_opa);

    $endereco_valido = is_array($partes_endereco)
        && isset($partes_endereco['scheme'], $partes_endereco['host'])
        && 'https' === strtolower($partes_endereco['scheme'])
        && empty($partes_endereco['user'])
        && empty($partes_endereco['pass'])
        && empty($partes_endereco['query'])
        && empty($partes_endereco['fragment'])
        && (!isset($partes_endereco['path']) || '' === $partes_endereco['path'] || '/' === $partes_endereco['path']);

    if (!$endereco_valido) {
        $endereco_opa = $endereco_atual;
    }

    if ('' === $identificador_canal) {
        $identificador_canal = $identificador_atual;
    }

    if (
        '' !== $identificador_canal
        && !preg_match('/^[a-zA-Z0-9_-]{1,128}$/', $identificador_canal)
    ) {
        $identificador_canal = $identificador_atual;
    }

    return array(
        'url_opa' => untrailingslashit($endereco_opa),
        'identificador_canal' => $identificador_canal,
    );
}

/**
 * Registra as opções do atendimento no painel do WordPress.
 */
function fastnet_registrar_configuracoes_atendimento()
{
    // O nome da opção é preservado para não perder as configurações existentes.
    register_setting(
        'fastnet_chat_settings',
        'fastnet_chat_settings',
        array('sanitize_callback' => 'fastnet_validar_configuracoes_atendimento')
    );
}
add_action('admin_init', 'fastnet_registrar_configuracoes_atendimento');

/**
 * Exibe a página de configuração do atendimento online.
 */
function fastnet_exibir_configuracoes_atendimento()
{
    if (!current_user_can('manage_options')) {
        return;
    }

    $configuracoes = fastnet_obter_configuracoes_privadas_atendimento();

    $endereco_opa = isset($configuracoes['url_opa'])
        ? $configuracoes['url_opa']
        : (isset($configuracoes['api_url']) ? $configuracoes['api_url'] : 'https://lara.fastnettelecom.com');

    $identificador_salvo = !empty($configuracoes['identificador_canal'])
        || !empty($configuracoes['legacy_key'])
        || !empty($configuracoes['channel_id']);
    ?>
    <div class="wrap">
        <h1>Atendimento online Fastnet</h1>
        <p>Configure a conexão com o Webchat oficial do Opa! Suite utilizado no site da Fastnet Telecom.</p>
        <p><strong>Endereço do Opa! Suite:</strong> informe somente o domínio principal, sem caminhos adicionais.</p>
        <p><strong>Identificador do canal:</strong> utilize o segundo parâmetro do código de instalação do Webchat fornecido pelo OPA. Ele identifica o canal público de atendimento e não é a senha de um usuário.</p>

        <form method="post" action="options.php">
            <?php settings_fields('fastnet_chat_settings'); ?>

            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row">
                        <label for="fastnet-atendimento-url">Endereço do Opa! Suite</label>
                    </th>
                    <td>
                        <input
                            name="fastnet_chat_settings[url_opa]"
                            id="fastnet-atendimento-url"
                            type="url"
                            class="regular-text"
                            value="<?php echo esc_attr($endereco_opa); ?>"
                            placeholder="https://lara.fastnettelecom.com"
                            required
                        >
                        <p class="description">Endereço utilizado para carregar o atendimento online da Fastnet.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="fastnet-atendimento-identificador">Identificador do canal de atendimento</label>
                    </th>
                    <td>
                        <input
                            name="fastnet_chat_settings[identificador_canal]"
                            id="fastnet-atendimento-identificador"
                            type="password"
                            class="regular-text"
                            value=""
                            autocomplete="new-password"
                            placeholder="<?php echo $identificador_salvo ? esc_attr__('Já configurado — deixe vazio para manter', 'victortech') : esc_attr__('Informe o identificador do canal', 'victortech'); ?>"
                        >
                        <p class="description">Deixe em branco para manter o identificador já salvo.</p>
                    </td>
                </tr>
            </table>

            <?php submit_button('Salvar configuração do atendimento'); ?>
        </form>
    </div>
    <?php
}

/**
 * Adiciona o atendimento online às configurações do WordPress.
 */
function fastnet_adicionar_menu_configuracoes_atendimento()
{
    add_options_page(
        'Atendimento online Fastnet',
        'Atendimento Fastnet',
        'manage_options',
        'fastnet-chat-settings',
        'fastnet_exibir_configuracoes_atendimento'
    );
}
add_action('admin_menu', 'fastnet_adicionar_menu_configuracoes_atendimento');

/**
 * Entrega ao carregador somente os dados necessários para iniciar o Webchat.
 */
function fastnet_obter_configuracao_webchat()
{
    $endereco_opa = fastnet_obter_valor_atendimento(
        'url_opa',
        array('FASTNET_OPA_WEBCHAT_URL', 'FASTNET_OPA_API_URL'),
        array('api_url')
    );

    $identificador_canal = fastnet_obter_valor_atendimento(
        'identificador_canal',
        array('FASTNET_OPA_WEBCHAT_TOKEN', 'FASTNET_OPA_LEGACY_KEY'),
        array('legacy_key', 'channel_id')
    );

    $partes_endereco = wp_parse_url($endereco_opa);

    $endereco_valido = is_array($partes_endereco)
        && isset($partes_endereco['scheme'], $partes_endereco['host'])
        && 'https' === strtolower($partes_endereco['scheme'])
        && empty($partes_endereco['user'])
        && empty($partes_endereco['pass'])
        && empty($partes_endereco['query'])
        && empty($partes_endereco['fragment'])
        && (!isset($partes_endereco['path']) || '' === $partes_endereco['path'] || '/' === $partes_endereco['path']);

    $identificador_valido = (bool) preg_match('/^[a-zA-Z0-9_-]{1,128}$/', $identificador_canal);

    return array(
        'endereco' => $endereco_valido ? untrailingslashit(esc_url_raw($endereco_opa)) : '',
        'identificador' => $identificador_valido ? $identificador_canal : '',
        'pronto' => $endereco_valido && $identificador_valido,
    );
}
