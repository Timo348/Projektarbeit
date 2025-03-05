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

// Datenbankobjekte: Tabelle todo (userid, todoid, todo_titel, todo_inhalt, todo_erstellt, todo_status)
// Abschnitt 2: Funktion To-Do Speichern

// Status 1 = To-Do, 2 = In Bearbeitung, 3 = Erledigt 4 = Gelöscht
function todoSpeichern(PDO $db) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['neue_todo'])) {
        $userid = $_SESSION['sesid'];
        $zeit = date('Y-m-d H:i:s');

        $todoTitel  = trim($_POST['todo_titel']);
        $todoInhalt = trim($_POST['todo_inhalt']);
        $status = 1; 

        try {
            $statement = $db->prepare("INSERT INTO todo 
            (userid, todo_titel, todo_inhalt, todo_erstellt, todo_status) 
            VALUES (:userid, :titel, :inhalt, :erstellt, :status)");
            $statement->bindParam(':userid', $userid);
            $statement->bindParam(':titel', $todoTitel);
            $statement->bindParam(':inhalt', $todoInhalt);
            $statement->bindParam(':erstellt', $zeit);
            $statement->bindParam(':status', $status);
            $statement->execute();
        } catch (PDOException $e) {
            die("To-Do konnte nicht eingefügt werden: " . $e->getMessage());
        }
    }
}

// Abschnitt 3: Funktion To-Do Bearbeitung

function todoBearbeiten(PDO $db) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['todo_bearbeiten'])) {
        $userid = $_SESSION['sesid'];
        $zeit = date('Y-m-d H:i:s');

        $todoTitel = trim($_POST['todo_titel']);
        $todoInhalt = trim($_POST['todo_inhalt']);

        try {
            $statement = $db->prepare("UPDATE todo SET todo_titel = :titel, todo_inhalt = :inhalt, todo_erstellt = :erstellt WHERE userid = :userid AND todoid = :todoid");
            $statement->bindParam(':userid', $userid);
            $statement->bindParam(':titel', $todoTitel);
            $statement->bindParam(':inhalt', $todoInhalt);
            $statement->bindParam(':erstellt', $zeit);
            $statement->execute();
        } catch (PDOException $e) {
            die("To-Do konnte nicht bearbeitet werden: " . $e->getMessage());
        }
    }
}

// Abschnitt 4: Funktion To-Do Löschung

function todoLoeschen(PDO $db) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['todo_loeschen'])) {
        $userid = $_SESSION['sesid'];
        $todoid = $_POST['todoid'];

        try {
            $statement = $db->prepare("UPDATE todo SET todo_status = 4 WHERE userid = :userid AND todoid = :todoid");
            $statement->bindParam(':userid', $userid);
            $statement->bindParam(':todoid', $todoid);
            $statement->execute();
        } catch (PDOException $e) {
            die("To-Do konnte nicht gelöscht werden: " . $e->getMessage());
        }
    }
}

// Abschnitt 5: Funktion To-Do Status

function todoStatus(PDO $db) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['todo_status'])) {
        $userid = $_SESSION['sesid'];
        $todoid = $_POST['todoid'];
        $status = $_POST['status'];

        try {
            $statement = $db->prepare("UPDATE todo SET todo_status = :status WHERE userid = :userid AND todoid = :todoid");
            $statement->bindParam(':userid', $userid);
            $statement->bindParam(':todoid', $todoid);
            $statement->bindParam(':status', $status);
            $statement->execute();
        } catch (PDOException $e) {
            die("To-Do Status konnte nicht geändert werden: " . $e->getMessage());
        }
    }
}

