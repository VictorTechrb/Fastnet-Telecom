<?php
/**
 * Gerenciamento de conteúdo da Fastnet Telecom.
 * Cadastros nativos do WordPress: planos, aplicativos, aplicativos inclusos e banners.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Registra os conteúdos editáveis no painel.
 */
function fastnet_registrar_tipos_de_conteudo() {
    $tipos = array(
        'plano' => array(
            'singular' => 'Plano',
            'plural'   => 'Planos',
            'icone'    => 'dashicons-chart-area',
            'posicao'  => 25,
            'publico'  => false,
            'suportes' => array( 'title', 'page-attributes' ),
        ),
        'aplicativo' => array(
            'singular' => 'Aplicativo',
            'plural'   => 'Aplicativos',
            'icone'    => 'dashicons-smartphone',
            'posicao'  => 26,
            'publico'  => true,
            'suportes' => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes' ),
        ),
        'aplicativo_incluso' => array(
            'singular' => 'Aplicativo incluso',
            'plural'   => 'Aplicativos inclusos',
            'icone'    => 'dashicons-awards',
            'posicao'  => 27,
            'publico'  => false,
            'suportes' => array( 'title', 'page-attributes' ),
        ),
        'banner_fastnet' => array(
            'singular' => 'Banner',
            'plural'   => 'Carrossel',
            'icone'    => 'dashicons-images-alt2',
            'posicao'  => 28,
            'publico'  => false,
            'suportes' => array( 'title', 'page-attributes' ),
        ),
    );

    foreach ( $tipos as $slug => $configuracao ) {
        $singular = $configuracao['singular'];
        $plural   = $configuracao['plural'];

        $rotulos = array(
            'name'               => $plural,
            'singular_name'      => $singular,
            'add_new'            => 'Adicionar novo',
            'add_new_item'       => 'Adicionar novo ' . strtolower( $singular ),
            'edit_item'          => 'Editar ' . strtolower( $singular ),
            'new_item'           => 'Novo ' . strtolower( $singular ),
            'view_item'          => 'Visualizar ' . strtolower( $singular ),
            'view_items'         => 'Visualizar ' . strtolower( $plural ),
            'search_items'       => 'Buscar ' . strtolower( $plural ),
            'not_found'          => 'Nenhum registro encontrado.',
            'not_found_in_trash' => 'Nenhum registro na lixeira.',
            'all_items'          => 'Todos os ' . strtolower( $plural ),
            'menu_name'          => $plural,
            'name_admin_bar'     => $singular,
        );

        $argumentos = array(
            'labels'             => $rotulos,
            'public'             => $configuracao['publico'],
            'publicly_queryable' => $configuracao['publico'],
            'exclude_from_search'=> ! $configuracao['publico'],
            'show_ui'            => true,
            'show_in_menu'       => true,
            'show_in_rest'       => true,
            'has_archive'        => 'aplicativo' === $slug,
            'rewrite'            => 'aplicativo' === $slug ? array( 'slug' => 'aplicativos', 'with_front' => false ) : false,
            'supports'           => $configuracao['suportes'],
            'menu_icon'          => $configuracao['icone'],
            'menu_position'      => $configuracao['posicao'],
            'hierarchical'       => false,
            'map_meta_cap'       => true,
        );

        register_post_type( $slug, $argumentos );
    }
}
add_action( 'init', 'fastnet_registrar_tipos_de_conteudo' );

/**
 * Atualiza as rotas uma única vez após a instalação dos novos tipos de conteúdo.
 */
function fastnet_atualizar_regras_de_reescrita() {
    if ( '1' === get_option( 'fastnet_gerenciador_regras_versao' ) ) {
        return;
    }

    flush_rewrite_rules( false );
    update_option( 'fastnet_gerenciador_regras_versao', '1' );
}
add_action( 'init', 'fastnet_atualizar_regras_de_reescrita', 99 );

/**
 * Lê os dados adicionais salvos em cada cadastro.
 */
function fastnet_obter_dados_do_conteudo( $post_id ) {
    $dados = get_post_meta( $post_id, '_fastnet_dados_conteudo', true );
    return is_array( $dados ) ? $dados : array();
}

/**
 * Formata valores monetários digitados com vírgula ou ponto.
 */
function fastnet_normalizar_preco( $valor ) {
    $valor = preg_replace( '/[^0-9,\.\-]/', '', (string) $valor );

    if ( false !== strpos( $valor, ',' ) && false !== strpos( $valor, '.' ) ) {
        if ( strrpos( $valor, ',' ) > strrpos( $valor, '.' ) ) {
            $valor = str_replace( '.', '', $valor );
            $valor = str_replace( ',', '.', $valor );
        } else {
            $valor = str_replace( ',', '', $valor );
        }
    } else {
        $valor = str_replace( ',', '.', $valor );
    }

    return max( 0, (float) $valor );
}

