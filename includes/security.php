<?php
/**
 * security.php - Input sanitization, CSRF, rate limiting, IP ban
 */

function sanitize_input($input) {
    if (is_array($input)) return array_map('sanitize_input', $input);
    if (!is_string($input)) return $input;
    $input = str_replace(chr(0), '', $input);
    $input = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $input);
    return trim($input);
}

function e($str) { return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8'); }

function validate_key_format($key) { return preg_match('/^[A-Za-z0-9_\-]{1,64}$/', $key); }
function validate_ip($ip) { return filter_var($ip, FILTER_VALIDATE_IP); }

function get_json_input() {
    $raw = file_get_contents('php://input');
    if (empty($raw)) return [];
    $data = json_decode($raw, true);
    if (!is_array($data)) return [];
    return sanitize_input($data);
}

function csrf_token() {
    if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf_token'];
}
function csrf_field() { return '<input type="hidden" name="_csrf" value="'.e(csrf_token()).'">'; }

function check_rate_limit($action, $max = 60) {
    $ip = get_client_ip();
    $data = file_exists(RATE_FILE) ? (json_decode(file_get_contents(RATE_FILE), true) ?: []) : [];
    $now = time();
    $key = "{$ip}:{$action}";
    foreach ($data as $k => $v) { if ($now - ($v['ts'] ?? 0) > 60) unset($data[$k]); }
    if (!isset($data[$key])) $data[$key] = ['count' => 0, 'ts' => $now];
    $data[$key]['count']++;
    file_put_contents(RATE_FILE, json_encode($data));
    if ($data[$key]['count'] > $max) {
        http_response_code(429);
        json_response(['status'=>'error','message'=>'Quá nhiều yêu cầu!'], 429);
        exit;
    }
}