// Abschnitt 6: Funktion To-Do Permanent Löschen
function todoPermanentLoeschen(PDO $db) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['todo_permanent_loeschen'])) {
        $userid = $_SESSION['sesid'];
        $todoid = $_POST['todoid'];

        try {
            // Option 1: Set status to 5 (permanent deleted)
            $statement = $db->prepare("UPDATE todo SET todo_status = 5 WHERE userid = :userid AND todoid = :todoid");
            $statement->bindParam(':userid', $userid);
            $statement->bindParam(':todoid', $todoid);
            $statement->execute();
            
            // Option 2 (alternative): Completely delete from database
            // $statement = $db->prepare("DELETE FROM todo WHERE userid = :userid AND todoid = :todoid");
            // $statement->bindParam(':userid', $userid);
            // $statement->bindParam(':todoid', $todoid);
            // $statement->execute();
        } catch (PDOException $e) {
            die("To-Do konnte nicht permanent gelöscht werden: " . $e->getMessage());
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do</title>
    <link rel="stylesheet" href="../nav.css">
    <link rel="stylesheet" href="todo.css">
    <script src="todo.js"></script>
</head>
<body>
    <nav>
        <a class="homebutton" href="../index.html">🏠</a>
        <a href="notizen.php">Notizen</a>
        <a href="../todo/todo.php">To-Do</a>
        <a href="../event/event.php">Timer</a>
        <a href="../user/login.php">Login</a>
    </nav>

    <!-- Form to add new todos -->
    <div class="todo-form">
        <form method="POST" action="">
            <h2>Neues To-Do erstellen</h2>
            <input type="text" name="todo_titel" placeholder="Titel" required>
            <textarea name="todo_inhalt" placeholder="Inhalt" required></textarea>
            <button type="submit" name="neue_todo">Hinzufügen</button>
        </form>
    </div>
    
    <!-- 4 Spalten für die Jeweiligen To-Do mit jeweils Titel, Inhalt, Zeit Ganz Unten angezeigt, Bearbeitungsknopf und Lösch knopf, Todos Sollen verschoben werden können -->
    <div class="todo-container">
        <!-- Spalte 1: Neu/Zu machen -->
        <div class="todo-column">
            <div class="column-header">To-Do</div>
            <?php
            // Todos mit Status 1 abrufen
            $stmt = $db->prepare("SELECT * FROM todo WHERE userid = :userid AND todo_status = 1 ORDER BY todo_erstellt DESC");
            $stmt->bindParam(':userid', $_SESSION['sesid']);
            $stmt->execute();
            
            while ($todo = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo '<div class="todo-item" data-id="' . $todo['todoid'] . '">';
                echo '<div class="todo-title">' . htmlspecialchars($todo['todo_titel']) . '</div>';
                echo '<div class="todo-content">' . htmlspecialchars($todo['todo_inhalt']) . '</div>';
                echo '<div class="todo-time">Erstellt: ' . $todo['todo_erstellt'] . '</div>';
                echo '<div class="todo-buttons">';
                echo '<form method="POST" style="display: inline;">';
                echo '<input type="hidden" name="todoid" value="' . $todo['todoid'] . '">';
                echo '<input type="hidden" name="status" value="2">';
                echo '<button type="submit" name="todo_status" class="edit-btn">→</button>';
                echo '</form>';
                echo '<button class="edit-btn">Bearbeiten</button>';
                echo '<form method="POST" style="display: inline;">';
                echo '<input type="hidden" name="todoid" value="' . $todo['todoid'] . '">';
                echo '<button type="submit" name="todo_loeschen" class="delete-btn">Löschen</button>';
                echo '</form>';
                echo '</div>';
                echo '</div>';
            }
            ?>
        </div>
        
        <!-- Spalte 2: In Bearbeitung -->
        <div class="todo-column">
            <div class="column-header">In Bearbeitung</div>
            <?php
            // Todos mit Status 2 abrufen
            $stmt = $db->prepare("SELECT * FROM todo WHERE userid = :userid AND todo_status = 2 ORDER BY todo_erstellt DESC");
            $stmt->bindParam(':userid', $_SESSION['sesid']);
            $stmt->execute();
            
            while ($todo = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo '<div class="todo-item" data-id="' . $todo['todoid'] . '">';
                echo '<div class="todo-title">' . htmlspecialchars($todo['todo_titel']) . '</div>';
                echo '<div class="todo-content">' . htmlspecialchars($todo['todo_inhalt']) . '</div>';
                echo '<div class="todo-time">Bearbeitet: ' . $todo['todo_erstellt'] . '</div>';
                echo '<div class="todo-buttons">';
                echo '<form method="POST" style="display: inline;">';
                echo '<input type="hidden" name="todoid" value="' . $todo['todoid'] . '">';
                echo '<input type="hidden" name="status" value="3">';
                echo '<button type="submit" name="todo_status" class="edit-btn">→</button>';
                echo '</form>';
                echo '<button class="edit-btn">Bearbeiten</button>';
                echo '<form method="POST" style="display: inline;">';
                echo '<input type="hidden" name="todoid" value="' . $todo['todoid'] . '">';
                echo '<button type="submit" name="todo_loeschen" class="delete-btn">Löschen</button>';
                echo '</form>';
                echo '</div>';
                echo '</div>';
            }
            ?>
        </div>
        
        <!-- Spalte 3: Erledigt -->
        <div class="todo-column">
            <div class="column-header">Erledigt</div>
            <?php
            // Todos mit Status 3 abrufen
            $stmt = $db->prepare("SELECT * FROM todo WHERE userid = :userid AND todo_status = 3 ORDER BY todo_erstellt DESC");
            $stmt->bindParam(':userid', $_SESSION['sesid']);
            $stmt->execute();
            
            while ($todo = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo '<div class="todo-item" data-id="' . $todo['todoid'] . '">';
                echo '<div class="todo-title">' . htmlspecialchars($todo['todo_titel']) . '</div>';
                echo '<div class="todo-content">' . htmlspecialchars($todo['todo_inhalt']) . '</div>';
                echo '<div class="todo-time">Erledigt: ' . $todo['todo_erstellt'] . '</div>';
                echo '<div class="todo-buttons">';
                echo '<form method="POST" style="display: inline;">';
                echo '<input type="hidden" name="todoid" value="' . $todo['todoid'] . '">';
                echo '<input type="hidden" name="status" value="1">';
                echo '<button type="submit" name="todo_status" class="edit-btn">↺</button>';
                echo '</form>';
                echo '<form method="POST" style="display: inline;">';
                echo '<input type="hidden" name="todoid" value="' . $todo['todoid'] . '">';
                echo '<button type="submit" name="todo_loeschen" class="delete-btn">Löschen</button>';
                echo '</form>';
                echo '</div>';
                echo '</div>';
            }
            ?>
        </div>
        
        <!-- Spalte 4: Gelöscht -->
        <div class="todo-column">
            <div class="column-header">Papierkorb</div>
            <?php
            // Todos mit Status 4 abrufen (gelöscht)
            $stmt = $db->prepare("SELECT * FROM todo WHERE userid = :userid AND todo_status = 4 ORDER BY todo_erstellt DESC LIMIT 10");
            $stmt->bindParam(':userid', $_SESSION['sesid']);
            $stmt->execute();
            
            while ($todo = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo '<div class="todo-item" data-id="' . $todo['todoid'] . '">';
                echo '<div class="todo-title">' . htmlspecialchars($todo['todo_titel']) . '</div>';
                echo '<div class="todo-content">' . htmlspecialchars($todo['todo_inhalt']) . '</div>';
                echo '<div class="todo-time">Gelöscht: ' . $todo['todo_erstellt'] . '</div>';
                echo '<div class="todo-buttons">';
                // Existing "Wiederherstellen" button
                echo '<form method="POST" style="display: inline;">';
                echo '<input type="hidden" name="todoid" value="' . $todo['todoid'] . '">';
                echo '<input type="hidden" name="status" value="1">';
                echo '<button type="submit" name="todo_status" class="edit-btn">Wiederherstellen</button>';
                echo '</form>';
                // New "Endgültig löschen" button
                echo '<form method="POST" style="display: inline;">';
                echo '<input type="hidden" name="todoid" value="' . $todo['todoid'] . '">';
                echo '<button type="submit" name="todo_permanent_loeschen" class="delete-btn">Endgültig löschen</button>';
                echo '</form>';
                echo '</div>';
                echo '</div>';
            }
            ?>
        </div>
    </div>

    <?php
    // Funktionen aufrufen
    todoSpeichern($db);
    todoBearbeiten($db);
    todoLoeschen($db);
    todoStatus($db);
    todoPermanentLoeschen($db);
    ?>

</body>
</html>