/**
 * Renderiza campos padronizados dentro das caixas de edição.
 */
function fastnet_renderizar_campo_admin( $chave, $rotulo, $valor, $tipo = 'text', $instrucoes = '', $opcoes = array() ) {
    $id   = 'fastnet-campo-' . sanitize_html_class( $chave );
    $nome = 'fastnet_dados[' . $chave . ']';

    echo '<div class="fastnet-campo-admin">';
    echo '<label for="' . esc_attr( $id ) . '"><strong>' . esc_html( $rotulo ) . '</strong></label>';

    if ( 'textarea' === $tipo ) {
        echo '<textarea class="widefat" rows="3" id="' . esc_attr( $id ) . '" name="' . esc_attr( $nome ) . '">' . esc_textarea( $valor ) . '</textarea>';
    } elseif ( 'select' === $tipo ) {
        echo '<select class="widefat" id="' . esc_attr( $id ) . '" name="' . esc_attr( $nome ) . '">';
        foreach ( $opcoes as $valor_opcao => $rotulo_opcao ) {
            echo '<option value="' . esc_attr( $valor_opcao ) . '" ' . selected( (string) $valor, (string) $valor_opcao, false ) . '>' . esc_html( $rotulo_opcao ) . '</option>';
        }
        echo '</select>';
    } elseif ( 'checkbox' === $tipo ) {
        echo '<label class="fastnet-opcao-marcacao"><input type="checkbox" id="' . esc_attr( $id ) . '" name="' . esc_attr( $nome ) . '" value="1" ' . checked( (string) $valor, '1', false ) . '> Sim</label>';
    } else {
        $atributos = '';
        if ( 'number' === $tipo ) {
            $atributos .= ' step="' . esc_attr( isset( $opcoes['step'] ) ? $opcoes['step'] : '1' ) . '"';
            $atributos .= ' min="' . esc_attr( isset( $opcoes['min'] ) ? $opcoes['min'] : '0' ) . '"';
        }
        echo '<input class="widefat" type="' . esc_attr( $tipo ) . '" id="' . esc_attr( $id ) . '" name="' . esc_attr( $nome ) . '" value="' . esc_attr( $valor ) . '"' . $atributos . '>';
    }

    if ( '' !== $instrucoes ) {
        echo '<p class="description">' . esc_html( $instrucoes ) . '</p>';
    }
    echo '</div>';
}

/**
 * Campo de imagem usando a biblioteca de mídia do WordPress.
 */
function fastnet_renderizar_campo_imagem_admin( $dados, $rotulo, $instrucoes = '' ) {
    $imagem_id = isset( $dados['imagem_id'] ) ? absint( $dados['imagem_id'] ) : 0;
    ?>
    <div class="fastnet-campo-admin fastnet-campo-imagem">
        <strong><?php echo esc_html( $rotulo ); ?></strong>
        <input type="hidden" name="fastnet_dados[imagem_id]" value="<?php echo esc_attr( $imagem_id ); ?>">
        <div class="fastnet-previa-imagem">
            <?php
            if ( $imagem_id ) {
                echo wp_get_attachment_image( $imagem_id, 'medium', false, array( 'alt' => '' ) );
            }
            ?>
        </div>
        <button type="button" class="button fastnet-selecionar-imagem" data-titulo="<?php echo esc_attr( $rotulo ); ?>">Selecionar imagem</button>
        <button type="button" class="button-link-delete fastnet-remover-imagem" <?php echo $imagem_id ? '' : 'hidden'; ?>>Remover imagem</button>
        <?php if ( $instrucoes ) : ?>
            <p class="description"><?php echo esc_html( $instrucoes ); ?></p>
        <?php endif; ?>
    </div>
    <?php
}

/**
 * Caixa de edição de planos.
 */
