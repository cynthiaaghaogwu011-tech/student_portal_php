<?php
require "admin.php";
require "db.php";

$id = filter_var($_GET["id"] ?? "", FILTER_VALIDATE_INT);
if ($id === false || $id < 1) {
    header("Location: course.php");
    exit;
}

$stmt = $pdo->prepare("SELECT id, code, title FROM courses WHERE id = :id");
$stmt->execute([":id" => $id]);
$course = $stmt->fetch();

if ($course === false) {
    header("Location: course.php"); // Redirect.
    exit();
}

$errors = [];
$code = $course["code"] ?? "";
$title = $course["title"] ?? "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $code = trim(strtoupper($_POST["code"] ?? ""));
    $title = trim($_POST["title"] ?? "");

    if ($code === "") {
        $errors[] = "code is required.";
    } elseif (!preg_match('/^[A-Z]{2,4}\s?\d{3}$/', $code)) { //filter for code.
        $errors[] = "Code is not valid (example: CSC101).";
    }
    if ($title === "") {
        $errors[] = "Title is required.";
    } elseif (strlen($title) > 150) {
        $errors[] = "Title must be 150 characters or fewer.";
    }
    if (count($errors) === 0) {
        try {
            $stmt = $pdo->prepare("UPDATE courses SET code = :code, title = :title WHERE id = :id");
            $stmt->execute([":code" => $code, ":title" => $title, ":id" => $id
            ]);
            header("Location: course.php");
            exit;
        } catch (PDOException $e) {
            if ($e->getcode() === "23000") {
                $errors[] = "That course code already exists.";
            } else {
                $errors[] = "Something went wrong. Please try again.";
            }
        }
    } 
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Course</title>
</head>
<body>
    <h1>Edit Course</h1>

    <?php foreach ($errors as $error): ?>
        <p style="color: red"><?= htmlspecialchars($error, ENT_QUOTES) ?></p>
    <?php endforeach; ?>
    <div>
        <a href="course.php">Courses</a> <a href="dashboard.php">Dashboard</a>
    </div>
    <form method="post">
        <label for="id">Id:</label>
        <input type="text" name="id" id="id" value="<?= htmlspecialchars($code, ENT_QUOTES) ?>">
        <br>
        <label for="code">Code:</label>
        <input type="text" name="code" id="code" value="<?= htmlspecialchars($code, ENT_QUOTES) ?>">
        <br>
        <label for="title">Title:</label>
        <input type="text" name="title" id="title" value="<?= htmlspecialchars($title,ENT_QUOTES) ?>">
        <br>
        <button type="submit">Save</button>
    </form>
    <p><a href="course.php">Back to Course</a></p>
</body>
</html>