<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../classes/PasswordEntry.php';
require_once '../classes/PasswordGenerator.php';
$entryObj = new PasswordEntry();
$message = '';
$generated = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate'])) {
    $gen = new PasswordGenerator();
    $generated = $gen->generate(
        (int)$_POST['length'],
        (int)$_POST['upper'],
        (int)$_POST['lower'],
        (int)$_POST['numbers'],
        (int)$_POST['special']
    );
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_password'])) {
    $entryObj->save(
        $_SESSION['user_id'],
        trim($_POST['site_name']),
        trim($_POST['site_password']),
        $_SESSION['enc_key']
    );
    $message = 'Password saved!';
}

$entries = $entryObj->getAll($_SESSION['user_id'], $_SESSION['enc_key']);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <style>
        body { font-family: Arial; max-width: 700px; margin: 40px auto; padding: 0 20px; }
        h2 { color: #333; }
        input[type=text], input[type=password] {
            padding: 8px; margin: 6px 0; width: 100%; box-sizing: border-box;
        }
        button {
            padding: 10px 20px; background: #4CAF50;
            color: white; border: none; cursor: pointer; margin-top: 6px;
        }
        .logout { float: right; background: #f44336; text-decoration: none;
                  color: white; padding: 8px 14px; border-radius: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background: #f2f2f2; }
        .success { color: green; }
    </style>
</head>
<body>

    <a href="logout.php" class="logout">Logout</a>
    <h2>Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!</h2>

    <?php if ($message): ?>
        <p class="success"><?= $message ?></p>
    <?php endif; ?>

    <h3>Generate a Password</h3>
    <form method="POST">
        <table>
            <tr>
                <td>Total length:</td>
                <td><input type="number" name="length"  value="9" min="4" max="32" style="width:60px"></td>
            </tr>
            <tr>
                <td>Uppercase letters:</td>
                <td><input type="number" name="upper"   value="2" min="0" style="width:60px"></td>
            </tr>
            <tr>
                <td>Lowercase letters:</td>
                <td><input type="number" name="lower"   value="2" min="0" style="width:60px"></td>
            </tr>
            <tr>
                <td>Numbers:</td>
                <td><input type="number" name="numbers" value="2" min="0" style="width:60px"></td>
            </tr>
            <tr>
                <td>Special charxacters:</td>
                <td><input type="number" name="special" value="2" min="0" style="width:60px"></td>
            </tr>
        </table>
        <button name="generate" type="submit">Generate</button>
    </form>

    <?php if ($generated): ?>
        <p>Generated password: <strong style="color: #2196F3; font-size: 1.2em;"><?= htmlspecialchars($generated) ?></strong></p>
        <p style="color: gray; font-size: 0.9em;">Copy it and paste it into the save form below!</p>
    <?php endif; ?>

    <h3>Save a New Password</h3>
    <form method="POST">
        <input type="text"     name="site_name"     placeholder="Site name (e.g. Gmail, Facebook)" required>
        <input type="text"     name="site_password" placeholder="Password for that site" required>
        <button name="save_password" type="submit">Save Password</button>
    </form>

    <h3>Your Saved Passwords</h3>
    <?php if (empty($entries)): ?>
        <p>No passwords saved yet.</p>
    <?php else: ?>
        <table>
            <tr>
                <th>Site</th>
                <th>Password</th>
                <th>Saved At</th>
            </tr>
            <?php foreach ($entries as $e): ?>
            <tr>
                <td><?= htmlspecialchars($e['site_name']) ?></td>
                <td><?= htmlspecialchars($e['plain_password']) ?></td>
                <td><?= $e['created_at'] ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

</body>
</html>