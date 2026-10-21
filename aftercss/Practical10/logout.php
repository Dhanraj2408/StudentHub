
<?php
require_once __DIR__ . "/auth.php";

if (!empty($_SESSION["user_id"])) {
    $userId = (int) $_SESSION["user_id"];

    // Remove remember-me tokens belonging to this user.
    // This logs out remembered sessions on other devices too.
    $delete = $conn->prepare(
        "DELETE FROM remember_tokens WHERE user_id = ?"
    );
    $delete->bind_param("i", $userId);
    $delete->execute();
}

clearRememberCookie();

$_SESSION = [];

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();

    setcookie(session_name(), "", [
        "expires" => time() - 3600,
        "path" => $params["path"],
        "secure" => $params["secure"],
        "httponly" => $params["httponly"],
        "samesite" => "Lax"
    ]);
}

session_destroy();

header("Location: login.php?logged_out=1");
exit;
?>
