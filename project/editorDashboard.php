<?php
// Start the session for the User
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'editor') {
    header("Location:login.php");
    exit();
}

// Imports
require "db.php";

// Variables
$editorName = $_SESSION['username']; // Get name from session
$editor_id = $_SESSION['user_id'];
global $db;

// Function to fetch content to display every content
function getContent($status = null) {
    global $db;

    $query = "SELECT c.*, u.username as creator_name, 
              (SELECT GROUP_CONCAT(CONCAT(com.comment, '|', ed.username) SEPARATOR '&&')
               FROM comments com 
               JOIN users ed ON com.editor_id = ed.id 
               WHERE com.content_id = c.id) as comments
              FROM content c
              JOIN users u ON c.creator_id = u.id";

    // If status parameter is provided, add WHERE clause to filter by status, to use in search
    if ($status) {
        $query .= " WHERE c.status = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$status]);
    } else {
        $stmt = $db->prepare($query);
        $stmt->execute();
    }

    return $stmt->fetchAll();
}

// Handle approve button
// When someone clicks an "approve" button
if (isset($_POST['approve'])) {

    // Get the ID of the content being approved
    $content_id = $_POST['content_id'];

    // Update the content's status to 'approved'
    $stmt = $db->prepare("UPDATE content SET status = 'approved' WHERE id = ?");
    $stmt->execute([$content_id]);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Handle delete button
if (isset($_POST['delete'])) {

    // Get the ID of the content to delete
    $content_id = $_POST['content_id'];

    // Permanently remove the content record
    $stmt = $db->prepare("DELETE FROM content WHERE id = ?");
    $stmt->execute([$content_id]);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Handle comment submission
if (isset($_POST['submit_comment'])) {
    $content_id = $_POST['content_id'];
    $comment = trim($_POST['comment']);

    // Only insert if comment is not empty
    if (!empty($comment)) {
        $stmt = $db->prepare("INSERT INTO comments (content_id, editor_id, comment) VALUES (?, ?, ?)");
        $stmt->execute([$content_id, $editor_id, $comment]);
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Handle search functionality
function searchContent($searchTerm, $searchType) {
    global $db;

    // Search in content titles and descriptions
    if ($searchType === 'content') {
        $query = "SELECT c.*, u.username as creator_name 
                 FROM content c 
                 JOIN users u ON c.creator_id = u.id 
                 WHERE c.title LIKE ? OR c.description LIKE ?";
        $searchTerm = "%$searchTerm%";
        $stmt = $db->prepare($query);
        $stmt->execute([$searchTerm, $searchTerm]);
    } else { // search creators
        $query = "SELECT c.*, u.username as creator_name 
                 FROM content c 
                 JOIN users u ON c.creator_id = u.id 
                 WHERE u.username LIKE ?";
        $searchTerm = "%$searchTerm%";
        $stmt = $db->prepare($query);
        $stmt->execute([$searchTerm]);
    }

    return $stmt->fetchAll();
}

// Get content based on search or filters
$content = [];

// Check for different types of filters and get appropriate content
if (isset($_GET['search_content']) && !empty($_GET['search_content'])) {
    // Search by content title or description
    $content = searchContent($_GET['search_content'], 'content');
} else if (isset($_GET['search_creator']) && !empty($_GET['search_creator'])) {
    // Search by creator username
    $content = searchContent($_GET['search_creator'], 'creator');
} else if (isset($_GET['status'])) {
    // Filter by content status
    $content = getContent($_GET['status']);
} else {
    // No filters - get all content
    $content = getContent();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editor Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
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

        .search-container {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }

        .search-box {
            flex: 1;
            display: flex;
            gap: 10px;
        }

        .search-input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
        }

        .search-input:focus {
            outline: none;
            border-color: #231942;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            transition: background 0.3s;
        }

        .btn-primary {
            background: #231942;
            color: white;
        }

        .btn-primary:hover {
            background: #362a55;
        }

        .content-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .content-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(35,25,66,0.1);
            overflow: hidden;
        }

        .content-image {
            width: 100%;
            height: 200px;
            background: #ddd;
            object-fit: cover;
        }

        .content-info {
            padding: 15px;
        }

        .creator-info {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
            color: #666;
        }

        .content-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #231942;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .comment-section {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
        }

        .comment-input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            margin-bottom: 10px;
            resize: vertical;
        }

        .comment-input:focus {
            outline: none;
            border-color: #231942;
        }

        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .tab {
            padding: 10px 20px;
            background: white;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.3s;
            color: #231942;
        }

        .tab.active {
            background: #231942;
            color: white;
        }

        .tab:hover {
            background: #f5f5f5;
        }

        .tab.active:hover {
            background: #362a55;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
            margin-bottom: 10px;
        }

        .status-pending {
            background: #b8860b;
            color: white;
        }

        .status-approved {
            background: #2d8a61;
            color: white;
        }

        .btn-delete {
            background: #dc3545;
        }

        .btn-delete:hover {
            background: #c82333;
        }

        .btn-comment {
            background: #2d8a61;
        }

        .btn-comment:hover {
            background: #246d4d;
        }
        a {
            text-decoration: none;
        }
        .no-results {
            text-align: center;
            padding: 40px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(35,25,66,0.1);
            margin: 20px 0;
            color: #666;
            font-size: 1.1em;
        }

        .no-results i {
            display: block;
            font-size: 2em;
            color: #231942;
            margin-bottom: 15px;
        }
    </style>
    <script>
        // Tab functionality for filtering content
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('.tab'); // Get all elements with 'tab' class

            // Add click handlers to each tab
            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    tabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');

                    let status = '';
                    if (this.textContent === 'Pending Approval') {
                        status = 'pending';
                    } else if (this.textContent === 'Approved') {
                        status = 'approved';
                    }

                    window.location.href = `${window.location.pathname}${status ? '?status=' + status : ''}`;
                });
            });
        });
    </script>