function fastnet_metabox_plano( $post ) {
    $dados = fastnet_obter_dados_do_conteudo( $post->ID );
    wp_nonce_field( 'fastnet_salvar_dados_conteudo', 'fastnet_nonce_conteudo' );

    echo '<div class="fastnet-grade-campos-admin">';
    fastnet_renderizar_campo_admin( 'velocidade', 'Velocidade (Mega)', isset( $dados['velocidade'] ) ? $dados['velocidade'] : '', 'number', 'Exemplo: 500.', array( 'step' => '1', 'min' => '1' ) );
    fastnet_renderizar_campo_admin( 'preco', 'Preço mensal (R$)', isset( $dados['preco'] ) ? $dados['preco'] : '', 'number', 'Informe o valor promocional mensal. Exemplo: 89,90.', array( 'step' => '0.01', 'min' => '0' ) );
    fastnet_renderizar_campo_admin( 'descricao_preco', 'Descrição abaixo do preço', isset( $dados['descricao_preco'] ) ? $dados['descricao_preco'] : '', 'text', 'Texto curto exibido logo abaixo do valor.' );
    fastnet_renderizar_campo_admin( 'beneficios', 'Benefícios do plano', isset( $dados['beneficios'] ) ? $dados['beneficios'] : '', 'textarea', 'Digite um benefício por linha.' );
    fastnet_renderizar_campo_admin( 'link_contratacao', 'Link para contratação', isset( $dados['link_contratacao'] ) ? $dados['link_contratacao'] : '', 'url', 'Opcional. Se ficar vazio, será usado o WhatsApp definido nas configurações da página inicial.' );
    fastnet_renderizar_campo_admin( 'destaque', 'Destacar este plano', isset( $dados['destaque'] ) ? $dados['destaque'] : '0', 'checkbox', 'Exibe um selo de destaque no cartão do plano.' );
    echo '</div>';
}

/**
 * Caixa de edição dos aplicativos disponíveis para contratação adicional.
 */
function fastnet_metabox_aplicativo( $post ) {
    $dados = fastnet_obter_dados_do_conteudo( $post->ID );
    wp_nonce_field( 'fastnet_salvar_dados_conteudo', 'fastnet_nonce_conteudo' );

    echo '<p>Use a imagem destacada do WordPress como logomarca do aplicativo. Os preços abaixo são os valores adicionais por categoria.</p>';
    echo '<div class="fastnet-grade-campos-admin">';
    fastnet_renderizar_campo_admin( 'categoria_app', 'Categoria', isset( $dados['categoria_app'] ) ? $dados['categoria_app'] : '', 'select', '', array(
        ''              => 'Selecione uma categoria',
        'Streaming'     => 'Streaming',
        'Música'        => 'Música',
        'Segurança'     => 'Segurança',
        'Jogos e rede'  => 'Jogos e rede',
        'Outros'        => 'Outros',
    ) );
    fastnet_renderizar_campo_admin( 'descricao_curta', 'Descrição curta', isset( $dados['descricao_curta'] ) ? $dados['descricao_curta'] : '', 'textarea', 'Resumo exibido na listagem de aplicativos.' );
    fastnet_renderizar_campo_admin( 'link_site', 'Site oficial', isset( $dados['link_site'] ) ? $dados['link_site'] : '', 'url' );
    fastnet_renderizar_campo_admin( 'link_play_store', 'Link da Google Play', isset( $dados['link_play_store'] ) ? $dados['link_play_store'] : '', 'url' );
    fastnet_renderizar_campo_admin( 'link_app_store', 'Link da App Store', isset( $dados['link_app_store'] ) ? $dados['link_app_store'] : '', 'url' );
    echo '</div>';

    echo '<h3>Preços adicionais por categoria</h3>';
    echo '<p class="description">Preencha somente as categorias em que este aplicativo estiver disponível. Deixe em branco ou use zero quando não se aplicar.</p>';
    echo '<div class="fastnet-grade-campos-admin fastnet-grade-precos-admin">';
    fastnet_renderizar_campo_admin( 'preco_padrao', 'Padrão (R$)', isset( $dados['preco_padrao'] ) ? $dados['preco_padrao'] : '', 'number', '', array( 'step' => '0.01', 'min' => '0' ) );
    fastnet_renderizar_campo_admin( 'preco_avancado', 'Avançado (R$)', isset( $dados['preco_avancado'] ) ? $dados['preco_avancado'] : '', 'number', '', array( 'step' => '0.01', 'min' => '0' ) );
    fastnet_renderizar_campo_admin( 'preco_top', 'TOP (R$)', isset( $dados['preco_top'] ) ? $dados['preco_top'] : '', 'number', '', array( 'step' => '0.01', 'min' => '0' ) );
    fastnet_renderizar_campo_admin( 'preco_premium', 'Premium (R$)', isset( $dados['preco_premium'] ) ? $dados['preco_premium'] : '', 'number', '', array( 'step' => '0.01', 'min' => '0' ) );
    echo '</div>';
}

/**
 * Caixa de edição dos aplicativos inclusos nos planos.
 */
function fastnet_metabox_aplicativo_incluso( $post ) {
    $dados = fastnet_obter_dados_do_conteudo( $post->ID );
    wp_nonce_field( 'fastnet_salvar_dados_conteudo', 'fastnet_nonce_conteudo' );
    fastnet_renderizar_campo_imagem_admin( $dados, 'Logomarca do aplicativo', 'Selecione a imagem e preencha o texto alternativo na biblioteca de mídia.' );
    fastnet_renderizar_campo_admin( 'descricao_curta', 'Descrição curta', isset( $dados['descricao_curta'] ) ? $dados['descricao_curta'] : '', 'text', 'Opcional. Usada como informação complementar.' );
}

