<?php
session_start();
include '../components/connected.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = (string) $_SESSION['user_id'];

// Handle Reply Submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reply_message'], $_POST['message_id'])) {
    $reply_message = trim($_POST['reply_message']);
    $message_id = intval($_POST['message_id']);

    if (!empty($reply_message) && $message_id > 0) {
        // Original message ka sender fetch karo
        $select_message = $conn->prepare("SELECT sender_id FROM message WHERE id = ?");
        $select_message->execute([$message_id]);
        $original_message = $select_message->fetch(PDO::FETCH_ASSOC);

        if ($original_message) {
            $receiver_id = (string) $original_message['sender_id']; // Admin ID

            // Reply insert karo
            $insert_reply = $conn->prepare("INSERT INTO message (sender_id, receiver_id, message, reply_to) VALUES (?, ?, ?, ?)");
            $insert_reply->execute([$user_id, $receiver_id, $reply_message, $message_id]);

            header("Location: ../user_pannel/user_message.php");
            exit();
        }
    }
}

// Fetch messages (excluding replies)
$select_messages = $conn->prepare("
    SELECT m.id, m.message, m.sender_id, 
           COALESCE(u.name, s.name, 'Unknown') AS sender_name
    FROM message m
    LEFT JOIN users u ON m.sender_id = u.id
    LEFT JOIN sellers s ON m.sender_id = s.id
    WHERE (m.receiver_id = ? OR m.sender_id = ?) AND m.reply_to IS NULL
    ORDER BY m.id DESC
");
$select_messages->execute([$user_id, $user_id]);
$messages = $select_messages->fetchAll(PDO::FETCH_ASSOC);

// Fetch replies separately
$select_replies = $conn->prepare("
    SELECT r.id, r.message, r.sender_id, r.reply_to,
           COALESCE(ru.name, rs.name, 'Unknown') AS reply_sender_name
    FROM message r
    LEFT JOIN users ru ON r.sender_id = ru.id
    LEFT JOIN sellers rs ON r.sender_id = rs.id
    WHERE r.reply_to IS NOT NULL
");
$select_replies->execute();
$replies = $select_replies->fetchAll(PDO::FETCH_ASSOC);

// Group replies by message_id
$replies_by_message = [];
foreach ($replies as $reply) {
    $replies_by_message[$reply['reply_to']][] = $reply;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Messages - F1</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #d1ecf1;
            color: #0c5460;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .content {
            flex-grow: 1;
            margin-top: 80px;
            margin-bottom: 60px;
            padding: 15px;
            overflow-y: auto;
        }

        .message-box {
            background-color: #bee5eb;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .message-box.user {
            background-color: #f8d7da;
            text-align: right;
        }

        .reply-box {
            background-color: #e2e3e5;
            border-left: 3px solid #17a2b8;
            padding: 10px;
            margin-top: 10px;
            border-radius: 5px;
            font-size: 0.9rem;
        }

        .btn {
            background-color: #17a2b8;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 5px;
        }

        .btn:hover {
            background-color: #138496;
        }

        .reply-form {
            display: none;
            margin-top: 10px;
        }

        .reply-form textarea {
            width: 100%;
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        header, footer {
            background-color: #17a2b8;
            color: white;
            text-align: center;
            padding: 10px 0;
            position: fixed;
            width: 100%;
            z-index: 1000;
        }

        header {
            top: 0;
        }

        footer {
            bottom: 0;
        }
    </style>
</head>

<body>
    <header>
        <a href="../user_pannel/dashboard.php" class="back-icon"><i class="fa fa-home"></i></a>
        User Messages
    </header>

    <div class="container mt-4">
        <h2 class="text-center">Your Messages</h2>

        <?php foreach ($messages as $message): ?>
            <div class="message-box <?= $message['sender_id'] == $user_id ? 'user' : '' ?>">
                <p><strong>From: </strong> <?= htmlspecialchars($message['sender_name']); ?></p>
                <p><strong>Message:</strong> <?= htmlspecialchars($message['message']); ?></p>

                <?php if (!empty($replies_by_message[$message['id']])): ?>
                    <?php foreach ($replies_by_message[$message['id']] as $reply): ?>
                        <div class="reply-box">
                            <p><strong>From <?= htmlspecialchars($reply['reply_sender_name']); ?>:</strong></p>
                            <p><?= htmlspecialchars($reply['message']); ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <button class="btn mt-2" onclick="toggleReplyForm(<?= $message['id']; ?>)">Reply</button>

                <div class="reply-form" id="reply-form-<?= $message['id']; ?>">
                    <form method="POST">
                        <textarea name="reply_message" placeholder="Write your reply..." required></textarea>
                        <input type="hidden" name="message_id" value="<?= $message['id']; ?>">
                        <button type="submit" class="btn mt-2">Send Reply</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <footer>
        F1 - All rights reserved &copy; 2025
    </footer>

    <script>
        function toggleReplyForm(messageId) {
            const replyForm = document.getElementById('reply-form-' + messageId);
            replyForm.style.display = (replyForm.style.display === 'block') ? 'none' : 'block';
        }
    </script>
</body>
</html>
