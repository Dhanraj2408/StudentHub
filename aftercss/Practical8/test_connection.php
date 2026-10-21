
<?php
require_once __DIR__ . "/db.php";

try {
    $stmt = $pdo->query("SELECT COUNT(*) AS total FROM students");
    $result = $stmt->fetch();

    $studentCount = (int)$result["total"];
    $message = "Database connection successful!";
} catch (PDOException $e) {
    error_log("Database test error: " . $e->getMessage());

    http_response_code(500);
    $message = "Connected, but the database query failed. Check the database tables.";
    $studentCount = null;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudentHub Database Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            margin: 0;
            padding: 30px;
        }

        .box {
            max-width: 600px;
            margin: 30px auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 3px 12px #00000015;
        }

        h1 {
            color: #1461b3;
        }

        .success {
            color: #16743b;
            background: #e4f7e9;
            padding: 12px;
            border-radius: 6px;
        }

        a {
            color: #1461b3;
        }
    </style>
</head>
<body>
    <main class="box">
        <h1>StudentHub Database Test</h1>

        <?php if ($studentCount !== null): ?>
            <p class="success">
                <?= htmlspecialchars($message, ENT_QUOTES, "UTF-8") ?>
            </p>

            <p>Database: <strong>studenthub_db</strong></p>
            <p>Students found: <strong><?= $studentCount ?></strong></p>
            <p>PDO connection and database query are working.</p>
        <?php else: ?>
            <p>
                <?= htmlspecialchars($message, ENT_QUOTES, "UTF-8") ?>
            </p>
        <?php endif; ?>

        <p><a href="students.php">View students</a></p>
    </main>
</body>
</html>
