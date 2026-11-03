<?php
function victortech_setup()
{
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo', [
        'height' => 80,
        'width' => 240,
        'flex-height' => true,
        'flex-width' => true,
    ]);
    register_nav_menus([
        'menu-principal' => __('Menu principal', 'victortech'),
    ]);
}
add_action('after_setup_theme', 'victortech_setup');

function victortech_register_plan_post_type()
{
    register_post_type('plano', [
        'labels' => [
            'name' => 'Planos',
            'singular_name' => 'Plano',
            'add_new' => 'Adicionar plano',
            'add_new_item' => 'Adicionar novo plano',
            'edit_item' => 'Editar plano',
            'new_item' => 'Novo plano',
            'view_item' => 'Ver plano',
            'search_items' => 'Buscar planos',
            'not_found' => 'Nenhum plano encontrado',
            'menu_name' => 'Planos',
        ],
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_icon' => 'dashicons-wifi',
        'supports' => ['title', 'page-attributes'],
        'show_in_rest' => true,
    ]);
}
add_action('init', 'victortech_register_plan_post_type');

function victortech_enqueue_assets()
{
    $style = get_template_directory() . '/style.css';

    // Google Fonts - Carregado corretamente via Enqueue
    wp_enqueue_style(
        'victortech-fonts',
        'https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Inter:wght@400;600;700;800&display=swap',
        [],
        null
    );

    // FontAwesome para ícones
    wp_enqueue_style(
        'font-awesome',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
        [],
        '6.4.0'
    );

    wp_enqueue_style(
        'victortech-style',
        get_stylesheet_uri(),
        ['victortech-fonts', 'font-awesome'],
        file_exists($style) ? filemtime($style) : wp_get_theme()->get('Version')
    );

    wp_enqueue_script(
        'victortech-carousel',
        get_template_directory_uri() . '/assets/js/carousel.js',
        [],
        wp_get_theme()->get('Version'),
        true
    );
    wp_enqueue_script(
        'victortech-header',
        get_template_directory_uri() . '/assets/js/header.js',
        [],
        wp_get_theme()->get('Version'),
        true
    );
    wp_enqueue_script(
        'victortech-plan-customizer',
        get_template_directory_uri() . '/assets/js/plan-customizer.js',
        [],
        wp_get_theme()->get('Version'),
        true
    );
    if (is_front_page() || is_home()) {
        $simulador = get_template_directory() . '/assets/js/simulador.js';
        if (file_exists($simulador)) {
            wp_enqueue_script(
                'victortech-simulador',
                get_template_directory_uri() . '/assets/js/simulador.js',
                [],
                filemtime($simulador),
                true
            );
        }
    }
}
add_action('wp_enqueue_scripts', 'victortech_enqueue_assets');

function victortech_atendimento_route()
{
    add_rewrite_rule(
        '^atendimento/?$',
        'index.php?victortech_atendimento=1',
        'top'
    );
}
add_action('init', 'victortech_atendimento_route');

function victortech_atendimento_query_var($vars)
{
    $vars[] = 'victortech_atendimento';
    return $vars;
}
add_filter('query_vars', 'victortech_atendimento_query_var');

function victortech_atendimento_redirect()
{
    if (!victortech_is_atendimento_request()) {
        return;
    }

    global $wp_query;
    $wp_query->is_404 = false;
    status_header(200);
}
add_action('template_redirect', 'victortech_atendimento_redirect', 1);

function victortech_atendimento_template($template)
{
    if (victortech_is_atendimento_request()) {
        status_header(200);
        $custom_template = get_theme_file_path('pagina-atendimento.php');
        if (file_exists($custom_template)) {
            return $custom_template;
        }
    }
    return $template;
}
add_filter('template_include', 'victortech_atendimento_template');

function victortech_atendimento_body_class($classes)
{
    if (victortech_is_atendimento_request()) {
        $classes[] = 'pagina-atendimento-body';
    }
    return $classes;
}
add_filter('body_class', 'victortech_atendimento_body_class');

function victortech_is_atendimento_request()
{
    if (get_query_var('victortech_atendimento')) {
        return true;
    }

    $request_path = wp_parse_url(
        wp_unslash($_SERVER['REQUEST_URI']),
        PHP_URL_PATH
    );
    return is_string($request_path) &&
        (bool) preg_match('#/atendimento/?$#', $request_path);
}

function victortech_atendimento_flush_rewrites()
{
    victortech_atendimento_route();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'victortech_atendimento_flush_rewrites');

