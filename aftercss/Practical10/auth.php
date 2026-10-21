
<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    ini_set("session.use_strict_mode", "1");
    ini_set("session.use_only_cookies", "1");

    session_set_cookie_params([
        "lifetime" => 0,
        "path" => "/",
        "secure" => !empty($_SERVER["HTTPS"]) &&
                    $_SERVER["HTTPS"] !== "off",
        "httponly" => true,
        "samesite" => "Lax"
    ]);

    session_start();
}

require_once __DIR__ . "/db.php";

const REMEMBER_COOKIE = "studenthub_remember";
const REMEMBER_DAYS = 30;

function setRememberCookie($value, $expires) {
    setcookie(REMEMBER_COOKIE, $value, [
        "expires" => $expires,
        "path" => "/practical10",
        "secure" => !empty($_SERVER["HTTPS"]) &&
                    $_SERVER["HTTPS"] !== "off",
        "httponly" => true,
        "samesite" => "Lax"
    ]);
}

function clearRememberCookie() {
    setRememberCookie("", time() - 3600);
}

function restoreRememberedLogin() {
    global $conn;

    if (!empty($_SESSION["user_id"])) {
        return true;
    }

    $cookie = $_COOKIE[REMEMBER_COOKIE] ?? "";

    if (!preg_match('/^([a-f0-9]{32}):([a-f0-9]{64})$/', $cookie, $m)) {
        return false;
    }

    $selector = $m[1];
    $validator = $m[2];

    $stmt = $conn->prepare(
        "SELECT r.user_id, r.token_hash, u.username, u.role
         FROM remember_tokens r
         JOIN users u ON u.id = r.user_id
         WHERE r.selector = ? AND r.expires_at > NOW()
         LIMIT 1"
    );

    $stmt->bind_param("s", $selector);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows !== 1) {
        clearRememberCookie();
        return false;
    }

    $stmt->bind_result($userId, $storedHash, $username, $role);
    $stmt->fetch();

    if (!hash_equals($storedHash, hash("sha256", $validator))) {
        // Revoke the suspicious/invalid token.
        $delete = $conn->prepare(
            "DELETE FROM remember_tokens WHERE selector = ?"
        );
        $delete->bind_param("s", $selector);
        $delete->execute();

        clearRememberCookie();
        return false;
    }

    // Rotate the token after successful use.
    $newSelector = bin2hex(random_bytes(16));
    $newValidator = bin2hex(random_bytes(32));
    $newHash = hash("sha256", $newValidator);
    $expires = time() + (REMEMBER_DAYS * 86400);
    $expiresSql = date("Y-m-d H:i:s", $expires);

    session_regenerate_id(true);

    $_SESSION["user_id"] = $userId;
    $_SESSION["username"] = $username;
    $_SESSION["role"] = $role;
    $_SESSION["last_activity"] = time();

    $update = $conn->prepare(
        "UPDATE remember_tokens
         SET selector = ?, token_hash = ?, expires_at = ?
         WHERE selector = ?"
    );
    $update->bind_param(
        "ssss",
        $newSelector,
        $newHash,
        $expiresSql,
        $selector
    );
    $update->execute();

    if ($update->affected_rows !== 1) {
        $_SESSION = [];
        session_regenerate_id(true);
        clearRememberCookie();
        return false;
    }

    setRememberCookie(
        $newSelector . ":" . $newValidator,
        $expires
    );

    return true;
}

function requireLogin() {
    $timeout = 300; // 5 minutes of inactivity

    if (empty($_SESSION["user_id"])) {
        if (restoreRememberedLogin()) {
            return;
        }

        header("Location: login.php");
        exit;
    }

    if (
        isset($_SESSION["last_activity"]) &&
        time() - $_SESSION["last_activity"] > $timeout
    ) {
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

        // A Remember Me token can restore the login if still valid.
        header("Location: login.php?timeout=1");
        exit;
    }

    $_SESSION["last_activity"] = time();
}

function requireRole($role) {
    requireLogin();

    if (($_SESSION["role"] ?? "") !== $role) {
        http_response_code(403);
        exit("Access denied: You do not have permission to view this page.");
    }
}
?>
