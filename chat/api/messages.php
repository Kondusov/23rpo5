<?php
require_once __DIR__ . '/_bootstrap.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/auth.php';

$user = require_auth();
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// Validate access to chat
function assert_chat_member(int $chatId, int $userId): void {
    $stmt = db()->prepare('SELECT 1 FROM chat_members WHERE chat_id=? AND user_id=?');
    $stmt->execute([$chatId, $userId]);
    if (!$stmt->fetch()) {
        json_response(['error' => 'forbidden'], 403);
    }
}

if ($method === 'GET') {
    $chatId = (int)($_GET['chat_id'] ?? 0);
    $afterId = isset($_GET['after_id']) ? (int)$_GET['after_id'] : 0;
    if ($chatId <= 0) json_response(['error' => 'chat_id_required'], 400);
    assert_chat_member($chatId, (int)$user['id']);

    $stmt = db()->prepare('SELECT id, chat_id, user_id, content, type, created_at FROM messages WHERE chat_id=? AND id>? ORDER BY id ASC LIMIT 100');
    $stmt->execute([$chatId, $afterId]);
    $messages = $stmt->fetchAll();
    json_response(['messages' => $messages]);
}

if ($method === 'POST') {
    $chatId = (int)($_POST['chat_id'] ?? 0);
    $content = trim($_POST['content'] ?? '');
    $type = ($_POST['type'] ?? 'text') === 'image' ? 'image' : 'text';
    if ($chatId <= 0) json_response(['error' => 'chat_id_required'], 400);
    if ($type === 'text' && $content === '') json_response(['error' => 'content_required'], 400);
    assert_chat_member($chatId, (int)$user['id']);

    $stmt = db()->prepare('INSERT INTO messages (chat_id, user_id, content, type) VALUES (?,?,?,?)');
    $stmt->execute([$chatId, $user['id'], $content, $type]);
    $id = (int)db()->lastInsertId();
    $stmt2 = db()->prepare('SELECT id, chat_id, user_id, content, type, created_at FROM messages WHERE id=?');
    $stmt2->execute([$id]);
    $message = $stmt2->fetch();
    json_response(['message' => $message]);
}

json_response(['error' => 'not_found'], 404);

