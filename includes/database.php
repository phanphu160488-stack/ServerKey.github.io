<?php
/**
 * database.php - JSON file storage helpers
 */

function load_config() {
    $defaults = [
        'admin_password'=>'admin123','html_encrypt'=>true,'anti_f12'=>true,
        'link4m_rate_per_1000'=>1.5,'link4m_api_key'=>'69c76b755e6016383f32fdc9',
        'link4m_api_key2'=>'69902dcc482df052bb6c2347','log_keep'=>2000,
        'notify_enabled'=>true,'notify_webhook'=>'',
        'notify_on_key_created'=>true,'notify_on_login_fail'=>true,'notify_on_ban'=>true,
    ];
    if (file_exists(CONFIG_FILE)) {
        $saved = json_decode(file_get_contents(CONFIG_FILE), true);
        if (is_array($saved)) $defaults = array_merge($defaults, $saved);
    }
    return $defaults;
}
function save_config($cfg) {
    return file_put_contents(CONFIG_FILE, json_encode($cfg, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE)) !== false;
}
function load_data() {
    if (file_exists(DATA_FILE)) {
        $d = json_decode(file_get_contents(DATA_FILE), true);
        if (is_array($d) && isset($d['keys'])) return $d;
    }
    return ['keys'=>[]];
}
function save_data($data) {
    return file_put_contents(DATA_FILE, json_encode($data, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE)) !== false;
}
function load_logs() {
    if (file_exists(LOG_FILE)) {
        $d = json_decode(file_get_contents(LOG_FILE), true);
        if (is_array($d)) return $d;
    }
    return [];
}
function save_logs($logs) {
    return file_put_contents(LOG_FILE, json_encode($logs, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE)) !== false;
}
function load_security() {
    if (file_exists(SECURITY_FILE)) {
        $d = json_decode(file_get_contents(SECURITY_FILE), true);
        if (is_array($d) && isset($d['banned_ips'])) return $d;
    }
    return ['banned_ips'=>[]];
}
function save_security($sec) {
    return file_put_contents(SECURITY_FILE, json_encode($sec, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE)) !== false;
}
function load_stats() {
    if (file_exists(STATS_FILE)) {
        $d = json_decode(file_get_contents(STATS_FILE), true);
        if (is_array($d)) return $d;
    }
    return ['total_clicks'=>0,'daily'=>[],'last_click'=>null,'estimated_earnings'=>0.0];
}
function save_stats($s) {
    return file_put_contents(STATS_FILE, json_encode($s, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE)) !== false;
}
function load_notifications() {
    if (file_exists(NOTIFY_FILE)) {
        $d = json_decode(file_get_contents(NOTIFY_FILE), true);
        if (is_array($d)) return $d;
    }
    return ['broadcasts'=>[]];
}
function save_notifications($n) {
    return file_put_contents(NOTIFY_FILE, json_encode($n, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE)) !== false;
}
