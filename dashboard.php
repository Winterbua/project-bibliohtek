<?php
/*
    Dieser erste Teil dient dazu,
    die Session zu starten und die Login daten zu empfangen.

*/
session_start();
require 'content/datenbankverbindung.php';

// Login-Schutz
if (empty($_SESSION['logged_in'])) {
    header("Location: login.php");
    exit;
}

// Variable um die Paramter intern auf der Website weiterzuleiten
$success = '';


// -------------------- ADD BOOK --------------------
/*
    Erklährung:
    Die PDO ist ein Objekt, das heißt es kann kein Code so eingefügt werden.
    Aus diesem Grund werden die Values mit einem : angeschrieben.
    Somit werden diese als Parameter und nicht als Code übergeben.
*/
if (isset($_POST['add'])) {
    $stmt = $pdo->prepare("
        INSERT INTO t_buecher
        (ISBN, Titel, Author, Verlag, Kategorie, Beschreibung, Anschaffungskosten, ausleih)
        VALUES (:isbn, :titel, :author, :verlag, :kategorie, :beschreibung, :kosten, :ausleih)
    ");
    $stmt->execute([
        'isbn' => $_POST['isbn'],
        'titel' => $_POST['titel'],
        'author' => $_POST['autor'],
        'verlag' => $_POST['verlag'],
        'kategorie' => $_POST['kategorie'],
        'beschreibung' => $_POST['beschreibung'],
        'kosten' => $_POST['anschaffungskosten'],
        'ausleih' => 1
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
$search_term = $_SESSION['search_term'] ?? '';

// Wenn ein neuer Begriff per GET kommt, Session aktualisieren
if (isset($_GET['q'])) {
    $search_term = trim($_GET['q']);
    $_SESSION['search_term'] = $search_term;
}

// Nur suchen, wenn $search_term nicht leer ist
if ($search_term !== '') {
    $stmt = $pdo->prepare("
        SELECT b.*, a.vorname, a.nachname, a.rueckgabedatum
        FROM t_buecher b
        LEFT JOIN t_ausleih a 
            ON a.ausleihNr = (
                SELECT a2.ausleihNr
                FROM t_ausleih a2
                WHERE a2.buchNr = b.buchNr
                AND a2.rueckgabedatum > NOW()
                ORDER BY a2.rueckgabedatum DESC
                LIMIT 1
            )
        WHERE b.Titel LIKE :q OR b.Author LIKE :q OR b.ISBN LIKE :q
        ORDER BY b.Titel
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


// ----------- Logik für das Ausleihen -----------
if (isset($_POST['ausleihen'])) {
    try {
        $pdo->beginTransaction();

        // Buch als ausgeliehen markieren
        $stmt = $pdo->prepare("
            UPDATE t_buecher 
            SET ausleih = 0 
            WHERE buchNr = ?
        ");
        $stmt->execute([$_POST['buchNr']]);

        // Ausleihe protokollieren
        $stmt = $pdo->prepare("
            INSERT INTO t_ausleih
            (vorname, nachname, personalNr, buchNr, ausleihdatum, rueckgabedatum)
            VALUES (?, ?, ?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 15 DAY))
        ");

        $stmt->execute([
            $_POST['vorname'],
            $_POST['nachname'],
            $_SESSION['personalNr'],
            $_POST['buchNr']
        ]);

        $pdo->commit();
        header("Location: dashboard.php");
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        die("Fehler beim Ausleihen");
    }
}


// ----------- Logik für das Rückgeben -----------
if (isset($_POST['zurueckgeben'])) {
    try {
        $pdo->beginTransaction();

        // Buch wieder verfügbar
        $stmt = $pdo->prepare("
            UPDATE t_buecher 
            SET ausleih = 1 
            WHERE buchNr = ?
        ");
        $stmt->execute([$_POST['buchNr']]);

        // Rueckgabe setzen
        $stmt = $pdo->prepare("
            UPDATE t_ausleih
            SET rueckgabedatum = NOW()
            WHERE buchNr = ? AND rueckgabedatum IS NULL
        ");
        $stmt->execute([$_POST['buchNr']]);

        $pdo->commit();
        header("Location: dashboard.php");
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        die("Fehler bei der Rückgabe");
    }
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
    <button name="add" class="btn btn-primary">Speichern</button>
</form>
<?php endif; ?>

<!-- EDIT BOOK -->
<?php if ($editBook): ?>
<h4>Buch bearbeiten</h4>
<form method="post" class="row g-3">
    <!-- Der Value ist ein PHP Code der den Text, der in der Datenbank steht direkt in das Formular ausgibt zum Bearbeiten -->
    <input type="hidden" name="buchNr" value="<?= $editBook['buchNr'] ?>" required>
    <input class="form-control" name="titel" value="<?= $editBook['Titel'] ?>" required>
    <input class="form-control" name="autor" value="<?= $editBook['Author'] ?>" required>
    <input class="form-control" name="isbn" value="<?= $editBook['ISBN'] ?>" required>
    <input class="form-control" name="verlag" value="<?= $editBook['Verlag'] ?>" required>
    <input class="form-control" name="kategorie" value="<?= $editBook['Kategorie'] ?>" required>
    <textarea class="form-control" name="beschreibung"><?= $editBook['Beschreibung'] ?></textarea required>
    <input class="form-control" type="number" step="0.01" name="anschaffungskosten" value="<?= $editBook['Anschaffungskosten'] ?>" required>
    <button name="update" class="btn btn-primary">Aktualisieren</button>
</form>
<?php endif; ?>

<!-- SEARCH RESULTS -->
<?php if ($books && !isset($_GET['action']) && !isset($_GET['edit'])): ?>
    <?php if ($books): ?>
    <h4>Bücher verwalten</h4>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Titel</th>
                <th>Autor</th>
                <th>ISBN</th>
                <th colspan="2">Aktionen</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($books as $b): ?>
            <tr>
                <!-- Die Funktion "htmlspecialchars" wird verwendet, um die Daten auslesesicher zu machen -->
                <td><?= htmlspecialchars($b['Titel']) ?></td>
                <td><?= htmlspecialchars($b['Author']) ?></td>
                <td><?= htmlspecialchars($b['ISBN']) ?></td>
                <td>
                    <!-- Eine kurze PHP Funktion, die prüft ob das Buch ausgeliehen ist oder nicht -->
                    <?php if ($b['ausleih'] == 1): ?>
                        <span class="badge bg-success">Verfügbar</span>
                    <?php else: ?>
                        <span class="badge bg-danger">Ausgeliehen</span>
                    <?php endif; ?>
                </td>
                <td class="d-flex gap-2">
                    <a href="dashboard.php?edit=<?= $b['buchNr'] ?>" class="btn btn-sm btn-warning">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <?php if ($b['ausleih'] == 1): ?>
                    <form method="post" class="d-flex gap-1">
                        <input type="hidden" name="buchNr" value="<?= $b['buchNr'] ?>">

                        <input type="text" name="vorname" class="form-control form-control-sm" placeholder="Vorname" required>
                        <input type="text" name="nachname" class="form-control form-control-sm" placeholder="Nachname" required>

                        <button name="ausleihen" class="btn btn-sm btn-success">
                            <i class="bi bi-box-arrow-right"></i>
                        </button>
                    </form>
                    <?php else: ?>
                        <button class="btn btn-sm btn-secondary" disabled>
                            <i class="bi bi-lock"></i>
                        </button>
                    <?php endif; ?>
                    <?php if ($b['ausleih'] == 0): ?>
                    <form method="post">
                        <input type="hidden" name="buchNr" value="<?= $b['buchNr'] ?>">
                        <button name="zurueckgeben" class="btn btn-sm btn-info">
                            <i class="bi bi-box-arrow-in-left"></i>
                        </button>
                    </form>
                    <?php endif; ?>
                    <form method="post" onsubmit="return confirm('Wirklich löschen?')">
                        <input type="hidden" name="buchNr" value="<?= $b['buchNr'] ?>">
                        <button name="delete" class="btn btn-sm btn-danger">
                            <i class="bi bi-trash"></i>
                        </button> 
                    </form>

                    <?php if ($b['ausleih'] == 0 && $b['vorname']): ?>
                        <span class="ms-2">
                            <?= htmlspecialchars($b['vorname'] . ' ' . $b['nachname']) ?>
                            (<?= date('d.m.Y', strtotime($b['rueckgabedatum'])) ?>)
                        </span>
                    <?php endif; ?>

                </td>
            </tr>

        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
<?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