function victortech_register_home_fields()
{
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group([
        'key' => 'group_victortech_home',
        'title' => 'Campos da Home',
        'fields' => [
            [
                'key' => 'field_carrossel_1',
                'label' => 'Carrossel_1',
                'name' => 'carrossel_1',
                'type' => 'image',
                'return_format' => 'array',
                'preview_size' => 'large',
            ],
            [
                'key' => 'field_carrossel_2',
                'label' => 'Carrossel_2',
                'name' => 'carrossel_2',
                'type' => 'image',
                'return_format' => 'array',
                'preview_size' => 'large',
            ],
            [
                'key' => 'field_carrossel_3',
                'label' => 'Carrossel_3',
                'name' => 'carrossel_3',
                'type' => 'image',
                'return_format' => 'array',
                'preview_size' => 'large',
            ],
            [
                'key' => 'field_carrossel_4',
                'label' => 'Carrossel_4',
                'name' => 'carrossel_4',
                'type' => 'image',
                'return_format' => 'array',
                'preview_size' => 'large',
            ],
            [
                'key' => 'field_carrossel_textos',
                'label' => 'Textos opcionais dos banners',
                'name' => 'carrossel_textos',
                'type' => 'message',
                'message' =>
                    'Os campos abaixo são opcionais. Se a arte já tiver textos, deixe-os vazios para exibir somente a imagem.',
            ],
            [
                'key' => 'field_carrossel_titulo_1',
                'label' => 'Carrossel 1 - Título',
                'name' => 'carrossel_titulo_1',
                'type' => 'text',
            ],
            [
                'key' => 'field_carrossel_subtitulo_1',
                'label' => 'Carrossel 1 - Texto',
                'name' => 'carrossel_subtitulo_1',
                'type' => 'textarea',
                'rows' => 2,
            ],
            [
                'key' => 'field_carrossel_link_1',
                'label' => 'Carrossel 1 - Link',
                'name' => 'carrossel_link_1',
                'type' => 'url',
            ],
            [
                'key' => 'field_carrossel_titulo_2',
                'label' => 'Carrossel 2 - Título',
                'name' => 'carrossel_titulo_2',
                'type' => 'text',
            ],
            [
                'key' => 'field_carrossel_subtitulo_2',
                'label' => 'Carrossel 2 - Texto',
                'name' => 'carrossel_subtitulo_2',
                'type' => 'textarea',
                'rows' => 2,
            ],
            [
                'key' => 'field_carrossel_link_2',
                'label' => 'Carrossel 2 - Link',
                'name' => 'carrossel_link_2',
                'type' => 'url',
            ],
            [
                'key' => 'field_carrossel_titulo_3',
                'label' => 'Carrossel 3 - Título',
                'name' => 'carrossel_titulo_3',
                'type' => 'text',
            ],
            [
                'key' => 'field_carrossel_subtitulo_3',
                'label' => 'Carrossel 3 - Texto',
                'name' => 'carrossel_subtitulo_3',
                'type' => 'textarea',
                'rows' => 2,
            ],
            [
                'key' => 'field_carrossel_link_3',
                'label' => 'Carrossel 3 - Link',
                'name' => 'carrossel_link_3',
                'type' => 'url',
            ],
            [
                'key' => 'field_carrossel_titulo_4',
                'label' => 'Carrossel 4 - Título',
                'name' => 'carrossel_titulo_4',
                'type' => 'text',
            ],
            [
                'key' => 'field_carrossel_subtitulo_4',
                'label' => 'Carrossel 4 - Texto',
                'name' => 'carrossel_subtitulo_4',
                'type' => 'textarea',
                'rows' => 2,
            ],
            [
                'key' => 'field_carrossel_link_4',
                'label' => 'Carrossel 4 - Link',
                'name' => 'carrossel_link_4',
                'type' => 'url',
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'page_type',
                    'operator' => '==',
                    'value' => 'front_page',
                ],
            ],
        ],
    ]);
}
add_action('acf/init', 'victortech_register_home_fields');

function victortech_register_plan_fields()
{
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group([
        'key' => 'group_victortech_plan',
        'title' => 'Dados do plano',
        'fields' => [
            [
                'key' => 'field_plano_velocidade',
                'label' => 'Velocidade',
                'name' => 'plano_velocidade',
                'type' => 'text',
                'wrapper' => ['width' => '33'],
            ],
            [
                'key' => 'field_plano_unidade',
                'label' => 'Unidade',
                'name' => 'plano_unidade',
                'type' => 'text',
                'default_value' => 'Mega',
                'wrapper' => ['width' => '33'],
            ],
            [
                'key' => 'field_plano_preco',
                'label' => 'Preço (R$)',
                'name' => 'plano_preco',
                'type' => 'text',
                'wrapper' => ['width' => '34'],
            ],
            [
                'key' => 'field_plano_obs_preco',
                'label' => 'Descrição abaixo do valor',
                'name' => 'plano_obs_preco',
                'type' => 'textarea',
                'rows' => 2,
                'instructions' =>
                    'Texto pequeno exibido logo abaixo do preço deste plano.',
            ],
            [
                'key' => 'field_plano_destaque',
                'label' => 'Plano em destaque',
                'name' => 'plano_destaque',
                'type' => 'true_false',
                'ui' => 1,
            ],
            [
                'key' => 'field_plano_aplicativos',
                'label' => 'Aplicativos inclusos',
                'name' => 'plano_aplicativos',
                'type' => 'textarea',
                'instructions' => 'Um aplicativo por linha.',
                'rows' => 4,
            ],
        ],
        'location' => [
            [
                [
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'plano',
                ],
            ],
        ],
    ]);
}
add_action('acf/init', 'victortech_register_plan_fields');


if (is_page_template('page-sobre.php') || is_page('sobre')) {
    wp_enqueue_style(
        'victortech-about',
        get_template_directory_uri() . '/assets/css/about.css',
        ['victortech-style'],
        '1.0'
    );
}

// Carrega o atendimento online oficial da Fastnet Telecom.
require_once get_template_directory() . '/inc/atendimento/inicializacao.php';
