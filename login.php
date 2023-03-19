<?php
require_once 'config.php';
$_SESSION['logged_in'] = true;
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Überprüfen, ob der Benutzer bereits eingeloggt ist
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true && $_SESSION['role'] === 'admin') {
    header('location: admin.php');
    exit;
}

// Wenn das Formular zum Einloggen gesendet wurde
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Benutzername und Passwort aus dem Formular abrufen
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Recaptcha-Abfrage
    $recaptchaResponse = $_POST['g-recaptcha-response'];
    $secretKey = 'SECRET_KEY_EINGEBEN';
    $url = 'https://www.google.com/recaptcha/api/siteverify';
    $data = array('secret' => $secretKey, 'response' => $recaptchaResponse);
    $options = array(
        'http' => array(
            'header' => 'Content-type: application/x-www-form-urlencoded\r\n',
            'method' => 'POST',
            'content' => http_build_query($data),
        ),
    );
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    $response = json_decode($result);

    if (!$response->success) {
        // Recaptcha-Überprüfung fehlgeschlagen
        $loginError = 'Ung&uuml;ltiges Recaptcha';
    } else {
        // Benutzerdaten aus der Datenbank abrufen
        $stmt = $conn->prepare("SELECT * FROM admin_users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        // Benutzername und Passwort überprüfen
        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                // Passwort korrekt - Benutzer einloggen
                session_start();
                $_SESSION['loggedin'] = true;
                $_SESSION['username'] = $username;
                $_SESSION['role'] =  'admin';

                // Weiterleiten zur Admin-Seite
                header('location: admin.php');
                exit;
            } else {
                // Fehlermeldung, wenn das Passwort nicht stimmt oder der Benutzer keine Admin-Rechte hat
                $loginError = 'Ung&uuml;ltiger Benutzername oder Passwort';
            }
        } else {
            // Fehlermeldung, wenn der Benutzer nicht gefunden wurde
            $loginError = 'Ung&uuml;ltiger Benutzername oder Passwort';
        }
    }
}

// HTML-Formular zum Einloggen anzeigen
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
<h1>Login</h1>
<?php if (isset($loginError)): ?>
<form>
    <p style="color: red; font-weight: bold;"><?php echo $loginError; ?></p>
</form>
<?php endif; ?>
<form action="login.php" method="post">
    <label for="username">Benutzername:</label>
    <input type="text" name="username" id="username"><br>
    <label for="password">Passwort:</label>
    <input type="password" name="password" id="password"><br>
    <div class="g-recaptcha" data-sitekey="SITEKEY_EINGEBEN"></div>
    <input type="submit" value="Einloggen">
</form>
    <footer>
        &copy; 2023 ChatGPT | <a href="index.php">zur&uuml;ck</a>
    </footer>
</body>
</html>
<?php
// Datenbankverbindung schließen
$conn->close();
?>
