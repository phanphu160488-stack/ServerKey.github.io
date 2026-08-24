<?php
/**
 * api/backup.php - Backup/Restore
 */
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

if ($path === '/api/admin/backup' && $method === 'POST') {
    admin_required();
    if (!is_dir(BACKUP_DIR)) mkdir(BACKUP_DIR, 0755, true);
    $files = [DATA_FILE, CONFIG_FILE, LOG_FILE, SECURITY_FILE, STATS_FILE, KEYS_TXT_FILE, USED_KEYS_FILE];
    $ts = date('Y-m-d_H-i-s');
    $backup_file = BACKUP_DIR."/backup_{$ts}.zip";
    $zip = new ZipArchive();
    if ($zip->open($backup_file, ZipArchive::CREATE|ZipArchive::OVERWRITE) === true) {
        foreach ($files as $f) { if (file_exists($f)) $zip->addFile($f, basename($f)); }
        $zip->close();
        add_log('ADMIN_BACKUP', "Admin tạo backup: ".basename($backup_file));
        json_response(['status'=>'success','file'=>basename($backup_file),'size'=>filesize($backup_file)]);
    } else {
        json_response(['status'=>'error','message'=>'Lỗi tạo backup'], 500);
    }
    exit;
}
if ($path === '/api/admin/backups' && $method === 'GET') {
    admin_required();
    $backups = [];
    if (is_dir(BACKUP_DIR)) {
        foreach (glob(BACKUP_DIR.'/backup_*.zip') as $f) {
            $backups[] = ['name'=>basename($f),'size'=>filesize($f),'time'=>date('Y-m-d H:i:s', filemtime($f))];
        }
    }
    rsort($backups);
    json_response(['status'=>'success','backups'=>$backups]);
    exit;
}
if ($path === '/api/admin/restore-backup' && $method === 'POST') {
    admin_required();
    $input = get_json_input(); $file = $input['file'] ?? '';
    if (!preg_match('/^backup_[\d\-_]+\.zip$/', $file)) { json_response(['status'=>'error','message'=>'Tên file không hợp lệ'], 400); exit; }
    $path_zip = BACKUP_DIR.'/'.$file;
    if (!file_exists($path_zip)) { json_response(['status'=>'error','message'=>'File không tồn tại'], 404); exit; }
    $zip = new ZipArchive();
    if ($zip->open($path_zip) === true) { $zip->extractTo(BASE_DIR); $zip->close(); add_log('ADMIN_RESTORE', "Admin restore từ $file"); json_response(['status'=>'success','message'=>'Đã restore']); }
    else { json_response(['status'=>'error','message'=>'Lỗi解压'], 500); }
    exit;
}
