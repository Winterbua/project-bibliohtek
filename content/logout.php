<?php
// Session starten
session_start();

// Alle Session-Daten löschen
$_SESSION = [];

// Session komplett zerstören
session_destroy();

// Optional: Cookies für Session löschen (falls gesetzt)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Zur Login-Seite weiterleiten
header("Location: ../login.php");
exit;
