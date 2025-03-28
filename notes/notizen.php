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
NotizLoeschen($db);

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

// Notiz Löschen Abschnitt 4:

function NotizLoeschen(PDO $db) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['loeschen'])) {
        $userid = $_SESSION['sesid'];
        try {
            $statement = $db->prepare("UPDATE notizen SET notiz_status = 0 WHERE userid = :userid AND notizid = :notizid");
            $statement->bindParam(':userid', $userid);
            $statement->bindParam(':notizid', $_POST['notizid']);
            $statement->execute();
        } catch (PDOException $e) {
            die("Notiz konnte nicht gelöscht werden: " . $e->getMessage());
        }
    }
}


?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Meine Notizen</title>
    <link rel="stylesheet" href="../nav.css">
    <link rel="stylesheet" href="notizen.css"> 
</head>
<body>
    <nav>
        <a class="homebutton" href="../index.php">🏠</a>
        <a href="notizen.php">Notizen</a>
        <a href="../todo/todo.php">To-Do</a>
        <a href="../event/event.php">Event</a>
        <a href="../user/login.php">Login</a>
        <a class="Ausloggen" href="../user/logout.php"><img src="../images/logout.png" alt=""> </a>
    </nav>


    <div id="notizForm">
        <form action="" method="post" name="notizen-form">
            <input type="text" name="notiz_name" placeholder="Notiz Titel">
            <br>
            <textarea name="notiz_inhalt" placeholder="Notiz Inhalt"></textarea>
            <br>
            <button type="submit" name="neue_notiz">Speichern</button>
        </form>
    </div>
    
    <div class="note-container">
    <?php
    foreach ($notizen as $notiz) {
        echo '<div class="note-feld">';
        echo "<h2>" . $notiz['notiz_name'] . "</h2>";
        echo "<p>" . $notiz['notiz_inhalt'] . "</p>";
        echo "<p>Erstellt: " .$notiz['notiz_erstellt'] . "</p>";
        echo "<p>Bearbeitet: " .$notiz['notiz_bearbeitet'] . "</p>";
        echo "<a class='bearbeiten' href='notizen.edit.php?notizid=" .$notiz['notizid'] . "'>Bearbeiten</a>";
        echo "<form action='' method='post' style='display:inline;'>";
        echo "<input type='hidden' name='notizid' value='" . $notiz['notizid'] . "'>";
        echo "<button type='submit' name='loeschen'>Löschen</button>";
        echo "</form>";
        echo "<hr>";
        echo "</div>";
    }
    ?>
    </div>
</body>
</html>