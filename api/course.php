<?php
require "../db.php";

header("Content-Type: application/json; charset=utf-8");
require __DIR__ . "/auth_jwt.php";
require_login();
if($_SERVER["REQUEST_METHOD"] !== "GET") {
    header("Allow: GET");
    http_response_code(405);
    echo json_encode(["error" => "Method not allowed"]);
    exit();
}
$id = filter_var($_GET["id"] ?? null, FILTER_VALIDATE_INT);
if ($id === false || $id < 1) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid id"]);
    exit();
}
$stmt = $pdo->prepare("SELECT id, code, title FROM courses WHERE id = :id");
$stmt->execute([":id" => $id]);
$course = $stmt->fetch();
if (!$course) {
    http_response_code(404);
    echo json_encode(["error" => "Course not found"]);
    exit();
}
echo json_encode(["course"=> $course], JSON_PRETTY_PRINT);