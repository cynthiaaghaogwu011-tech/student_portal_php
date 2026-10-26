<?php
session_start();
require "db.php";
require "csrf.php";
$error = "";
$email = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    csrf_check();
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $stmt = $pdo->prepare("SELECT id, name, password_hash, role FROM users WHERE email = :email");
    $stmt->execute([":email" => $email]);
    $user = $stmt->fetch();
    
    if ($user !== false && password_verify($password, $user["password_hash"])) {
    session_regenerate_id(true);
    $_SESSION["user_id"] = $user["id"];
    $_SESSION["user_name"] = $user["name"];
    $_SESSION["role"] = $user["role"];
    header("Location: dashboard.php");
    exit;
    }
    $error = "Invalid email or password.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log in</title>
</head>
<body>
    <h1>Log in</h1>

    <?php if ($error !== ""): ?>
        <p style="color: red;"><?= $error ?></p>
    <?php endif; ?>

    <form method="post" novalidate>
        <label for="name">Email:</label>
        <input type="text" id="name" name="email" value="<?= htmlspecialchars($email, ENT_QUOTES) ?>">
        <br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password">
        <br>
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES) ?>">
        <button type="submit">log in</button>
    </form>
</body>
</html>