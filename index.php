<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Projektarbeit-Homepage</title>
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="nav.css">
</head>
<body>
<nav>
    <a class="homebutton" href="index.php">🏠</a>
    <a href="notes/notizen.php">Notizen</a>
    <a href="todo/todo.php">To-Do</a>
    <a href="event/event.php">Event</a>
    <a href="user/login.php">Login</a>
    <a class="Ausloggen" href="user/logout.php"><img src="images/logout.png" alt=""> </a>
</nav>

<?php
session_start();
if (!isset($_SESSION['sesid'])) {
    echo "<h1>Sie sind nicht eingeloggt</h1>";
    echo "<h2>Bitte loggen Sie sich ein</h2>";
    echo "<button><a href='user/login.php'>Einloggen</a></button>";
}
else {
    echo "<h1>Willkommen auf unserer Website von unserer Projektarbeit :D </h1>";
    echo "<h2>Sie sind eingeloggt als " . $_SESSION['sesuser'] . "</h2>";
    // Letze bearbeitete sache von Notizen, Event und 2 Letze Todo anzeigen noch Programmieren

    try {
        
            $db = new PDO('sqlite:' . __DIR__ . '/database/projektdatenbank.db');
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Datenbankverbindung fehlgeschlagen: " . $e->getMessage());
        }

        $stmt = $db->prepare("SELECT * FROM notizen ORDER BY notizid DESC LIMIT 1");
        $stmt->execute();
        $notiz = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($notiz) {
            echo "<h2>Letzte Notiz:</h2>";
            echo "<h2>" . $notiz['notiz_name'] . "</h2>";
            echo "<p>" . $notiz['notiz_inhalt'] . "</p>";
            echo "<p>Erstellt: " .$notiz['notiz_erstellt'] . "</p>";
            echo "<p>Bearbeitet: " .$notiz['notiz_bearbeitet'] . "</p>";
        } else {
            echo "<h2>Keine Notizen gefunden.</h2>";
        }

    }








?>


</body>
</html>