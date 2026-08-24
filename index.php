<?php
/**
 * ============================================================
 * NEXUS KEY SERVER - PHP Router
 * ============================================================
 * This is the main entry point. It:
 * 1. Loads config and includes
 * 2. Routes requests to API handlers or page renderers
 *
 * For free hosting (Apache): uses .htaccess for URL rewriting
 * For local dev: use `php -S localhost:8000 router.php`
 * ============================================================
 */

// === LOAD CORE FILES ===
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/helpers.php';

// === ROUTE ===
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$path = rtrim($path, '/');

// Check banned IP
check_banned_ip();

// === API ROUTES ===

// Auth
if ($path === '/login' && $method === 'POST') { require __DIR__ . '/api/auth.php'; exit; }
if ($path === '/logout') { require __DIR__ . '/api/auth.php'; exit; }

// Public: check key, get key, show key
if ($path === '/check' || $path === '/api/check-key' || $path === '/getkey' || $path === '/showkey' || $path === '/api/lookup-key') {
    require __DIR__ . '/api/public.php';
    exit;
}

// Public: notifications
if ($path === '/api/notifications') { require __DIR__ . '/api/notifications.php'; exit; }

// Admin APIs
if (strpos($path, '/api/admin/') === 0) {
    // Determine which API file to load
    $api = substr($path, 10); // Remove '/api/admin/'
    
    if (in_array($api, ['create-key','delete-key','update-key','ban-key','unban-key','bulk-create','bulk-delete','reset-keys','get-keys'])) {
        require __DIR__ . '/api/keys.php'; exit;
    }
    if (in_array($api, ['ban-ip','unban-ip','banned-ips'])) {
        require __DIR__ . '/api/security.php'; exit;
    }
    if (in_array($api, ['notify-broadcast','notifications','notify-delete'])) {
        require __DIR__ . '/api/notifications.php'; exit;
    }
    if ($api === 'link4m-stats') { require __DIR__ . '/api/stats.php'; exit; }
    if ($api === 'settings') { require __DIR__ . '/api/settings.php'; exit; }
    if (in_array($api, ['backup','backups','restore-backup'])) { require __DIR__ . '/api/backup.php'; exit; }
    if (in_array($api, ['system','check-perms','logs','clear-logs'])) { require __DIR__ . '/api/system.php'; exit; }
}

// Keys.txt download
if ($path === '/api/keys.txt') { require __DIR__ . '/api/system.php'; exit; }

// === PAGE ROUTES ===

// Admin dashboard
if ($path === '/admin') {
    if (!($_SESSION['is_admin'] ?? false)) { header('Location: /login'); exit; }
    output_html(file_get_contents(__DIR__ . '/pages/admin.php'), false);
    exit;
}

// Login page
if ($path === '/login' && $method === 'GET') {
    output_html(file_get_contents(__DIR__ . '/pages/login.php'), false);
    exit;
}

// Home page
if ($path === '' || $path === '/') {
    output_html(file_get_contents(__DIR__ . '/pages/home.php'));
    exit;
}

// === 404 ===
http_response_code(404);
echo '<!DOCTYPE html><html><head><title>404</title></head><body style="background:#070913;color:#fff;display:flex;align-items:center;justify-content:center;height:100vh;font-family:sans-serif"><h1>404 - Không tìm thấy trang</h1></body></html>';
