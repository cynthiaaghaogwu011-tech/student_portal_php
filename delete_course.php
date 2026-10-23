<?php
require "admin.php";
require "db.php";
require "csrf.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: course.php");
    exit;
}
csrf_check();
$id = filter_var($_POST["id"] ?? "", FILTER_VALIDATE_INT);
if ($id === false || $id < 1) {
    header("Location: course.php");
    exit();
}
$stmt = $pdo->prepare("DELETE FROM courses WHERE id = :id");
$stmt->execute([":id" => $id]);

header("Location: course.php");
exit;