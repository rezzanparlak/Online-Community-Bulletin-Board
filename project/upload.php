<?php
session_start();
require "db.php";
global $db;

// Check if user is logged in and is a content creator
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'content_creator') {
    header("Location: login.php");
    exit();
}

// Check user permissions to see if they are allowed to add content
$stmt = $db->prepare("SELECT can_add_content FROM user_permissions WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$permissions = $stmt->fetch();

if (!$permissions || !$permissions['can_add_content']) {
    header("Location: contentCreatorDashboard.php?error=You don't have permission to add content");
    exit();
}

// Function to handle file upload
function handleFileUpload($file) {
    $targetDir = "posters/";
    $errors = [];

    // Create posters directory if it doesn't exist
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    // Get file extension
    $imageFileType = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));

    // Generate unique filename
    $fileName = uniqid() . '_' . time() . '.' . $imageFileType;
    $targetFile = './' . $targetDir . $fileName;

    // Validate image
    $check = getimagesize($file["tmp_name"]);
    if ($check === false) {
        return ["error" => "File is not an image.", "path" => null];
    }

    // Check file size (limit to 5MB)
    if ($file["size"] > 5000000) {
        return ["error" => "File is too large. Maximum size is 5MB.", "path" => null];
    }

    // Check file format
    $allowedFormats = ["jpg", "jpeg", "png"];
    if (!in_array($imageFileType, $allowedFormats)) {
        return ["error" => "Only JPG, JPEG, PNG files are allowed.", "path" => null];
    }

    // Upload file
    if (!move_uploaded_file($file["tmp_name"], $targetFile)) {
        return ["error" => "Failed to upload file.", "path" => null];
    }

    return ["error" => null, "path" => $targetFile];
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $errors = [];

    // Validate required fields
    if (empty($_POST['title'])) {
        $errors[] = "Title is required";
    }
    if (empty($_POST['category'])) {
        $errors[] = "Category is required";
    }
    if (empty($_POST['description'])) {
        $errors[] = "Description is required";
    }

    // Check if file was uploaded
    if (!isset($_FILES["image"]) || $_FILES["image"]["error"] !== UPLOAD_ERR_OK) {
        $errors[] = "Please upload an image";
    } else {
        // Handle file upload
        $uploadResult = handleFileUpload($_FILES["image"]);
        if ($uploadResult["error"]) {
            $errors[] = $uploadResult["error"];
        }
    }

    // If there are no errors, do database insertion
    if (empty($errors)) {
        $sql = "INSERT INTO content (creator_id, title, description, image_path, img_category, status) 
                VALUES (?, ?, ?, ?, ?, 'pending')";

        $stmt = $db->prepare($sql);
        $success = $stmt->execute([
            $_SESSION['user_id'],
            $_POST['title'],
            $_POST['description'],
            $uploadResult["path"],
            $_POST['category']
        ]);

        if ($success) {
            header("Location: contentCreatorDashboard.php?success=1");
            exit();
        } else {
            $errors[] = "Failed to save content to database";
        }
    }

    // If there are errors, redirect back with error messages
    if (!empty($errors)) {
        $errorString = implode(", ", $errors);
        header("Location: contentCreatorAddContent.php?error=" . urlencode($errorString));
        exit();
    }
}
?>
