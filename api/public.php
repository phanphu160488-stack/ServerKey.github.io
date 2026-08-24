<?php
/**
 * api/public.php - Public API: check key, get key, show key
 */
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
global $KEY_DURATIONS;

// GET KEY (Link4m bypass)
if ($path === '/getkey' && in_array($method, ['GET', 'POST'])) {
    check_rate_limit('getkey', 10);
    $input = get_json_input();
    $duration = $input['duration'] ?? ($_GET['duration'] ?? '1day');
    if (!array_key_exists($duration, $KEY_DURATIONS)) $duration = '1day';
    $token = bin2hex(random_bytes(16));
    $_SESSION['bypass_'.$token] = ['duration'=>$duration,'ts'=>time()];
    $host = (isset($_SERVER['HTTPS'])&&$_SERVER['HTTPS']==='on'?'https':'http').'://'.($_SERVER['HTTP_HOST']??'localhost');
    $shortened = shorten_with_link4m($host.'/showkey?token='.$token);
    $is_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) || (strpos($_SERVER['HTTP_ACCEPT']??'','application/json')!==false);
    if ($is_ajax || $method === 'POST') {
        if (!$shortened) json_response(['status'=>'error','message'=>'Link4m lỗi, thử lại!','token'=>$token], 502);
        else { add_log('GETKEY', "Tạo link Link4m ($duration)",['token'=>substr($token,0,8)]); json_response(['status'=>'success','bypass_url'=>$shortened,'token'=>$token,'duration'=>$duration]); }
        exit;
    }
    header("Location: ".($shortened?:"/showkey?token=$token")); exit;
}

// SHOW KEY (after Link4m bypass)
if ($path === '/showkey') {
    $token = sanitize_input($_GET['token'] ?? '');
    $skey = 'bypass_'.$token;
    if (empty($token) || empty($_SESSION[$skey])) { output_html(render_show_key(null, 'Token không hợp lệ!')); exit; }
    $info = $_SESSION[$skey]; unset($_SESSION[$skey]);
    $duration = $info['duration'];
    $key = generate_random_key();
    $now = new DateTime();
    $days = $KEY_DURATIONS[$duration] ?? 1;
    $expiry_str = ($days === -1) ? 'forever' : (clone $now)->modify("+$days days")->format('c');

    $data = load_data();
    $data['keys'][] = ['key'=>$key,'created_at'=>$now->format('c'),'expiry'=>$expiry_str,'duration'=>$duration,'status'=>'active','device_id'=>null,'devices'=>[],'used_at'=>null,'created_by'=>'link4m_bypass'];
    save_data($data);
    file_put_contents(KEYS_TXT_FILE, "KEY: $key | Duration: $duration | Created: ".$now->format('Y-m-d H:i:s')." | Expiry: $expiry_str\n", FILE_APPEND);
    add_log('KEY_ISSUED', "Phát hành key $key ($duration) qua Link4m");
    record_link4m_click();
    $cfg = load_config();
    if (!empty($cfg['notify_on_key_created'])) send_webhook("🔑 Key mới: **$key** ($duration)");
    output_html(render_show_key($key, null, $duration, $expiry_str));
    exit;
}

// CHECK KEY
if (($path === '/check' || $path === '/api/check-key') && in_array($method, ['GET', 'POST'])) {
    check_rate_limit('check', 30);
    $input = ($method === 'POST') ? get_json_input() : sanitize_input($_GET);
    $key = $input['key'] ?? ''; $device_id = $input['device_id'] ?? null; $ping = ($input['ping'] ?? 'false') === 'true';
    if (empty($key)) { output_html(render_index()); exit; }
    if (!validate_key_format($key)) { json_response(['status'=>'error','message'=>'Key không hợp lệ'], 400); exit; }
    $data = load_data(); $now = new DateTime(); $target = $device_id ?: get_auto_device_id();
    foreach ($data['keys'] as &$k) {
        if ($k['key'] !== $key) continue;
        if (($k['expiry']??'') !== 'forever') {
            try { if ($now > new DateTime($k['expiry'])) { $k['status']='expired'; save_data($data); json_response(['status'=>'error','message'=>'Key hết hạn','expiry'=>$k['expiry']], 400); exit; } } catch(Exception $e){}
        }
        if (($k['status']??'')==='banned') { json_response(['status'=>'error','message'=>'Key bị BAN!'], 403); exit; }
        if (($k['status']??'')==='inactive') { json_response(['status'=>'error','message'=>'Key bị vô hiệu hóa'], 400); exit; }
        if (($k['status']??'')==='expired') { json_response(['status'=>'error','message'=>'Key hết hạn'], 400); exit; }
        if ($ping) { json_response(['status'=>'success','message'=>'Key hợp lệ','key'=>$k['key'],'expiry'=>$k['expiry'],'device_id'=>$target]); exit; }
        $is_forever = ($k['duration']??'')==='forever'||($k['expiry']??'')==='forever';
        $max = $is_forever ? 2 : 1;
        $devices = $k['devices'] ?? []; if (empty($devices)&&!empty($k['device_id'])) $devices = [$k['device_id']];
        if (in_array($target, $devices)) { json_response(['status'=>'success','message'=>"Key hợp lệ cho $target",'device_id'=>$target,'devices_count'=>count($devices).'/'.$max,'expiry'=>$k['expiry']]); exit; }
        if (count($devices) < $max) {
            $devices[] = $target; $k['devices']=$devices; $k['device_id']=$devices[0]; $k['used_at']=$now->format('c');
            save_data($data); file_put_contents(USED_KEYS_FILE, "{$key}|{$target}|{$k['expiry']}\n", FILE_APPEND);
            add_log('KEY_ACTIVATED', "Key $key kích hoạt bởi $target");
            json_response(['status'=>'success','message'=>"Kích hoạt [".count($devices)."/$max]", 'device_id'=>$target,'expiry'=>$k['expiry']]); exit;
        }
        $msg = $is_forever ? "Key vĩnh viễn đủ 2 device!" : "Key chỉ 1 device ($devices[0])!";
        json_response(['status'=>'error','message'=>$msg,'registered_devices'=>$devices], 400); exit;
    }
    unset($k);
    json_response(['status'=>'error','message'=>'Key không tồn tại'], 400); exit;
}

// LOOKUP KEY (for app)
if ($path === '/api/lookup-key' && $method === 'GET') {
    $token = sanitize_input($_GET['token'] ?? '');
    if (empty($token)) { json_response(['status'=>'error','message'=>'Thiếu token'], 400); exit; }
    if (!empty($_SESSION['bypass_'.$token])) { json_response(['status'=>'pending','message'=>'Đang chờ...']); exit; }
    json_response(['status'=>'invalid','message'=>'Token hết hạn'], 404); exit;
}
