<?php
/**
 * api/system.php - System info, permissions check, keys.txt
 */
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

if ($path === '/api/admin/system' && $method === 'GET') {
    admin_required();
    $files_info = [];
    foreach ([DATA_FILE, CONFIG_FILE, LOG_FILE, SECURITY_FILE, STATS_FILE] as $f) {
        if (file_exists($f)) $files_info[basename($f)] = ['size'=>filesize($f),'modified'=>date('Y-m-d H:i:s', filemtime($f))];
    }
    json_response(['status'=>'success','php_version'=>phpversion(),'server_software'=>$_SERVER['SERVER_SOFTWARE']??'Unknown','os'=>php_uname(),'memory'=>round(memory_get_peak_usage(true)/1048576,1).' MB','disk_total'=>round(disk_total_space(BASE_DIR)/1073741824,2).' GB','disk_free'=>round(disk_free_space(BASE_DIR)/1073741824,2).' GB','files'=>$files_info]);
    exit;
}
if ($path === '/api/admin/check-perms' && $method === 'GET') {
    admin_required();
    $files = ['config.json'=>CONFIG_FILE,'keys_data.json'=>DATA_FILE,'keys.txt'=>KEYS_TXT_FILE,'used_keys.txt'=>USED_KEYS_FILE,'logs.json'=>LOG_FILE,'security.json'=>SECURITY_FILE,'link4m_stats.json'=>STATS_FILE];
    $result = []; $ok = true;
    foreach ($files as $name => $p) {
        $exists = file_exists($p); $w = $exists ? is_writable($p) : is_writable(dirname($p));
        if (!$w) $ok = false;
        $result[$name] = ['exists'=>$exists,'writable'=>$w];
    }
    json_response(['status'=>'success','all_writable'=>$ok,'perms'=>$result]);
    exit;
}
if ($path === '/api/admin/logs' && $method === 'GET') {
    admin_required();
    $logs = load_logs(); $f = $_GET['action'] ?? '';
    if ($f) $logs = array_values(array_filter($logs, fn($l) => ($l['action']??'') === $f));
    $limit = min(intval($_GET['limit'] ?? 500), 2000);
    $logs = array_reverse(array_slice($logs, -$limit));
    json_response(['status'=>'success','logs'=>$logs]);
    exit;
}
if ($path === '/api/admin/clear-logs' && $method === 'POST') {
    admin_required();
    save_logs([]); add_log('ADMIN_CLEAR_LOGS', 'Admin xóa toàn bộ log');
    json_response(['status'=>'success','message'=>'Đã xóa log']);
    exit;
}
if ($path === '/api/keys.txt') {
    if (!file_exists(KEYS_TXT_FILE)) file_put_contents(KEYS_TXT_FILE, "# NEXUS KEY SERVER\n");
    header('Content-Type: text/plain'); readfile(KEYS_TXT_FILE); exit;
}
