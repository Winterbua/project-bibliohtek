<?php
    session_start();
    require('content/datenbankverbindung.php');

    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT personalNr, buchNr
            FROM users
            WHERE username = :username AND password = :password";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':username' => $username,
        ':password' => $password
    ]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $_SESSION['username']   = $user['username'];
        $_SESSION['personalNr'] = $user['personalNr'];
        $_SESSION['buchNr']     = $user['buchNr'];

        echo "Session set successfully";
    }

    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
</body>
</html>