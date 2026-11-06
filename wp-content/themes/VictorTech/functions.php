<?php
/**
 * Configurações e recursos do tema VictorTech.
 */

function victortech_setup() {
    add_theme_support( 'title-tag' );
    add_theme_support( 'custom-logo' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );

    register_nav_menus(
        array(
            'menu-principal' => 'Menu principal',
        )
    );
}
add_action( 'after_setup_theme', 'victortech_setup' );

function victortech_enqueue_assets() {
    $tema_uri = get_template_directory_uri();
    $tema_dir = get_template_directory();

    wp_enqueue_style(
        'victortech-style',
        get_stylesheet_uri(),
        array(),
        filemtime( $tema_dir . '/style.css' )
    );

    wp_enqueue_script(
        'victortech-header',
        $tema_uri . '/assets/js/header.js',
        array(),
        filemtime( $tema_dir . '/assets/js/header.js' ),
        true
    );

    wp_enqueue_script(
        'victortech-carousel',
        $tema_uri . '/assets/js/carousel.js',
        array(),
        filemtime( $tema_dir . '/assets/js/carousel.js' ),
        true
    );

    wp_enqueue_style(
        'victortech-conteudos-dinamicos',
        $tema_uri . '/assets/css/conteudos-dinamicos.css',
        array( 'victortech-style' ),
        filemtime( $tema_dir . '/assets/css/conteudos-dinamicos.css' )
    );

    wp_enqueue_script(
        'victortech-plan-customizer',
        $tema_uri . '/assets/js/plan-customizer.js',
        array(),
        filemtime( $tema_dir . '/assets/js/plan-customizer.js' ),
        true
    );

    if ( is_post_type_archive( 'aplicativo' ) ) {
        wp_enqueue_script(
            'victortech-catalogo-aplicativos',
            $tema_uri . '/assets/js/catalogo-aplicativos.js',
            array(),
            filemtime( $tema_dir . '/assets/js/catalogo-aplicativos.js' ),
            true
        );
    }
}
add_action( 'wp_enqueue_scripts', 'victortech_enqueue_assets' );


/**
 * Carrega os cadastros editáveis do painel da Fastnet.
 */
require_once get_template_directory() . '/inc/gerenciador-conteudo.php';
