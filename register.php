<?php
require "db.php";
require "csrf.php";
$errors = [];
$success = false;
$name = "";
$email = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    csrf_check();
    $name = trim($_POST["fullname"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($name === "") {
        $errors[] = "Name is required.";
    }
    if ($email === "") {
        $errors[] = "Email is required.";
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email is not valid.";
    }
    if ($password === "") {
       $errors[] = "Password is required.";
    } elseif(strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters.";
    }
    if (count($errors) === 0) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        try {
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash) VALUES(:name, :email, :password_hash)");
            $stmt->execute([":name" => $name, ":email" => $email, ":password_hash" => $hash]);
            $success = true;
            $name = "";
            $email = "";
        } catch (PDOException $e) {
            if ($e->getcode() === "23000") {
                $errors[] = "That email is already registered.";
            }else {
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
    <title>Create Account</title>
</head>
<body>
    <h1>Create Account</h1>
    <?php if($success): ?>
        <p style="color: green;">Account created successfully.</p>
        <p><a href="login.php">Log in now</a></p>
    <?php endif; ?>

    <?php foreach ($errors as $error): ?>
        <p style="color: red;"><?= $error ?></p>
    <?php endforeach; ?>

    <form method="post" novalidate>
        <label for="name">Name:</label>
        <input type="text" id="name" name="fullname" value="<?= htmlspecialchars($name, ENT_QUOTES) ?>">
        <br>
        <label for="email">Email:</label>
        <input type="text" id="email" name="email" value="<?= htmlspecialchars($email, ENT_QUOTES) ?>">
        <br>
        <label for="password">Password:</label>
        <input type="text" id="password" name="password">
        <br>
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES) ?>">
        <button type="submit">Create account</button>
    </form>
    <p>Already have an account? <a href="login.php">Log in</a></p>
</body>
</html>