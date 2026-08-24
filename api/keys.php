<?php
/**
 * api/keys.php - Key CRUD, bulk ops, ban/unban
 */
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

// GET KEYS
if ($path === '/api/get-keys' && $method === 'GET') {
    admin_required();
    $data = load_data(); $now = new DateTime(); $updated = false;
    foreach ($data['keys'] as &$k) {
        if (($k['status']??'')==='active' && ($k['expiry']??'')!=='forever') {
            try { if ($now > new DateTime($k['expiry'])) { $k['status']='expired'; $updated=true; } } catch(Exception $e){}
        }
    }
    unset($k);
    if ($updated) save_data($data);
    $txt = file_exists(KEYS_TXT_FILE) ? file_get_contents(KEYS_TXT_FILE) : '';
    json_response(['status'=>'success','keys'=>$data['keys'],'txt_content'=>$txt]);
    exit;
}

// CREATE KEY
if ($path === '/api/admin/create-key' && $method === 'POST') {
    admin_required(); check_rate_limit('create-key', 20);
    $input = get_json_input();
    $duration = $input['duration'] ?? '1day';
    $custom_key = $input['custom_key'] ?? null;
    global $KEY_DURATIONS;
    if (!array_key_exists($duration, $KEY_DURATIONS)) $duration = '1day';

    if (!empty($custom_key)) {
        if (!validate_key_format($custom_key)) { json_response(['status'=>'error','message'=>'Key chỉ dùng chữ, số, gạch ngang (tối đa 64 ký tự)'], 400); exit; }
        $data = load_data();
        foreach ($data['keys'] as $k) { if ($k['key'] === $custom_key) { json_response(['status'=>'error','message'=>'Key đã tồn tại'], 400); exit; } }
        $key = $custom_key;
    } else {
        $key = generate_random_key();
    }

    $now = new DateTime();
    $days = $KEY_DURATIONS[$duration] ?? 1;
    $expiry_str = ($days === -1) ? 'forever' : (clone $now)->modify("+$days days")->format('c');

    $data = load_data();
    $data['keys'][] = ['key'=>$key,'created_at'=>$now->format('c'),'expiry'=>$expiry_str,'duration'=>$duration,'status'=>'active','device_id'=>null,'devices'=>[],'used_at'=>null,'created_by'=>'admin'];
    save_data($data);

    // Verify
    $v = load_data(); $found = false;
    foreach ($v['keys'] as $k) { if ($k['key']===$key) { $found=true; break; } }
    if (!$found) { json_response(['status'=>'error','message'=>'Lỗi lưu file!'], 500); exit; }

    file_put_contents(KEYS_TXT_FILE, "KEY: $key | Duration: $duration | Created: ".$now->format('Y-m-d H:i:s')." | Expiry: $expiry_str\n", FILE_APPEND);
    add_log('ADMIN_CREATE_KEY', "Admin tạo key $key ($duration)");
    $cfg = load_config();
    if (!empty($cfg['notify_on_key_created'])) { send_webhook("🔑 Tạo key: **$key** ($duration)"); add_broadcast("Tạo key mới", "Key $key ($duration) đã được tạo", "success", "admin"); }
    json_response(['status'=>'success','key'=>$key,'duration'=>$duration,'expiry'=>$expiry_str]);
    exit;
}

// DELETE KEY
if ($path === '/api/admin/delete-key' && $method === 'POST') {
    admin_required();
    $input = get_json_input(); $key = $input['key'] ?? '';
    $data = load_data();
    $data['keys'] = array_values(array_filter($data['keys'], fn($k) => $k['key'] !== $key));
    save_data($data);
    add_log('ADMIN_DELETE_KEY', "Admin xóa key $key");
    json_response(['status'=>'success','message'=>'Đã xóa key']);
    exit;
}

// UPDATE KEY
if ($path === '/api/admin/update-key' && $method === 'POST') {
    admin_required();
    $input = get_json_input(); $key = $input['key'] ?? ''; $new_status = $input['status'] ?? '';
    if (!in_array($new_status, ['active','inactive','banned','expired'])) { json_response(['status'=>'error','message'=>'Trạng thái không hợp lệ'], 400); exit; }
    $data = load_data();
    foreach ($data['keys'] as &$k) {
        if ($k['key'] === $key) { $k['status'] = $new_status; save_data($data); add_log('ADMIN_UPDATE_KEY', "Admin đổi key $key -> $new_status"); json_response(['status'=>'success','message'=>"Đã đổi sang $new_status"]); exit; }
    }
    unset($k);
    json_response(['status'=>'error','message'=>'Không tìm thấy key'], 404); exit;
}

