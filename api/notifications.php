<?php
/**
 * api/notifications.php - Broadcast system
 */
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

if ($path === '/api/admin/notify-broadcast' && $method === 'POST') {
    admin_required();
    $input = get_json_input();
    $title = sanitize_input($input['title'] ?? '');
    $message = sanitize_input($input['message'] ?? '');
    $type = in_array($input['type'] ?? '', ['info','warning','danger','success']) ? $input['type'] : 'info';
    if (empty($title) || empty($message)) { json_response(['status'=>'error','message'=>'Thiếu title/message'], 400); exit; }
    add_broadcast($title, $message, $type, 'all');
    $cfg = load_config();
    if (!empty($cfg['notify_webhook'])) send_webhook("📢 **$title**: $message");
    add_log('ADMIN_BROADCAST', "Admin gửi thông báo: $title");
    json_response(['status'=>'success','message'=>'Đã gửi thông báo']);
    exit;
}
if ($path === '/api/admin/notifications' && $method === 'GET') {
    admin_required();
    $n = load_notifications();
    $limit = min(intval($_GET['limit'] ?? 50), 100);
    $n['broadcasts'] = array_reverse(array_slice($n['broadcasts'], -$limit));
    json_response(['status'=>'success','notifications'=>$n['broadcasts']]);
    exit;
}
if ($path === '/api/notifications' && $method === 'GET') {
    $n = load_notifications();
    $broadcasts = array_reverse(array_slice($n['broadcasts'] ?? [], -20));
    $broadcasts = array_filter($broadcasts, fn($b) => in_array($b['level'] ?? 'all', ['all','user']));
    json_response(['status'=>'success','notifications'=>array_values($broadcasts)]);
    exit;
}
if ($path === '/api/admin/notify-delete' && $method === 'POST') {
    admin_required();
    $input = get_json_input(); $id = $input['id'] ?? '';
    $n = load_notifications();
    $n['broadcasts'] = array_values(array_filter($n['broadcasts'], fn($b) => ($b['id'] ?? '') !== $id));
    save_notifications($n);
    json_response(['status'=>'success','message'=>'Đã xóa']);
    exit;
}
