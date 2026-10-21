
<?php
// Practical 8: PDO Database Connection

$host = "localhost";
$port = "3306";
$dbname = "studenthub_db";
$username = "root";
$password = ""; // Common default for local XAMPP MySQL

$dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);
} catch (PDOException $e) {
    // Log the detailed error on the server, not in the browser.
    error_log("Database connection error: " . $e->getMessage());

    http_response_code(500);
    exit("Database connection failed. Check your XAMPP MySQL service and db.php settings.");
}
?>
