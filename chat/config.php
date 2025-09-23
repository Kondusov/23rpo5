<?php
// Basic configuration
define('DB_HOST', '127.0.0.1:3307');
define('DB_NAME', 'chat_app');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Long-polling max wait seconds
define('LPOLL_TIMEOUT', 25);
define('LPOLL_INTERVAL_MS', 500);

// Error handling for API
ini_set('display_errors', '0');
ini_set('log_errors', '1');

// Session
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
session_name('CHATSESSID');

function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    }
    return $pdo;
}

function json_response($data, int $status = 200): void {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    // Ensure no stray output corrupts JSON
    if (ob_get_level() > 0) {
        while (ob_get_level() > 0) { ob_end_clean(); }
    }
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

