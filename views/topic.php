<?php
include '../includes/config.php';
include '../includes/header.php';

// Проверка дали ID на тема е зададено
$topic_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($topic_id <= 0) {
    die('Invalid topic ID.');
}

// Вземане на информация за темата
$topic_sql = "SELECT topic_id, topic_subject FROM topics WHERE topic_id = ?";
$topic_stmt = $conn->prepare($topic_sql);
$topic_stmt->bind_param('i', $topic_id);
$topic_stmt->execute();
$topic_result = $topic_stmt->get_result();

if ($topic_result->num_rows == 0) {
    die('Topic not found.');
}

$topic = $topic_result->fetch_assoc();

// Вземане на постовете за темата
$posts_sql = "
    SELECT 
        posts.post_id, 
        posts.post_content, 
        posts.post_date, 
        users.user_id, 
        users.user_name 
    FROM 
        posts 
    LEFT JOIN 
        users 
    ON 
        posts.post_by = users.user_id 
    WHERE 
        posts.post_topic = ?
    ORDER BY 
        posts.post_date ASC";

$posts_stmt = $conn->prepare($posts_sql);
$posts_stmt->bind_param('i', $topic_id);
$posts_stmt->execute();
$posts_result = $posts_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($topic['topic_subject']); ?></title>
    <link rel="stylesheet" href="../layout/css/topic.css">
</head>
<body>
<div class="container">
    <h1><?php echo htmlspecialchars($topic['topic_subject']); ?></h1>

    <h2>Posts</h2>
    <?php while ($post = $posts_result->fetch_assoc()): ?>
        <div class="post">
            <h3><?php echo htmlspecialchars($post['user_name']); ?></h3>
            <small>Posted on <?php echo date('F j, Y, g:i a', strtotime($post['post_date'])); ?></small>
            <p><?php echo htmlspecialchars($post['post_content']); ?></p>
        </div>
    <?php endwhile; ?>

    <?php if (isset($_SESSION['signed_in']) && $_SESSION['signed_in']): ?>
        <div class="reply-form">
            <h2>Reply to this topic</h2>
            <form method="post" action="reply.php?id=<?php echo $topic_id; ?>">
                <textarea name="reply-content" placeholder="Your reply..." required></textarea><br><br>
                <button type="submit">Submit Reply</button>
            </form>
        </div>
    <?php else: ?>
        <p>You must <a href="signin.php">sign in</a> to reply to this topic.</p>
    <?php endif; ?>
</div>


<?php
include '../includes/footer.php';
?>