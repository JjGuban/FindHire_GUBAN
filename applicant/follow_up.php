<?php
session_start();
require '../core/dbConfig.php';
require '../core/models.php';

if ($_SESSION['role'] !== 'Applicant') {
    header("Location: ../dashboard.php");
    exit;
}

$applicantId = $_SESSION['user_id'];

// Initialize $selectedHR to avoid undefined variable warning
$selectedHR = null;

// Fetch HR representatives
$stmt = $pdo->query("SELECT id, username FROM users WHERE role = 'HR'");
$hrUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Safely handle the 'hr_id' parameter from GET request
$selectedHR = $_GET['hr_id'] ?? null;

// Fetch messages with a specific HR
$messages = $selectedHR ? getMessages($applicantId, $selectedHR) : [];

// Handle message submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $receiverId = $_POST['hr_id'];
    $content = trim($_POST['content']);

    if (!empty($content)) {
        sendMessage($applicantId, $receiverId, $content);
        $success = "Message sent successfully.";
        $messages = getMessages($applicantId, $receiverId); // Refresh messages
    } else {
        $error = "Message content cannot be empty.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Applicant - Follow Up</title>
    <link rel="stylesheet" href="../styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f8fb;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #007bff;
            text-align: center;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        .form-group select, .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            text-align: center;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .messages {
            border: 1px solid #ccc;
            padding: 10px;
            max-height: 300px;
            overflow-y: auto;
            border-radius: 5px;
            background: #f9f9f9;
        }

        .messages p {
            margin: 10px 0;
        }

        .error {
            color: red;
            margin-bottom: 10px;
        }

        .success {
            color: green;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Message HR</h1>

        <?php if (!empty($error)): ?>
            <p class="error"> <?= htmlspecialchars($error) ?> </p>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <p class="success"> <?= htmlspecialchars($success) ?> </p>
        <?php endif; ?>

        <form method="GET">
            <div class="form-group">
                <label for="hr_id">HR Representative:</label>
                <select name="hr_id" id="hr_id" required onchange="this.form.submit()">
                    <option value="">Select HR</option>
                    <?php foreach ($hrUsers as $hr): ?>
                        <option value="<?= $hr['id'] ?>" <?= $selectedHR == $hr['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($hr['username']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>

        <?php if ($selectedHR): ?>
            <div>
                <h2>Messages with <?= htmlspecialchars($hrUsers[array_search($selectedHR, array_column($hrUsers, 'id'))]['username']) ?></h2>
                <div class="messages">
                    <?php foreach ($messages as $msg): ?>
                        <p>
                            <strong><?= htmlspecialchars($msg['sender_name']) ?>:</strong>
                            <?= nl2br(htmlspecialchars($msg['content'])) ?>
                            <small>(<?= htmlspecialchars($msg['sent_at']) ?>)</small>
                        </p>
                    <?php endforeach; ?>
                </div>
                <form method="POST">
                    <input type="hidden" name="hr_id" value="<?= $selectedHR ?>">
                    <div class="form-group">
                        <textarea name="content" placeholder="Type your message here..." required></textarea>
                    </div>
                    <button type="submit" class="btn">Send Follow-Up</button>
                </form>
            </div>
        <?php endif; ?>

        <div>
            <a href="apply_job.php" class="btn" style="margin-top: 20px;">Back to Job Listings</a>
        </div>
    </div>
</body>
</html>
