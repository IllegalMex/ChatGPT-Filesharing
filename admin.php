<?php
require_once 'config.php';

// Check if the login form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login']))
{
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if the username and password are correct
    $stmt = $conn->prepare("SELECT * FROM admin_users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    if ($user && password_verify($password, $user['password']))
    {
        // Login successful, save user information in the session
        session_start();
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
    }
    else
    {
        // Login failed
        $error = "Invalid username or password.";
    }
}
// Check if the user is logged in
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'admin')
{
    header('location: login.php');
    exit;
}

// Check if the form to delete files has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteFilesSubmit']))
{
    $filenames = $_POST['deleteFiles'];
    // Convert the filenames to an array and loop through them to delete the files from the database and folder
    foreach ($filenames as $filename)
    {
        $stmt = $conn->prepare("DELETE FROM files WHERE filename = ?");
        $stmt->bind_param("s", $filename);
        $stmt->execute();

        $uploadDir = 'data/';
        $uploadFile = $uploadDir . $filename;
        unlink($uploadFile);
    }
}

// Filter the files based on filename or link
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['filterFiles']))
{
    $filter = $_POST['filter'];
    $sql = "SELECT * FROM files WHERE filename LIKE '%$filter%' OR link LIKE '%$filter%'";
}
else
{
    $sql = "SELECT * FROM files";
}
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Area - ChatGPT File Sharing</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <h1>Admin-Portal - ChatGPT Filesharing</h1>
    <form action="admin.php" method="post">
<label for="uploadFile">Detailsuche:</label>
        <input type="text" name="filter" placeholder="Gebe Dateiname oder Dateilink ein...">
        <input type="submit" name="filterFiles" value="Filter">
    </form>
<h2>Dateiliste:</h2>   
 <form action="admin.php" method="post">
        <table>
<thead>
            <tr>
                <th>Dateiname</th>
                <th>Dateilink</th>
                <th>hochgeladen am</th>
                <th>Aktionen</th>
                <th>Massenl&ouml;schung</th>
            </tr>
</thead>
<tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['filename']; ?></td>
                    <td><?php echo $row['link']; ?></td>
                    <td><?php echo date('d.m.Y', strtotime($row['upload_date'])); ?></td>
                    <td>
                        <a href="download.php?link=<?php echo $row['link']; ?>">Download</a> |
                        <a href="delete.php?link=<?php echo $row['link']; ?>">L&ouml;schen</a>
                    </td>
                    <td>
                        <input type="checkbox" name="deleteFiles[]" value="<?php echo $row['filename']; ?>">
                    </td>
                </tr>
            <?php
endwhile; ?>
 </tbody>
        </table>
 <input type="submit" name="deleteFilesSubmit" value="L&ouml;sche ausgew&auml;hlte Dateien">       
    </form>
    <footer>
        &copy; 2023 ChatGPT | <a href="index.php">zur&uuml;ck</a> | <a href="changepassword.php">Passwort &auml;ndern</a> | <a href="logout.php">Logout</a>
    </footer>
</body>
</html>
<?php
// Close database connection
$conn->close();
?>
