<?php
include '../includes/config.php';
include '../includes/header.php';
?>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <title>Форум</title>
    <link rel="stylesheet" href="../layout/css/index.css">
</head>
<body>
    <div class="container">
        <h2>Forum Categories</h2>

        <?php
        // Вземане на категориите и последната тема от базата данни
        $sql = "
            SELECT 
                c.cat_id, 
                c.cat_name, 
                c.cat_description, 
                c.cat_photo, 
                t.topic_id, 
                t.topic_subject, 
                t.topic_date 
            FROM 
                categories c
            LEFT JOIN 
                topics t 
            ON 
                c.cat_id = t.topic_cat 
            GROUP BY 
                c.cat_id
            ORDER BY 
                c.cat_id ASC";

        $result = mysqli_query($conn, $sql);

        if (!$result) {
            echo '<p>Error: Could not fetch categories. Please try again later.</p>';
        } else {
            if (mysqli_num_rows($result) == 0) {
                echo '<p>No categories defined yet.</p>';
            } else {
                echo '<table>
                        <tr>
                            <th>Category</th>
                            <th>Last Topic</th>
                        </tr>';
                
                while ($row = mysqli_fetch_assoc($result)) {
                    echo '<tr>';
                        // Колона за категория
                        echo '<td class="leftpart">';
                            if (!empty($row['cat_photo'])) {
                                echo '<img src="uploads/' . htmlspecialchars($row['cat_photo']) . '" alt="' . htmlspecialchars($row['cat_name']) . '">';
                            } else {
                                echo '<img src="uploads/default.png" alt="Default category image">';
                            }
                            echo '<div>';
                                echo '<h3><a href="category.php?id=' . $row['cat_id'] . '">' . htmlspecialchars($row['cat_name']) . '</a></h3>';
                                echo '<p>' . htmlspecialchars($row['cat_description']) . '</p>';
                            echo '</div>';
                        echo '</td>';

                        // Колона за последната тема
                        echo '<td class="rightpart">';
                        if ($row['topic_id']) {
                            echo '<a href="topic.php?id=' . $row['topic_id'] . '">' . htmlspecialchars($row['topic_subject']) . '</a><br>';
                            echo '<small>Posted on ' . date('F j, Y, g:i a', strtotime($row['topic_date'])) . '</small>';
                        } else {
                            echo '<small>No topics yet.</small>';
                        }
                        echo '</td>';
                    echo '</tr>';
                }

                echo '</table>';
            }
        }
        ?>
    </div>
</body>
</html>

<?php
include '../includes/footer.php';
?>
