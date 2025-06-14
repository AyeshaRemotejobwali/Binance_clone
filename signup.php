<?php
require 'db.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $two_factor_secret = bin2hex(random_bytes(16)); // Mock 2FA secret

    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, two_factor_secret) VALUES (?, ?, ?, ?)");
        $stmt->execute([$username, $email, $password, $two_factor_secret]);
        echo "<script>alert('Signup successful! Please login.'); window.location.href = 'login.php';</script>";
    } catch (PDOException $e) {
        echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Binance Clone</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: #0e1726;
            color: #ffffff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .form-container {
            background: #1a2332;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
            width: 100%;
            max-width: 400px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #f0b90b;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input {
            width: 100%;
            padding: 10px;
            border: 1px solid #2a3444;
            border-radius: 5px;
            background: #2a3444;
            color: #ffffff;
        }
        input:focus {
            outline: none;
            border-color: #f0b90b;
        }
        .btn {
            width: 100%;
            padding: 10px;
            background: #f0b90b;
            color: #0e1726;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }
        .btn:hover {
            background: #ffffff;
        }
        .link {
            text-align: center;
            margin-top: 15px;
        }
        .link a {
            color: #f0b90b;
            text-decoration: none;
        }
        .link a:hover {
            color: #ffffff;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Sign Up</h2>
        <form method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn">Sign Up</button>
        </form>
        <div class="link">
            <p>Already have an account? <a href="#" onclick="redirectTo('login.php')">Login</a></p>
        </div>
    </div>
    <script>
        function redirectTo(page) {
            window.location.href = page;
        }
    </script>
</body>
</html>
