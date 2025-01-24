<?php
// Imports
require "db.php";

// Variables
global $db;

// Get search term if any
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Simple query with optional search
$sql = "SELECT content.*, users.username 
        FROM content 
        JOIN users ON content.creator_id = users.id 
        WHERE content.status = 'approved'";

// Add search if term entered
if ($search) {
    $sql .= " AND (title LIKE ? OR description LIKE ?)";
}

$stmt = $db->prepare($sql);

// Execute with or without search params
if ($search) {
    $searchTerm = "%$search%";
    $stmt->execute([$searchTerm, $searchTerm]);
} else {
    $stmt->execute();
}
$contents = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bulletin Board</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #231942;
            --primary-light: #362a55;
            --accent-color: #5e548e;
            --text-color: #2c2c2c;
            --background-color: #f5f5f8;
            --card-shadow: 0 8px 16px rgba(35,25,66,0.1);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }

        body {
            background: var(--background-color);
            min-height: 100vh;
            color: var(--text-color);
        }

        .navbar {
            background: var(--primary-color);
            color: white;
            padding: 1.2rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .logo {
            font-size: 28px;
            font-weight: 800;
            background: linear-gradient(45deg, #fff, #e0e0e0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }

        .nav-actions {
            display: flex;
            gap: 25px;
            align-items: center;
        }

        .nav-links {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .nav-link {
            color: white;
            text-decoration: none;
            font-size: 15px;
            font-weight: 500;
            opacity: 0.9;
            transition: var(--transition);
            padding: 8px 12px;
            border-radius: 6px;
        }

        .nav-link:hover {
            opacity: 1;
            background: rgba(255,255,255,0.1);
        }

        .search-container {
            background: linear-gradient(135deg, #ffffff, #f8f9fa);
            padding: 25px;
            margin: 30px auto;
            max-width: 800px;
            border-radius: 15px;
            box-shadow: var(--card-shadow);
        }

        .search-box {
            display: flex;
            gap: 15px;
        }

        .search-input {
            flex: 1;
            padding: 12px 20px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 16px;
            transition: var(--transition);
        }

        .search-input:focus {
            outline: none;
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(94,84,142,0.2);
        }

        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            transition: var(--transition);
            font-size: 15px;
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
            box-shadow: 0 4px 6px rgba(35,25,66,0.2);
        }

        .btn-primary:hover {
            background: var(--primary-light);
            transform: translateY(-2px);
        }

        .btn-outline {
            background: rgba(255,255,255,0.15);
            color: white;
            border: 2px solid rgba(255,255,255,0.3);
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            padding: 8px 20px;
        }

        .btn-outline:hover {
            background: rgba(255,255,255,0.25);
            border-color: rgba(255,255,255,0.5);
            transform: translateY(-2px);
        }

        .content-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 30px;
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .content-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: var(--card-shadow);
            transition: var(--transition);
            border: 1px solid rgba(0,0,0,0.05);
        }

        .content-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(35,25,66,0.15);
        }

        .content-image-container {
            position: relative;
            width: 100%;
            padding-bottom: 66.67%;
            overflow: hidden;
            background: #f8f9fa;
        }

        .content-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: var(--transition);
        }

        .content-card:hover .content-image {
            transform: scale(1.05);
        }

        .content-info {
            padding: 25px;
        }

        .content-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #666;
            font-size: 14px;
            margin-bottom: 15px;
        }

        .content-meta span {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .content-meta i {
            color: var(--accent-color);
        }

        .content-title {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 12px;
            color: var(--primary-color);
            line-height: 1.4;
        }

        .content-description {
            color: #666;
            line-height: 1.6;
            font-size: 15px;
        }

        @media (max-width: 768px) {
            .navbar {
                padding: 1rem;
            }

            .search-container {
                margin: 20px;
                padding: 20px;
            }

            .content-grid {
                grid-template-columns: 1fr;
                gap: 20px;
                padding: 15px;
            }

            .logo {
                font-size: 24px;
            }
        }
        :root {
            /* Light theme variables */
            --primary-color: #231942;
            --primary-light: #362a55;
            --accent-color: #5e548e;
            --text-color: #2c2c2c;
            --background-color: #f5f5f8;
            --card-background: #ffffff;
            --card-shadow: 0 8px 16px rgba(35,25,66,0.1);
            --search-background: linear-gradient(135deg, #ffffff, #f8f9fa);
            --input-border: #e0e0e0;
        }

        /* Dark theme variables */
        [data-theme="dark"] {
            --primary-color: #5e548e;
            --primary-light: #7a71a5;
            --accent-color: #9f98c9;
            --text-color: #e1e1e1;
            --background-color: #1a1a1a;
            --card-background: #2d2d2d;
            --card-shadow: 0 8px 16px rgba(0,0,0,0.2);
            --search-background: linear-gradient(135deg, #2d2d2d, #252525);
            --input-border: #404040;
        }

        body {
            background: var(--background-color);
            color: var(--text-color);
            transition: background-color 0.3s ease;
        }

        .content-card {
            background: var(--card-background);
        }

        .search-container {
            background: var(--search-background);
        }

        .search-input {
            background: var(--card-background);
            color: var(--text-color);
            border-color: var(--input-border);
        }

        .content-title {
            color: var(--primary-color);
        }

        .content-description {
            color: var(--text-color);
        }

        .theme-toggle {
            background: transparent;
            border: none;
            color: white;
            cursor: pointer;
            padding: 8px 12px;
            border-radius: 6px;
            transition: var(--transition);
            opacity: 0.9;
        }

        .theme-toggle:hover {
            opacity: 1;
            background: rgba(255,255,255,0.1);
        }

        [data-theme="dark"] .content-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0,0,0,0.3);
        }
    </style>
</head>
<body>
<nav class="navbar">
    <div class="logo">Bulletin Board</div>
    <div class="nav-actions">
        <div class="nav-links">
            <button id="theme-toggle" class="theme-toggle">
                <i class="fas fa-moon"></i>
            </button>
            <a href="./allContentJson.php" class="nav-link">
                API
            </a>
        </div>
        <a href="./login.php" class="btn btn-outline">
            <i class="fas fa-user"></i>
            Login
        </a>
    </div>

</nav>

<div class="search-container">
    <form method="GET" action="" class="search-box">
        <input type="text"
               name="search"
               class="search-input"
               placeholder="Search content..."
               value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-search"></i> Search
        </button>
    </form>
</div>

<div class="content-grid">
    <?php foreach ($contents as $content): ?>
        <div class="content-card">
            <div class="content-image-container">
                <img src="<?php echo htmlspecialchars($content['image_path']); ?>"
                     alt="<?php echo htmlspecialchars($content['title']); ?>"
                     class="content-image">
            </div>
            <div class="content-info">
                <div class="content-meta">
                        <span>
                            <i class="fas fa-user"></i>
                            <?php echo htmlspecialchars($content['username']); ?>
                        </span>
                </div>
                <h2 class="content-title">
                    <?php echo htmlspecialchars($content['title']); ?>
                </h2>
                <p class="content-description">
                    <?php echo htmlspecialchars($content['description']); ?>
                </p>
            </div>
        </div>
    <?php endforeach; ?>
</div>
</body>
<script>
    // toggle functionality
    const themeToggle = document.getElementById('theme-toggle');
    const themeIcon = themeToggle.querySelector('i');

    // Check for saved theme preference
    const savedTheme = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-theme', savedTheme);
    updateThemeIcon(savedTheme);

    themeToggle.addEventListener('click', () => {
        const currentTheme = document.documentElement.getAttribute('data-theme');
        const newTheme = currentTheme === 'light' ? 'dark' : 'light';

        document.documentElement.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);
        updateThemeIcon(newTheme);
    });

    function updateThemeIcon(theme) {
        if (theme === 'dark') {
            themeIcon.classList.remove('fa-moon');
            themeIcon.classList.add('fa-sun');
        } else {
            themeIcon.classList.remove('fa-sun');
            themeIcon.classList.add('fa-moon');
        }
    }

    // Add smooth transition when hovering over cards
    document.querySelectorAll('.content-card').forEach(card => {
        card.addEventListener('mouseenter', () => {
            card.style.transform = 'translateY(-5px)';
        });

        card.addEventListener('mouseleave', () => {
            card.style.transform = 'translateY(0)';
        });
    });
</script>
</html>