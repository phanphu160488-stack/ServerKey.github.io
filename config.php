<?php
/**
 * config.php - Constants, paths, and session
 */
if (session_status() === PHP_SESSION_NONE) session_start();

define('BASE_DIR', dirname(__DIR__));
define('PHP_DIR', __DIR__);
define('DATA_FILE', BASE_DIR . '/keys_data.json');
define('CONFIG_FILE', BASE_DIR . '/config.json');
define('LOG_FILE', BASE_DIR . '/logs.json');
define('SECURITY_FILE', BASE_DIR . '/security.json');
define('STATS_FILE', BASE_DIR . '/link4m_stats.json');
define('KEYS_TXT_FILE', BASE_DIR . '/keys.txt');
define('USED_KEYS_FILE', BASE_DIR . '/used_keys.txt');
define('NOTIFY_FILE', BASE_DIR . '/notifications.json');
define('BACKUP_DIR', BASE_DIR . '/backups');
define('RATE_FILE', BASE_DIR . '/rate_limit.json');
define('LINK4M_URL', 'https://link4m.co/api-shorten/v2');
define('LINK4M_STATS_URL', 'https://link4m.co/api/statistics/v2');

$KEY_DURATIONS = ['1day'=>1,'3day'=>3,'7day'=>7,'30day'=>30,'365day'=>365,'forever'=>-1];
