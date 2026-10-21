
<?php
require_once "auth.php";
requireRole("student");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>StudentHub</h1>
    <h2>Student Dashboard</h2>

    <p>Welcome,
        <?php echo htmlspecialchars($_SESSION["username"], ENT_QUOTES, "UTF-8"); ?>!
    </p>
    <p>Your role: Student</p>
    <p>Your account is logged in securely.</p>

    <a class="button-link" href="logout.php">Log Out</a>
</div>
</body>
</html>
