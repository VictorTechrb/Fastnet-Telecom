<?php
/**
 * Configuração e carregamento do chat OPA.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function fastnet_obter_configuracao_chat_opa() {
    $padrao = array(
        'ativo'                => 0,
        'url'                  => '',
        'token'                => '',
        'permitir_login_anonimo' => 1,
        'facebook_appid'       => '',
        'google_credential'    => '',
        'google_oauth'         => '',
    );

    $salvas = get_option( 'fastnet_chat_opa', array() );

    return wp_parse_args( is_array( $salvas ) ? $salvas : array(), $padrao );
}

function fastnet_sanitizar_configuracao_chat_opa( $entrada ) {
    $entrada = is_array( $entrada ) ? $entrada : array();

    return array(
        'ativo'                  => empty( $entrada['ativo'] ) ? 0 : 1,
        'url'                    => isset( $entrada['url'] ) ? untrailingslashit( esc_url_raw( $entrada['url'] ) ) : '',
        'token'                  => isset( $entrada['token'] ) ? sanitize_text_field( $entrada['token'] ) : '',
        'permitir_login_anonimo' => empty( $entrada['permitir_login_anonimo'] ) ? 0 : 1,
        'facebook_appid'         => isset( $entrada['facebook_appid'] ) ? sanitize_text_field( $entrada['facebook_appid'] ) : '',
        'google_credential'      => isset( $entrada['google_credential'] ) ? sanitize_text_field( $entrada['google_credential'] ) : '',
        'google_oauth'           => isset( $entrada['google_oauth'] ) ? sanitize_text_field( $entrada['google_oauth'] ) : '',
    );
}

function fastnet_registrar_configuracao_chat_opa() {
    register_setting(
        'fastnet_chat_opa_grupo',
        'fastnet_chat_opa',
        array( 'sanitize_callback' => 'fastnet_sanitizar_configuracao_chat_opa' )
    );
}
add_action( 'admin_init', 'fastnet_registrar_configuracao_chat_opa' );

function fastnet_adicionar_pagina_chat_opa() {
    add_theme_page(
        'Configuração do chat',
        'Chat OPA',
        'manage_options',
        'fastnet-chat-opa',
        'fastnet_renderizar_pagina_chat_opa'
    );
}
add_action( 'admin_menu', 'fastnet_adicionar_pagina_chat_opa' );

function fastnet_renderizar_pagina_chat_opa() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $opcoes = fastnet_obter_configuracao_chat_opa();
    ?>
    <div class="wrap">
        <h1>Configuração do chat OPA</h1>
        <p>As credenciais ficam salvas nas configurações do WordPress e não no código do tema.</p>
        <form method="post" action="options.php">
            <?php settings_fields( 'fastnet_chat_opa_grupo' ); ?>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row">Chat</th>
                    <td>
                        <label for="fastnet-chat-opa-ativo">
                            <input id="fastnet-chat-opa-ativo" name="fastnet_chat_opa[ativo]" type="checkbox" value="1" <?php checked( $opcoes['ativo'], 1 ); ?>>
                            Ativar chat no site
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="fastnet-chat-opa-url">URL base</label></th>
                    <td>
                        <input class="regular-text" id="fastnet-chat-opa-url" name="fastnet_chat_opa[url]" type="url" value="<?php echo esc_attr( $opcoes['url'] ); ?>" placeholder="https://seu-dominio.com">
                        <p class="description">Exemplo: https://site.fastnettelecom.com</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="fastnet-chat-opa-token">Token</label></th>
                    <td>
                        <input class="regular-text" id="fastnet-chat-opa-token" name="fastnet_chat_opa[token]" type="password" value="<?php echo esc_attr( $opcoes['token'] ); ?>" autocomplete="new-password">
                    </td>
                </tr>
                <tr>
                    <th scope="row">Login</th>
                    <td>
                        <label for="fastnet-chat-opa-anonimo">
                            <input id="fastnet-chat-opa-anonimo" name="fastnet_chat_opa[permitir_login_anonimo]" type="checkbox" value="1" <?php checked( $opcoes['permitir_login_anonimo'], 1 ); ?>>
                            Permitir login anônimo
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="fastnet-chat-opa-facebook">Facebook App ID</label></th>
                    <td><input class="regular-text" id="fastnet-chat-opa-facebook" name="fastnet_chat_opa[facebook_appid]" type="text" value="<?php echo esc_attr( $opcoes['facebook_appid'] ); ?>"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="fastnet-chat-opa-google-credential">Google Credential</label></th>
                    <td><input class="regular-text" id="fastnet-chat-opa-google-credential" name="fastnet_chat_opa[google_credential]" type="text" value="<?php echo esc_attr( $opcoes['google_credential'] ); ?>"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="fastnet-chat-opa-google-oauth">Google OAuth</label></th>
                    <td><input class="regular-text" id="fastnet-chat-opa-google-oauth" name="fastnet_chat_opa[google_oauth]" type="text" value="<?php echo esc_attr( $opcoes['google_oauth'] ); ?>"></td>
                </tr>
            </table>
            <?php submit_button( 'Salvar configuração' ); ?>
        </form>
    </div>
    <?php
}

function fastnet_carregar_chat_opa() {
    if ( is_admin() ) {
        return;
    }

    $opcoes = fastnet_obter_configuracao_chat_opa();

    if ( empty( $opcoes['ativo'] ) || empty( $opcoes['url'] ) || empty( $opcoes['token'] ) ) {
        return;
    }

    $base_url = untrailingslashit( $opcoes['url'] );
    $script   = $base_url . '/js/opa.js';
    $config   = array(
        'permitir_login_anonimo' => ! empty( $opcoes['permitir_login_anonimo'] ) ? 'on' : 'off',
        'facebook_appid'         => $opcoes['facebook_appid'],
        'google_credential'      => $opcoes['google_credential'],
        'google_oauth'           => $opcoes['google_oauth'],
    );
    ?>
    <style id="fastnet-chat-opa-position">
        .opa_ico {
            display: none !important;
        }

        .fastnet-chat-btn {
            position: fixed;
            right: 24px;
            bottom: 24px;
            z-index: 9999;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            min-height: 60px;
            padding: 12px 18px;
            border: 0;
            border-radius: 999px;
            background: #0e5ae8;
            color: #fff;
            cursor: pointer;
            font-family: inherit;
            text-decoration: none;
            box-shadow: 0 8px 24px rgba(14, 90, 232, .25);
            transition: background .2s ease, transform .2s ease, box-shadow .2s ease;
        }

        .fastnet-chat-btn[hidden] {
            display: none;
        }

        .fastnet-chat-icon {
            display: flex;
            flex-shrink: 0;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
        }

        .fastnet-chat-icon svg {
            display: block;
            width: 26px;
            height: 26px;
            fill: none;
            stroke: #fff;
        }

        .fastnet-chat-text {
            display: flex;
            flex-direction: column;
            gap: 3px;
            line-height: 1.2;
            text-align: left;
        }

        .fastnet-chat-text strong {
            color: #fff;
            font-size: 14px;
            font-weight: 700;
            white-space: nowrap;
        }

        .fastnet-chat-text small {
            color: #dce9ff;
            font-size: 11px;
        }

        .fastnet-chat-arrow {
            width: 17px;
            height: 17px;
            margin-left: 4px;
        }

        .fastnet-chat-btn:hover {
            background: #0846b8;
            color: #fff;
            transform: translateY(-3px);
            box-shadow: 0 12px 28px rgba(14, 90, 232, .35);
        }

        .fastnet-chat-btn:focus-visible {
            outline: 3px solid #fff;
            outline-offset: 3px;
        }

        @media (max-width: 600px) {
            .fastnet-chat-btn {
                right: 16px;
                bottom: calc(16px + env(safe-area-inset-bottom, 0px));
                gap: 9px;
                min-height: 54px;
                padding: 11px 14px;
            }

            .fastnet-chat-icon svg {
                width: 23px;
                height: 23px;
            }

            .fastnet-chat-text strong {
                font-size: 12px;
            }

            .fastnet-chat-text small {
                font-size: 10px;
            }

            .fastnet-chat-arrow {
                display: none;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .fastnet-chat-btn {
                transition: none;
            }

            .fastnet-chat-btn:hover {
                transform: none;
            }
        }
    </style>
    <button class="fastnet-chat-btn" type="button" aria-label="Falar com atendente" hidden>
        <span class="fastnet-chat-icon" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 14v-3a9 9 0 0 1 18 0v3"/>
                <path d="M5 13h2v7H5a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2Z"/>
                <path d="M19 13h-2v7h2a2 2 0 0 1 2-2v-3a2 2 0 0 1-2-2Z"/>
                <path d="M19 20a4 4 0 0 1-4 3h-3"/>
            </svg>
        </span>
        <span class="fastnet-chat-text">
            <strong>Falar com atendente</strong>
            <small>Atendimento Fastnet</small>
        </span>
        <svg class="fastnet-chat-arrow" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M5 12h13M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </button>
    <script>
    (function (w, d, src, token, config) {
        var chatButton = d.querySelector('.fastnet-chat-btn');
        var script = d.createElement('script');
        script.async = true;
        script.src = src;
        d.head.appendChild(script);
        script.onload = function () {
            if (typeof opa !== 'undefined' && typeof opa.init === 'function') {
                opa.init(src.replace(/\/js\/opa\.js$/, ''), token, config);
                chatButton.hidden = false;
                chatButton.addEventListener('click', function () {
                    if (opa.estado_minimize_div_chat === false) {
                        opa.chat();
                    } else if (opa.estrutura.chat) {
                        opa.estrutura.chat.style.display = 'flex';
                        if (opa.estrutura.rodape_input) {
                            opa.estrutura.rodape_input.focus();
                        }
                    }
                });
            }
        };
    })(window, document, <?php echo wp_json_encode( $script ); ?>, <?php echo wp_json_encode( $opcoes['token'] ); ?>, <?php echo wp_json_encode( wp_json_encode( $config ) ); ?>);
    </script>
    <?php
}
add_action( 'wp_footer', 'fastnet_carregar_chat_opa', 20 );
