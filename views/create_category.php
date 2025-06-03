<?php


// Include the database connection
include '../includes/config.php';
include '../includes/header.php';

// Initialize a message variable to store feedback
$message = "";

if (!isset($_SESSION['signed_in']) || !$_SESSION['signed_in']) {
    die("You must be signed in to create a topic.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the data from the form and sanitize inputs
    $cat_name = htmlspecialchars(trim($_POST['cat_name']));
    $cat_description = htmlspecialchars(trim($_POST['cat_description']));
    $cat_photo = null;

    // Handle the uploaded file
    if (isset($_FILES['cat_photo']) && $_FILES['cat_photo']['error'] == 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES['cat_photo']['name']);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if the file is an image
        $check = getimagesize($_FILES['cat_photo']['tmp_name']);
        if ($check === false) {
            $message = "Error: The uploaded file is not a valid image.";
        } elseif (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            $message = "Error: Only JPG, JPEG, PNG, and GIF files are allowed.";
        } elseif ($_FILES['cat_photo']['size'] > 5000000) { // Limit file size to 5MB
            $message = "Error: The file size exceeds the limit of 5MB.";
        } else {
            // Move the uploaded file to the target directory
            if (move_uploaded_file($_FILES['cat_photo']['tmp_name'], $target_file)) {
                $cat_photo = basename($_FILES['cat_photo']['name']);
            } else {
                $message = "Error: There was an error uploading the file.";
            }
        }
    }

    if (empty($message)) {
        // Check if category name already exists
        $check_sql = "SELECT COUNT(*) FROM categories WHERE cat_name = ?";
        $stmt = $conn->prepare($check_sql);
        $stmt->bind_param('s', $cat_name);
        $stmt->execute();
        $stmt->bind_result($category_exists);
        $stmt->fetch();
        $stmt->close();

        if ($category_exists > 0) {
            $message = "Error: A category with this name already exists.";
        } else {
            // Prepare the SQL statement to insert the category
            $sql = "INSERT INTO categories (cat_name, cat_description, cat_photo) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('sss', $cat_name, $cat_description, $cat_photo);
            $result = $stmt->execute();
            $stmt->close();

            // Provide feedback to the user
            if ($result) {
                $message = "New category successfully added!";
            } else {
                $message = "Error: Could not add the category.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <title>Създаване на категория</title>
    <link rel="stylesheet" href="../layout/css/createCategory.css">
</head>
<body>
    <div id="create-category-page">
        <div class="container">
            <h1>Създаване на категория</h1>

            <!-- Feedback message -->
            <?php if (!empty($message)): ?>
                <div class="message <?php echo strpos($message, 'Error') !== false ? 'error' : 'success'; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <!-- Form to create a new category -->
            <form method="post" action="" enctype="multipart/form-data">
                <label for="cat_name">Име на категория:</label>
                <input type="text" id="cat_name" name="cat_name" placeholder="Въведете име на категория" required />

                <label for="cat_description">Описание на категория:</label>
                <textarea id="cat_description" name="cat_description" placeholder="Въведете описание на категория" required></textarea>

                <label for="cat_photo">Снимка на категория:</label>
                <input type="file" id="cat_photo" name="cat_photo" accept="image/*" />

                <input type="submit" value="Създай категория" />
            </form>

            <a href="index.php">Назад към началната страница</a>
        </div>
    </div>
</body>
</html>

<?php
include '../includes/footer.php';
?>
