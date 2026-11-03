<?php
require "admin.php";
require "db.php";
require "csrf.php";
$errors = [];
$success = "";
$code = "";
$title = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    csrf_check();
    $code = strtoupper(trim($_POST["code"] ?? ""));
$code = str_replace(" ", "", $code);
$title = trim($_POST["title"] ?? "");

    if($code === "") {
        $errors[] = "Code is required.";
    } elseif (!preg_match('/^[A-Z]{2,4}\s?\d{3}$/', $code)) { //filter for code.
        $errors[] = "Code is not valid (example: CSC101).";
    }
    if($title === "") {
        $errors[] = "Title is required.";
    } elseif (strlen($title) > 150) {
        $errors[] = "Title must be 150 characters or fewer.";
    }

    if (count($errors) === 0) {
        try{
            $stmt = $pdo->prepare("INSERT INTO courses (code, title) VALUES (:code, :title)");
            $stmt->execute([":code" => $code, ":title" => $title]);
            $success = true;
            $code = "";
            $title = "";
        } catch (PDOException $e) {
            if ($e->getCode() === "23000") {
                $errors[] = "That code is already registered.";
            } else{
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
    <title>Add Course</title>
</head>
<body>
    <h1>Add Course</h1>

    <?php if($success): ?>
        <p style="color: green;">Course added successfully.</p>
    <?php endif; ?>
    <?php foreach ($errors as $error): ?>
        <p style="color: red"><?= htmlspecialchars($error, ENT_QUOTES) ?></p>
    <?php endforeach; ?>
    <div>
        <a href="course.php">Courses</a> <a href="dashboard.php">Dashboard</a>
    </div>
    <form method="post" novalidate>
        <label for="code">Code</label>
        <input type="text" name="code" id="code" value="<?= htmlspecialchars($code, ENT_QUOTES) ?>">
        <br>
        <label for="title">Title</label>
        <input type="text" name="title" id="title" value="<?= htmlspecialchars($title,ENT_QUOTES) ?>">
        <br>
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES) ?>">
        <button type="submit">Save</button>
    </form>
</body>
</html>