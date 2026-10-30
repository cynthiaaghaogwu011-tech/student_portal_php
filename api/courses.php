<?php
require __DIR__ . "/../db.php";
header( "Content-Type: application/json; charset=utf-8");
if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    header("Allow: GET");
    http_response_code(405);
    echo json_encode(["error" => "Method not allowed"]);
    exit();
}
$stmt = $pdo->query("SELECT id, code, title FROM courses ORDER BY code");
$courses = $stmt->fetchAll();

echo json_encode(["courses" => $courses], JSON_PRETTY_PRINT);  // Json encode returns json as string but doesnt print it so echo must be added b4 it.