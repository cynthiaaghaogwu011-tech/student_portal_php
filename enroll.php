<?php
require "auth.php";
require "db.php";
require "csrf.php";
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: course_available.php");
    exit();
}
csrf_check();
$userId = $_SESSION["user_id"];
$courseId = filter_var($_POST["course_id"] ?? "", FILTER_VALIDATE_INT);
if ($courseId === false || $courseId < 1) {
    header("Location: course_available.php");
    exit();
}
try {
    $stmt = $pdo->prepare("INSERT INTO enrollments (user_id, course_id) VALUES (:user_id, :course_id)");
    $stmt->execute([":user_id" => $userId, ":course_id" => $courseId]);
    header("Location: my_course.php");
    exit();
    } catch (PDOexception $e) {
        if ($e->getcode() === "23000") {
            header("Location: course_available.php");
            exit();
        } else {
            exit("Something went wrong.");
        }
    }