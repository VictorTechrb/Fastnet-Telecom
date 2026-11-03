<?php
function victortech_customize_register( $wp_customize ) {
    $wp_customize->add_section( 'victortech_atendimento', array( 'title' => __( 'Dados Gerais e Atendimento', 'victortech' ), 'priority' => 30 ) );

    $public_fields = array(
        'victortech_0800' => array( 'Número 0800', '0800', 'sanitize_text_field' ),
        'victortech_central_url' => array( 'Link público da Central/WhatsApp', 'url', 'esc_url_raw' ),
        'victortech_header_text' => array( 'Texto do cabeçalho', 'text', 'sanitize_text_field' ),
        'victortech_footer_text' => array( 'Texto de direitos autorais do rodapé', 'text', 'sanitize_text_field' ),
    );
    foreach ( $public_fields as $id => $field ) {
        $wp_customize->add_setting( $id, array( 'default' => '', 'sanitize_callback' => $field[2] ) );
        $wp_customize->add_control( $id, array( 'section' => 'victortech_atendimento', 'label' => $field[0], 'type' => $field[1] ) );
    }

    $wp_customize->add_setting( 'victortech_opa_api_url', array( 'default' => '', 'sanitize_callback' => 'esc_url_raw', 'capability' => 'manage_options' ) );
    $wp_customize->add_control( 'victortech_opa_api_url', array(
        'section' => 'victortech_atendimento',
        'label' => __( 'URL interna da API de mensagens OPA', 'victortech' ),
        'description' => __( 'Uso interno do proxy server-side. Solicite a rota oficial ao fornecedor.', 'victortech' ),
        'type' => 'url',
        'capability' => 'manage_options',
    ) );

    $wp_customize->add_setting( 'victortech_opa_token', array( 'default' => '', 'sanitize_callback' => 'sanitize_text_field', 'capability' => 'manage_options' ) );
    $wp_customize->add_control( 'victortech_opa_token', array(
        'section' => 'victortech_atendimento',
        'label' => __( 'Token interno OPA', 'victortech' ),
        'description' => __( 'Nunca é enviado ao navegador. Preferencialmente use VICTORTECH_OPA_TOKEN no ambiente.', 'victortech' ),
        'type' => 'password',
        'capability' => 'manage_options',
    ) );
}
add_action( 'customize_register', 'victortech_customize_register' );

function victortech_chat_config() {
    return array(
        'url' => esc_url( get_theme_mod( 'victortech_central_url', '' ) ),
        'phone' => sanitize_text_field( get_theme_mod( 'victortech_0800', '' ) ),
    );
}

function victortech_opa_token() {
    $token = defined( 'FASTNET_OPA_TOKEN' ) ? (string) FASTNET_OPA_TOKEN : '';
    if ( '' === trim( $token ) && defined( 'VICTORTECH_OPA_TOKEN' ) ) {
        $token = (string) VICTORTECH_OPA_TOKEN;
    }
    if ( '' === trim( $token ) && defined( 'VICTORTECH_OPA_TOKEN_FILE' ) && file_exists( VICTORTECH_OPA_TOKEN_FILE ) ) {
        $token = (string) file_get_contents( VICTORTECH_OPA_TOKEN_FILE );
    }
    if ( '' === trim( $token ) ) {
        $token = (string) getenv( 'VICTORTECH_OPA_TOKEN' );
    }
    if ( '' === trim( $token ) ) {
        $token = (string) get_theme_mod( 'victortech_opa_token', '' );
    }
    return trim( $token );
}

function victortech_enqueue_opa() {
    /*
     * O atendimento agora é controlado pelo widget Fastnet.
     * O SDK original cria um segundo popup e ignora a persistência local.
     * A interface web do OPA continua disponível pelo link de fallback.
     */
}
add_action( 'wp_enqueue_scripts', 'victortech_enqueue_opa', 20 );

