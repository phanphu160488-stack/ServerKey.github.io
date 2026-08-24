<?php
/**
 * api/settings.php - Get/Save settings
 */
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

if ($path === '/api/admin/settings' && $method === 'GET') {
    admin_required();
    $cfg = load_config(); unset($cfg['admin_password']);
    json_response(['status'=>'success','settings'=>$cfg]);
    exit;
}
if ($path === '/api/admin/settings' && $method === 'POST') {
    admin_required();
    $input = get_json_input(); $cfg = load_config();
    $allowed = ['admin_password'=>'string','html_encrypt'=>'bool','anti_f12'=>'bool','link4m_rate_per_1000'=>'float','link4m_api_key'=>'string','link4m_api_key2'=>'string','log_keep'=>'int','notify_enabled'=>'bool','notify_webhook'=>'string','notify_on_key_created'=>'bool','notify_on_login_fail'=>'bool','notify_on_ban'=>'bool'];
    foreach ($allowed as $field => $type) {
        if (array_key_exists($field, $input)) {
            if ($type==='bool') $cfg[$field] = filter_var($input[$field], FILTER_VALIDATE_BOOLEAN);
            elseif ($type==='int') $cfg[$field] = intval($input[$field]);
            elseif ($type==='float') $cfg[$field] = floatval($input[$field]);
            else $cfg[$field] = substr(strval($input[$field]), 0, 500);
        }
    }
    save_config($cfg);
    add_log('ADMIN_SETTINGS', 'Admin cập nhật cài đặt');
    json_response(['status'=>'success','message'=>'Đã lưu cài đặt']);
    exit;
}
