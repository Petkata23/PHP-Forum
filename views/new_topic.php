<?php
ob_start(); // Стартира буфериране на изхода
include '../includes/config.php';
include '../includes/header.php';

// Проверка дали потребителят е влязъл
if (!isset($_SESSION['signed_in']) || !$_SESSION['signed_in']) {
    die("You must be signed in to create a topic.");
}

// Проверка дали ID на категорията е предадено
$category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;

// Ако няма ID на категорията, показва форма за избор на категория
if ($category_id <= 0) {
    // Вземане на списъка с категории от базата данни
    $categories_sql = "SELECT cat_id, cat_name FROM categories";
    $categories_result = $conn->query($categories_sql);

    if (!$categories_result) {
        die("Error fetching categories: " . $conn->error);
    }
    ?>
    <!DOCTYPE html>
    <html lang="bg">
    <head>
        <meta charset="UTF-8">
        <title>Изберете категория</title>
        <link rel="stylesheet" href="../layout/css/newTopic.css">
    </head>
    <body>
        <div id="new-topic-page">
            <div class="container">
                <h1>Изберете категория</h1>
                <form method="GET">
                    <label for="category">Категория:</label>
                    <select name="category_id" id="category" required>
                        <option value="" disabled selected>-- Изберете категория --</option>
                        <?php while ($row = $categories_result->fetch_assoc()): ?>
                            <option value="<?php echo $row['cat_id']; ?>">
                                <?php echo htmlspecialchars($row['cat_name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <button type="submit">Продължи</button>
                </form>
            </div>
        </div>
    </body>
    </html>
    <?php
    ob_end_flush(); // Освобождава буферирането на изхода
    exit;
}

// Проверка дали формулярът е изпратен
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Вземане на данни от формуляра
    $topic_by = $_SESSION['user_id']; // Текущ потребител
    $topic_subject = htmlspecialchars(trim($_POST['topic_subject']));
    $message = htmlspecialchars(trim($_POST['message']));

    // Проверка за празни полета
    if (empty($topic_subject) || empty($message)) {
        die("Error: All fields are required.");
    }

    // Стартиране на транзакция
    $conn->begin_transaction();

    try {
        // Вмъкване на темата
        $topic_sql = "INSERT INTO topics (topic_cat, topic_by, topic_subject, topic_date) 
                      VALUES (?, ?, ?, NOW())";
        $topic_stmt = $conn->prepare($topic_sql);
        $topic_stmt->bind_param('iis', $category_id, $topic_by, $topic_subject);
        $topic_stmt->execute();

        // Вземане на ID на новосъздадената тема
        $topic_id = $conn->insert_id;

        // Вмъкване на съобщението като първи пост
        $post_sql = "INSERT INTO posts (post_content, post_date, post_topic, post_by) 
                     VALUES (?, NOW(), ?, ?)";
        $post_stmt = $conn->prepare($post_sql);
        $post_stmt->bind_param('sii', $message, $topic_id, $topic_by);
        $post_stmt->execute();

        // Потвърждаване на транзакцията
        $conn->commit();

        // Пренасочване към новата тема
        header("Location: topic.php?id=$topic_id");
        ob_end_flush(); // Освобождава буферирането на изхода
        exit;
    } catch (Exception $e) {
        // В случай на грешка - връщане на транзакцията
        $conn->rollback();
        die("Error: Failed to create topic and message. Please try again.");
    }
}

// Вземане на името на категорията от базата данни
$category_sql = "SELECT cat_name FROM categories WHERE cat_id = ?";
$category_stmt = $conn->prepare($category_sql);
$category_stmt->bind_param('i', $category_id);
$category_stmt->execute();
$category_result = $category_stmt->get_result();

if ($category_result->num_rows == 0) {
    die("Category not found.");
}

$category = $category_result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <title>Създаване на нова тема</title>
    <link rel="stylesheet" href="../layout/css/newTopic.css">
</head>
<body>
    <div id="new-topic-page-2">
        <div class="container">
            <h1>Създаване на нова тема в '<?php echo htmlspecialchars($category['cat_name']); ?>'</h1>
            <form method="POST">
                <label for="topic_subject">Заглавие на темата:</label>
                <input type="text" name="topic_subject" id="topic_subject" placeholder="Заглавие на темата" required>
                
                <label for="message">Съобщение:</label>
                <textarea name="message" id="message" placeholder="Напишете вашето съобщение тук..." required></textarea>
                
                <button type="submit">Създай тема</button>
            </form>
        </div>
    </div>
</body>
</html>

<?php
include '../includes/footer.php';
?>
