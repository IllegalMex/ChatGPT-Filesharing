<?php
// Login Daten
$login_username = "admin";
$login_password = "admin123";

// Datenbankverbindung herstellen
$servername = "localhost"; // IP-Adresse des Servers
$username = "username"; // Benutzername f�r die Datenbank
$password = "password"; // Passwort f�r die Datenbank
$dbname = "dbname"; // Name der Datenbank

// Verbindung zur Datenbank herstellen
$conn = new mysqli($servername, $username, $password, $dbname);

// Pr�fen, ob die Verbindung erfolgreich war
if ($conn->connect_error)
{
    // Fehlermeldung ausgeben und das Skript beenden
    die("Connection failed: " . $conn->connect_error);
}
?>