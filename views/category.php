<?php
include '../includes/config.php';
include '../includes/header.php';

// Проверка дали ID на категория е зададено
$category_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($category_id <= 0) {
    die('Invalid category ID.');
}

// Вземане на информация за категорията
$category_sql = "SELECT cat_name, cat_photo FROM categories WHERE cat_id = ?";
$category_stmt = $conn->prepare($category_sql);
$category_stmt->bind_param('i', $category_id);
$category_stmt->execute();
$category_result = $category_stmt->get_result();

if ($category_result->num_rows == 0) {
    die('Category not found.');
}

$category = $category_result->fetch_assoc();

// Вземане на темите в категорията
$topics_sql = "
    SELECT 
        topic_id, 
        topic_subject, 
        topic_date 
    FROM 
        topics 
    WHERE 
        topic_cat = ?
    ORDER BY 
        topic_date ASC";
$topics_stmt = $conn->prepare($topics_sql);
$topics_stmt->bind_param('i', $category_id);
$topics_stmt->execute();
$topics_result = $topics_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <title>Topics in '<?php echo htmlspecialchars($category['cat_name']); ?>'</title>
    <link rel="stylesheet" href="../layout/css/category.css">
</head>
<body>
<div class="container">
    <h1>Topics in '<?php echo htmlspecialchars($category['cat_name']); ?>' category</h1>
    <?php if (!empty($category['cat_photo'])): ?>
        <img src="uploads/<?php echo htmlspecialchars($category['cat_photo']); ?>" alt="<?php echo htmlspecialchars($category['cat_name']); ?> image" class="category-image">
    <?php endif; ?>
    <?php if ($topics_result->num_rows > 0): ?>
        <table>
            <tr>
                <th>Topic</th>
                <th>Created at</th>
            </tr>
            <?php while ($topic = $topics_result->fetch_assoc()): ?>
                <tr>
                    <td><a href="topic.php?id=<?php echo $topic['topic_id']; ?>"><?php echo htmlspecialchars($topic['topic_subject']); ?></a></td>
                    <td><?php echo date('d-m-Y', strtotime($topic['topic_date'])); ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No topics found in this category.</p>
    <?php endif; ?>
    <div class="create-topic">
        <a href="new_topic.php?category_id=<?php echo $category_id; ?>">Create New Topic</a>
    </div>
</div>
</body>
</html>

<?php
include '../includes/footer.php'; 
?>
