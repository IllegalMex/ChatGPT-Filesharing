<?php
require_once 'config.php';
session_start();

$links = [];

// Wenn das Formular zum Hochladen von Dateien gesendet wurde
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['uploadFile']))
{
    $uploadDir = 'data/';

    // Schleife über alle hochgeladenen Dateien
    foreach ($_FILES['uploadFile']['name'] as $i => $fileName)
    {
        // Datei in den Upload-Ordner speichern
        $uploadFile = $uploadDir . basename($fileName);
        move_uploaded_file($_FILES['uploadFile']['tmp_name'][$i], $uploadFile);

        // Eindeutigen Link erstellen
        $link = uniqid();

        // Prüfen ob der Link bereits in der Datenbank vorhanden ist
        $stmt = $conn->prepare("SELECT * FROM files WHERE link = ?");
        $stmt->bind_param("s", $link);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($result->num_rows > 0)
        {
            $link = uniqid();
            $stmt->bind_param("s", $link);
            $stmt->execute();
            $result = $stmt->get_result();
        }

        // Dateiinformationen in der Datenbank speichern
        $stmt = $conn->prepare("INSERT INTO files (filename, link, upload_date) VALUES (?, ?, NOW())");
        $stmt->bind_param("ss", $fileName, $link);
        $stmt->execute();

        // Cookie mit dem Dateilink setzen, wenn es nicht schon existiert
        if (!isset($_COOKIE["file_$link"]))
        {
            setcookie("file_$link", $fileName, time() + 3600 * 24 * 30, "/");
        }

        $links[] = $link;
    }
}

// Array zum Speichern der Links aus dem Cookie
$cookieLinks = [];

// Schleife über alle Cookies, die mit "file_" beginnen
foreach ($_COOKIE as $name => $value)
{
    if (strpos($name, "file_") === 0)
    {
        $link = substr($name, 5); // Link aus dem Cookie-Namen extrahieren
        // Prüfen, ob die Datei auf dem Server noch vorhanden ist
        $stmt = $conn->prepare("SELECT * FROM files WHERE link = ?");
        $stmt->bind_param("s", $link);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0)
        {
            // Datei aus dem Cookie löschen, wenn sie nicht mehr auf dem Server gespeichert ist
            setcookie($name, "", time() - 3600, "/");
        }
        else
        {
            $cookieLinks[] = $link;
        }
    }
}

// Wenn es gespeicherte Links im Cookie gibt, füge sie zu den aktuellen Links hinzu
if (!empty($cookieLinks))
{
    $links = array_merge($links, $cookieLinks);
}

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Meine Dateifreigabe-Seite</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <h1>ChatGPT Filesharing</h1>
    <form action="index.php" method="post" enctype="multipart/form-data">
        <label for="uploadFile">Dateien ausw&auml;hlen:</label>
        <input type="file" name="uploadFile[]" id="uploadFile" multiple>
        <input type="submit" value="Hochladen">
    </form>
    <?php if (!empty($links)): ?>
    <h2>Hochgeladene Dateien:</h2>
<form>
    <table>
        <thead>
            <tr>
                <th>Link</th>
                <th>Dateiname</th>
                <th>Uploaddatum</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($links as $link): ?>
                <?php
        $stmt = $conn->prepare("SELECT * FROM files WHERE link = ?");
        $stmt->bind_param("s", $link);
        $stmt->execute();
        $result = $stmt->get_result();
        $file = $result->fetch_assoc();
?>
                <tr>
                    <td>
                        <?php $fileLink = "https://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/download.php?link=" . $link; ?>
                        <a href="<?php echo $fileLink ?>"><?php echo $link ?></a>
                    </td>
                    <td><?php echo $file['filename'] ?></td>
                    <td><?php echo $file['upload_date'] ?></td>
                </tr>
            <?php
    endforeach; ?>
        </tbody>
    </table>
</form>
    <?php
endif; ?>
    <footer>
        &copy; 2023 ChatGPT | <a href="admin.php">Admin-Bereich</a>
    </footer>
</body>
</html>
<?php
// Datenbankverbindung schließen
$conn->close();
?>
