<?php
require "auth.php";
require "db.php";

$stmt = $pdo->prepare(
    "SELECT courses.id, courses.code, courses.title
    FROM enrollments
    JOIN courses ON enrollments.course_id = courses.id
    WHERE enrollments.user_id = :user_id
    ORDER BY courses.code"
);
$stmt->execute([":user_id" => $_SESSION["user_id"]]);
$courses = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My courses</title>
</head>
<body>
    <h1>My Courses</h1>
    <?php if (count($courses) === 0): ?>
        <p>You havent enrolled in any courses yet<a href="course_available.php">Courses Available</a>.</p>
        <?php else: ?>
        <?php foreach ($courses as $course): ?>
            <div>
                <h2><?= htmlspecialchars($course["code"], ENT_QUOTES) ?> - <?= htmlspecialchars($course["title"], ENT_QUOTES) ?></h2>
                <form action="drop.php" method="POST" onsubmit="return confirm('Drop this course');">
                    <input type="hidden" name="course_id" value="<?= htmlspecialchars((string) $course["id"], ENT_QUOTES) ?>">
                    <button type="submit">Drop</button>
                </form>
            </div>
        <?php endforeach; ?>
    <?php endif; ?><br>
    <div>
        <a href="dashboard.php">Dashboard</a> 
        |<a href="course.php">Course</a> |
        <a href="logout.php">Logout</a>
    </div>
</body>
</html>