<?php
require "auth.php";
require "db.php";
require "csrf.php";
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: my_course.php");
    exit();
}
csrf_check();
$userId = $_SESSION["user_id"];
$courseId = filter_var($_POST["course_id"] ?? "", FILTER_VALIDATE_INT);
if ($courseId === false || $courseId < 1) {
    header("Location: my_course.php");
    exit();
}
$stmt = $pdo->prepare("DELETE FROM enrollments WHERE user_id = :user_id AND course_id = :course_id");
$stmt->execute([":user_id" => $userId, ":course_id" => $courseId]);
header("Location: my_course.php");
exit();