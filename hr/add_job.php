<?php
session_start();
require '../core/dbConfig.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'HR') {
    header("Location: ../dashboard.php");
    exit;
}

$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $created_by = $_SESSION['user_id'];

    if (empty($title) || empty($description)) {
        $errorMessage = 'Title and description cannot be empty.';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO job_posts (title, description, created_by) VALUES (?, ?, ?)");
            $stmt->execute([$title, $description, $created_by]);
            header('Location: job_posts.php');
            exit();
        } catch (PDOException $e) {
            $errorMessage = 'Error: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Job Post</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 50%;
            margin: 50px auto;
            background: #fff;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        h1 {
            color: #003366;
            text-align: center;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 10px 5px;
            background-color: #0056b3;
            color: #fff;
            text-decoration: none;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-align: center;
        }

        .btn:hover {
            background-color: #004494;
        }

        .btn-danger {
            background-color: #d9534f;
        }

        .btn-danger:hover {
            background-color: #c9302c;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            font-weight: bold;
            color: #003366;
            margin-bottom: 5px;
        }

        input, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 16px;
        }

        input:focus, textarea:focus {
            border-color: #0056b3;
            outline: none;
        }

        .btn-primary {
            background-color: #007bff;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .error {
            background-color: #f8d7da;
            color: #842029;
            padding: 10px;
            border: 1px solid #f5c2c7;
            border-radius: 4px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Add a New Job Post</h1>
        <a href="job_posts.php" class="btn">Back to Job Posts</a>
        <a href="../core/handleForms.php?logout=1" class="btn btn-danger">Logout</a>

        <?php if (!empty($errorMessage)): ?>
            <div class="error">
                <?php echo htmlspecialchars($errorMessage); ?>
            </div>
        <?php endif; ?>

        <form action="add_job.php" method="POST">
            <div class="form-group">
                <label for="title">Job Title:</label>
                <input type="text" id="title" name="title" required>
            </div>
            <div class="form-group">
                <label for="description">Job Description:</label>
                <textarea id="description" name="description" rows="5" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Add Job</button>
        </form>
    </div>
</body>
</html>