// BAN / UNBAN KEY
if ($path === '/api/admin/ban-key' && $method === 'POST') {
    admin_required();
    $input = get_json_input(); $key = $input['key'] ?? '';
    $data = load_data();
    foreach ($data['keys'] as &$k) {
        if ($k['key'] === $key) { $k['status']='banned'; $k['banned_at']=date('c'); save_data($data); add_log('ADMIN_BAN_KEY', "Admin BAN key $key"); add_broadcast("Key BAN", "Key $key bị BAN", "danger", "all"); json_response(['status'=>'success','message'=>"Đã BAN $key"]); exit; }
    }
    unset($k);
    json_response(['status'=>'error','message'=>'Không tìm thấy key'], 404); exit;
}
if ($path === '/api/admin/unban-key' && $method === 'POST') {
    admin_required();
    $input = get_json_input(); $key = $input['key'] ?? '';
    $data = load_data();
    foreach ($data['keys'] as &$k) {
        if ($k['key'] === $key) { $k['status']='active'; unset($k['banned_at']); save_data($data); add_log('ADMIN_UNBAN_KEY', "Admin gỡ BAN $key"); json_response(['status'=>'success','message'=>"Đã gỡ BAN $key"]); exit; }
    }
    unset($k);
    json_response(['status'=>'error','message'=>'Không tìm thấy key'], 404); exit;
}

// BULK CREATE
if ($path === '/api/admin/bulk-create' && $method === 'POST') {
    admin_required(); check_rate_limit('bulk-create', 5);
    $input = get_json_input();
    $count = min(max(intval($input['count'] ?? 1), 1), 100);
    $duration = $input['duration'] ?? '1day'; $prefix = sanitize_input($input['prefix'] ?? 'NEXUS');
    global $KEY_DURATIONS;
    if (!array_key_exists($duration, $KEY_DURATIONS)) $duration = '1day';
    if (!preg_match('/^[A-Za-z0-9_]{1,20}$/', $prefix)) $prefix = 'NEXUS';

    $data = load_data(); $created = []; $now = new DateTime();
    $days = $KEY_DURATIONS[$duration] ?? 1;
    $expiry_str = ($days === -1) ? 'forever' : (clone $now)->modify("+$days days")->format('c');
    for ($i = 0; $i < $count; $i++) {
        $key = generate_random_key($prefix);
        $data['keys'][] = ['key'=>$key,'created_at'=>$now->format('c'),'expiry'=>$expiry_str,'duration'=>$duration,'status'=>'active','device_id'=>null,'devices'=>[],'used_at'=>null,'created_by'=>'admin_bulk'];
        $created[] = $key;
        file_put_contents(KEYS_TXT_FILE, "KEY: $key | Duration: $duration | Created: ".$now->format('Y-m-d H:i:s')." | Expiry: $expiry_str\n", FILE_APPEND);
    }
    save_data($data);
    add_log('ADMIN_BULK_CREATE', "Admin tạo $count key ($duration)");
    json_response(['status'=>'success','count'=>$count,'keys'=>$created,'duration'=>$duration]);
    exit;
}

// BULK DELETE
if ($path === '/api/admin/bulk-delete' && $method === 'POST') {
    admin_required();
    $input = get_json_input(); $status = $input['status'] ?? '';
    if (!in_array($status, ['expired','banned','inactive'])) { json_response(['status'=>'error','message'=>'Invalid'], 400); exit; }
    $data = load_data(); $before = count($data['keys']);
    $data['keys'] = array_values(array_filter($data['keys'], fn($k) => ($k['status']??'') !== $status));
    $deleted = $before - count($data['keys']);
    save_data($data);
    add_log('ADMIN_BULK_DELETE', "Admin xóa $deleted key ($status)");
    json_response(['status'=>'success','deleted'=>$deleted]);
    exit;
}

// RESET ALL
if ($path === '/api/admin/reset-keys' && $method === 'POST') {
    admin_required();
    $input = get_json_input();
    if (($input['confirm'] ?? false) !== true) { json_response(['status'=>'error','message'=>'Cần confirm=true'], 400); exit; }
    $data = load_data(); $total = count($data['keys'] ?? []);
    $data['keys'] = []; save_data($data);
    file_put_contents(KEYS_TXT_FILE, "# RESET - ".date('Y-m-d H:i:s')." ($total removed)\n");
    add_log('ADMIN_RESET_KEYS', "Admin RESET $total keys");
    add_broadcast("RESET TOÀN BỘ", "$total key đã bị xóa!", "danger", "admin");
    json_response(['status'=>'success','message'=>"Đã reset $total keys"]);
    exit;
}
