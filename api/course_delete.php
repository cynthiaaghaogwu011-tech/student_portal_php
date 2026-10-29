<?php
require "../db.php";
header("Content-Type: application/json; charset=utf-8");
if ($_SERVER["REQUEST_METHOD"] !== "DELETE") {
    header("Allow: DELETE");
    http_response_code(405);
    echo json_encode(["error"=> "Method not allowed."]);
    exit();
}

$id = filter_var($_GET["id"] ?? null, FILTER_VALIDATE_INT);
if ($id === false || $id < 1) {
    http_response_code(400);
    echo json_encode(["error"=> "Invalid id"]);
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
try {
    $stmt = $pdo->prepare("DELETE FROM courses WHERE id = :id");
    $stmt->execute([":id" => $id]);
    $deleted = $stmt->rowCount();
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "something went wrong"]);
    error_log($e->getMessage());
    exit();
}
if ($deleted === 0) {
    http_response_code(404);
    echo json_encode(["error" => "Course not found"]);
    exit();
}

http_response_code(200);
echo json_encode(["course" => $course]);