<?php
require_once __DIR__ . '/_bootstrap.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/auth.php';

$user = require_auth();
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($method === 'GET') {
    // List chats for current user
    $stmt = db()->prepare('SELECT c.id, c.name, c.is_direct, c.created_at
        FROM chats c
        JOIN chat_members m ON m.chat_id = c.id
        WHERE m.user_id = ?
        ORDER BY c.created_at DESC');
    $stmt->execute([$user['id']]);
    $chats = $stmt->fetchAll();
    json_response(['chats' => $chats]);
}

if ($method === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $isDirect = (int)($_POST['is_direct'] ?? 0);
    $memberIdsRaw = trim($_POST['members'] ?? ''); // comma separated user ids
    $memberIds = array_filter(array_map('intval', array_filter(array_map('trim', explode(',', $memberIdsRaw)))));
    if ($isDirect) {
        // For direct chat, ensure exactly one other member provided
        $memberIds = array_values(array_unique(array_filter($memberIds, fn($id)=> $id !== (int)$user['id'])));
        if (count($memberIds) !== 1) {
            json_response(['error' => 'direct_chat_requires_one_other_member'], 400);
        }
        // Try find existing direct chat between users
        $otherId = $memberIds[0];
        $stmt = db()->prepare('SELECT c.id FROM chats c
            JOIN chat_members m1 ON m1.chat_id=c.id AND m1.user_id=?
            JOIN chat_members m2 ON m2.chat_id=c.id AND m2.user_id=?
            WHERE c.is_direct=1
            GROUP BY c.id HAVING COUNT(*)=2 LIMIT 1');
        $stmt->execute([$user['id'], $otherId]);
        $existing = $stmt->fetch();
        if ($existing) {
            json_response(['chat_id' => (int)$existing['id'], 'existing' => true]);
        }
        $name = '';
    } else {
        if ($name === '') {
            json_response(['error' => 'name_required'], 400);
        }
    }

    db()->beginTransaction();
    try {
        $stmt = db()->prepare('INSERT INTO chats (name, is_direct) VALUES (?, ?)');
        $stmt->execute([$name, $isDirect ? 1 : 0]);
        $chatId = (int)db()->lastInsertId();

        $stmtMem = db()->prepare('INSERT INTO chat_members (chat_id, user_id) VALUES (?, ?)');
        // Add creator
        $stmtMem->execute([$chatId, $user['id']]);
        // Add provided members
        foreach ($memberIds as $mid) {
            if ($mid === (int)$user['id']) continue;
            $stmtMem->execute([$chatId, $mid]);
        }

        db()->commit();
        json_response(['chat_id' => $chatId, 'existing' => false]);
    } catch (Throwable $e) {
        db()->rollBack();
        json_response(['error' => 'create_failed'], 500);
    }
}

json_response(['error' => 'not_found'], 404);

