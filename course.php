<?php
require "admin.php";
require "db.php";
require "csrf.php";

$stmt = $pdo->query("SELECT code, title, id FROM courses  ORDER BY code ASC");
$courses = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Courses</title>
</head>
<body>
    <div>
        <?php if(count($courses) === 0): ?>
            <p>No courses found yet.</p>
        <?php else: ?>
        <div>
            <a href="dashboard.php">Dashboard</a>
        </div>
        <table border="1" cellpadding="12">
            <tr>
                <th>Code</th>
                <th>title</th>
                <th>Edit</th>
                <th>Delete</th>
            </tr>
            <?php foreach($courses as $course): ?>
                <tr>
                    <td><?= htmlspecialchars($course["code"], ENT_QUOTES) ?></td>
                    <td><?= htmlspecialchars($course["title"], ENT_QUOTES)?></td>
                    <td><a href="edit_courses.php?id=<?= (int) $course["id"] ?>">Edit </a></td>
                    <td>
                        <form method="post" action="delete_course.php" onsubmit="return confirm('Delete this course?');">
                            <input type="hidden" name="id" value="<?= (int) $course["id"] ?>">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES) ?>">
                            <button type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
        <p><a href="add_course.php">Add Course</a></p>
    </div>
</body>
</html>