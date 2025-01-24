<?php
session_start();
require "db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'editor') {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content_id = $_POST['content_id'];
    $comment = $_POST['comment'];
    $editor_id = $_SESSION['user_id'];

    try {
        // Insert the new comment
        $stmt = $db->prepare("INSERT INTO comments (content_id, editor_id, comment) VALUES (?, ?, ?)");
        $stmt->execute([$content_id, $editor_id, $comment]);

        // Fetch the editor's username
        $stmt = $db->prepare("SELECT username FROM users WHERE id = ?");
        $stmt->execute([$editor_id]);
        $editor = $stmt->fetch();

        echo json_encode([
            'success' => true,
            'comment' => $comment,
            'editor' => $editor['username']
        ]);
    } catch (Exception $e) {
        echo json_encode(['error' => 'Failed to submit comment']);
    }
}