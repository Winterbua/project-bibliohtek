<?php
session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/content/datenbankverbindung.php';

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $personalNr = trim($_POST['personalNr']);
    $username   = trim($_POST['username']);
    $password   = $_POST['password'];

    if ($personalNr === "" || $username === "" || $password === "") {
        $error = "Bitte alle Felder ausfüllen";
    } else {
        // Passwort hashen
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        try {
            $stmt = $pdo->prepare(
                "INSERT INTO t_bibliothekar (personalNr, username, password)
                 VALUES (:personalNr, :username, :password)"
            );

            $stmt->execute([
                'personalNr' => $personalNr,
                'username'   => $username,
                'password'   => $passwordHash
            ]);

            $success = "Registrierung erfolgreich. Du kannst dich jetzt einloggen.";
        } catch (PDOException $e) {
            $error = "Personalnummer oder Benutzername existiert bereits";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Registrierung</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include 'content/navbar.php'; ?>

<div class="container mt-5" style="max-width: 450px;">
    <h2 class="mb-3">Registrierung</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="mb-3">
            <input type="text" name="personalNr" class="form-control"
                   placeholder="Personalnummer" required>
        </div>

        <div class="mb-3">
            <input type="text" name="username" class="form-control"
                   placeholder="Benutzername" required>
        </div>

        <div class="mb-3">
            <input type="password" name="password" class="form-control"
                   placeholder="Passwort" required>
        </div>

        <button type="submit" class="btn btn-success w-100">
            Registrieren
        </button>
    </form>
</div>

<?php include 'content/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
