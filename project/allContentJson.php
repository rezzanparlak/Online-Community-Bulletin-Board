<?php
// Imports
global $db;
require "db.php";

$sql = "SELECT * FROM content";

$stmt = $db->prepare($sql);
$stmt->execute();
$contents = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Turn it into Json String
echo json_encode($contents);