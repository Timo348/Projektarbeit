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

$userid = $_SESSION['sesid']; // Benutzer-ID aus der Session holen

// Event hinzufügen
if (isset($_POST['add'])) {
    $ecd_name = $_POST['ecd_name'];
    $ecd_datum = $_POST['ecd_datum'];
    $ecd_inhalt = $_POST['ecd_inhalt'];

    try {
        $sql = "INSERT INTO ereignisscountdown (userid, ecd_name, ecd_datum, ecd_inhalt) 
                VALUES (:userid, :ecd_name, :ecd_datum, :ecd_inhalt)";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':userid', $userid);
        $stmt->bindParam(':ecd_name', $ecd_name);
        $stmt->bindParam(':ecd_datum', $ecd_datum);
        $stmt->bindParam(':ecd_inhalt', $ecd_inhalt);
        $stmt->execute();

        // Nach erfolgreichem Einfügen die Seite neuladen
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;

    } catch (PDOException $e) {
        echo "Fehler beim Erstellen des Ereignisses: " . $e->getMessage();
    }
}

// Event bearbeiten
if (isset($_POST['edit'])) {
    $ecdid = $_POST['ecdid'];
    $ecd_name = $_POST['ecd_name'];
    $ecd_datum = $_POST['ecd_datum'];
    $ecd_inhalt = $_POST['ecd_inhalt'];

    try {
        $sql = "UPDATE ereignisscountdown 
                SET ecd_name = :ecd_name, ecd_datum = :ecd_datum, ecd_inhalt = :ecd_inhalt
                WHERE ecdid = :ecdid AND userid = :userid";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':ecdid', $ecdid);
        $stmt->bindParam(':userid', $userid);
        $stmt->bindParam(':ecd_name', $ecd_name);
        $stmt->bindParam(':ecd_datum', $ecd_datum);
        $stmt->bindParam(':ecd_inhalt', $ecd_inhalt);
        $stmt->execute();

        header("Location: " . $_SERVER['PHP_SELF']);
        exit;

    } catch (PDOException $e) {
        echo "Fehler beim Bearbeiten des Ereignisses: " . $e->getMessage();
    }
}

// Event löschen
if (isset($_POST['delete'])) {
    $ecdid = $_POST['ecdid'];

    try {
        $sql = "DELETE FROM ereignisscountdown WHERE ecdid = :ecdid AND userid = :userid";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':ecdid', $ecdid);
        $stmt->bindParam(':userid', $userid);
        $stmt->execute();

        header("Location: " . $_SERVER['PHP_SELF']);
        exit;

    } catch (PDOException $e) {
        echo "Fehler beim Löschen des Ereignisses: " . $e->getMessage();
    }
}

// Ereignisse aus der Datenbank abrufen
try {
    $sql = "SELECT * FROM ereignisscountdown WHERE userid = :userid ORDER BY ecd_datum DESC";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':userid', $userid);
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
        <a href="../event/event.php">Event</a>
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
        <?php if (!empty($events)): ?>
            <?php foreach ($events as $event): ?>
                <li>
                <div class="event-container">
                    <div class="event-zustand">

                        <div class="event-name"><?= htmlspecialchars($event['ecd_name'])?></div>
                        <div class="event-inhalt"><?= htmlspecialchars($event['ecd_inhalt'])?></div>
                        <div class="event-datum"><?= htmlspecialchars($event['ecd_datum'])?></div>
                        <div class="bearbeit-bereich">Bearbeiten:</div>
                        
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="ecdid" value="<?= $event['ecdid']?>">
                        <input type="text" name="ecd_name" value="<?= htmlspecialchars($event['ecd_name'])?>">
                        <input type="date" name="ecd_datum" value="<?= $event['ecd_datum'] ?>">
                        <textarea name="ecd_inhalt"><?= htmlspecialchars($event['ecd_inhalt']) ?></textarea>
                        <button type="submit" name="edit" class="bearbeiten">Bearbeiten</button>
                        <button type="submit" name="delete" class="loeschen">Löschen</button>
                    </form>
                    </div>
                </div>
                </li>
            <?php endforeach; ?>
        <?php else: ?>
            <li>Es sind keine Ereignisse vorhanden.</li>
        <?php endif; ?>
    </ul>
</body>
</html>
