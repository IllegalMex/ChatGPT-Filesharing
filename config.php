<?php
// Login Daten
$login_username = "admin";
$login_password = "admin123";

// Datenbankverbindung herstellen
$servername = "localhost"; // IP-Adresse des Servers
$username = "username"; // Benutzername für die Datenbank
$password = "password"; // Passwort für die Datenbank
$dbname = "dbname"; // Name der Datenbank

// Verbindung zur Datenbank herstellen
$conn = new mysqli($servername, $username, $password, $dbname);

// Prüfen, ob die Verbindung erfolgreich war
if ($conn->connect_error)
{
    // Fehlermeldung ausgeben und das Skript beenden
    die("Connection failed: " . $conn->connect_error);
}
?>