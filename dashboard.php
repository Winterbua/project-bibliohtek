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

$error = "";
$success = "";

// === NEUES BUCH HINZUFÜGEN ===
if (isset($_POST['add'])) {
    $titel = trim($_POST['titel']);
    $autor = trim($_POST['autor']);
    $isbn  = trim($_POST['isbn']);
    $verlag = trim($_POST['verlag']);
    $kategorie = trim($_POST['kategorie']);
    $beschreibung = trim($_POST['beschreibung']);
    $anschaffungskosten = trim($_POST['anschaffungskosten']);

    if ($titel === "" || $autor === "") {
        $error = "Bitte die Pflichtfelder ausfüllen (Titel, Author)";
    } else {
        try {
            $stmt = $pdo->prepare(
                "INSERT INTO t_buecher 
                (Titel, Author, ISBN, Verlag, Kategorie, Beschreibung, Anschaffungskosten)
                VALUES (:titel, :author, :isbn, :verlag, :kategorie, :beschreibung, :anschaffungskosten)"
            );
            $stmt->execute([
                'titel' => $titel,
                'author' => $autor,
                'isbn' => $isbn ?: null,
                'verlag' => $verlag ?: null,
                'kategorie' => $kategorie ?: null,
                'beschreibung' => $beschreibung ?: null,
                'anschaffungskosten' => $anschaffungskosten ?: null
            ]);
            $success = "Buch hinzugefügt.";
        } catch (PDOException $e) {
            $error = "Fehler beim Hinzufügen: " . $e->getMessage();
        }
    }
}

// === BUCH LÖSCHEN ===
if (isset($_GET['delete'])) {
    $buchNr = $_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM t_buecher WHERE buchNr = :buchNr");
        $stmt->execute(['buchNr' => $buchNr]);
        $success = "Buch gelöscht.";
    } catch (PDOException $e) {
        $error = "Fehler beim Löschen: " . $e->getMessage();
    }
}

// === BUCH BEARBEITEN ===
if (isset($_GET['edit'])) {
    $editId = $_GET['edit'];
    try {
        $stmt = $pdo->prepare("SELECT * FROM t_buecher WHERE buchNr = :buchNr");
        $stmt->execute(['buchNr' => $editId]);
        $bookToEdit = $stmt->fetch();
        if (!$bookToEdit) {
            $error = "Buch nicht gefunden.";
        }
    } catch (PDOException $e) {
        $error = "Fehler beim Laden des Buchs: " . $e->getMessage();
    }
}

if (isset($_POST['update'])) {
    $buchNr = $_POST['buchNr'];
    $titel = trim($_POST['titel']);
    $autor = trim($_POST['autor']);
    $isbn  = trim($_POST['isbn']);
    $verlag = trim($_POST['verlag']);
    $kategorie = trim($_POST['kategorie']);
    $beschreibung = trim($_POST['beschreibung']);
    $anschaffungskosten = trim($_POST['anschaffungskosten']);

    if ($titel === "" || $autor === "") {
        $error = "Bitte die Pflichtfelder ausfüllen (Titel, Author)";
    } else {
        try {
            $stmt = $pdo->prepare(
                "UPDATE t_buecher SET 
                 Titel = :titel,
                 Author = :author,
                 ISBN = :isbn,
                 Verlag = :verlag,
                 Kategorie = :kategorie,
                 Beschreibung = :beschreibung,
                 Anschaffungskosten = :anschaffungskosten
                 WHERE buchNr = :buchNr"
            );
            $stmt->execute([
                'titel' => $titel,
                'author' => $autor,
                'isbn' => $isbn ?: null,
                'verlag' => $verlag ?: null,
                'kategorie' => $kategorie ?: null,
                'beschreibung' => $beschreibung ?: null,
                'anschaffungskosten' => $anschaffungskosten ?: null,
                'buchNr' => $buchNr
            ]);
            $success = "Buch aktualisiert.";
            unset($bookToEdit); // Formular schließen
        } catch (PDOException $e) {
            $error = "Fehler beim Aktualisieren: " . $e->getMessage();
        }
    }
}

