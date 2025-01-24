<?php
session_start();
require "db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'content_creator') {
    header("Location: login.php");
    exit();
}

$contentCreatorId = $_SESSION['user_id']; 
global $db;

try {
    $stmt = $db->prepare("SELECT * FROM content WHERE creator_id = :creator_id");
    $stmt->execute([':creator_id' => $contentCreatorId]);
    $contents = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode($contents);
} catch (Exception $e) {
    header('Content-Type: application/json', true, 500);
    echo json_encode(['error' => 'An error occurred while fetching content.', 'details' => $e->getMessage()]);
}
?>
