<?php
require 'core/dbConfig.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $role = $_POST['role'];

    // Validate inputs
    if (empty($username)) $errors[] = "Username is required.";
    if (empty($password)) $errors[] = "Password is required.";
    if (!in_array($role, ['Applicant', 'HR'])) $errors[] = "Invalid role selected.";

    if (empty($errors)) {
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Insert user
        try {
            $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            $stmt->execute([$username, $hashedPassword, $role]);
            header("Location: login.php");
            exit;
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
    <title>Register - FindHire</title>
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
        .register-container {
            background: #ffffff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
        }
        .register-container h1 {
            margin-bottom: 20px;
            color: #0056b3;
        }
        .register-container form {
            display: flex;
            flex-direction: column;
        }
        .register-container input[type="text"],
        .register-container input[type="password"],
        .register-container select {
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .register-container button {
            padding: 10px;
            background-color: #007bff;
            border: none;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }
        .register-container button:hover {
            background-color: #0056b3;
        }
        .register-container .error {
            color: #dc3545;
            text-align: left;
            margin-bottom: 10px;
        }
        .register-container .login-link {
            margin-top: 15px;
        }
        .register-container .login-link a {
            text-decoration: none;
            color: #0056b3;
        }
        .register-container .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h1>Register</h1>
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
            <label>Role: 
                <select name="role">
                    <option value="Applicant">Applicant</option>
                    <option value="HR">HR</option>
                </select>
            </label>
            <button type="submit">Register</button>
        </form>
        <div class="login-link">
            <a href="login.php">Already have an account? Login here.</a>
        </div>
    </div>
</body>
</html>