/**
 * Caixa de edição de cada banner do carrossel.
 */
function fastnet_metabox_banner( $post ) {
    $dados = fastnet_obter_dados_do_conteudo( $post->ID );
    wp_nonce_field( 'fastnet_salvar_dados_conteudo', 'fastnet_nonce_conteudo' );
    fastnet_renderizar_campo_imagem_admin( $dados, 'Imagem do banner', 'Use uma imagem horizontal. O texto alternativo deve ser preenchido na biblioteca de mídia.' );
    echo '<div class="fastnet-grade-campos-admin">';
    fastnet_renderizar_campo_admin( 'subtitulo', 'Texto complementar', isset( $dados['subtitulo'] ) ? $dados['subtitulo'] : '', 'textarea', 'Opcional. Aparece junto ao título do banner.' );
    fastnet_renderizar_campo_admin( 'link', 'Link do botão', isset( $dados['link'] ) ? $dados['link'] : '', 'url', 'Pode apontar para uma página interna ou endereço externo.' );
    fastnet_renderizar_campo_admin( 'texto_botao', 'Texto do botão', isset( $dados['texto_botao'] ) ? $dados['texto_botao'] : 'Conhecer ofertas', 'text' );
    echo '</div>';
    echo '<p class="description">Use o campo Ordem, na lateral do editor, para mudar a posição deste banner no carrossel.</p>';
}

/**
 * Registra todas as caixas de edição.
 */
function fastnet_adicionar_caixas_de_conteudo() {
    add_meta_box( 'fastnet-dados-plano', 'Informações do plano', 'fastnet_metabox_plano', 'plano', 'normal', 'high' );
    add_meta_box( 'fastnet-dados-aplicativo', 'Informações do aplicativo', 'fastnet_metabox_aplicativo', 'aplicativo', 'normal', 'high' );
    add_meta_box( 'fastnet-dados-aplicativo-incluso', 'Informações do aplicativo incluso', 'fastnet_metabox_aplicativo_incluso', 'aplicativo_incluso', 'normal', 'high' );
    add_meta_box( 'fastnet-dados-banner', 'Conteúdo do banner', 'fastnet_metabox_banner', 'banner_fastnet', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'fastnet_adicionar_caixas_de_conteudo' );

/**
 * Salva os campos com validação, permissões e sanitização.
 */
function fastnet_salvar_dados_dos_conteudos( $post_id, $post, $update ) {
    $tipos_permitidos = array( 'plano', 'aplicativo', 'aplicativo_incluso', 'banner_fastnet' );

    if ( ! $post || ! in_array( $post->post_type, $tipos_permitidos, true ) ) {
        return;
    }

    if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $post_id ) ) {
        return;
    }

    if ( ! isset( $_POST['fastnet_nonce_conteudo'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['fastnet_nonce_conteudo'] ) ), 'fastnet_salvar_dados_conteudo' ) ) {
        return;
    }

    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    $recebidos = isset( $_POST['fastnet_dados'] ) && is_array( $_POST['fastnet_dados'] ) ? wp_unslash( $_POST['fastnet_dados'] ) : array();
    $dados     = array();

    if ( 'plano' === $post->post_type ) {
        $dados['velocidade']       = isset( $recebidos['velocidade'] ) ? absint( $recebidos['velocidade'] ) : 0;
        $dados['preco']            = isset( $recebidos['preco'] ) ? fastnet_normalizar_preco( $recebidos['preco'] ) : 0;
        $dados['descricao_preco']  = isset( $recebidos['descricao_preco'] ) ? sanitize_text_field( $recebidos['descricao_preco'] ) : '';
        $dados['beneficios']       = isset( $recebidos['beneficios'] ) ? sanitize_textarea_field( $recebidos['beneficios'] ) : '';
        $dados['link_contratacao'] = isset( $recebidos['link_contratacao'] ) ? esc_url_raw( $recebidos['link_contratacao'] ) : '';
        $dados['destaque']         = isset( $recebidos['destaque'] ) ? '1' : '0';
    } elseif ( 'aplicativo' === $post->post_type ) {
        $categorias_validas = array( 'Streaming', 'Música', 'Segurança', 'Jogos e rede', 'Outros' );
        $categoria          = isset( $recebidos['categoria_app'] ) ? sanitize_text_field( $recebidos['categoria_app'] ) : '';
        $dados['categoria_app'] = in_array( $categoria, $categorias_validas, true ) ? $categoria : '';
        $dados['descricao_curta'] = isset( $recebidos['descricao_curta'] ) ? sanitize_textarea_field( $recebidos['descricao_curta'] ) : '';
        $dados['link_site'] = isset( $recebidos['link_site'] ) ? esc_url_raw( $recebidos['link_site'] ) : '';
        $dados['link_play_store'] = isset( $recebidos['link_play_store'] ) ? esc_url_raw( $recebidos['link_play_store'] ) : '';
        $dados['link_app_store'] = isset( $recebidos['link_app_store'] ) ? esc_url_raw( $recebidos['link_app_store'] ) : '';
        foreach ( array( 'preco_padrao', 'preco_avancado', 'preco_top', 'preco_premium' ) as $campo_preco ) {
            $dados[ $campo_preco ] = isset( $recebidos[ $campo_preco ] ) ? fastnet_normalizar_preco( $recebidos[ $campo_preco ] ) : 0;
        }
    } elseif ( 'aplicativo_incluso' === $post->post_type ) {
        $imagem_id = isset( $recebidos['imagem_id'] ) ? absint( $recebidos['imagem_id'] ) : 0;
        $dados['imagem_id'] = $imagem_id && wp_attachment_is_image( $imagem_id ) ? $imagem_id : 0;
        $dados['descricao_curta'] = isset( $recebidos['descricao_curta'] ) ? sanitize_text_field( $recebidos['descricao_curta'] ) : '';
    } elseif ( 'banner_fastnet' === $post->post_type ) {
        $imagem_id = isset( $recebidos['imagem_id'] ) ? absint( $recebidos['imagem_id'] ) : 0;
        $dados['imagem_id'] = $imagem_id && wp_attachment_is_image( $imagem_id ) ? $imagem_id : 0;
        $dados['subtitulo'] = isset( $recebidos['subtitulo'] ) ? sanitize_textarea_field( $recebidos['subtitulo'] ) : '';
        $dados['link'] = isset( $recebidos['link'] ) ? esc_url_raw( $recebidos['link'] ) : '';
        $dados['texto_botao'] = isset( $recebidos['texto_botao'] ) ? sanitize_text_field( $recebidos['texto_botao'] ) : 'Conhecer ofertas';
    }

    update_post_meta( $post_id, '_fastnet_dados_conteudo', $dados );
}
add_action( 'save_post', 'fastnet_salvar_dados_dos_conteudos', 10, 3 );

