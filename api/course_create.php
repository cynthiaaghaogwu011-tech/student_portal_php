<?php
require "../db.php";
header("Content-Type: application/json; charset=utf-8"); // This tells the client-end its json.
  require __DIR__ . "/auth_jwt.php";
  require_admin();
if ($_SERVER["REQUEST_METHOD"] !== "POST") { //Only POST is allowed.
    header("Allow: POST");
    http_response_code(405);
    echo json_encode(["error"=> "Method not allowed"]);
    exit();
}
//Read the raw body and turn the json text into php array.
$raw = file_get_contents("php://input"); // php://input means "raw body of this request(raw text)" and File_get_contents reads reads it into a string.
$data = json_decode($raw, true); // Json_decode returns an array. (it changes the raw text to php array.)

if(!is_array($data)) { // if the decoding failed the request is bad.
    http_response_code(400);
    echo json_encode(["error" => "Invalid JSON body"]);
    exit();
}
$code = is_string($data["code"] ?? null) ? trim(strtoupper($data["code"])) : "";
$code = str_replace(" ", "", $code);
$title = is_string($data["title"] ?? null) ? trim($data["title"]) : "";
//Validate
if ($code === "" || $title === "") {
    http_response_code(400);
    echo json_encode(["error" => "Code and title are required."]);
    exit();
}
if (!preg_match('/^[A-Z]{2,4}\d{3}$/', $code)) {
    http_response_code(400);
    echo json_encode(["error" => "Code is not valid (example: CSC101)"]);
    exit();
}
// Save to db.
try{
    $stmt = $pdo->prepare("INSERT INTO courses (code, title) VALUES (:code, :title)");
    $stmt->execute([":code" => $code, ":title" => $title]);
} catch (PDOException $e) {
    if ($e->getcode() === "23000") {
        http_response_code(409); // Conflict
        echo json_encode(["error" => "Course code already exists"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Something went wrong"]);
    }
    exit();
}
http_response_code(201); // Means Created (success)
echo json_encode([
    "course" => [
    "id" => (int) $pdo->lastInsertId(), // the table creates/inserts the id so the (int), $pdo->lastinsertId() gets the id of row inserted.
    "code" => $code,
    "title" => $title
    ]
]);