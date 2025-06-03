<?php
session_start();
include 'init.php'; // Вашият инициализационен файл
?>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Форум</title>
    <link rel="stylesheet" href="../layout/css/header.css">    
</head>
<body>
    <header>
        <div class="logo">
            <a href="index.php">Форум</a>
        </div>

        <nav>
            <ul>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="new_topic.php">Нова тема</a></li>
                    <li><a href="create_category.php">Нова категория</a></li>
                    <li><a href="logout.php">Изход</a></li>
                <?php else: ?>
                    <li><a href="login.php">Вход</a></li>
                    <li><a href="signup.php">Регистрация</a></li>
                <?php endif; ?>
            </ul>
            <form class="search-form" action="search.php" method="GET">
                <input type="text" name="search_query" placeholder="Търсене" required>
                <button type="submit">Търсене</button>
            </form>
        </nav>

        <?php if (isset($_SESSION['user_id'])): 
            // Извличане на потребителска информация от базата данни
            $stmt = $conn->prepare("SELECT user_avatar, user_name FROM users WHERE user_id = ?");
            $stmt->bind_param("i", $_SESSION['user_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0):
                $user = $result->fetch_assoc();
                $avatar = !empty($user['user_avatar']) ? $user['user_avatar'] : 'default-avatar.png';
        ?>
            <div class="user-info">
                <img src="uploads/avatars/<?php echo htmlspecialchars($avatar); ?>" alt="Аватар">
                <span>Здравей, <?php echo htmlspecialchars($user['user_name']); ?></span>
            </div>
        <?php endif; $stmt->close(); endif; ?>
    </header>
</body>
</html>