/**
 * Configurações gerais de conteúdo da página inicial.
 */
function fastnet_obter_configuracoes_pagina_inicial() {
    $padrao = array(
        'home_h1_seo'          => 'Internet 100% Fibra Óptica de alta velocidade',
        'home_titulo_planos'   => 'Internet pensada para a sua rotina',
        'home_subtitulo_planos'=> 'Planos 100% fibra óptica, estabilidade total e instalação rápida.',
        'apps_inclusos_colunas' => 4,
        'apps_inclusos_linhas'  => 2,
        'whatsapp_url'          => get_theme_mod( 'victortech_central_url', '' ),
    );

    $salvas = get_option( 'fastnet_pagina_inicial', array() );
    return wp_parse_args( is_array( $salvas ) ? $salvas : array(), $padrao );
}

function fastnet_sanitizar_configuracoes_pagina_inicial( $entrada ) {
    $entrada = is_array( $entrada ) ? $entrada : array();

    return array(
        'home_h1_seo'           => isset( $entrada['home_h1_seo'] ) ? sanitize_text_field( $entrada['home_h1_seo'] ) : '',
        'home_titulo_planos'    => isset( $entrada['home_titulo_planos'] ) ? sanitize_text_field( $entrada['home_titulo_planos'] ) : '',
        'home_subtitulo_planos' => isset( $entrada['home_subtitulo_planos'] ) ? sanitize_textarea_field( $entrada['home_subtitulo_planos'] ) : '',
        'apps_inclusos_colunas' => min( 6, max( 1, absint( isset( $entrada['apps_inclusos_colunas'] ) ? $entrada['apps_inclusos_colunas'] : 4 ) ) ),
        'apps_inclusos_linhas'  => min( 10, max( 1, absint( isset( $entrada['apps_inclusos_linhas'] ) ? $entrada['apps_inclusos_linhas'] : 2 ) ) ),
        'whatsapp_url'          => isset( $entrada['whatsapp_url'] ) ? esc_url_raw( $entrada['whatsapp_url'] ) : '',
    );
}

function fastnet_registrar_configuracoes_pagina_inicial() {
    register_setting(
        'fastnet_pagina_inicial_grupo',
        'fastnet_pagina_inicial',
        array( 'sanitize_callback' => 'fastnet_sanitizar_configuracoes_pagina_inicial' )
    );
}
add_action( 'admin_init', 'fastnet_registrar_configuracoes_pagina_inicial' );