// === ALLE BÜCHER LADEN ===
try {
    $stmt = $pdo->query("SELECT * FROM t_buecher ORDER BY Titel ASC");
    $books = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Fehler beim Laden der Bücher: " . $e->getMessage();
    $books = [];
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="author" content="Manuel Mayr">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="style/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include 'content/navbar.php'; ?>

<div id="real_body" class="container mt-5">

    <p>Logged in as: <strong><?= htmlspecialchars($_SESSION['username']) ?></strong></p>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <!-- NEUES BUCH HINZUFÜGEN -->
    <div class="card mb-4">
        <div class="card-header">Neues Buch hinzufügen</div>
        <div class="card-body">
            <form method="post">
                <div class="mb-3">
                    <input type="text" name="titel" class="form-control" placeholder="Titel" required>
                </div>
                <div class="mb-3">
                    <input type="text" name="autor" class="form-control" placeholder="Author" required>
                </div>
                <div class="mb-3">
                    <input type="text" name="isbn" class="form-control" placeholder="ISBN">
                </div>
                <div class="mb-3">
                    <input type="text" name="verlag" class="form-control" placeholder="Verlag">
                </div>
                <div class="mb-3">
                    <input type="text" name="kategorie" class="form-control" placeholder="Kategorie">
                </div>
                <div class="mb-3">
                    <textarea name="beschreibung" class="form-control" placeholder="Beschreibung"></textarea>
                </div>
                <div class="mb-3">
                    <input type="number" step="0.01" name="anschaffungskosten" class="form-control" placeholder="Anschaffungskosten">
                </div>
                <button type="submit" name="add" class="btn btn-success">Hinzufügen</button>
            </form>
        </div>
    </div>

    <!-- BUCH BEARBEITEN -->
    <?php if (isset($bookToEdit) && $bookToEdit): ?>
    <div class="card mb-4">
        <div class="card-header">Buch bearbeiten</div>
        <div class="card-body">
            <form method="post">
                <input type="hidden" name="buchNr" value="<?= htmlspecialchars($bookToEdit['buchNr']) ?>">

                <div class="mb-3">
                    <input type="text" name="titel" class="form-control"
                           value="<?= htmlspecialchars($bookToEdit['Titel']) ?>" placeholder="Titel" required>
                </div>
                <div class="mb-3">
                    <input type="text" name="autor" class="form-control"
                           value="<?= htmlspecialchars($bookToEdit['Author']) ?>" placeholder="Author" required>
                </div>
                <div class="mb-3">
                    <input type="text" name="isbn" class="form-control"
                           value="<?= htmlspecialchars($bookToEdit['ISBN']) ?>" placeholder="ISBN">
                </div>
                <div class="mb-3">
                    <input type="text" name="verlag" class="form-control"
                           value="<?= htmlspecialchars($bookToEdit['Verlag']) ?>" placeholder="Verlag">
                </div>
                <div class="mb-3">
                    <input type="text" name="kategorie" class="form-control"
                           value="<?= htmlspecialchars($bookToEdit['Kategorie']) ?>" placeholder="Kategorie">
                </div>
                <div class="mb-3">
                    <textarea name="beschreibung" class="form-control" placeholder="Beschreibung"><?= htmlspecialchars($bookToEdit['Beschreibung']) ?></textarea>
                </div>
                <div class="mb-3">
                    <input type="number" step="0.01" name="anschaffungskosten" class="form-control"
                           value="<?= htmlspecialchars($bookToEdit['Anschaffungskosten']) ?>" placeholder="Anschaffungskosten">
                </div>

                <button type="submit" name="update" class="btn btn-warning">Aktualisieren</button>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <!-- BÜCHER ÜBERSICHT -->
    <div class="card">
        <div class="card-header">Alle Bücher</div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>BuchNr</th>
                        <th>Titel</th>
                        <th>Author</th>
                        <th>ISBN</th>
                        <th>Verlag</th>
                        <th>Kategorie</th>
                        <th>Beschreibung</th>
                        <th>Anschaffungskosten</th>
                        <th>Aktionen</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($books as $b): ?>
                        <tr>
                            <td><?= htmlspecialchars($b['buchNr']) ?></td>
                            <td><?= htmlspecialchars($b['Titel']) ?></td>
                            <td><?= htmlspecialchars($b['Author']) ?></td>
                            <td><?= htmlspecialchars($b['ISBN']) ?></td>
                            <td><?= htmlspecialchars($b['Verlag']) ?></td>
                            <td><?= htmlspecialchars($b['Kategorie']) ?></td>
                            <td><?= htmlspecialchars($b['Beschreibung']) ?></td>
                            <td><?= htmlspecialchars($b['Anschaffungskosten']) ?></td>
                            <td>
                                <a href="dashboard.php?edit=<?= urlencode($b['buchNr']) ?>"
                                   class="btn btn-sm btn-primary">Bearbeiten</a>
                                <a href="dashboard.php?delete=<?= urlencode($b['buchNr']) ?>"
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('Wirklich löschen?');">Löschen</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?php include 'content/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