</head>
<body>

<nav class="sidebar">
    <div class="logo">Editor Panel</div>
    <ul class="nav-links">
        <li>
            <a href="editorDashboard.php" class="nav-item">
                <i class="fas fa-home"></i>
                Dashboard
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
        <h1>Editor Dashboard</h1>
        <div class="user-info">
            <i class="fas fa-user-circle"></i>
            <span> <?= $editorName ?></span>
        </div>
    </div>

    <div class="tabs">
        <div class="tab active">All Content</div>
        <div class="tab">Pending Approval</div>
        <div class="tab">Approved</div>
    </div>

    <div class="search-container">
        <form method="GET" class="search-box">
            <input type="text" name="search_content" class="search-input" placeholder="Search content..." value="<?= isset($_GET['search_content']) ? htmlspecialchars($_GET['search_content']) : '' ?>">
            <button type="submit" class="btn btn-primary">Search Content</button>
        </form>
        <form method="GET" class="search-box">
            <input type="text" name="search_creator" class="search-input" placeholder="Search creators..." value="<?= isset($_GET['search_creator']) ? htmlspecialchars($_GET['search_creator']) : '' ?>">
            <button type="submit" class="btn btn-primary">Search Creators</button>
        </form>
    </div>

    <div class="content-grid">
        <!-- Loop through each content item in the $content array -->
        <?php foreach ($content as $item): ?>
            <div class="content-card">
                <!-- PHP security function : htmlspecialchars -->
                <img src="<?= htmlspecialchars($item['image_path']) ?>" alt="Content" class="content-image">
                <div class="content-info">
                    <div class="creator-info">
                        <i class="fas fa-user"></i>
                        <span>Created by: <?= htmlspecialchars($item['creator_name']) ?></span>
                    </div>
                    <div class="content-title"><?= htmlspecialchars($item['title']) ?></div>
                    <span class="status-badge status-<?= strtolower($item['status']) ?>">
                    <?= ucfirst($item['status']) ?>
                </span>
                    <p><?= htmlspecialchars($item['description']) ?></p>

                    <div class="comment-section">
                        <div class="comments-container">
                            <?php if (!empty($item['comments'])): ?>
                                <?php
                                $commentPairs = explode('&&', $item['comments']);
                                foreach ($commentPairs as $pair):
                                    list($comment, $editor) = explode('|', $pair);
                                    ?>
                                    <p class="comment">
                                        <i class="fas fa-comment"></i>
                                        <strong><?= htmlspecialchars($editor) ?>:</strong>
                                        <?= htmlspecialchars($comment) ?>
                                    </p>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <form method="post" action="<?= $_SERVER['PHP_SELF'] ?>">
                            <input type="hidden" name="content_id" value="<?= $item['id'] ?>">
                            <textarea name="comment" class="comment-input" placeholder="Add a comment..."></textarea>
                            <div class="action-buttons">
                                <?php if ($item['status'] === 'pending'): ?>
                                    <button type="submit" name="approve" value="1" class="btn btn-primary">Approve</button>
                                <?php endif; ?>
                                <button type="submit" name="delete" value="1" class="btn btn-delete">Delete</button>
                                <button type="submit" name="submit_comment" value="1" class="btn btn-comment">Submit Comment</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if (empty($content)): ?>
        <div class="no-results">
            <i class="fas fa-search"></i>
            <?php if (isset($_GET['search_content'])): ?>
                <p>No content found matching "<?= htmlspecialchars($_GET['search_content']) ?>"</p>
            <?php elseif (isset($_GET['search_creator'])): ?>
                <p>No creators found matching "<?= htmlspecialchars($_GET['search_creator']) ?>"</p>
            <?php else: ?>
                <p>No content available</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>

</div>
</body>
</html>