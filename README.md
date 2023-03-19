# ChatGPT-Filesharing

Die Datei-Upload-Webanwendung ist eine einfache durch ChatGPT entwickelte Webanwendung, die es Benutzern ermöglicht, Dateien auf einen Server hochzuladen und Links zu diesen Dateien zu generieren, die dann geteilt werden können. Die Anwendung verfügt über ein Admin-Backend, in dem der Administrator die hochgeladenen Dateien verwalten und die Anwendung konfigurieren kann.
## Funktionen

Datei-Upload: Benutzer können Dateien auf den Server hochladen.
Eindeutige Links: Eindeutige Links werden automatisch generiert und den Benutzern zur Verfügung gestellt.
Cookie-Unterstützung: Die Anwendung unterstützt Cookies, um den Benutzern zu ermöglichen, ihre hochgeladenen Dateien später wiederzufinden.
Passwort ändern: Benutzer können ihr Passwort ändern.
Admin-Backend: Der Administrator kann hochgeladene Dateien verwalten, Benutzerkonten verwalten und Einstellungen konfigurieren.

## Voraussetzungen

* PHP 7 oder höher
* MySQL oder eine andere Datenbank, die von PHP unterstützt wird
* Schreibrechte für den Ordner data für das Speichern von hochgeladenen Dateien

## Installation

1. Laden Sie die Dateien von GitHub herunter.
2. Kopieren Sie alle Dateien in das Verzeichnis, das für Ihre Webanwendung vorgesehen ist.
3. Importieren Sie die database.sql-Datei in Ihre MySQL-Datenbank.
4. Bearbeiten Sie die config.php-Datei, um die Datenbankinformationen und andere Einstellungen anzupassen.
5. Öffnen Sie die Datei index.php in Ihrem Webbrowser und Sie sollten die Startseite der Anwendung sehen.

## Verwendung
#### Hochladen von Dateien

Öffnen Sie die Startseite der Anwendung.
Klicken Sie auf die Schaltfläche "Datei auswählen" und wählen Sie die Datei aus, die Sie hochladen möchten.
Klicken Sie auf die Schaltfläche "Hochladen".
Ein eindeutiger Link zur hochgeladenen Datei wird generiert und auf der Seite angezeigt.

#### Verwalten von Dateien (nur für Administratoren)

Öffnen Sie das Admin-Backend, indem Sie auf den Link "Admin" klicken.
Melden Sie sich als Administrator an.
Sie können jetzt hochgeladene Dateien anzeigen, herunterladen und löschen.

#### Passwort ändern

Öffnen Sie die Seite "Passwort ändern".
Geben Sie Ihr altes Passwort ein und wählen Sie ein neues Passwort. Das Standardpasswort ist 'admin123' für den Benutzer 'admin'.
Klicken Sie auf die Schaltfläche "Passwort ändern".
Ihr Passwort wird aktualisiert.

## Anpassung

Die Anwendung verwendet eine CSS-Datei namens style.css, die bearbeitet werden kann, um das Aussehen der Anwendung anzupassen.
## Hinweise

Dieses Projekt wurde erstellt mit Hilfe von ChatGPT. Es ist wichtig, dass Sie sicherstellen, dass keine illegalen Inhalte hochgeladen werden. Der Autor dieses Projekts (IllegalMex) übernimmt keine Haftung für die Nutzung dieses Projekts durch Dritte.

Beim ersten Login ins Admin-Backend wird ein Standardpasswort verwendet. Es wird dringend empfohlen, das Passwort sofort nach dem ersten Login zu ändern, um die Sicherheit des Admin-Bereichs zu gewährleisten.
    
## Autor

Dieses Skript wurde von ChatGPT erstellt, einem großen Sprachmodell, das auf der GPT-3.5-Architektur von OpenAI basiert.
## Lizenz

Dieses Skript ist unter der MIT-Lizenz veröffentlicht. Eine Kopie der Lizenz finden Sie in der Datei LICENSE.
