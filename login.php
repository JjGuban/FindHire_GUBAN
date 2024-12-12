<?php
require 'core/dbConfig.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Validate inputs
    if (empty($username)) $errors[] = "Username is required.";
    if (empty($password)) $errors[] = "Password is required.";

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                session_start();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];

                // Redirect based on role
                if ($user['role'] === 'HR') {
                    header("Location: hr/job_posts.php");
                } else {
                    header("Location: applicant/apply_job.php");
                }
                exit;
            } else {
                $errors[] = "Invalid username or password.";
            }
        } catch (PDOException $e) {
            $errors[] = "Error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FindHire</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            color: #333;
        }
        .login-container {
            background: #ffffff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
        }
        .login-container h1 {
            margin-bottom: 20px;
            color: #007bff;
        }
        .login-container form {
            display: flex;
            flex-direction: column;
        }
        .login-container input[type="text"],
        .login-container input[type="password"] {
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .login-container button {
            padding: 10px;
            background-color: #007bff;
            border: none;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }
        .login-container button:hover {
            background-color: #0056b3;
        }
        .login-container .error {
            color: #dc3545;
            text-align: left;
            margin-bottom: 10px;
        }
        .login-container .register-link {
            margin-top: 15px;
        }
        .login-container .register-link a {
            text-decoration: none;
            color: #007bff;
        }
        .login-container .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Login</h1>
        <?php if ($errors): ?>
            <div class="error">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <form method="POST">
            <label>Username: <input type="text" name="username" required></label>
            <label>Password: <input type="password" name="password" required></label>
            <button type="submit">Login</button>
        </form>
        <div class="register-link">
            <a href="register.php">Don't have an account? Register here.</a>
        </div>
    </div>
</body>
</html>
