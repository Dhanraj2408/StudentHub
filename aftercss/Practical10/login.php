
<?php
require_once __DIR__ . "/auth.php";

if (!empty($_SESSION["user_id"])) {
    header(
        "Location: " .
        (($_SESSION["role"] ?? "") === "admin"
            ? "admin.php"
            : "dashboard.php")
    );
    exit;
}

$message = "";

if (isset($_GET["timeout"])) {
    $message = "Your session expired. Please log in again.";
} elseif (isset($_GET["logged_out"])) {
    $message = "You have logged out successfully.";
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $remember = isset($_POST["remember"]);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) ||
        $password === "") {
        $message = "Enter a valid email and password.";
    } else {
        $stmt = $conn->prepare(
            "SELECT id, username, password_hash, role
             FROM users WHERE email = ? LIMIT 1"
        );
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($id, $username, $hash, $role);
            $stmt->fetch();

            if (password_verify($password, $hash)) {
                session_regenerate_id(true);

                $_SESSION["user_id"] = $id;
                $_SESSION["username"] = $username;
                $_SESSION["role"] = $role;
                $_SESSION["last_activity"] = time();

                $update = $conn->prepare(
                    "UPDATE users SET last_login = NOW() WHERE id = ?"
                );
                $update->bind_param("i", $id);
                $update->execute();

                if ($remember) {
                    $selector = bin2hex(random_bytes(16));
                    $validator = bin2hex(random_bytes(32));
                    $tokenHash = hash("sha256", $validator);
                    $expires = time() + (30 * 86400);
                    $expiresSql = date("Y-m-d H:i:s", $expires);

                    $save = $conn->prepare(
                        "INSERT INTO remember_tokens
                         (user_id, selector, token_hash, expires_at)
                         VALUES (?, ?, ?, ?)"
                    );
                    $save->bind_param(
                        "isss",
                        $id,
                        $selector,
                        $tokenHash,
                        $expiresSql
                    );
                    $save->execute();

                    setRememberCookie(
                        $selector . ":" . $validator,
                        $expires
                    );
                }

                header(
                    "Location: " .
                    ($role === "admin" ? "admin.php" : "dashboard.php")
                );
                exit;
            }
        }

        $message = "Incorrect email or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>StudentHub Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>StudentHub</h1>
    <h2>Secure Login</h2>

    <?php if ($message !== ""): ?>
        <p class="message" role="alert">
            <?php echo htmlspecialchars($message, ENT_QUOTES, "UTF-8"); ?>
        </p>
    <?php endif; ?>

    <form method="POST" action="login.php">
        <label for="email">Email address</label>
        <input type="email" id="email" name="email"
               autocomplete="username" required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password"
               autocomplete="current-password" required>

        <label class="remember-label">
            <input type="checkbox" name="remember" value="1">
            Remember me on this device for 30 days
        </label>

        <button type="submit">Log In</button>
    </form>

    <p class="hint">
        Use an account created in Practical 9.
    </p>
</div>
</body>
</html>
