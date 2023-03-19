<?php
require_once 'config.php';

// Link aus der GET-Parameter abrufen
if (isset($_GET['link'])) {
    $link = $_GET['link'];

    // Datei aus der Datenbank und dem Upload-Ordner löschen
    $stmt = $conn->prepare("SELECT * FROM files WHERE link = ?");
    $stmt->bind_param("s", $link);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $file = 'data/' . $row['filename'];
        unlink($file);

        $stmt = $conn->prepare("DELETE FROM files WHERE link = ?");
        $stmt->bind_param("s", $link);
        $stmt->execute();
    }
}

// Zurück zur Admin-Seite weiterleiten
header('Location: admin.php');

// Datenbankverbindung schließen
$conn->close();
?>