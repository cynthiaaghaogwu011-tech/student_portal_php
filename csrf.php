<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
function csrf_token(): string
{
    if (empty($_SESSION["csrf_token"])) {
        $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
    }
    return $_SESSION["csrf_token"];
}

function csrf_check(): void
{
    $sent = $_POST["csrf_token"] ?? ""; // Reads what is sent.
    $real = $_SESSION["csrf_token"] ?? ""; // Reads what session holds.

    if (!is_string($sent) || $real === "" || !hash_equals($real, $sent)) { // The !hash checks if the 2 blanks (token($sent, $real)) match and rejects if it doesnt.
        http_response_code(403);
        exit("Invalid request.");
    }
}