<?php
require_once 'config.php';

// Link aus der GET-Parameter abrufen
if (isset($_GET['link'])) {
$link = $_GET['link'];
// Dateiinformationen aus der Datenbank abrufen
$stmt = $conn->prepare("SELECT * FROM files WHERE link = ?");
$stmt->bind_param("s", $link);
$stmt->execute();
$result = $stmt->get_result();

// Wenn die Datei gefunden wurde, herunterladen
if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    $file = 'data/' . $row['filename'];

    // HTTP-Header setzen
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($file) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));

    // Datei ausgeben
    readfile($file);
} else {
    echo 'Datei nicht gefunden.';
}
}

// Datenbankverbindung schließen
$conn->close();
?>