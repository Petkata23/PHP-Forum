<?php
// includes/config.php

$host = 'localhost'; // или IP адрес на сървъра
$dbname = 'forum';   // Името на базата данни
$username = 'root';  // Потребителско име за достъп до базата данни
$password = '';      // Парола за достъп до базата данни
$port = 3307;        // Порт (ако е различен от стандартния 3307)

try {
    // Създаване на съединение с база данни чрез PDO
    $conn = new mysqli($host, $username, $password, $dbname, $port);

    // Проверка за грешки при свързване
    if ($conn->connect_error) {
        die('Connection failed: ' . $conn->connect_error);
    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