function fastnet_adicionar_pagina_configuracoes() {
    add_theme_page(
        'Configurações da página inicial',
        'Página inicial Fastnet',
        'manage_options',
        'fastnet-pagina-inicial',
        'fastnet_renderizar_pagina_configuracoes'
    );
}
add_action( 'admin_menu', 'fastnet_adicionar_pagina_configuracoes' );

function fastnet_renderizar_pagina_configuracoes() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $opcoes = fastnet_obter_configuracoes_pagina_inicial();
    ?>
    <div class="wrap">
        <h1>Configurações da página inicial</h1>
        <p>Edite os textos principais da página inicial e a organização dos aplicativos inclusos nos planos.</p>
        <form method="post" action="options.php">
            <?php settings_fields( 'fastnet_pagina_inicial_grupo' ); ?>
            <h2>Textos da página inicial</h2>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label for="fastnet-home-h1">Título principal (H1)</label></th>
                    <td><input class="regular-text" id="fastnet-home-h1" name="fastnet_pagina_inicial[home_h1_seo]" type="text" value="<?php echo esc_attr( $opcoes['home_h1_seo'] ); ?>"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="fastnet-home-planos-titulo">Título da seção de planos</label></th>
                    <td><input class="regular-text" id="fastnet-home-planos-titulo" name="fastnet_pagina_inicial[home_titulo_planos]" type="text" value="<?php echo esc_attr( $opcoes['home_titulo_planos'] ); ?>"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="fastnet-home-planos-subtitulo">Descrição da seção de planos</label></th>
                    <td><textarea class="large-text" rows="3" id="fastnet-home-planos-subtitulo" name="fastnet_pagina_inicial[home_subtitulo_planos]"><?php echo esc_textarea( $opcoes['home_subtitulo_planos'] ); ?></textarea></td>
                </tr>
                <tr>
                    <th scope="row"><label for="fastnet-home-whatsapp">Link padrão para contratação pelo WhatsApp</label></th>
                    <td>
                        <input class="regular-text" id="fastnet-home-whatsapp" name="fastnet_pagina_inicial[whatsapp_url]" type="url" value="<?php echo esc_attr( $opcoes['whatsapp_url'] ); ?>" placeholder="https://wa.me/55...">
                        <p class="description">O link definido em cada plano terá prioridade sobre este endereço.</p>
                    </td>
                </tr>
            </table>
            <h2>Aplicativos inclusos nos planos</h2>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label for="fastnet-apps-colunas">Colunas de ícones</label></th>
                    <td><input class="small-text" id="fastnet-apps-colunas" name="fastnet_pagina_inicial[apps_inclusos_colunas]" type="number" min="1" max="6" step="1" value="<?php echo esc_attr( $opcoes['apps_inclusos_colunas'] ); ?>"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="fastnet-apps-linhas">Linhas de ícones</label></th>
                    <td><input class="small-text" id="fastnet-apps-linhas" name="fastnet_pagina_inicial[apps_inclusos_linhas]" type="number" min="1" max="10" step="1" value="<?php echo esc_attr( $opcoes['apps_inclusos_linhas'] ); ?>"></td>
                </tr>
            </table>
            <?php submit_button( 'Salvar configurações' ); ?>
        </form>
    </div>
    <?php
}

/**
 * Mantém compatibilidade com campos ACF antigos da página inicial.
 */
function fastnet_campo_home( $nome_campo, $padrao = '' ) {
    $opcoes = get_option( 'fastnet_pagina_inicial', array() );

    if ( is_array( $opcoes ) && isset( $opcoes[ $nome_campo ] ) && '' !== $opcoes[ $nome_campo ] ) {
        return $opcoes[ $nome_campo ];
    }

    if ( function_exists( 'get_field' ) ) {
        $valor_acf = get_field( $nome_campo );
        if ( null !== $valor_acf && false !== $valor_acf && '' !== $valor_acf ) {
            return $valor_acf;
        }
    }

    return get_theme_mod( $nome_campo, $padrao );
}

/**
 * Alias para textos editáveis usados em componentes da página inicial.
 */
function fastnet_campo( $nome_campo, $padrao = '' ) {
    return fastnet_campo_home( $nome_campo, $padrao );
}

/**
 * Retorna os banners cadastrados no painel em ordem de exibição.
 */
