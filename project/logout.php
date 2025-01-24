<?php
// Start the session for the User
session_start();

if (isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
    session_destroy();
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout</title>
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
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: #f5f5f8;
        }

        .modal {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 2px 10px rgba(35,25,66,0.1);
            text-align: center;
            width: 400px;
            animation: fadeIn 0.3s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .icon {
            font-size: 50px;
            color: #231942;
            margin-bottom: 20px;
        }

        h2 {
            color: #231942;
            margin-bottom: 15px;
            font-size: 24px;
            font-weight: 600;
        }

        p {
            color: #666;
            margin-bottom: 30px;
            font-size: 16px;
            line-height: 1.5;
        }

        .button-group {
            display: flex;
            gap: 15px;
            justify-content: center;
        }

        button {
            padding: 12px 30px;
            border: none;
            border-radius: 25px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            min-width: 120px;
        }

        .confirm {
            background-color: #231942;
            color: white;
        }

        .confirm:hover {
            background-color: #362a55;
            transform: translateY(-2px);
        }

        .cancel {
            background-color: #e0e0e0;
            color: #333;
        }

        .cancel:hover {
            background-color: #d0d0d0;
            transform: translateY(-2px);
        }

        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            animation: fadeInBg 0.3s ease-out;
        }

        @keyframes fadeInBg {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
    </style>
</head>
<body>
<div class="modal-overlay">
    <div class="modal">
        <i class="fas fa-sign-out-alt icon"></i>
        <h2>Confirm Logout</h2>
        <p>Are you sure you want to logout from your account?</p>
        <div class="button-group">
            <button onclick="window.location.href='logout.php?confirm=yes'" class="confirm">Yes, Logout</button>
            <button onclick="history.back()" class="cancel">Cancel</button>
        </div>
    </div>
</div>
</body>
</html>