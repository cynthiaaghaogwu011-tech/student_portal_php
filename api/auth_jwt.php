<?php
require_once __DIR__ . "/../db.php";
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function require_login(): array
{
    $header = $_SERVER["HTTP_AUTHORIZATION"]
    ?? $_SERVER["REDIRECT_HTTP_AUTHORIZATION"]
    ?? "";
if ($header === "" && function_exists("getallheaders")) {
    $h = array_change_key_case(getallheaders(), CASE_LOWER);
    $header = $h["authorization"] ?? "";
}
    if (!preg_match('/^Bearer\s+(\S+)$/', $header, $m)) {
        http_response_code(401);
        echo json_encode(["error" => "Missing token"]);
        exit();
    }
    try {
        $decoded = JWT::decode($m[1], new Key($_ENV["JWT_SECRET"], "HS256"));
    } catch (Throwable $e) {
        http_response_code(401);
        echo json_encode(["error" => "Invalid or expired token"]);
        exit();
    }
    return (array) $decoded;
}

function require_admin(): array
{
    $user = require_login();
    if (($user["role"] ?? "") !== "admin") {
        http_response_code(403);
        echo json_encode(["error" => "Admins only"]);
        exit();
    }
    return $user;
}