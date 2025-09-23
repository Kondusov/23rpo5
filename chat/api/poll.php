<?php
require_once __DIR__ . '/_bootstrap.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/auth.php';

$user = require_auth();

$chatId = (int)($_GET['chat_id'] ?? 0);
$afterId = isset($_GET['after_id']) ? (int)$_GET['after_id'] : 0;
if ($chatId <= 0) json_response(['error' => 'chat_id_required'], 400);

// Ensure membership
$stmt = db()->prepare('SELECT 1 FROM chat_members WHERE chat_id=? AND user_id=?');
$stmt->execute([$chatId, $user['id']]);
if (!$stmt->fetch()) json_response(['error' => 'forbidden'], 403);

$start = time();
while (true) {
    $stmt = db()->prepare('SELECT id, chat_id, user_id, content, type, created_at FROM messages WHERE chat_id=? AND id>? ORDER BY id ASC');
    $stmt->execute([$chatId, $afterId]);
    $rows = $stmt->fetchAll();
    if (!empty($rows)) {
        json_response(['messages' => $rows]);
    }

    if ((time() - $start) >= LPOLL_TIMEOUT) {
        json_response(['messages' => []]);
    }
    usleep(LPOLL_INTERVAL_MS * 1000);
}

