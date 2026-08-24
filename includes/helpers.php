<?php
/**
 * helpers.php - Key generation, logging, notifications, Link4m, HTML protection
 */

// === SECURITY ===
function get_client_ip() {
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) { $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']); return trim($ips[0]); }
    return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
}
function is_ip_banned($ip) {
    $sec = load_security();
    foreach ($sec['banned_ips'] as $e) { if (($e['ip']??'') === $ip) return $e; }
    return null;
}
function check_banned_ip() {
    $ip = get_client_ip();
    if (is_ip_banned($ip) && !($_SESSION['is_admin'] ?? false)) {
        http_response_code(403);
        echo '<!DOCTYPE html><html><head><title>Blocked</title></head><body style="background:#070913;color:#ff1744;display:flex;align-items:center;justify-content:center;height:100vh;font-family:sans-serif"><div>⛔ IP đã bị chặn!</div></body></html>';
        exit;
    }
}
function admin_required() {
    if (!($_SESSION['is_admin'] ?? false)) { json_response(['status'=>'error','message'=>'Chưa đăng nhập'], 401); exit; }
}
function json_response($data, $code = 200) {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}

// === LOGGING ===
function add_log($action, $message, $extra = []) {
    $logs = load_logs();
    $entry = ['time'=>date('c'),'action'=>$action,'message'=>$message,'ip'=>get_client_ip()];
    if ($extra) $entry['extra'] = $extra;
    $logs[] = $entry;
    $max = intval((load_config())['log_keep'] ?? 2000);
    if (count($logs) > $max) $logs = array_slice($logs, -$max);
    save_logs($logs);
}

// === NOTIFICATIONS ===
function add_broadcast($title, $message, $type = 'info', $level = 'all') {
    $n = load_notifications();
    $n['broadcasts'][] = ['id'=>bin2hex(random_bytes(8)),'title'=>$title,'message'=>$message,'type'=>$type,'level'=>$level,'created_at'=>date('c'),'read_by'=>[]];
    if (count($n['broadcasts']) > 100) $n['broadcasts'] = array_slice($n['broadcasts'], -100);
    save_notifications($n);
}
function send_webhook($message) {
    $cfg = load_config();
    if (empty($cfg['notify_webhook']) || empty($cfg['notify_enabled'])) return;
    $ch = curl_init($cfg['notify_webhook']);
    curl_setopt_array($ch, [CURLOPT_POST=>true, CURLOPT_POSTFIELDS=>json_encode(['content'=>$message,'username'=>'NEXUS']), CURLOPT_HTTPHEADER=>['Content-Type: application/json'], CURLOPT_RETURNTRANSFER=>true, CURLOPT_TIMEOUT=>10]);
    curl_exec($ch); curl_close($ch);
}

// === LINK4M ===
function record_link4m_click() {
    $stats = load_stats();
    $today = date('Y-m-d');
    $stats['total_clicks'] = ($stats['total_clicks'] ?? 0) + 1;
    $stats['daily'][$today] = ($stats['daily'][$today] ?? 0) + 1;
    $stats['last_click'] = date('c');
    $rate = floatval((load_config())['link4m_rate_per_1000'] ?? 1.5);
    $stats['estimated_earnings'] = round($stats['total_clicks'] / 1000.0 * $rate, 4);
    save_stats($stats);
}
function shorten_with_link4m($target_url) {
    $cfg = load_config();
    $keys = [];
    foreach ([$cfg['link4m_api_key'] ?? '', $cfg['link4m_api_key2'] ?? ''] as $k) { $k = trim($k); if ($k && !in_array($k, $keys)) $keys[] = $k; }
    if (empty($keys)) return null;
    shuffle($keys);
    $headers = ['User-Agent: Mozilla/5.0','Accept: application/json','Referer: https://link4m.co/'];
    foreach ($keys as $api_key) {
        $url = LINK4M_URL . '?' . http_build_query(['api'=>$api_key,'url'=>$target_url]);
        $ch = curl_init($url);
        curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER=>true, CURLOPT_HTTPHEADER=>$headers, CURLOPT_TIMEOUT=>10, CURLOPT_SSL_VERIFYPEER=>false]);
        $resp = curl_exec($ch); $code = curl_getinfo($ch, CURLINFO_HTTP_CODE); curl_close($ch);
        if ($code === 200 && $resp) { $d = json_decode($resp, true); if (($d['status']??'')==='success' && !empty($d['shortenedUrl'])) return $d['shortenedUrl']; }
    }
    return null;
}

// === KEY GENERATION ===
function generate_random_key($prefix = 'NEXUS') {
    return strtoupper($prefix).'-'.strtoupper(bin2hex(random_bytes(2))).'-'.strtoupper(bin2hex(random_bytes(2))).'-'.strtoupper(bin2hex(random_bytes(2)));
}
function get_auto_device_id() { return 'DEV-'.strtoupper(substr(md5(get_client_ip().':'.($_SERVER['HTTP_USER_AGENT']??'')), 0, 12)); }

// === HTML PROTECTION ===
$ANTI_F12_SCRIPT = <<<'JS'
<script>(function(){function k(e){var c=e.keyCode||e.which;if(c===123)return true;if(e.ctrlKey&&e.shiftKey&&(c===73||c===74||c===67))return true;if(e.ctrlKey&&(c===85||c===83))return true;return false}document.addEventListener("keydown",function(e){if(k(e)){e.preventDefault();e.stopPropagation();return false}},true);document.addEventListener("contextmenu",function(e){e.preventDefault();return false});var d=false;setInterval(function(){var w=window.outerWidth-window.innerWidth>160,h=window.outerHeight-window.innerHeight>160;if(w||h){if(!d){d=true;document.body.innerHTML='<div style="position:fixed;inset:0;background:#070913;color:#ff1744;display:flex;align-items:center;justify-content:center;font-family:sans-serif;font-size:20px;z-index:99999">⚠️ ĐÓNG DEVTOOLS</div>'}}else d=false},1e3)})()</script>
JS;

function encrypt_html($html) {
    $key = random_int(3, 250); $enc = '';
    foreach (unpack('C*', $html) as $b) $enc .= chr($b ^ $key);
    $payload = base64_encode($enc);
    return '<!DOCTYPE html><html lang="vi"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>NEXUS</title></head><body><script>(function(){var K='.$key.',D="'.$payload.'",r=atob(D),b=new Uint8Array(r.length);for(var i=0;i<r.length;i++)b[i]=r.charCodeAt(i)^K;document.write(new TextDecoder().decode(b))})()</script></body></html>';
}
function protect_html($html) {
    $cfg = load_config();
    if (!empty($cfg['anti_f12'])) $html = str_replace('</body>', $ANTI_F12_SCRIPT."\n</body>", $html);
    if (!empty($cfg['html_encrypt'])) $html = encrypt_html($html);
    return $html;
}
function output_html($html, $protect = true) {
    header('Content-Type: text/html; charset=utf-8');
    if ($protect) {
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        if (in_array(rtrim($path, '/'), ['/', '/showkey', '/check'])) { echo protect_html($html); return; }
    }
    echo $html;
}
