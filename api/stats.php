<?php
/**
 * api/stats.php - Link4m earnings stats
 */
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

if ($path === '/api/admin/link4m-stats' && $method === 'GET') {
    admin_required();
    $stats = load_stats(); $cfg = load_config();
    $rate = floatval($cfg['link4m_rate_per_1000'] ?? 1.5);
    $today = date('Y-m-d'); $daily = $stats['daily'] ?? [];
    $last7 = [];
    for ($i = 6; $i >= 0; $i--) { $d = date('Y-m-d', strtotime("-{$i} days")); $last7[] = ['date'=>$d,'clicks'=>$daily[$d] ?? 0]; }
    json_response(['status'=>'success','total_clicks'=>$stats['total_clicks']??0,'today_clicks'=>$daily[$today]??0,'estimated_earnings'=>$stats['estimated_earnings']??0.0,'rate_per_1000'=>$rate,'last_click'=>$stats['last_click']??null,'last_7_days'=>$last7]);
    exit;
}
