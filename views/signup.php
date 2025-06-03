<?php
include '../includes/config.php';
?>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <title>Регистрация</title>
    <link rel="stylesheet" href="../layout/css/register.css">   
</head>
<body>
    <div class="container">
        <h3>Регистрация</h3>
        <?php
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            echo '<form method="post" action="" enctype="multipart/form-data">
                <label for="user_name">Потребителско име:</label>
                <input type="text" id="user_name" name="user_name" required>

                <label for="user_pass">Парола:</label>
                <input type="password" id="user_pass" name="user_pass" required>

                <label for="user_pass_check">Повторете паролата:</label>
                <input type="password" id="user_pass_check" name="user_pass_check" required>

                <label for="user_email">Електронна поща:</label>
                <input type="email" id="user_email" name="user_email" required>

                <label for="user_avatar">Качете аватар:</label>
                <input type="file" id="user_avatar" name="user_avatar" accept="image/*">

                <input type="submit" value="Регистрирай се">
            </form>';

            echo '<p>Already registered? <a href="login.php">Sign in here</a>.</p>'; // Добавен линк за вход
        } else {
            $errors = [];
            $avatarDirectory = 'uploads/avatars'; // Абсолютен път до директорията за аватари
            $avatarURLPath = 'uploads/avatars/'; // Път за съхранение в базата

            // Проверка за потребителско име
            if (!ctype_alnum($_POST['user_name']) || strlen($_POST['user_name']) > 30) {
                $errors[] = 'Потребителското име може да съдържа само букви и цифри и не може да бъде по-дълго от 30 символа.';
            }

            // Проверка за паролата
            if ($_POST['user_pass'] != $_POST['user_pass_check']) {
                $errors[] = 'Двете пароли не съвпадат.';
            }

            // Проверка за качения аватар
            $avatarFileName = 'default-avatar.png';
            if (isset($_FILES['user_avatar']) && $_FILES['user_avatar']['error'] == 0) {
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                if (!in_array($_FILES['user_avatar']['type'], $allowedTypes)) {
                    $errors[] = 'Моля, качете валидно изображение (JPG, PNG или GIF).';
                } else {
                    $fileExt = pathinfo($_FILES['user_avatar']['name'], PATHINFO_EXTENSION);
                    $avatarFileName = uniqid() . '.' . $fileExt;
                    $avatarFilePath = $avatarDirectory . '/' . $avatarFileName;

                    if (!move_uploaded_file($_FILES['user_avatar']['tmp_name'], $avatarFilePath)) {
                        $errors[] = 'Възникна проблем при качването на аватара.';
                    }
                }
            }

            // Проверка за дублиране на потребителско име
            $check_sql = "SELECT COUNT(*) FROM users WHERE user_name = ?";
            $check_stmt = mysqli_prepare($conn, $check_sql);
            mysqli_stmt_bind_param($check_stmt, 's', $_POST['user_name']);
            mysqli_stmt_execute($check_stmt);
            mysqli_stmt_bind_result($check_stmt, $user_exists);
            mysqli_stmt_fetch($check_stmt);
            mysqli_stmt_close($check_stmt);

            if ($user_exists > 0) {
                $errors[] = 'Потребителското име вече е заето.';
            }

            // Ако има грешки, показваме ги
            if (!empty($errors)) {
                echo '<div class="error-list">';
                foreach ($errors as $error) {
                    echo '<p>' . htmlspecialchars($error) . '</p>';
                }
                echo '</div>';
            } else {
                $sql = "INSERT INTO users (user_name, user_pass, user_email, user_date, user_level, user_avatar) 
                        VALUES (?, ?, ?, NOW(), 0, ?)";
                $stmt = mysqli_prepare($conn, $sql);
                $password = password_hash($_POST['user_pass'], PASSWORD_BCRYPT);

                $avatarDBPath = $avatarURLPath . $avatarFileName;
                mysqli_stmt_bind_param($stmt, 'ssss', $_POST['user_name'], $password, $_POST['user_email'], $avatarDBPath);
                if (!mysqli_stmt_execute($stmt)) {
                    echo '<div class="error-list">Възникна грешка при регистрацията. Моля, опитайте отново.</div>';
                } else {
                    echo '<div class="success">Успешно се регистрирахте. Можете да <a href="login.php">влезете</a> и да започнете да публикувате!</div>';
                }
            }
        }
        ?>
    </div>
</body>
</html>

<?php
include '../includes/footer.php';
?>
