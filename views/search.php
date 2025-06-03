<?php
include '../includes/config.php';
include '../includes/header.php';

// Проверка дали има подаден параметър за търсене
if (isset($_GET['search_query'])) {
    $searchQuery = $_GET['search_query'];
} else {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
    <link rel="stylesheet" href="../layout/css/search.css">
</head>
<body>
<div class="container">
    <h2>Search Results for: "<?php echo htmlspecialchars($searchQuery); ?>"</h2>

    <?php
    $searchTerm = "%" . $conn->real_escape_string($searchQuery) . "%";

    // Търсене в категории
    $queryCategories = "
        SELECT 
            cat_id, 
            cat_name, 
            cat_description, 
            cat_photo 
        FROM categories 
        WHERE cat_name LIKE ? OR cat_description LIKE ?
    ";
    $stmtCategories = $conn->prepare($queryCategories);
    $stmtCategories->bind_param("ss", $searchTerm, $searchTerm);
    $stmtCategories->execute();
    $resultCategories = $stmtCategories->get_result();

    if ($resultCategories->num_rows > 0) {
        echo '<h3>Categories:</h3>';
        while ($row = $resultCategories->fetch_assoc()) {
            echo '<div class="forum-item">';
            echo '<div class="forum-header">';
            echo '<a href="category.php?id=' . $row['cat_id'] . '">' . htmlspecialchars($row['cat_name']) . '</a>';
            echo '</div>';
            echo '<div class="forum-body">';
            if (!empty($row['cat_photo'])) {
                echo '<img src="uploads/' . htmlspecialchars($row['cat_photo']) . '" alt="' . htmlspecialchars($row['cat_name']) . '">';
            }
            echo '<p>' . htmlspecialchars($row['cat_description']) . '</p>';
            echo '</div>';
            echo '</div>';
        }
    }

    // Търсене в теми
    $queryTopics = "
        SELECT 
            topic_id, 
            topic_subject, 
            topic_date, 
            topic_photo 
        FROM topics 
        WHERE topic_subject LIKE ?
    ";
    $stmtTopics = $conn->prepare($queryTopics);
    $stmtTopics->bind_param("s", $searchTerm);
    $stmtTopics->execute();
    $resultTopics = $stmtTopics->get_result();

    if ($resultTopics->num_rows > 0) {
        echo '<h3>Topics:</h3>';
        while ($row = $resultTopics->fetch_assoc()) {
            echo '<div class="forum-item">';
            echo '<div class="forum-header">';
            echo '<a href="topic.php?topic_id=' . $row['topic_id'] . '">' . htmlspecialchars($row['topic_subject']) . '</a>';
            echo '</div>';
            echo '<div class="forum-body">';
            if (!empty($row['topic_photo'])) {
                echo '<img src="uploads/' . htmlspecialchars($row['topic_photo']) . '" alt="' . htmlspecialchars($row['topic_subject']) . '">';
            }
            echo '<p>Created on: ' . htmlspecialchars($row['topic_date']) . '</p>';
            echo '</div>';
            echo '</div>';
        }
    }

    if ($resultCategories->num_rows === 0 && $resultTopics->num_rows === 0) {
        echo '<div class="no-results">No results found for "<strong>' . htmlspecialchars($searchQuery) . '</strong>".</div>';
    }
    ?>
</div>
</body>
</html>

<?php
include '../includes/footer.php';
?>
