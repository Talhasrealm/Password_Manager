<?php
session_start();
require_once '../classes/User.php';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error = 'Please fill in all fields!';
    } else {
       $userObj = new User();
    try {
        if ($userObj->register($username, $password)) {
            $success = 'Account created! You can now login.';
        }
    } catch (PDOException $e) {
        $error = 'Username already exists! Please choose another.';
    }
    }
}   
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <style>
        body { font-family: Arial; max-width: 400px; margin: 80px auto; }
        input { width: 100%; padding: 8px; margin: 8px 0; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #4CAF50; color: white; border: none; cursor: pointer; }
        .error { color: red; }
        .success { color: green; }
    </style>
</head>
<body>
    <h2>Register</h2>

    <?php if ($error): ?>
        <p class="error"><?= $error ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p class="success"><?= $success ?></p>
    <?php endif; ?>

    <form method="POST">
        <input type="text"     name="username" placeholder="Choose a username" required>
        <input type="password" name="password" placeholder="Choose a password" required>
        <button type="submit">Register</button>
    </form>

    <br>
    <a href="login.php">Already have an account? Login here</a>
</body>
</html>