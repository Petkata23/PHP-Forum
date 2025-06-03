<?php
include '../includes/config.php';
include '../includes/header.php';

// Вземане на ID на темата от URL
$topic_id = isset($_GET['topic_id']) ? intval($_GET['topic_id']) : 0;

if ($topic_id <= 0) {
    die('Invalid topic ID.');
}

// Подготовка и изпълнение на заявка за постовете
$sql = "
    SELECT 
        p.post_content, 
        p.post_date, 
        u.user_name 
    FROM 
        posts p
    LEFT JOIN 
        users u 
    ON 
        p.post_by = u.user_id 
    WHERE 
        p.post_topic = ? 
    ORDER BY 
        p.post_date ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $topic_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die('<p>No posts found for this topic.</p>');
}
?>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <title>Topic Posts</title>
    <link rel="stylesheet" href="../layout/css/posts.css">
</head>
<body>
<div class="container">
    <h1>Posts in Topic</h1>

    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="post">
            <h3><?php echo htmlspecialchars($row['user_name']); ?></h3>
            <small>Posted on <?php echo date('F j, Y, g:i a', strtotime($row['post_date'])); ?></small>
            <p><?php echo htmlspecialchars($row['post_content']); ?></p>
        </div>
    <?php endwhile; ?>

    <?php if (isset($_SESSION['signed_in']) && $_SESSION['signed_in']): ?>
        <div class="reply-form">
            <h2>Reply to this topic</h2>
            <form method="post" action="reply.php?topic_id=<?php echo $topic_id; ?>">
                <textarea name="reply_content" placeholder="Write your reply here..." required></textarea>
                <button type="submit">Submit Reply</button>
            </form>
        </div>
    <?php else: ?>
        <p>You must <a href="signin.php">sign in</a> to reply to this topic.</p>
    <?php endif; ?>
</div>
</body>
</html>

<?php
include '../includes/footer.php';
?>
