<?php

// Abschnitt 1: Verbindung + Session

try {
    $db = new PDO('sqlite:' . __DIR__ . '/../database/projektdatenbank.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Datenbankverbindung fehlgeschlagen: " . $e->getMessage());
}

session_start();

if (!isset($_SESSION['sesid'])) {
    header("Location: ../user/login.php");
    exit;
}

// Abschnitt 2: Funktion Notiz Erstellung

function NotizSpeichern(PDO $db) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['neue_notiz'])) {
        $userid = $_SESSION['sesid'];
        $zeit = date('Y-m-d H:i:s');

        $notenName = trim($_POST['notiz_name']);
        $notenInhalt = trim($_POST['notiz_inhalt']);
        $status = 1;

        try {
            $statement = $db->prepare("INSERT INTO notizen (userid, notiz_name, notiz_inhalt, notiz_erstellt, notiz_bearbeitet, notiz_status)VALUES (:userid, :name, :inhalt, :erstellt, :bearbeitet, :status)");
            $statement->bindParam(':userid', $userid);
            $statement->bindParam(':name', $notenName);
            $statement->bindParam(':inhalt', $notenInhalt);
            $statement->bindParam(':erstellt', $zeit);
            $statement->bindParam(':bearbeitet', $zeit);
            $statement->bindParam(':status', $status);
            $statement->execute();
        } catch (PDOException $e) {
            die("Notiz konnte nicht eingefügt werden: " . $e->getMessage());
        }
    }
}

NotizSpeichern($db);

// Abschnitt 3: Notizen abrufen

$userid = $_SESSION['sesid'];
try {
    $statementanzeige = $db->prepare("SELECT * FROM notizen 
                                WHERE userid = :userid AND notiz_status = 1 
                                ORDER BY notiz_bearbeitet DESC");
    $statementanzeige->bindParam(':userid', $userid);
    $statementanzeige->execute();
    $notizen = $statementanzeige->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Fehler beim Abrufen der Notizen: " . $e->getMessage());
}


?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Meine Notizen</title>
    <link rel="stylesheet" href="../nav.css">
</head>
<body>
    <nav>
        <a class="homebutton" href="../index.html">🏠</a>
        <a href="notizen.php">Notizen</a>
        <a href="../todo/todo.php">To-Do</a>
        <a href="../event/event.php">Timer</a>
        <a href="../user/login.php">Login</a>
    </nav>

    <button onclick="document.getElementById('notizForm').style.display='block';">Notiz Erstellen</button>
    <div id="notizForm" style="display:none;">
        <form action="" method="post">
            <input type="text" name="notiz_name" placeholder="Notiz Titel">
            <br>
            <textarea name="notiz_inhalt" placeholder="Notiz Inhalt"></textarea>
            <br>
            <button type="submit" name="neue_notiz">Speichern</button>
        </form>
    </div>

    <?php
  
    foreach ($notizen as $notiz) {
        echo "<h2>" . $notiz['notiz_name'] . "</h2>";
        echo "<p>" . $notiz['notiz_inhalt'] . "</p>";
        echo "<p>Erstellt: " . $notiz['notiz_erstellt'] . "</p>";
        echo "<p>Bearbeitet: " . $notiz['notiz_bearbeitet'] . "</p>";
        // Noch machen Löschen und Bearbeiten
        echo "<button> Bearbeiten </button>";
        echo "<button> Löschen </button>";
        echo "<hr>";
    }
    ?>
</body>
</html>