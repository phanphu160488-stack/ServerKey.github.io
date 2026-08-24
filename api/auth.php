<?php
/**
 * api/auth.php - Login/Logout API
 */
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

if ($path === '/login' && $method === 'POST') {
    check_rate_limit('login', 10);
    $input = get_json_input();
    $password = $input['password'] ?? '';
    $cfg = load_config();
    if ($password === ($cfg['admin_password'] ?? 'admin123')) {
        $_SESSION['is_admin'] = true;
        $_SESSION['admin_login_time'] = time();
        add_log('ADMIN_LOGIN', 'Admin đăng nhập thành công');
        json_response(['status'=>'success','message'=>'Đăng nhập thành công']);
    } else {
        add_log('ADMIN_LOGIN_FAIL', 'Đăng nhập admin thất bại');
        if (!empty($cfg['notify_on_login_fail'])) send_webhook("⚠️ Login THẤT BẠI từ IP: ".get_client_ip());
        json_response(['status'=>'error','message'=>'Mật khẩu sai!'], 401);
    }
    exit;
}
if ($path === '/logout') {
    unset($_SESSION['is_admin'], $_SESSION['admin_login_time']);
    add_log('ADMIN_LOGOUT', 'Admin đăng xuất');
    header('Location: /login'); exit;
}