function fastnet_obter_banners() {
    $publicacoes = get_posts( array(
        'post_type'      => 'banner_fastnet',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'ASC' ),
    ) );

    $banners = array();

    foreach ( $publicacoes as $publicacao ) {
        $dados = fastnet_obter_dados_do_conteudo( $publicacao->ID );

        if ( empty( $dados['imagem_id'] ) ) {
            continue;
        }

        $banners[] = array(
            'imagem'       => absint( $dados['imagem_id'] ),
            'titulo'       => get_the_title( $publicacao->ID ),
            'subtitulo'    => isset( $dados['subtitulo'] ) ? $dados['subtitulo'] : '',
            'link'         => isset( $dados['link'] ) ? $dados['link'] : '',
            'texto_botao'  => isset( $dados['texto_botao'] ) ? $dados['texto_botao'] : 'Conhecer ofertas',
        );
    }

    return $banners;
}

/**
 * Retorna os aplicativos adicionais agrupados por categoria de preço.
 */
function fastnet_obter_aplicativos_por_categoria() {
    $categorias = array(
        'padrao'    => array( 'titulo' => 'Padrão', 'apps' => array() ),
        'avancado'  => array( 'titulo' => 'Avançado', 'apps' => array() ),
        'top'       => array( 'titulo' => 'TOP', 'apps' => array() ),
        'premium'   => array( 'titulo' => 'Premium', 'apps' => array() ),
    );

    $publicacoes = get_posts( array(
        'post_type'      => 'aplicativo',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
    ) );

    $campos_preco = array(
        'padrao'   => 'preco_padrao',
        'avancado' => 'preco_avancado',
        'top'      => 'preco_top',
        'premium'  => 'preco_premium',
    );

    foreach ( $publicacoes as $publicacao ) {
        $dados     = fastnet_obter_dados_do_conteudo( $publicacao->ID );
        $imagem_id = get_post_thumbnail_id( $publicacao->ID );

        foreach ( $campos_preco as $categoria => $campo_preco ) {
            $preco = isset( $dados[ $campo_preco ] ) ? (float) $dados[ $campo_preco ] : 0;

            if ( $preco <= 0 ) {
                continue;
            }

            $categorias[ $categoria ]['apps'][] = array(
                'nome'      => get_the_title( $publicacao->ID ),
                'preco'     => $preco,
                'imagem_id' => $imagem_id,
                'descricao' => isset( $dados['descricao_curta'] ) ? $dados['descricao_curta'] : '',
            );
        }
    }

    return $categorias;
}

/**
 * Retorna cada aplicativo adicional uma única vez para a lista dos planos.
 */
function fastnet_obter_aplicativos_adicionais() {
    $aplicativos = array();
    $publicacoes = get_posts( array(
        'post_type'      => 'aplicativo',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
    ) );

    foreach ( $publicacoes as $publicacao ) {
        $dados  = fastnet_obter_dados_do_conteudo( $publicacao->ID );
        $precos = array_filter(
            array(
                isset( $dados['preco_padrao'] ) ? (float) $dados['preco_padrao'] : 0,
                isset( $dados['preco_avancado'] ) ? (float) $dados['preco_avancado'] : 0,
                isset( $dados['preco_top'] ) ? (float) $dados['preco_top'] : 0,
                isset( $dados['preco_premium'] ) ? (float) $dados['preco_premium'] : 0,
            ),
            function ( $preco ) {
                return $preco > 0;
            }
        );

        if ( empty( $precos ) ) {
            continue;
        }

        $aplicativos[] = array(
            'nome'      => get_the_title( $publicacao->ID ),
            'preco'     => min( $precos ),
            'imagem_id' => get_post_thumbnail_id( $publicacao->ID ),
        );
    }

    return $aplicativos;
}

/**
 * Retorna os aplicativos que aparecem como inclusos nos cartões dos planos.
 */
function fastnet_obter_aplicativos_inclusos() {
    return get_posts( array(
        'post_type'      => 'aplicativo_incluso',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
    ) );
}

/**
 * Carrega os recursos do seletor de imagens somente nas telas necessárias.
 */
function fastnet_carregar_recursos_admin_conteudo( $pagina ) {
    if ( ! function_exists( 'get_current_screen' ) ) {
        return;
    }

    $tela = get_current_screen();
    $tipos = array( 'plano', 'aplicativo', 'aplicativo_incluso', 'banner_fastnet' );

    if ( ! $tela || ! in_array( $tela->post_type, $tipos, true ) ) {
        return;
    }

    $diretorio = get_template_directory();
    $endereco  = get_template_directory_uri();

    wp_enqueue_style(
        'fastnet-administracao-conteudos',
        $endereco . '/assets/css/administracao-conteudos.css',
        array(),
        file_exists( $diretorio . '/assets/css/administracao-conteudos.css' ) ? filemtime( $diretorio . '/assets/css/administracao-conteudos.css' ) : '1.0.0'
    );

    if ( ! in_array( $tela->post_type, array( 'aplicativo_incluso', 'banner_fastnet' ), true ) ) {
        return;
    }

    wp_enqueue_media();

    wp_enqueue_script(
        'fastnet-administracao-conteudos',
        $endereco . '/assets/js/administracao-conteudos.js',
        array( 'jquery' ),
        file_exists( $diretorio . '/assets/js/administracao-conteudos.js' ) ? filemtime( $diretorio . '/assets/js/administracao-conteudos.js' ) : '1.0.0',
        true
    );
}
add_action( 'admin_enqueue_scripts', 'fastnet_carregar_recursos_admin_conteudo' );

