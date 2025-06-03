<?php
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function sanitizeInput($data) {
    $data = trim($data); // Премахва празните интервали от началото и края
    $data = stripslashes($data); // Премахва бекслешове (ако има такива)
    $data = htmlspecialchars($data); // Превръща специални символи в HTML ентитети (например < -> &lt;)
    return $data;
}


function checkLogin($username, $password) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM users WHERE user_name = :username');
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    return password_verify($password, $user['user_pass']);
}
?>
