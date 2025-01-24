<?php
// Imports
require "db.php";

// Start the session for the User
session_start();

// Variables
$success = false;
$redirects = [
    'admin' => './adminDashboard.php',
    'editor' => './editorDashboard.php',
    'content_creator' => './contentCreatorDashboard.php'
];

// Validation for the form
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    extract($_POST); // $username, $email, $password, $terms

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

    // Validate terms
    if (!isset($terms)) {
        $error["terms"] = "You must agree to the Terms & Conditions";
    }

    if (empty($error)) {

        // Prepare the SQL statement
        $stmt = $db->prepare("SELECT id, username, password, type FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {

            // Store user data in session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_type'] = $user['type'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['last_activity'] = time();

            // Redirect to dashboard
            header("Location: " . $redirects[$user['type']]);
            exit;
        } else {
            $error['auth'] = "Invalid email or password";
        }
        $success = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f5f8;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            display: flex;
            width: 1000px;
            max-width: 90%;
            padding: 40px;
            gap: 60px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 2px 4px rgba(35,25,66,0.1);
        }

        .signup-section {
            flex: 1;
            padding-top: 40px;
        }

        .image-section {
            flex: 1;
            display: flex;
            align-items: flex-start;
            justify-content: center;
        }

        h1 {
            font-size: 42px;
            color: #231942;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .subtitle {
            color: #666;
            margin-bottom: 40px;
            font-size: 16px;
            line-height: 1.5;
        }

        .form-group {
            margin-bottom: 25px;
        }

        label {
            display: block;
            color: #231942;
            margin-bottom: 8px;
            font-size: 14px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 8px 0;
            border: none;
            border-bottom: 1px solid #ddd;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-bottom-color: #231942;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 25px;
        }

        .checkbox-group input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: #231942;
        }

        .checkbox-group label {
            color: #666;
            margin-bottom: 0;
        }

        .checkbox-group a {
            color: #231942;
            text-decoration: none;
        }

        button {
            background-color: #231942;
            color: white;
            padding: 15px 40px;
            border: none;
            border-radius: 25px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
            width: 100%;
        }

        button:hover {
            background-color: #362a55;
        }

        .illustration {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
        }

        .error-message {
            color: #dc3545;
            font-size: 14px;
            margin-top: 5px;
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

        @media (max-width: 768px) {
            .container {
                flex-direction: column;
                padding: 20px;
            }

            .image-section {
                display: none;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="signup-section">
        <h1>Welcome Back!</h1>
        <p class="subtitle">Log in and restart your aesthetic journey!</p>

        <form method="POST" action="">
            <div class="form-group">
                <label for="username">User Name</label>
                <input
                        type="text"
                        id="username"
                        name="username"
                        placeholder="e.g. example"
                        value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                >
                <?php if (isset($error['username'])): ?>
                    <div class="error-message"><?php echo $error['username']; ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="e.g. example@mail.com"
                        value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                >
                <?php if (isset($error['email'])): ?>
                    <div class="error-message"><?php echo $error['email']; ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Enter your password"
                >
                <?php if (isset($error['password'])): ?>
                    <div class="error-message"><?php echo $error['password']; ?></div>
                <?php endif; ?>
            </div>

            <div class="checkbox-group">
                <input
                        type="checkbox"
                        id="terms"
                        name="terms"
                    <?= isset($_POST['terms']) ? 'checked' : ''; ?>
                >
                <label for="terms">I agree to the <a href="./conditions.php">Terms & Conditions</a></label>
                <?php if (isset($error['terms'])): ?>
                    <div class="error-message"><?php echo $error['terms']; ?></div>
                <?php endif; ?>
            </div>

            <button type="submit">Sign In</button>
        </form>
    </div>
    <div class="image-section">
        <img src="./pics/welcome.jpg" alt="Person reading with cat illustration" class="illustration">
    </div>
</div>
</body>
</html>