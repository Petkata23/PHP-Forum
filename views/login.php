<?php
include '../includes/config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Взимане на данните от формуляра
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Проверка за съществуващ потребител
    $sql = "SELECT user_id, user_name, user_pass, user_level FROM users WHERE user_name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['user_pass'])) {
        // Вход успешен
        $_SESSION['signed_in'] = true;
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['user_name'] = $user['user_name'];
        $_SESSION['user_level'] = $user['user_level'];
        echo "Login successful! Welcome, " . htmlspecialchars($user['user_name']) . ".";
        header('Location: index.php');
        exit;
    } else {
        // Грешно потребителско име или парола
        $error_message = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign In</title>
    <link rel="stylesheet" href="../layout/css/login.css">
</head>
<body>
    <div class="login-container">
        <h2>Sign In</h2>

        <?php if (!empty($error_message)): ?>
            <div class="error-message">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" name="username" id="username" placeholder="Enter your username" required>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" placeholder="Enter your password" required>
            </div>

            <button type="submit">Login</button>
        </form>

        <p>Don't have an account? <a href="signup.php">Register here</a>.</p>
    </div>
</body>
</html>

<?php
include '../includes/footer.php';
?>

