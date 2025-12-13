<?php
session_start();
include 'content/datenbankverbindung.php';

// Initialize variables for remembering selections
$selected_value = $_POST['spalten'] ?? '';
$search_term    = $_POST['suchbegriff'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="manuel mayr">
    <link href="style/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Suchen</title>
</head>
<body>
    <?php include 'content/navbar.php' ?>
    <div id="real_body">
        <h1>Suchfunktion Datenbank</h1>

        <form action="suchen.php" method="post" class="p-3">
            <div class="row g-3 align-items-center">
                <div class="col-md-6">
                    <input
                        type="text"
                        name="suchbegriff"
                        id="suchbegriff"
                        class="form-control"
                        placeholder="Hier Suchbegriff eingeben..."
                        value="<?php echo htmlspecialchars($search_term); ?>">
                </div>
                <div class="col-md-3">
                    <select name="spalten" id="spalten" class="form-select">
                        <option value="Titel"     <?php if($selected_value == 'Titel') echo 'selected'; ?>>Titel</option>
                        <option value="ISBN"      <?php if($selected_value == 'ISBN') echo 'selected'; ?>>ISBN</option>
                        <option value="Verlag"    <?php if($selected_value == 'Verlag') echo 'selected'; ?>>Verlag</option>
                        <option value="Kategorie" <?php if($selected_value == 'Kategorie') echo 'selected'; ?>>Kategorie</option>
                        <option value="Author"    <?php if($selected_value == 'Author') echo 'selected'; ?>>Author</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">Suchen</button>
                </div>
            </div>
        </form>

        <?php include 'content/suchfunktion.php'; ?>

    </div>
    <?php include 'content/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
