<?php
// Start the session for the User
session_start();

// Imports
global $db;
require "db.php";

// Variables
$success = false;
$error = [];
$adminName = $_SESSION['username']; // Get admin name from session

// Fill the users dropdown from the database
$stmtUser = $db->prepare("SELECT * FROM users WHERE type IN ('content_creator') ORDER BY username");
$stmtUser->execute();
$Users = $stmtUser->fetchAll();

// Get the selected user ID if form was submitted
$selectedUserId = isset($_POST['user']) ? $_POST['user'] : '';

// Fetch existing permissions if user is selected
$existingPermissions = null;
if ($selectedUserId) {
    $stmtPerms = $db->prepare("SELECT * FROM user_permissions WHERE user_id = ?");
    $stmtPerms->execute([$selectedUserId]);
    $existingPermissions = $stmtPerms->fetch();
}

// Validation for the form
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Validate user selection
    if (!isset($_POST['user']) || empty($_POST['user'])) {
        $error['user'] = "Please select a user";
    }

    // Convert checkbox values to 0 or 1
    $permissions = [
        'can_search_own_content' => isset($_POST['can_search_own_content']) ? 1 : 0,
        'can_view_others_content' => isset($_POST['can_view_others_content']) ? 1 : 0,
        'can_add_content' => isset($_POST['can_add_content']) ? 1 : 0,
        'can_edit_content' => isset($_POST['can_edit_content']) ? 1 : 0,
        'can_delete_content' => isset($_POST['can_delete_content']) ? 1 : 0
    ];

    // Check if the user already has permissions
    $stmtCheck = $db->prepare("SELECT id FROM user_permissions WHERE user_id = ?");
    $stmtCheck->execute([$selectedUserId]);
    $exists = $stmtCheck->fetch();

    if ($exists) {
        // Update existing permissions
        $stmtUpdate = $db->prepare("
                UPDATE user_permissions 
                SET 
                    can_search_own_content = :can_search_own_content,
                    can_view_others_content = :can_view_others_content,
                    can_add_content = :can_add_content,
                    can_edit_content = :can_edit_content,
                    can_delete_content = :can_delete_content
                WHERE user_id = :user_id
            ");
        $stmtUpdate->execute(array_merge($permissions, ['user_id' => $selectedUserId]));
    } else {
        // Insert new permissions
        $stmtInsert = $db->prepare("
                INSERT INTO user_permissions 
                (user_id, can_search_own_content, can_view_others_content, can_add_content, can_edit_content, can_delete_content)
                VALUES 
                (:user_id, :can_search_own_content, :can_view_others_content, :can_add_content, :can_edit_content, :can_delete_content)
            ");
        $stmtInsert->execute(array_merge($permissions, ['user_id' => $selectedUserId]));
    }
    $success = true;
}

// Fixing Sticky Behavior
if ($success) {
    $stmtPerms = $db->prepare("SELECT * FROM user_permissions WHERE user_id = ?");
    $stmtPerms->execute([$selectedUserId]);
    $currentPermissions = $stmtPerms->fetch();
}

// Or when user is selected in dropdown
elseif ($selectedUserId) {
    $stmtPerms = $db->prepare("SELECT * FROM user_permissions WHERE user_id = ?");
    $stmtPerms->execute([$selectedUserId]);
    $currentPermissions = $stmtPerms->fetch();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Assign Rights</title>
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

        .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 15px;
        }

        .form-group select:focus {
            outline: none;
            border-color: #231942;
        }

        .permissions-list {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 6px;
            border: 1px solid #ddd;
            margin-bottom: 20px;
        }

        .permission-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding: 10px;
            background: white;
            border-radius: 4px;
        }

        .permission-item:last-child {
            margin-bottom: 0;
        }

        .permission-item input[type="checkbox"] {
            margin-right: 10px;
            width: 18px;
            height: 18px;
            accent-color: #231942;
        }

        .permission-item label {
            color: #231942;
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
                <h1>Assign User Rights</h1>
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
                        Permissions updated successfully!
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="user">Select User</label>
                    <select id="user" name="user" required>
                        <option value="">Choose a user</option>

                        <?php
                        foreach ($Users as $u) {
                            $selected = ($selectedUserId == $u['id']) ? 'selected' : '';
                            echo '<option value="' . $u['id'] . '" ' . $selected . '>' . $u['username'] . '</option>';
                        }
                        ?>

                    </select>
                    <?php
                        if (isset($error['user'])) {
                            echo "<div class='error-message'>{$error['user']}</div>";
                        }
                    ?>
                </div>

                <div class="permissions-list">
                    <div class="permission-item">
                        <input type="checkbox"
                               id="can_search_own_content"
                               name="can_search_own_content"
                               value="1"
                            <?php echo (isset($currentPermissions['can_search_own_content']) && $currentPermissions['can_search_own_content']) ? 'checked' : ''; ?>>
                        <label for="can_search_own_content">Search Own Content</label>
                    </div>

                    <div class="permission-item">
                        <input type="checkbox"
                               id="can_view_others_content"
                               name="can_view_others_content"
                               value="1"
                            <?php echo (isset($currentPermissions['can_view_others_content']) && $currentPermissions['can_view_others_content']) ? 'checked' : ''; ?>>
                        <label for="can_view_others_content">View Others' Content</label>
                    </div>

                    <div class="permission-item">
                        <input type="checkbox"
                               id="can_add_content"
                               name="can_add_content"
                               value="1"
                            <?php echo (isset($currentPermissions['can_add_content']) && $currentPermissions['can_add_content']) ? 'checked' : ''; ?>>
                        <label for="can_add_content">Add Content</label>
                    </div>

                    <div class="permission-item">
                        <input type="checkbox"
                               id="can_edit_content"
                               name="can_edit_content"
                               value="1"
                            <?php echo (isset($currentPermissions['can_edit_content']) && $currentPermissions['can_edit_content']) ? 'checked' : ''; ?>>
                        <label for="can_edit_content">Edit Content</label>
                    </div>

                    <div class="permission-item">
                        <input type="checkbox"
                               id="can_delete_content"
                               name="can_delete_content"
                               value="1"
                            <?php echo (isset($currentPermissions['can_delete_content']) && $currentPermissions['can_delete_content']) ? 'checked' : ''; ?>>
                        <label for="can_delete_content">Delete Content</label>
                    </div>
                </div>

                <button type="submit" class="btn">Update Permissions</button>
            </form>
        </div>
    </div>
</body>
</html>