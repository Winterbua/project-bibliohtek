<?php
    $allowed_columns = [
        'buchNr',
        'ISBN',
        'Titel',
        'Author',
        'Verlag',
        'Kategorie',
        'Beschreibung',
        'Anschaffungskosten'
    ];

    if (isset($_POST['suchbegriff'], $_POST['spalten'])) {

        $suchbegriff = $_POST['suchbegriff'];
        $spalte = $_POST['spalten'];

        // Check column
        if (!in_array($spalte, $allowed_columns)) {
            die("<div class='alert alert-danger'>Ungültige Spalte!</div>");
        }

        // Safe prepared statement
        $sql = "SELECT * FROM t_buecher WHERE $spalte LIKE ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(["%$suchbegriff%"]);

        $rows = $stmt->fetchAll();

        if ($rows) {
            echo "<div class='table-responsive'>";
            echo "<table class='table table-striped table-bordered table-hover'>";

            // Headers
            echo "<thead class='table-dark'><tr>";
            foreach (array_keys($rows[0]) as $header) {
                echo "<th>" . htmlspecialchars($header) . "</th>";
            }
            echo "</tr></thead><tbody>";

            // Rows
            foreach ($rows as $row) {
                echo "<tr>";
                foreach ($row as $value) {
                    echo "<td>" . htmlspecialchars($value) . "</td>";
                }
                echo "</tr>";
            }

            echo "</tbody></table>";
            echo "</div>";

        } else {
            echo "<div class='alert alert-warning'>Keine Daten gefunden.</div>";
        }
    }
?>
