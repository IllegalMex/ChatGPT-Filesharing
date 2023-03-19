<?php
require_once 'config.php';

// Check if the login form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if the username and password are correct
    $stmt = $conn->prepare("SELECT * FROM admin_users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    if ($user && password_verify($password, $user['password'])) {
        // Login successful, save user information in the session
        session_start();
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
    } else {
        // Login failed
        $error = "Invalid username or password.";
    }
}

// Check if the user is logged in
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'admin') {
    header('location: login.php');
    exit;
}


// Check if the form to update the password has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updatePassword'])) {
    $currentPassword = $_POST['currentPassword'];
    $newPassword = $_POST['newPassword'];
    $confirmPassword = $_POST['confirmPassword'];

    // Check if the current password is correct
    $stmt = $conn->prepare("SELECT password FROM admin_users WHERE username = ?");
    $stmt->bind_param("s", $_SESSION['username']);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    if (!password_verify($currentPassword, $row['password'])) {
        $error = "The current password is incorrect.";
    }

    // Check if the new password is valid
    if ($newPassword !== $confirmPassword) {
        $error = "The new passwords do not match.";
    } elseif (strlen($newPassword) < 8) {
        $error = "The new password must be at least 8 characters long.";
    } else {
        // Update the password in the database
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE admin_users SET password = ? WHERE username = ?");
        $stmt->bind_param("ss", $hashedPassword, $_SESSION['username']);
        $stmt->execute();
        $success = "The password has been updated successfully.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin-Portal - ChatGPT Filesharing</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <h1>Admin-Portal - ChatGPT Filesharing</h1>

    <h2>Passwort &auml;ndern</h2>
<?php if (isset($error)) { ?>
    <p class="error"><?php echo $error; ?></p>
<?php } elseif (isset($success)) { ?>
    <p class="success"><?php echo $success; ?></p>
<?php } ?>
    <form action="changepassword.php" method="post">
        <label for="currentPassword">Aktuelles Passwort:</label>
        <input type="password" id="currentPassword" name="currentPassword" required><br>
        <label for="newPassword">Neues Passwort:</label>
        <input type="password" id="newPassword" name="newPassword" minlength="8" required><br>
        <label for="confirmPassword">Neues Passwort best&auml;tigen:</label>
        <input type="password" id="confirmPassword" name="confirmPassword" minlength="8" required><br>
        <input type="submit" name="updatePassword" value="Passwort &auml;ndern">
    </form>
    <footer>
        &copy; 2023 ChatGPT | <a href="admin.php">zur&uuml;ck</a> | <a href="logout.php">Logout</a>
    </footer>
</body>
</html>

<?php
// Datenbankverbindung schließen
$conn->close();
?>