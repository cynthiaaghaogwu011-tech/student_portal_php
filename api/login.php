<?php
require_once __DIR__ . "/../db.php"; // Loads it only the first time and if another file already loaded it, php skips it.
header("Content-Type: application/json; charset=utf-8");
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Allow: POST");
    http_response_code(405);
    echo json_encode(["error" => "Method not allowed"]);
    exit();
}
$raw = file_get_contents("php://input");
$data = json_decode($raw, true);
if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid Json body"]);
    exit();
}
$email =  $data["email"] ?? null;
$password = $data["password"] ?? null;
if (!is_string($email) || !is_string($password) || trim($email) === "" || $password === "") {
    http_response_code(400);
    echo json_encode(["error" => "Email and password are required"]);
    exit();
}
$email = trim($email);
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid email format"]);
    exit();
}
$stmt = $pdo->prepare("SELECT id, role, password_hash FROM users WHERE email = :email");
$stmt->execute([":email" => $email]);
$user = $stmt->fetch();
if ($user === false || !password_verify($password, $user["password_hash"])) {
    http_response_code(401);
    echo json_encode(["error" => "Invalid email or password"]);
    exit();
}
