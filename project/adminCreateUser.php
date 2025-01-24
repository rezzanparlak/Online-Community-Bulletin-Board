<?php
// Start the session for the User
session_start();

// Imports
require "db.php";

// Variables
$success = false;
$adminName = $_SESSION['username']; // Get admin name from session

// Validation for the form
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    extract($_POST); // $username, $email, $password, $userType

    // Error array
    $error = [];

    // Validate username
    if (!isset($username) || strlen(trim($username)) < 3) {
        $error["username"] = "Username must be at least 3 characters long";
    }

    // Validate email
    if (!isset($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error["email"] = "Please enter a valid email address";
    }

    // Validate password
    if (!isset($password) || strlen($password) < 5) {
        $error["password"] = "Password must be at least 5 characters long";
    }

    // Validate userType
    if (!isset($userType) || !in_array($userType, ["content_creator", "editor"])) {
        $error["userType"] = "Please select a user type";
    }

    if (empty($error)) {
        $check = $db->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $check->execute([$username, $email]);

        if ($check->rowCount() > 0) {
            $error['database'] = "Username or email already exists";
        } else {
            // Hash the password and insert user
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $db->prepare("INSERT INTO users (username, email, password, type) VALUES (?, ?, ?, ?)");
            $stmt->execute([$username, $email, $hashedPassword, $userType]);

            $success = true;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Create User</title>
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
            max-width: 600px;
            margin: 0 auto;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #231942;
            font-weight: 500;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 15px;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #231942;
        }

        .btn {
            background: #231942;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            width: 100%;
            transition: background 0.3s;
        }

        .btn:hover {
            background: #362a55;
        }

        .logout-link {
            margin-top: auto;
            border-top: 1px solid #362a55;
            padding-top: 20px;
        }
        .error-message {
            color: #dc3545;
            font-size: 14px;
            margin-top: 5px;
        }

        .form-group input.error,
        .form-group select.error {
            border-color: #dc3545;
        }

        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            text-align: center;
            border: 1px solid #c3e6cb;
        }
    </style>
    <script>
        function updateClock() {
            const clock = document.getElementById('clock');
            const now = new Date();
            clock.textContent = now.toLocaleTimeString();
        }
        setInterval(updateClock, 1000);
        window.onload = updateClock;
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
                <h1>Create New User</h1>
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

        <div class="form-container">
            <form action="" method="POST">

                <?php if ($success): ?>
                    <div class="success-message">
                        User created successfully!
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required placeholder="Enter username"  value="<?= isset($_POST['username']) ? $_POST['username'] : ''; ?>">
                    <?php if (isset($error['username'])): ?>
                        <div class="error-message"><?php echo $error['username']; ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required placeholder="Enter email" value="<?= isset($_POST['email']) ? $_POST['email'] : ''; ?>">
                    <?php if (isset($error['email'])): ?>
                        <div class="error-message"><?php echo $error['email']; ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required placeholder="Enter password">
                    <?php if (isset($error['password'])): ?>
                        <div class="error-message"><?php echo $error['password']; ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="userType">User Type</label>
                    <select id="userType" name="userType" required>
                        <option value="" <?= isset($userType) && $userType === "" ? " selected" : ""  ?>>Select user type</option>
                        <option value="content_creator" <?= isset($userType) && $userType === "content_creator" ? " selected" : ""  ?>>Content Creator</option>
                        <option value="editor" <?= isset($userType) && $userType === "editor" ? " selected" : ""  ?>>Editor</option>
                    </select>
                    <?php if (isset($error['userType'])): ?>
                        <div class="error-message"><?php echo $error['userType']; ?></div>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn">Create User</button>
            </form>
        </div>
    </div>
</body>
</html>