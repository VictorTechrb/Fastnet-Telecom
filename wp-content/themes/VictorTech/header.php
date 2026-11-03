<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<?php
// Dados 100% editáveis via Aparência > Personalizar (com fallback seguro)
$telefone_suporte = get_theme_mod( 'fastnet_telefone_suporte', '(79) 3045-4880' );
$link_central_ixc = get_theme_mod( 'fastnet_link_central', 'https://ixc.fastnettelecom.com/central_assinante_web/login/' );
$texto_botao_ixc  = get_theme_mod( 'fastnet_texto_central', 'Central do Assinante' );
$link_velocimetro = get_theme_mod( 'fastnet_link_velocimetro', 'https://speedixc.fastnettelecom.com/' );
?>

<header class="cabecalho-fastnet">
    <!-- Barra Superior Slim (Atalhos + Localidade + Suporte) -->
    <div class="barra-superior-fastnet">
        <div class="container-cabecalho">
            <div class="atalhos-cabecalho">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="ativo">Para você</a>
                <a href="<?php echo esc_url( home_url( '/para-empresas/' ) ); ?>">Para empresas</a>
            </div>
            <div class="contatos-cabecalho">
                <span class="telefone-topo">Central: <strong><?php echo esc_html( $telefone_suporte ); ?></strong></span>
                <a class="link-topo-suporte" href="<?php echo esc_url( $link_velocimetro ); ?>">Velocímetro</a>
            </div>
        </div>
    </div>

    <!-- Barra Principal Vermelha Fastnet -->
    <div class="barra-principal-fastnet">
        <div class="container-cabecalho">
            <a class="marca-fastnet" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
                <?php if ( function_exists( 'has_custom_logo' ) && has_custom_logo() ) : ?>
                    <?php the_custom_logo(); ?>
                <?php elseif ( file_exists( get_template_directory() . '/assets/images/fastbranco.webp' ) ) : ?>
                    <img
                        class="logo-fastnet"
                        src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/fastbranco.webp' ); ?>"
                        alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>"
                        width="154"
                        height="42"
                    >
                <?php else : ?>
                    <span class="marca-fastnet-nome">FASTNET TELECOM</span>
                    <span class="marca-fastnet-descricao">Empresa de internet e telecomunicações dentro de sergipe com melhor ping para jogos e seus trabalhos.</span>
                <?php endif; ?>
            </a>

            <nav class="navegacao-fastnet" id="menu-principal-fastnet" aria-label="Menu principal de navegação">
                <?php
                wp_nav_menu(
                    array(
                        'theme_location' => 'menu-principal',
                        'container'      => false,
                        'fallback_cb'    => false,
                        'menu_class'     => 'menu-fastnet',
                    )
                );
                ?>
                <ul class="menu-fastnet">
                    <li><a href="<?php echo esc_url( home_url( '/sobrenos/' ) ); ?>">Sobre Nós</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/contato/' ) ); ?>">Contato</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/contratoseguranca/' ) ); ?>">Contratos e Segurança</a></li>
                </ul>
            </nav>

            <div class="acoes-direita-cabecalho">
                <a class="botao-pilula-assinante" href="<?php echo esc_url( $link_central_ixc ); ?>" target="_blank" rel="noopener noreferrer">
                    <?php echo esc_html( $texto_botao_ixc ); ?>
                </a>
                <button class="botao-menu-mobile" type="button" aria-expanded="false" aria-controls="menu-principal-fastnet">
                    ☰
                </button>
            </div>
        </div>
    </div>
</header>