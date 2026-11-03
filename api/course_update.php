<?php
require "../db.php";
header("Content-Type: application/json; charset=utf-8");
require __DIR__ . "/auth_jwt.php";
require_admin();
if ($_SERVER["REQUEST_METHOD"] !== "PUT") {
    header("Allow: PUT");
    http_response_code(405);
    echo json_encode(["error"=> "Method not allowed"]);
    exit();
}
$id = filter_var($_GET["id"] ?? null, FILTER_VALIDATE_INT);
if ($id === false || $id < 1) {
    http_response_code(400);
    echo json_encode(["error"=> "Invalid id"]);
    exit();
}
$raw = file_get_contents("php://input");
$data = json_decode($raw, true);
if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid JSON body"]);
    exit();
}
$code = is_string($data["code"] ?? null) ? strtoupper(trim($data["code"])) : "";
$code = str_replace(" ", "", $code);
$title = is_string($data["title"] ?? null) ? trim($data["title"]) : "";
if ($code === "" || $title === "") {
    http_response_code(400);
    echo json_encode(["error" => "Code and title are required."]);
    exit();
}
if (strlen($title) > 150) {
    http_response_code(400);
    echo json_encode(["error" => "Title must be 150 characters or fewer."]);
    exit();
}
if (!preg_match('/^[A-Z]{2,4}\d{3}$/', $code)) {
    http_response_code(400);
    echo json_encode(["error" => "Code is not valid (example: CSC101)"]);
    exit();
}
$stmt = $pdo->prepare("SELECT id, code, title FROM courses WHERE id = :id");
$stmt->execute([":id" => $id]);
$course = $stmt->fetch();
if (!$course) {
    http_response_code(404);
    echo json_encode(["error" => "Course not found."]);
    exit();
}
try{
        $stmt = $pdo->prepare("UPDATE courses SET code = :code, title = :title WHERE id = :id");
        $stmt->execute([":code" => $code, ":title" => $title, ":id" => $id]);
    } catch (PDOException $e) {
        if($e->getCode() === "23000") {
            http_response_code(409);
            echo json_encode(["error"=> "Course code already exists"]);
        } else {
            http_response_code(500);
            echo json_encode(["error"=> "Something went wrong."]);
        }
        exit();
}

http_response_code(200);
echo json_encode(["course" => [
    "id" => $id,"code" => $code, "title" => $title
]], JSON_PRETTY_PRINT);