<?php
include '../includes/config.php';  
session_start();

// Проверка дали потребителят е влязъл
if (!isset($_SESSION['signed_in']) || !$_SESSION['signed_in']) {
    die('You must be signed in to post a reply.');
}

// Проверка дали ID на тема е зададено
$topic_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($topic_id <= 0) {
    die('Invalid topic ID.');
}

// Проверка дали формата е изпратена
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $reply_content = htmlspecialchars(trim($_POST['reply-content']));
    $user_id = $_SESSION['user_id'];

    if (empty($reply_content)) {
        die('Reply content cannot be empty.');
    }

    // Вмъкване на отговора
    $sql = "INSERT INTO posts (post_content, post_date, post_topic, post_by) VALUES (?, NOW(), ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sii', $reply_content, $topic_id, $user_id);

    if ($stmt->execute()) {
        header("Location: topic.php?id=$topic_id");
        exit;
    } else {
        die('Error: Could not save reply.');
    }
} else {
    die('This file cannot be accessed directly.');
}

include '../includes/footer.php';
?>
