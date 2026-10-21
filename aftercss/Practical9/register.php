
<?php
session_start();
require_once "db.php";

if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

$message = "";
$messageType = "";
$oldUsername = "";
$oldEmail = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $oldUsername = trim($_POST["username"] ?? "");
    $oldEmail = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirmPassword = $_POST["confirm_password"] ?? "";
    $csrfToken = $_POST["csrf_token"] ?? "";

    if (!hash_equals($_SESSION["csrf_token"], $csrfToken)) {
        $message = "Invalid form request. Please refresh and try again.";
    } elseif (
        !preg_match('/^[a-zA-Z0-9_]{3,30}$/', $oldUsername)
    ) {
        $message = "Username must be 3–30 characters (letters, numbers or underscore).";
    } elseif (
        !filter_var($oldEmail, FILTER_VALIDATE_EMAIL) ||
        strlen($oldEmail) > 100
    ) {
        $message = "Please enter a valid email address.";
    } elseif (
        strlen($password) < 8 ||
        !preg_match('/[A-Za-z]/', $password) ||
        !preg_match('/[0-9]/', $password)
    ) {
        $message = "Password must be at least 8 characters and contain a letter and a number.";
    } elseif ($password !== $confirmPassword) {
        $message = "Passwords do not match.";
    } else {
        try {
            // Check whether the username or email already exists.
            $check = $conn->prepare(
                "SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1"
            );
            $check->bind_param("ss", $oldUsername, $oldEmail);
            $check->execute();
            $check->store_result();

            if ($check->num_rows > 0) {
                $message = "Username or email already exists. Please use different details.";
            } else {
                // Hash the password before saving it.
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                $insert = $conn->prepare(
                    "INSERT INTO users (username, email, password_hash)
                     VALUES (?, ?, ?)"
                );
                $insert->bind_param(
                    "sss",
                    $oldUsername,
                    $oldEmail,
                    $hashedPassword
                );
                $insert->execute();

                $message = "Registration successful!";
                $messageType = "success";
                $oldUsername = "";
                $oldEmail = "";

                // Generate a fresh token after successful registration.
                $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
            }
        } catch (mysqli_sql_exception $e) {
            // Log technical details privately; do not display them to users.
            error_log($e->getMessage());

            if ((int)$e->getCode() === 1062) {
                $message = "Username or email already exists. Please use different details.";
            } else {
                $message = "Registration could not be completed. Please try again.";
            }
        }
    }

    if ($messageType !== "success" && $message !== "") {
        $messageType = "error";
    }
}

function e($value) {
    return htmlspecialchars($value, ENT_QUOTES, "UTF-8");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudentHub - Secure Registration</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="register-container">
    <h1>StudentHub</h1>
    <h2>Create Your Account</h2>
    <p class="subtitle">Register to get started</p>

    <?php if ($message !== ""): ?>
        <div class="message <?php echo e($messageType); ?>" role="status">
            <?php echo e($message); ?>
        </div>
    <?php endif; ?>

    <form id="registerForm" method="POST" action="register.php">
        <input
            type="hidden"
            name="csrf_token"
            value="<?php echo e($_SESSION["csrf_token"]); ?>"
        >

        <label for="username">Username</label>
        <input
            type="text"
            id="username"
            name="username"
            minlength="3"
            maxlength="30"
            pattern="[A-Za-z0-9_]{3,30}"
            title="Use 3–30 letters, numbers or underscores."
            value="<?php echo e($oldUsername); ?>"
            autocomplete="username"
            required
        >

        <label for="email">Email Address</label>
        <input
            type="email"
            id="email"
            name="email"
            maxlength="100"
            value="<?php echo e($oldEmail); ?>"
            autocomplete="email"
            required
        >

        <label for="password">Password</label>
        <input
            type="password"
            id="password"
            name="password"
            minlength="8"
            autocomplete="new-password"
            required
        >
        <small>At least 8 characters, including a letter and a number.</small>

        <label for="confirm_password">Confirm Password</label>
        <input
            type="password"
            id="confirm_password"
            name="confirm_password"
            minlength="8"
            autocomplete="new-password"
            required
        >
        <small id="passwordMessage" aria-live="polite"></small>

        <button type="submit">Register</button>
    </form>

    <p class="footer-text">StudentHub Secure Registration</p>
</div>

<script>
const form = document.getElementById("registerForm");
const password = document.getElementById("password");
const confirmPassword = document.getElementById("confirm_password");
const passwordMessage = document.getElementById("passwordMessage");

function checkPasswords() {
    if (confirmPassword.value === "") {
        confirmPassword.setCustomValidity("");
        passwordMessage.textContent = "";
        return;
    }

    if (password.value !== confirmPassword.value) {
        confirmPassword.setCustomValidity("Passwords do not match.");
        passwordMessage.textContent = "Passwords do not match.";
    } else {
        confirmPassword.setCustomValidity("");
        passwordMessage.textContent = "Passwords match.";
    }
}

password.addEventListener("input", checkPasswords);
confirmPassword.addEventListener("input", checkPasswords);

form.addEventListener("submit", function(event) {
    checkPasswords();

    if (!form.checkValidity()) {
        event.preventDefault();
        form.reportValidity();
    }
});
</script>

</body>
</html>
