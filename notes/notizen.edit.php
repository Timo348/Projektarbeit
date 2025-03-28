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


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['bearbeiten'])) {
        NotizBearbeiten($db);
        header("Location: notizen.php");
        exit;
    } elseif (isset($_POST['loeschen'])) {
        NotizLoeschen($db);
        header("Location: notizen.php");
        exit;
    }
}

$userid = $_SESSION['sesid'];
try {
    $statementanzeige = $db->prepare("SELECT * FROM notizen 
                                WHERE userid = :userid 
                                ORDER BY notiz_bearbeitet DESC");
    $statementanzeige->bindParam(':userid', $userid);
    $statementanzeige->execute();
    $notizen = $statementanzeige->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Fehler beim Abrufen der Notizen: " . $e->getMessage());
}


// Funktion um Notiz zu bearbeiten

function NotizBearbeiten(PDO $db) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bearbeiten'])) {
        $userid = $_SESSION['sesid'];
        $zeit = date('Y-m-d H:i:s');

        $notenName = trim($_POST['notiz_name']);
        $notenInhalt = trim($_POST['notiz_inhalt']);


        try {
            $statement = $db->prepare("UPDATE notizen SET notiz_name = :name, notiz_inhalt = :inhalt, notiz_bearbeitet = :bearbeitet WHERE userid = :userid AND notizid = :notizid");
            $statement->bindParam(':userid', $userid);
            $statement->bindParam(':name', $notenName);
            $statement->bindParam(':inhalt', $notenInhalt);
            $statement->bindParam(':bearbeitet', $zeit);
            $statement->bindParam(':notizid', $_POST['notizid']);
            $statement->execute();
        } catch (PDOException $e) {
            die("Notiz konnte nicht bearbeitet werden: " . $e->getMessage());
        }
    }
}

function NotizLoeschen(PDO $db) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['loeschen'])) {
        $userid = $_SESSION['sesid'];
        try {
            $statement = $db->prepare("UPDATE notizen  WHERE userid = :userid AND notizid = :notizid");
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
        <a class="homebutton" href="../index.html">🏠</a>
        <a href="notizen.php">Notizen</a>
        <a href="../todo/todo.php">To-Do</a>
        <a href="../event/event.php">Event</a>
        <a href="../user/login.php">Login</a>
    </nav>

    
    <div class="note-container">
    <?php
    foreach ($notizen as $notiz) {
        echo '<div class="note-feld">';
        echo '<form method="post">';
        echo '<input type="hidden" name="notizid" value="' . $notiz['notizid'] . '">';
        echo '<input type="text" name="notiz_name" value="' . $notiz['notiz_name'] . '">';
        echo '<textarea name="notiz_inhalt" rows="5">' . $notiz['notiz_inhalt'] . '</textarea>';
        echo "<p>Erstellt: " . $notiz['notiz_erstellt'] . "</p>";
        echo "<p>Bearbeitet: " . $notiz['notiz_bearbeitet'] . "</p>";
        echo '<button type="submit" name="bearbeiten">Speichern</button>';
        echo '<button type="submit" name="loeschen">Löschen</button>';
        echo '</form>';
        echo "<hr>";
        echo "</div>";
    }
    ?>
    </div>
</body>
</html>