<?php
/**
 * Tabelas, sessões e histórico do chat.
 */
if (!defined('ABSPATH')) { exit; }

define('FASTNET_CHAT_DB_VERSION', '1');

function fastnet_chat_sessions_table()
{
    global $wpdb;
    return $wpdb->prefix . 'fastnet_chat_sessions';
}

function fastnet_chat_messages_table()
{
    global $wpdb;
    return $wpdb->prefix . 'fastnet_chat_messages';
}

function fastnet_chat_install_tables()
{
    global $wpdb;
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    $collate = $wpdb->get_charset_collate();
    $sessions = fastnet_chat_sessions_table();
    $messages = fastnet_chat_messages_table();

    dbDelta("CREATE TABLE {$sessions} (
        session_id char(36) NOT NULL,
        user_id varchar(60) NOT NULL,
        token_hash char(64) NOT NULL,
        created_at datetime NOT NULL,
        last_activity datetime NOT NULL,
        PRIMARY KEY  (session_id),
        UNIQUE KEY user_id (user_id),
        KEY last_activity (last_activity)
    ) {$collate};");

    dbDelta("CREATE TABLE {$messages} (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        session_id char(36) NOT NULL,
        remote_id varchar(100) DEFAULT NULL,
        direction varchar(12) NOT NULL,
        message_type varchar(12) NOT NULL,
        message_text longtext NULL,
        media_url text NULL,
        media_name varchar(255) NULL,
        media_type varchar(100) NULL,
        delivery_status varchar(16) NOT NULL DEFAULT 'sent',
        created_at datetime NOT NULL,
        PRIMARY KEY  (id),
        UNIQUE KEY remote_id (remote_id),
        KEY session_id_id (session_id,id),
        KEY delivery_status (delivery_status)
    ) {$collate};");

    $sessions_ok = $wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $sessions));
    $messages_ok = $wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $messages));
    if ($sessions_ok === $sessions && $messages_ok === $messages) {
        update_option('fastnet_chat_db_version', FASTNET_CHAT_DB_VERSION, false);
        return true;
    }

    error_log('Fastnet Chat: falha ao criar ou verificar as tabelas.');
    return false;
}

function fastnet_chat_maybe_install_tables()
{
    if (FASTNET_CHAT_DB_VERSION !== (string) get_option('fastnet_chat_db_version', '')) {
        fastnet_chat_install_tables();
    }
}
add_action('init', 'fastnet_chat_maybe_install_tables', 1);

function fastnet_chat_get_session($session_id)
{
    global $wpdb;
    if (!preg_match('/^[a-f0-9-]{36}$/i', (string) $session_id)) { return null; }

    return $wpdb->get_row(
        $wpdb->prepare('SELECT session_id,user_id,token_hash FROM ' . fastnet_chat_sessions_table() . ' WHERE session_id = %s LIMIT 1', $session_id),
        ARRAY_A
    );
}

function fastnet_chat_create_session()
{
    global $wpdb;
    try {
        $id = wp_generate_uuid4();
        $token = bin2hex(random_bytes(32));
    } catch (Exception $error) {
        return new WP_Error('chat_random_error', 'Não foi possível iniciar o atendimento com segurança.', array('status' => 500));
    }

    $now = current_time('mysql', true);
    $user_id = 'fastnet-' . $id;
    $ok = $wpdb->insert(
        fastnet_chat_sessions_table(),
        array(
            'session_id' => $id,
            'user_id' => $user_id,
            'token_hash' => hash('sha256', $token),
            'created_at' => $now,
            'last_activity' => $now,
        ),
        array('%s', '%s', '%s', '%s', '%s')
    );

    if (1 !== $ok) {
        return new WP_Error('chat_session_error', 'Não foi possível iniciar o atendimento. Tente novamente.', array('status' => 500));
    }

    return array('session_id' => $id, 'user_id' => $user_id, 'token' => $token);
}

function fastnet_chat_authenticate_session($session_id, $token)
{
    $session = fastnet_chat_get_session($session_id);
    if (!$session || !is_string($token) || '' === $token) { return false; }
    return hash_equals($session['token_hash'], hash('sha256', $token)) ? $session : false;
}

function fastnet_chat_touch_session($session_id)
{
    global $wpdb;
    $wpdb->update(
        fastnet_chat_sessions_table(),
        array('last_activity' => current_time('mysql', true)),
        array('session_id' => $session_id),
        array('%s'),
        array('%s')
    );
}

function fastnet_chat_store_message($data)
{
    global $wpdb;
    $defaults = array(
        'remote_id' => null,
        'message_text' => '',
        'media_url' => null,
        'media_name' => null,
        'media_type' => null,
        'delivery_status' => 'sent',
        'created_at' => current_time('mysql', true),
    );
    $data = wp_parse_args($data, $defaults);
    $ok = $wpdb->insert(
        fastnet_chat_messages_table(),
        $data,
        array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')
    );

    if (1 !== $ok) { return false; }
    fastnet_chat_touch_session($data['session_id']);
    return (int) $wpdb->insert_id;
}

function fastnet_chat_remote_message_exists($remote_id)
{
    global $wpdb;
    if ('' === (string) $remote_id) { return false; }

    return (bool) $wpdb->get_var(
        $wpdb->prepare('SELECT id FROM ' . fastnet_chat_messages_table() . ' WHERE remote_id = %s LIMIT 1', $remote_id)
    );
}