function victortech_opa_proxy() {
    check_ajax_referer( 'fastnet_opa_proxy', 'nonce' );

    // Log para depuração
    error_log('Chat Proxy Access: ' . print_r($_POST, true));

    $message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';

    $conversation_id = isset( $_POST['conversation_id'] ) ? sanitize_text_field( wp_unslash( $_POST['conversation_id'] ) ) : '';
    $city = isset( $_POST['city'] ) ? sanitize_text_field( wp_unslash( $_POST['city'] ) ) : '';
    $configured_url = defined( 'FASTNET_OPA_BASE_URL' ) ? (string) FASTNET_OPA_BASE_URL : '';
    $api_url = '' !== trim( $configured_url )
        ? esc_url_raw( $configured_url )
        : esc_url_raw( get_theme_mod( 'victortech_opa_api_url', '' ) );
    if ( '' === $api_url ) {
        $api_url = 'https://lara.fastnettelecom.com';
    }
    $token = victortech_opa_token();

    if ( '' === $message || strlen( $message ) > 2000 ) {
        wp_send_json_error( array( 'message' => __( 'Informe uma mensagem válida.', 'victortech' ) ) );
    }
    if ( '' === $api_url ) {
        wp_send_json_error( array( 'message' => __( 'A Central ainda não está configurada pelo administrador.', 'victortech' ) ) );
    }
    $api_host = wp_parse_url( $api_url, PHP_URL_HOST );
    $api_path = trim( (string) wp_parse_url( $api_url, PHP_URL_PATH ), '/' );
    if ( 'lara.fastnettelecom.com' === strtolower( (string) $api_host ) && '' === $api_path ) {
        wp_send_json_success(
            array(
                'transport' => 'web',
                'url'       => $api_url,
            )
        );
    }
    if ( '' === $token ) {
        wp_send_json_error( array( 'message' => __( 'A Central ainda não está configurada pelo administrador.', 'victortech' ) ) );
    }

    $request_args = array(
        'timeout' => 15,
        'headers' => array(
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ),
        'body' => wp_json_encode( array(
            'message' => $message,
            'conversation_id' => $conversation_id,
            'city' => $city,
        ) ),
    );
    $is_local = false !== strpos( home_url(), '.local' ) || ( defined( 'WP_DEBUG' ) && WP_DEBUG );
    if ( $is_local ) {
        $request_args['sslverify'] = false;
    }

    $response = wp_remote_post( $api_url, $request_args );

    if ( is_wp_error( $response ) ) {
        $error_message = $response->get_error_message();
        wp_send_json_error( array(
            'message' => $error_message,
            'technical' => $error_message,
        ) );
    }
    $status = wp_remote_retrieve_response_code( $response );
    $raw_body = wp_remote_retrieve_body( $response );
    $body = json_decode( $raw_body, true );
    if ( $status < 200 || $status >= 300 ) {
        $error_message = sprintf( 'OPA! Suite retornou o status HTTP %d.', $status );
        wp_send_json_error( array(
            'message' => $error_message,
            'technical' => $error_message,
        ) );
    }
    if ( '' === trim( $raw_body ) ) {
        wp_send_json_success(
            array(
                'transport' => 'web',
                'url'       => $api_url,
            )
        );
    }
    if ( ! is_array( $body ) ) {
        $content_type = wp_remote_retrieve_header( $response, 'content-type' );
        if ( false !== strpos( strtolower( (string) $content_type ), 'text/html' ) ) {
            wp_send_json_success(
                array(
                    'transport' => 'web',
                    'url'       => $api_url,
                )
            );
        }
        wp_send_json_error( array( 'message' => __( 'A Central retornou uma resposta que não pôde ser interpretada.', 'victortech' ) ) );
    }
    wp_send_json_success( $body );
}
add_action( 'wp_ajax_fastnet_opa_proxy', 'victortech_opa_proxy' );
add_action( 'wp_ajax_nopriv_fastnet_opa_proxy', 'victortech_opa_proxy' );
