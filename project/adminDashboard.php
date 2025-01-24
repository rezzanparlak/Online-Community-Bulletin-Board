<?php
// Start the session for the User
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location:login.php");
    exit();
}

// Imports
require "db.php";

// Variables
$adminName = $_SESSION['username']; // Get admin name from session
$userType = ["admin","content_creator", "editor"];
global $db;

// Access all users
$rs = $db->query("select * from users") ;
$users = $rs->fetchAll() ;

// Total use count
$totalUsersNo = $rs->rowCount();

// Total content_creator count
$creatorQuery = $db->query("SELECT * FROM users WHERE type = 'content_creator'");
$contentCreatorsNo = $creatorQuery->rowCount();

// Total editor count
$editorQuery = $db->query("SELECT * FROM users WHERE type = 'editor'");
$editorsNo = $editorQuery->rowCount();

// Total content count
$contentQuery = $db->query("SELECT * FROM content");
$contentCount = $contentQuery->rowCount();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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
            transition: transform 0.3s ease;
        }

        .sidebar:hover {
            transform: translateX(5px);
        }

        .logo {
            text-align: center;
            padding: 20px;
            font-size: 24px;
            font-weight: bold;
            border-bottom: 1px solid #362a55;
            margin-bottom: 20px;
            position: relative;
            overflow: hidden;
        }

        .logo::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(
                    to right,
                    transparent,
                    rgba(255, 255, 255, 0.1),
                    transparent
            );
            transform: rotate(45deg);
            animation: shimmer 3s infinite;
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%) rotate(45deg); }
            100% { transform: translateX(100%) rotate(45deg); }
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
            position: relative;
        }

        .nav-item:hover {
            background: #362a55;
            transform: translateX(10px);
        }

        .nav-item i {
            width: 20px;
            transition: transform 0.3s ease;
        }

        .nav-item:hover i {
            transform: scale(1.2);
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
            animation: slideDown 0.5s ease-out;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .clock {
            font-size: 1.2em;
            color: #231942;
            padding: 8px 15px;
            background: #f8f9fa;
            border-radius: 6px;
            border: 1px solid #e9ecef;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .clock i {
            color: #231942;
        }

        @keyframes slideDown {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #231942;
        }

        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(35,25,66,0.1);
        }

        .stat-card {
            padding: 20px;
            border-radius: 8px;
            background: #fff;
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
            animation: fadeIn 0.5s ease-out forwards;
            opacity: 0;
        }

        .stat-card:nth-child(1) { animation-delay: 0.1s; }
        .stat-card:nth-child(2) { animation-delay: 0.2s; }
        .stat-card:nth-child(3) { animation-delay: 0.3s; }
        .stat-card:nth-child(4) { animation-delay: 0.4s; }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(35,25,66,0.1);
        }

        .stat-card h3 {
            color: #231942;
            margin-bottom: 10px;
            font-size: 18px;
        }

        .stat-card .number {
            font-size: 32px;
            font-weight: bold;
            color: #362a55;
            animation: countUp 1.5s ease-out forwards;
            opacity: 0;
        }

        @keyframes countUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .stat-card .description {
            color: #6c757d;
            font-size: 14px;
            margin-top: 5px;
        }

        .logout-link {
            margin-top: auto;
            border-top: 1px solid #362a55;
            padding-top: 20px;
        }

        .user-info i {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        .clock {
            font-size: 1.2em;
            color: #231942;
            margin-right: 20px;
        }
    </style>
    <script>
        // for the clock
        function updateClock() {
            const clock = document.getElementById('clock');
            const now = new Date();
            clock.textContent = now.toLocaleTimeString();
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>
</head>
<body>
<nav class="sidebar">
    <div class="logo">
        Admin Panel
    </div>
    <ul class="nav-links">
        <li>
            <a href="adminDashboard.php" class="nav-item">
                <i class="fas fa-home"></i>
                Dashboard
            </a>
        </li>
        <li>
            <a href="adminCreateUser.php" class="nav-item">
                <i class="fas fa-user-plus"></i>
                Create User
            </a>
        </li>
        <li>
            <a href="adminAssignRights.php" class="nav-item">
                <i class="fas fa-user-shield"></i>
                Assign Rights
            </a>
        </li>
        <li class="logout-link">
            <a href="logout.php" class="nav-item">
                <i class="fas fa-sign-out-alt"></i>
                Logout
            </a>
        </li>
    </ul>
</nav>

<div class="main-content">
    <div class="header">
        <div class="header-left">
            <h1>Dashboard Overview</h1>
        </div>
        <div class="header-right">
            <div class="clock">
                <i class="fas fa-clock"></i>
                <span id="clock"></span>
            </div>
            <div class="user-info">
                <i class="fas fa-user-circle"></i>
                <span>Welcome, Admin <?= $adminName ?></span>
            </div>
        </div>
    </div>

    <div class="stats-container">
        <div class="stat-card">
            <h3>Content Creators</h3>
            <div class="number"><?= $contentCreatorsNo ?></div>
            <div class="description">Active content creators in the system</div>
        </div>
        <div class="stat-card">
            <h3>Editors</h3>
            <div class="number"><?= $editorsNo ?></div>
            <div class="description">Active editors managing content</div>
        </div>
        <div class="stat-card">
            <h3>Total Contents</h3>
            <div class="number"><?= $contentCount ?></div>
            <div class="description">Total pieces of content in the system</div>
        </div>
        <div class="stat-card">
            <h3>Total Users</h3>
            <div class="number"><?= $totalUsersNo ?></div>
            <div class="description">Total registered users</div>
        </div>
    </div>
</div>
</body>
</html>