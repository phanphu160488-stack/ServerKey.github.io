<?php
/**
 * api/security.php - Ban/Unban IP
 */
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

if ($path === '/api/admin/ban-ip' && $method === 'POST') {
    admin_required();
    $input = get_json_input(); $ip = $input['ip'] ?? '';
    if (!validate_ip($ip)) { json_response(['status'=>'error','message'=>'IP không hợp lệ'], 400); exit; }
    $sec = load_security();
    foreach ($sec['banned_ips'] as $e) { if (($e['ip']??'')===$ip) { json_response(['status'=>'error','message'=>"IP $ip đã bị ban"], 400); exit; } }
    $sec['banned_ips'][] = ['ip'=>$ip,'banned_at'=>date('c'),'reason'=>$input['reason'] ?? 'Admin ban'];
    save_security($sec);
    add_log('ADMIN_BAN_IP', "Admin BAN IP $ip");
    add_broadcast("IP BAN", "IP $ip bị chặn", "danger", "admin");
    json_response(['status'=>'success','message'=>"Đã BAN IP $ip"]);
    exit;
}
if ($path === '/api/admin/unban-ip' && $method === 'POST') {
    admin_required();
    $input = get_json_input(); $ip = $input['ip'] ?? '';
    $sec = load_security();
    $sec['banned_ips'] = array_values(array_filter($sec['banned_ips'], fn($e) => ($e['ip']??'') !== $ip));
    save_security($sec);
    add_log('ADMIN_UNBAN_IP', "Admin gỡ BAN IP $ip");
    json_response(['status'=>'success','message'=>"Đã gỡ BAN IP $ip"]);
    exit;
}
if ($path === '/api/admin/banned-ips' && $method === 'GET') {
    admin_required();
    json_response(['status'=>'success','banned_ips'=>load_security()['banned_ips']]);
    exit;
}
