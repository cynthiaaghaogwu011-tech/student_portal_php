<?php
require "auth.php";


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
</head>
<body>
    <h1>Dashboard</h1>
    <div>
        <p>
            Logged in as
            <?= htmlspecialchars($_SESSION["user_name"], ENT_QUOTES) ?>
            <?= htmlspecialchars($_SESSION["role"], ENT_QUOTES) ?>
            <a href="logout.php">Logout</a>
        </p>
        <?php if(($_SESSION["role"] ?? "") === "admin"): ?>
            <h1>Admin tools</h1>
            <p><a href="course.php">Manage Courses</a></p>
            <p><a href="admin_enrollments.php">Enrollments</a></p>
        <?php endif; ?>
    </div>
    <div>
        <h2>Student</h2>
        <a href="course.php">My Courses</a>
    </div>
</body>
</html>