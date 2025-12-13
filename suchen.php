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
    <title>Suchen</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php include 'content/navbar.php'; ?>

<div class="container py-5">

    <div class="card shadow-sm mb-4">
        <div class="card-body">

            <h4 class="card-title mb-4">
                <i class="bi bi-search"></i> Datenbanksuche
            </h4>

            <form action="suchen.php" method="post">
                <div class="row g-3 align-items-end">

                    <div class="col-md-6">
                        <label class="form-label">Suchbegriff</label>
                        <input
                            type="text"
                            name="suchbegriff"
                            class="form-control"
                            placeholder="Suchbegriff eingeben..."
                            value="<?= htmlspecialchars($search_term); ?>">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Kategorie</label>
                        <select name="spalten" class="form-select">
                            <option value="Titel"     <?= $selected_value == 'Titel' ? 'selected' : '' ?>>Titel</option>
                            <option value="ISBN"      <?= $selected_value == 'ISBN' ? 'selected' : '' ?>>ISBN</option>
                            <option value="Verlag"    <?= $selected_value == 'Verlag' ? 'selected' : '' ?>>Verlag</option>
                            <option value="Kategorie" <?= $selected_value == 'Kategorie' ? 'selected' : '' ?>>Kategorie</option>
                            <option value="Author"    <?= $selected_value == 'Author' ? 'selected' : '' ?>>Autor</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search"></i> Suchen
                        </button>
                    </div>

                </div>
            </form>

        </div>
    </div>

    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($search_term)): ?>
        <div class="card shadow-sm">
            <div class="card-body">
                <?php include 'content/suchfunktion.php'; ?>
            </div>
        </div>
    <?php endif; ?>

    
</div>

<?php include 'content/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
