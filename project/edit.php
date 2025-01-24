<?php
session_start();
global $db;

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'content_creator') {
    header("Location:login.php");
    exit();
}

require "db.php";

$CCName = $_SESSION['username']; 
$contentId = $_GET['content_id'] ?? null; 
$title = $img_category = $description = "";

if ($contentId) {
    $stmt = $db->prepare("SELECT * FROM content WHERE id = :id");
    $stmt->execute([':id' => $contentId]);
    $content = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($content) {
        $title = $content['title'] ?? 'Untitled';
        $img_category = $content['img_category'] ?? 'general';
        $description = $content['description'] ?? '';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $img_category = $_POST['category'];
    $description = $_POST['description'];


    if ($contentId) {
        $stmt = $db->prepare("UPDATE content SET title = :title, img_category = :img_category, description = :description WHERE id = :id");
        $stmt->execute([
            ':title' => $title,
            ':img_category' => $img_category,
            ':description' => $description,
            ':id' => $contentId,
        ]);

        header("Location: contentCreatorDashboard.php");
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Content</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            display: flex;
            min-height: 100vh;
            background: #f5f5f8;
        }

        .sidebar {
            width: 250px;
            background: #231942;
            color: white;
            padding: 20px 0;
            position: fixed;
            height: 100vh;
        }

        .logo {
            text-align: center;
            padding: 20px;
            font-size: 24px;
            font-weight: bold;
            border-bottom: 1px solid #362a55;
            margin-bottom: 20px;
        }

        .nav-links {
            list-style: none;
        }

        .nav-item {
            padding: 15px 25px;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 10px;
            color: white;
            text-decoration: none;
        }

        .nav-item:hover {
            background: #362a55;
        }

        .nav-item i {
            width: 20px;
        }

        .main-content {
            flex: 1;
            margin-left: 250px;
            padding: 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(35,25,66,0.1);
            margin-bottom: 20px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #231942;
        }

        .form-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(35,25,66,0.1);
            max-width: 800px;
            margin: 0 auto;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #231942;
            font-weight: 500;
        }

        .form-group input[type="text"],
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 15px;
        }

        .form-group input[type="text"]:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #231942;
        }

        .form-group textarea {
            height: 150px;
            resize: vertical;
        }

        .image-upload i {
            font-size: 40px;
            color: #231942;
            margin-bottom: 10px;
        }

        .image-upload p {
            color: #666;
            margin: 10px 0;
        }

        .btn-container {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            flex: 1;
            transition: background 0.3s;
        }

        .btn-primary {
            background: #231942;
            color: white;
        }

        .btn-primary:hover {
            background: #362a55;
        }

        .btn-secondary {
            background: #666;
            color: white;
        }

        .btn-secondary:hover {
            background: #555;
        }

        input[type="file"] {
            display: none;
        }

        a {
            text-decoration: none;
        }
    </style>
</head>
<body>

<nav class="sidebar">
    <div class="logo">
        Content Creator
    </div>
    <ul class="nav-links">
        <li>
            <a href="contentCreatorDashboard.php" class="nav-item">
                <i class="fas fa-pencil-alt"></i>
                My Content
            </a>
        </li>
        <li>
            <a href="home.php" class="nav-item">
                <i class="fas fa-globe"></i>
                All Content
            </a>
        </li>
        <li>
            <a href="./contentCreatorJson.php" class="nav-item">
                <i class="fas fa-code"></i>
                JSON API
            </a>
        </li>
        <li>
            <a href="logout.php" class="nav-item">
                <i class="fas fa-sign-out-alt"></i>
                Logout
            </a>
        </li>
    </ul>
</nav>

<div class="main-content">
    <div class="header">
        <h1>Edit Content</h1>
        <div class="user-info">
            <i class="fas fa-user-circle"></i>
            <span><?= $CCName ?></span>
        </div>
    </div>

    <div class="form-container">
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" id="title" name="title" value="<?= htmlspecialchars($title, ENT_QUOTES) ?>" required>
            </div>

            <div class="form-group">
                <label for="category">Category</label>
                <select id="category" name="category" required>
    <option value="events" <?= $img_category === 'events' ? 'selected' : '' ?>>Events</option>
    <option value="sports" <?= $img_category === 'sports' ? 'selected' : '' ?>>Sports</option>
    <option value="announcement" <?= $img_category === 'announcement' ? 'selected' : '' ?>>Announcement</option>
    <option value="academic" <?= $img_category === 'academic' ? 'selected' : '' ?>>Academic</option>
                </select>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" required><?= htmlspecialchars($description, ENT_QUOTES) ?></textarea>
        </div>
            </div>

            <div class="btn-container">
                <button type="button" onclick="window.location.href='contentCreatorDashboard.php'" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>
