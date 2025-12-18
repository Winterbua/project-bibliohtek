<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require 'content/datenbankverbindung.php';

// Login-Schutz
if (empty($_SESSION['logged_in'])) {
    header("Location: login.php");
    exit;
}

$error = '';
$success = '';

// -------------------- ADD BOOK --------------------
if (isset($_POST['add'])) {
    $stmt = $pdo->prepare("
        INSERT INTO t_buecher
        (ISBN, Titel, Author, Verlag, Kategorie, Beschreibung, Anschaffungskosten)
        VALUES (:isbn, :titel, :author, :verlag, :kategorie, :beschreibung, :kosten)
    ");
    $stmt->execute([
        'isbn' => $_POST['isbn'] ?: null,
        'titel' => $_POST['titel'],
        'author' => $_POST['autor'],
        'verlag' => $_POST['verlag'] ?: null,
        'kategorie' => $_POST['kategorie'] ?: null,
        'beschreibung' => $_POST['beschreibung'] ?: null,
        'kosten' => $_POST['anschaffungskosten'] ?: null
    ]);

    header("Location: dashboard.php?success=added");
    exit;
}

// -------------------- DELETE BOOK --------------------
if (isset($_POST['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM t_buecher WHERE buchNr = ?");
    $stmt->execute([$_POST['buchNr']]);
    header("Location: dashboard.php?success=deleted");
    exit;
}

// -------------------- UPDATE BOOK --------------------
if (isset($_POST['update'])) {
    $stmt = $pdo->prepare("
        UPDATE t_buecher SET
            ISBN = :isbn,
            Titel = :titel,
            Author = :author,
            Verlag = :verlag,
            Kategorie = :kategorie,
            Beschreibung = :beschreibung,
            Anschaffungskosten = :kosten
        WHERE buchNr = :id
    ");
    $stmt->execute([
        'isbn' => $_POST['isbn'],
        'titel' => $_POST['titel'],
        'author' => $_POST['autor'],
        'verlag' => $_POST['verlag'],
        'kategorie' => $_POST['kategorie'],
        'beschreibung' => $_POST['beschreibung'],
        'kosten' => $_POST['anschaffungskosten'],
        'id' => $_POST['buchNr']
    ]);

    header("Location: dashboard.php?success=updated");
    exit;
}

// -------------------- SEARCH --------------------
$books = [];
// Session damit der Searchterm nach dem Reload nicht gelöscht wird 
$search_term = $_SESSION['search_term'] ?? '';
if (!empty($_GET['q'])) {
    $search_term = $_GET['q'];
    $_SESSION['search_term'] = $search_term;
    $stmt = $pdo->prepare("
        SELECT * FROM t_buecher
        WHERE Titel LIKE :q OR Author LIKE :q OR ISBN LIKE :q
        ORDER BY Titel
    ");
    $stmt->execute(['q' => '%' . $search_term . '%']);
    $books = $stmt->fetchAll();
}

// -------------------- EDIT LOAD --------------------
$editBook = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM t_buecher WHERE buchNr = ?");
    $stmt->execute([$_GET['edit']]);
    $editBook = $stmt->fetch();
}

// -------------------- TOGGLE AUSLEIH --------------------
if (isset($_GET['toggle'])) {
    $stmt = $pdo->prepare("
        UPDATE t_buecher
        SET ausleih = NOT ausleih
        WHERE buchNr = ?
    ");
    $stmt->execute([$_GET['toggle']]);

    header("Location: dashboard.php");
    exit;
}


?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Verwaltung</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php include 'content/navbar.php'; ?>

<div class="container py-5">

<!-- SUCCESS MESSAGES -->
<?php if ($_GET['success'] ?? ''): ?>
    <div class="alert alert-success">
        Aktion erfolgreich ausgeführt.
    </div>
<?php endif; ?>

<!-- ACTION BUTTONS -->
<div class="d-flex gap-2 mb-4">
    <a href="dashboard.php?action=add" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Buch hinzufügen
    </a>
</div>

<!-- SEARCH -->
<form class="input-group mb-4">
    <input type="text" name="q" class="form-control" placeholder="Buch suchen..." value="<?= htmlspecialchars($search_term); ?>">
    <button class="btn btn-outline-secondary">Suchen</button>
</form>

<!-- SEARCH RESULTS -->
<?php if ($books): ?>
<table class="table table-striped">
    <thead>
        <tr>
            <th>Titel</th>
            <th>Autor</th>
            <th>ISBN</th>
            <th>Aktionen</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($books as $b): ?>
        <tr>
            <td><?= htmlspecialchars($b['Titel']) ?></td>
            <td><?= htmlspecialchars($b['Author']) ?></td>
            <td><?= htmlspecialchars($b['ISBN']) ?></td>
            <td class="d-flex gap-2">
                <a href="dashboard.php?toggle=<?= $b['buchNr'] ?>"
                class="btn btn-sm <?= $b['ausleih'] ? 'btn-success' : 'btn-secondary' ?>">
                    <i class="bi <?= $b['ausleih'] ? 'bi-check-lg' : 'bi-x-lg' ?>"></i>
                </a>
                <a href="dashboard.php?edit=<?= $b['buchNr'] ?>" class="btn btn-sm btn-warning">
                    <i class="bi bi-pencil"></i>
                </a>
                <form method="post" onsubmit="return confirm('Wirklich löschen?')">
                    <input type="hidden" name="buchNr" value="<?= $b['buchNr'] ?>">
                    <button name="delete" class="btn btn-sm btn-danger">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

<!-- ADD BOOK -->
<?php if (($_GET['action'] ?? '') === 'add'): ?>
<h4>Neues Buch</h4>
<form method="post" class="row g-3">
    <input class="form-control" name="titel" placeholder="Titel" required>
    <input class="form-control" name="autor" placeholder="Autor" required>
    <input class="form-control" name="isbn" placeholder="ISBN">
    <input class="form-control" name="verlag" placeholder="Verlag">
    <input class="form-control" name="kategorie" placeholder="Kategorie">
    <textarea class="form-control" name="beschreibung" placeholder="Beschreibung"></textarea>
    <input class="form-control" type="number" step="0.01" name="anschaffungskosten" placeholder="Kosten">
    <button name="add" class="btn btn-success">Speichern</button>
</form>
<?php endif; ?>

<!-- EDIT BOOK -->
<?php if ($editBook): ?>
<h4>Buch bearbeiten</h4>
<form method="post" class="row g-3">
    <input type="hidden" name="buchNr" value="<?= $editBook['buchNr'] ?>">
    <input class="form-control" name="titel" value="<?= $editBook['Titel'] ?>" required>
    <input class="form-control" name="autor" value="<?= $editBook['Author'] ?>" required>
    <input class="form-control" name="isbn" value="<?= $editBook['ISBN'] ?>">
    <input class="form-control" name="verlag" value="<?= $editBook['Verlag'] ?>">
    <input class="form-control" name="kategorie" value="<?= $editBook['Kategorie'] ?>">
    <textarea class="form-control" name="beschreibung"><?= $editBook['Beschreibung'] ?></textarea>
    <input class="form-control" type="number" step="0.01" name="anschaffungskosten" value="<?= $editBook['Anschaffungskosten'] ?>">
    <button name="update" class="btn btn-primary">Aktualisieren</button>
</form>
<?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
