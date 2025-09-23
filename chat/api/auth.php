<?php
require_once __DIR__ . '/_bootstrap.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/auth.php';

start_session_if_needed();

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$action = $_GET['action'] ?? $_POST['action'] ?? '';

if ($method === 'POST' && $action === 'register') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    if ($username === '' || $password === '') {
        json_response(['error' => 'username_and_password_required'], 400);
    }
    $stmt = db()->prepare('SELECT id FROM users WHERE username = ?');
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        json_response(['error' => 'username_taken'], 409);
    }
    $hash = password_hash($password, PASSWORD_BCRYPT);
    $stmt = db()->prepare('INSERT INTO users (username, password_hash) VALUES (?, ?)');
    $stmt->execute([$username, $hash]);
    $userId = (int)db()->lastInsertId();
    $_SESSION['user_id'] = $userId;
    json_response(['ok' => true, 'user' => ['id' => $userId, 'username' => $username]]);
}

if ($method === 'POST' && $action === 'login') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    if ($username === '' || $password === '') {
        json_response(['error' => 'username_and_password_required'], 400);
    }
    $stmt = db()->prepare('SELECT id, password_hash FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $row = $stmt->fetch();
    if (!$row || !password_verify($password, $row['password_hash'])) {
        json_response(['error' => 'invalid_credentials'], 401);
    }
    $_SESSION['user_id'] = (int)$row['id'];
    json_response(['ok' => true, 'user' => ['id' => (int)$row['id'], 'username' => $username]]);
}

if ($method === 'POST' && $action === 'logout') {
    session_destroy();
    json_response(['ok' => true]);
}

if ($method === 'GET' && $action === 'me') {
    $user = current_user();
    if (!$user) json_response(['user' => null]);
    json_response(['user' => $user]);
}

json_response(['error' => 'not_found'], 404);

