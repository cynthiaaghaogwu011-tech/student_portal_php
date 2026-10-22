<?php
require "admin.php";
require "db.php";

$stmt = $pdo->query(
    "SELECT courses.code, courses.title, users.name, users.email, enrollments.enrolled_at
    FROM enrollments
    JOIN users ON enrollments.user_id = users.id
    JOIN courses ON enrollments.course_id = courses.id
    ORDER BY courses.code, users.name"
);
$enrollments = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Enrollments</title>
</head>
<body>
    <div>
        <h1>Admin Enrollments</h1>
        <div>
            <a href="dashboard.php">Dashboard</a> <a href="course.php">Courses</a> <a href="logout.php">Logout</a>
        </div>
    </div>
    <?php if(count($enrollments) === 0): ?>
        <p>No enrollments yet.</p>
        <?php else: ?>
        <table border="2" cellpadding="12">
            <tr>
                <th>Code</th>
                <th>Title</th>
                <th>Name</th>
                <th>Email</th>
                <th>Enrolled At</th>
            </tr>
            <?php foreach($enrollments as $enrollment): ?>
                <tr>
                    <td><?= htmlspecialchars($enrollment["code"], ENT_QUOTES) ?></td>
                    <td><?= htmlspecialchars($enrollment["title"], ENT_QUOTES) ?></td>
                    <td><?=  htmlspecialchars($enrollment["name"], ENT_QUOTES) ?></td>
                    <td><?= htmlspecialchars($enrollment["email"], ENT_QUOTES)  ?></td>
                    <td><?= htmlspecialchars($enrollment["enrolled_at"], ENT_QUOTES) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</body>
</html>