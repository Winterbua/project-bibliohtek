<?php
    session_start();
    header('Location: suchen.php');
?>
<!-- This Document is only for the Webserver to have a index.php file
     And for me to have a Template file where all the links and inlcudations are set
-->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="manuel mayr">
    <title>Document</title>
    <link href="style/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'content/navbar.php'; ?>
    <?php include 'content/datenbankverbindung.php'; ?>
    <div id="real_body">



    </div>
    <?php include 'content/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>