/**
 * Colunas úteis nas listas do painel.
 */
function fastnet_colunas_lista_planos( $colunas ) {
    return array(
        'cb'        => isset( $colunas['cb'] ) ? $colunas['cb'] : '',
        'title'     => 'Plano',
        'velocidade'=> 'Velocidade',
        'preco'     => 'Preço mensal',
        'destaque'  => 'Destaque',
        'date'      => 'Data',
    );
}
add_filter( 'manage_plano_posts_columns', 'fastnet_colunas_lista_planos' );

function fastnet_colunas_lista_aplicativos( $colunas ) {
    return array(
        'cb'        => isset( $colunas['cb'] ) ? $colunas['cb'] : '',
        'title'     => 'Aplicativo',
        'categoria' => 'Categoria',
        'precos'    => 'Preços cadastrados',
        'date'      => 'Data',
    );
}
add_filter( 'manage_aplicativo_posts_columns', 'fastnet_colunas_lista_aplicativos' );

function fastnet_colunas_lista_aplicativos_inclusos( $colunas ) {
    return array(
        'cb'    => isset( $colunas['cb'] ) ? $colunas['cb'] : '',
        'title' => 'Aplicativo incluso',
        'logo'  => 'Logomarca',
        'date'  => 'Data',
    );
}
add_filter( 'manage_aplicativo_incluso_posts_columns', 'fastnet_colunas_lista_aplicativos_inclusos' );

function fastnet_colunas_lista_banners( $colunas ) {
    return array(
        'cb'        => isset( $colunas['cb'] ) ? $colunas['cb'] : '',
        'title'     => 'Banner',
        'imagem'    => 'Imagem',
        'subtitulo' => 'Texto complementar',
        'date'      => 'Data',
    );
}
add_filter( 'manage_banner_fastnet_posts_columns', 'fastnet_colunas_lista_banners' );

function fastnet_renderizar_colunas_lista( $coluna, $post_id ) {
    $tipo  = get_post_type( $post_id );
    $dados = fastnet_obter_dados_do_conteudo( $post_id );

    if ( 'plano' === $tipo ) {
        if ( 'velocidade' === $coluna && ! empty( $dados['velocidade'] ) ) {
            echo esc_html( number_format_i18n( (int) $dados['velocidade'] ) . ' Mega' );
        } elseif ( 'preco' === $coluna && isset( $dados['preco'] ) ) {
            echo esc_html( 'R$ ' . number_format_i18n( (float) $dados['preco'], 2 ) );
        } elseif ( 'destaque' === $coluna ) {
            echo ! empty( $dados['destaque'] ) ? 'Sim' : 'Não';
        }
    } elseif ( 'aplicativo' === $tipo ) {
        if ( 'categoria' === $coluna ) {
            echo esc_html( isset( $dados['categoria_app'] ) ? $dados['categoria_app'] : '—' );
        } elseif ( 'precos' === $coluna ) {
            $quantidade = 0;
            foreach ( array( 'preco_padrao', 'preco_avancado', 'preco_top', 'preco_premium' ) as $campo ) {
                if ( ! empty( $dados[ $campo ] ) ) {
                    $quantidade++;
                }
            }
            echo esc_html( $quantidade . ' de 4 categorias' );
        }
    } elseif ( 'aplicativo_incluso' === $tipo && 'logo' === $coluna ) {
        if ( ! empty( $dados['imagem_id'] ) ) {
            echo wp_get_attachment_image( absint( $dados['imagem_id'] ), array( 48, 48 ), false, array( 'style' => 'max-width:48px;height:auto;' ) );
        } else {
            echo 'Sem imagem';
        }
    } elseif ( 'banner_fastnet' === $tipo ) {
        if ( 'imagem' === $coluna && ! empty( $dados['imagem_id'] ) ) {
            echo wp_get_attachment_image( absint( $dados['imagem_id'] ), array( 80, 45 ), false, array( 'style' => 'max-width:80px;height:auto;' ) );
        } elseif ( 'subtitulo' === $coluna ) {
            echo esc_html( isset( $dados['subtitulo'] ) ? wp_trim_words( $dados['subtitulo'], 12 ) : '' );
        }
    }
}
add_action( 'manage_posts_custom_column', 'fastnet_renderizar_colunas_lista', 10, 2 );
