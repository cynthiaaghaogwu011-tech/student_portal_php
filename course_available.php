<?php
require "auth.php";
require "db.php";

// Get all available courses.
$stmt = $pdo->prepare("SELECT * FROM courses");
$stmt->execute();
$courses = $stmt->fetchAll();

//Get  the current user's enrolled course IDs
$stmt = $pdo->prepare(
    "SELECT course_id FROM enrollments WHERE user_id = :user_id"
);
$stmt->execute([
    ":user_id" => $_SESSION["user_id"]
]);
$enrolledIds = $stmt->fetchAll(PDO::FETCH_COLUMN);  // FetchAll() retrieves all the rows returned byt the query. while PDO::FETCH_COLUMN tells PHP to retrieve only the values from a single column.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Courses</title>
</head>
<body>
    <h1>Available Courses</h1>
    <div>
        <a href="course.php">Courses</a> <a href="dashboard.php">Dashboard</a><br>
        <a href="logout.php">Logout</a>
    </div>
    <?php if (empty($courses)): ?>
        <p>No courses are available at the moment</p>
    <?php else: ?>
        <?php foreach ($courses as $course): ?>

            <div>
                <h2><?= htmlspecialchars($course["code"], ENT_QUOTES) ?> - <?= htmlspecialchars($course["title"], ENT_QUOTES) ?></h2>
                <?php if (in_array((int) $course["id"], $enrolledIds)): ?>
                    <p>Enrolled</p>
                <?php else: ?>
                    <form action="enroll.php" method="POST">
                        
                        <input type="hidden" name="course_id" value="<?= htmlspecialchars((string) $course["id"], ENT_QUOTES) ?>">
                        <button type="submit">Enroll</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>