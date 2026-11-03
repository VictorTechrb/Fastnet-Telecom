<?php
/**
 * Inicialização e carregamento dos assets do chat.
 */
if (!defined('ABSPATH')) { exit; }

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/service.php';
require_once __DIR__ . '/rest.php';

function fastnet_chat_enqueue_assets()
{
    $directory = get_template_directory();
    $uri = get_template_directory_uri();
    $css = $directory . '/assets/chat/chat.css';
    $js = $directory . '/assets/chat/chat.js';

    if (file_exists($css)) {
        wp_enqueue_style('fastnet-chat', $uri . '/assets/chat/chat.css', array('victortech-style'), (string) filemtime($css));
    }
    if (file_exists($js)) {
        wp_enqueue_script('fastnet-chat', $uri . '/assets/chat/chat.js', array(), (string) filemtime($js), true);
        wp_localize_script('fastnet-chat', 'FastnetChatConfig', fastnet_chat_browser_config());
    }
}
add_action('wp_enqueue_scripts', 'fastnet_chat_enqueue_assets', 20);
