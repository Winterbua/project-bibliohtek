<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require 'content/datenbankverbindung.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare(
        "SELECT personalNr, password FROM t_bibliothekar WHERE username = :username"
    );
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['username'] = $username;
        $_SESSION['logged_in'] = true;

        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Benutzername oder Passwort falsch";
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link href="style/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include 'content/navbar.php'; ?>

<div class="container mt-5" style="max-width: 400px;">

    <h2 class="mb-3">Login</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" action="login.php">
        <div class="mb-3">
            <input type="text" name="username" class="form-control" placeholder="Benutzername" required>
        </div>

        <div class="mb-3">
            <input type="password" name="password" class="form-control" placeholder="Passwort" required>
        </div>

        <button type="submit" class="btn btn-primary w-100">Login</button>
    </form>

</div>

<?php include 'content/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
