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

// Funktion zum Erstellen eines neuen Ereignisses
function createEvent($userid, $ecd_name, $ecd_datum, $ecd_inhalt) {
    global $db;
    try {
        // Daten in die Tabelle 'ereignisscountdown' einfügen
        $sql = "INSERT INTO ereignisscountdown (userid, ecd_name, ecd_datum, ecd_inhalt) 
                VALUES (:userid, :ecd_name, :ecd_datum, :ecd_inhalt)";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':userid', $userid);
        $stmt->bindParam(':ecd_name', $ecd_name);
        $stmt->bindParam(':ecd_datum', $ecd_datum);
        $stmt->bindParam(':ecd_inhalt', $ecd_inhalt);
        $stmt->execute();
        echo "Ereignis erfolgreich erstellt!";
    } catch (PDOException $e) {
        echo "Fehler beim Erstellen des Ereignisses: " . $e->getMessage();
    }
}

// Funktion zum Bearbeiten eines Ereignisses
function editEvent($ecdid, $ecd_name, $ecd_datum, $ecd_inhalt) {
    global $db;
    try {
        // Ereignis in der Tabelle 'ereignisscountdown' aktualisieren
        $sql = "UPDATE ereignisscountdown 
                SET ecd_name = :ecd_name, ecd_datum = :ecd_datum, ecd_inhalt = :ecd_inhalt
                WHERE ecdid = :ecdid";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':ecdid', $ecdid);
        $stmt->bindParam(':ecd_name', $ecd_name);
        $stmt->bindParam(':ecd_datum', $ecd_datum);
        $stmt->bindParam(':ecd_inhalt', $ecd_inhalt);
        $stmt->execute();
        echo "Ereignis erfolgreich bearbeitet!";
    } catch (PDOException $e) {
        echo "Fehler beim Bearbeiten des Ereignisses: " . $e->getMessage();
    }
}

// Funktion zum Löschen eines Ereignisses
function deleteEvent($ecdid) {
    global $db;
    try {
        // Ereignis aus der Tabelle 'ereignisscountdown' löschen
        $sql = "DELETE FROM ereignisscountdown WHERE ecdid = :ecdid";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':ecdid', $ecdid);
        $stmt->execute();
        echo "Ereignis erfolgreich gelöscht!";
    } catch (PDOException $e) {
        echo "Fehler beim Löschen des Ereignisses: " . $e->getMessage();
    }
}

// Abfrage der Ereignisse aus der Datenbank
try {
    $sql = "SELECT * FROM ereignisscountdown WHERE userid = :userid";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':userid', $_SESSION['sesid']); // Nimm die Benutzer-ID aus der Session
    $stmt->execute();
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Fehler beim Abrufen der Ereignisse: " . $e->getMessage();
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Event Timer</title>
    <link rel="stylesheet" href="event.css">
    <link rel="stylesheet" href="../nav.css">
</head>
<body>

    <nav>
        <a class="homebutton" href="../index.html">🏠</a>
        <a href="../notes/notizen.php">Notizen</a>
        <a href="../todo/todo.php">To-Do</a>
        <a href="../event/event.php">Timer</a>
        <a href="../user/login.php">Login</a>
    </nav>
    <form method="post" class="event-form">
    <h2>Event hinzufügen</h2>
        <input type="text" name="ecd_name" placeholder="Event Name" required>
        <input type="date" name="ecd_datum" required>
        <textarea name="ecd_inhalt" placeholder="Beschreibung"></textarea>
        <button type="submit" name="add">Hinzufügen</button>
    </form>
    
    <h2>Events</h2>
    <ul>
        <?php if (isset($events) && count($events) > 0): ?>
            <?php foreach ($events as $event): ?>
                <li>
                    <strong><?= htmlspecialchars($event['ecd_name']) ?></strong>
                    <?= htmlspecialchars($event['ecd_datum']) ?><br>
                    <?= htmlspecialchars($event['ecd_inhalt']) ?>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="userid" value="1">
                        <input type="hidden" name="ecdid" value="<?= $event['ecdid'] ?>">
                        <input type="text" name="ecd_name" value="<?= htmlspecialchars($event['ecd_name']) ?>">
                        <input type="date" name="ecd_datum" value="<?= $event['ecd_datum'] ?>">
                        <textarea name="ecd_inhalt"><?= htmlspecialchars($event['ecd_inhalt']) ?></textarea>
                        <button type="submit" name="edit">Bearbeiten</button>
                        <button type="submit" name="delete">Löschen</button>
                    </form>
                </li>
            <?php endforeach; ?>
        <?php else: ?>
            <li>Es sind keine Ereignisse vorhanden.</li>
        <?php endif; ?>
    </ul>
</body>
</html>
