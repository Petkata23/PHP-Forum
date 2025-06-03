<?php
include('../includes/config.php');     
include('../includes/header.php');

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Fetch the user data
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare('SELECT * FROM users WHERE user_id = :user_id');
$stmt->execute(['user_id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "User not found.";
    exit;
}

// Fetch the topics created by the user
$stmt = $pdo->prepare('SELECT * FROM topics WHERE topic_by = :user_id');
$stmt->execute(['user_id' => $user_id]);
$topics = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <h1>User Profile: <?= htmlspecialchars($user['user_name']) ?></h1>
    <p>Email: <?= htmlspecialchars($user['user_email']) ?></p>
    <p>Member since: <?= $user['user_date'] ?></p>

    <h2>Your Topics</h2>
    <ul>
        <?php foreach ($topics as $topic): ?>
            <li><a href="topic.php?topic_id=<?= $topic['topic_id'] ?>"><?= htmlspecialchars($topic['topic_subject']) ?></a></li>
        <?php endforeach; ?>
    </ul>
</div>

<?php include('../includes/footer.php'); ?>
