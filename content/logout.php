<!--
    Dieses Script ist da, um die Session in der die Anmeldedaten gespeichert sind
    zu zerstören und somit die Verwaltungs / Dashboard Seite zu blockieren
-->

<?php
// Session starten
session_start();

// Alle Session-Daten löschen
$_SESSION = [];

// Session komplett zerstören
session_destroy();

// Zur Login-Seite weiterleiten
header("Location: ../login.php");
exit;
