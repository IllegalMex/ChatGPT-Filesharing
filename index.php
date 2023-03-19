<?php
require_once 'config.php';
session_start();

$links = [];

// Wenn das Formular zum Hochladen von Dateien gesendet wurde
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['uploadFile']))
{
    // Überprüfung des reCaptcha-Status
    $url = 'https://www.google.com/recaptcha/api/siteverify';
    $data = array(
        'secret' => 'SECRET_KEY_EINGEBEN',
        'response' => $_POST['g-recaptcha-response']
    );
    $options = array(
        'http' => array (
            'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data)
        )
    );
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    $response = json_decode($result);

    if ($response->success)
    {
        $uploadDir = 'data/';

        // Check if number of uploaded files exceed 20
if (count($_FILES['uploadFile']['name']) > 20) {
    $uploadError = "Es k&uuml;nnen maximal 20 Dateien gleichzeitig hochgeladen werden.";
    $_SESSION['uploadError'] = $uploadError;
    header("Location: index.php");
    exit; // Stop file upload
}
        // Schleife über alle hochgeladenen Dateien
    foreach ($_FILES['uploadFile']['name'] as $i => $fileName) {
        // Prüfen, ob der Dateityp und die Dateigröße zulässig sind
        $allowedTypes = ['txt', 'zip', 'jpg', 'rar'];
        $maxSize = 1 * 1024 * 1024; // 100 MB in Bytes
        $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $fileSize = $_FILES['uploadFile']['size'][$i];
        if (!in_array($fileType, $allowedTypes)) {
            $uploadError = 'Nur Dateien mit den Endungen .txt, .jpg, .zip oder .rar sind erlaubt.';
	$_SESSION['uploadError'] = $uploadError;
	header("Location: index.php");
            exit;
        } else if ($fileSize > $maxSize) {
            $uploadError = 'Die maximale Dateigr&ouml;&szlig;e von 100 MB wurde &uuml;berschritten.';
	$_SESSION['uploadError'] = $uploadError;
	header("Location: index.php");
            exit;
        } else {
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
                // Gültigkeit Cookie für 365 Tage
                setcookie("file_$link", $fileName, time() + 3600 * 24 * 30, "/");
            }
            $links[] = $link;
		$uploadDone = [];
		$_SESSION['uploadDone'] = $uploadDone;
        }
    }



}
    else
    {
        // Wenn reCaptcha-Überprüfung fehlschlägt, wird eine Fehlermeldung ausgegeben
        $uploadError = 'Ung&uuml;ltiges Recaptcha';
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
    <title>ChatGPT Filesharing</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
    <h1>ChatGPT Filesharing</h1>

<! Fehlermeldung anzeigen>
<?php if(isset($_SESSION['uploadError'])): ?>
<form>
    <p style="color: red; font-weight: bold;"><?php echo $_SESSION['uploadError']; ?></p>
</form>
<?php unset($_SESSION['uploadError']); ?>
<?php endif; ?>

<!Generierten Downloadlink anzeigen>
<?php if(isset($_SESSION['uploadDone'])): ?>
<form>
<?php // Wenn mindestens eine Datei hochgeladen wurde, werden die Links angezeigt
if (count($links) > 0) {
    echo "Neue Links zum Herunterladen:<br>";
    foreach ($links as $link) {
        if (!in_array($link, $cookieLinks)) {
            echo "https://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/download.php?link=" . $link . "<br>";
        }
    }
}
?>
    
</form>
<?php unset($_SESSION['uploadDone']); ?>
<?php endif; ?>

    <form action="index.php" method="post" enctype="multipart/form-data">
        <label for="uploadFile">Dateien ausw&auml;hlen:</label>
        <input type="file" name="uploadFile[]" id="uploadFile" multiple>
      <div class="g-recaptcha" data-sitekey="SITEKEY_EINGEBEN"></div>
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
                <th>Aktionen</th>
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
                    <td><a href="deleteself.php?link=<?php echo $link ?>">L&ouml;schen</a></td>
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