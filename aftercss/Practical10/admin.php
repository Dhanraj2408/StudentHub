
<?php
require_once "auth.php";
requireRole("admin");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>StudentHub</h1>
    <h2>Admin Dashboard</h2>

    <p>Welcome,
        <?php echo htmlspecialchars($_SESSION["username"], ENT_QUOTES, "UTF-8"); ?>!
    </p>
    <p>Your role: Admin</p>
    <p>You have access to the admin dashboard.</p>

    <a class="button-link" href="logout.php">Log Out</a>
</div>
</body>
</html>
