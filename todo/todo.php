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
        header("Location: todo.php");
        exit;
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
        header("Location: todo.php");
        exit;
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
        header("Location: todo.php");
        exit;
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
        header("Location: todo.php");
        exit;
    }
}

// Abschnitt 6: Funktion To-Do Permanent Löschen
function todoPermanentLoeschen(PDO $db) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['todo_permanent_loeschen'])) {
        $userid = $_SESSION['sesid'];
        $todoid = $_POST['todoid'];

        try {
            $statement = $db->prepare("UPDATE todo SET todo_status = 5 WHERE userid = :userid AND todoid = :todoid");
            $statement->bindParam(':userid', $userid);
            $statement->bindParam(':todoid', $todoid);
            $statement->execute();
        } catch (PDOException $e) {
            die("To-Do konnte nicht permanent gelöscht werden: " . $e->getMessage());
        }
        header("Location: todo.php");
        exit;
    }
}

// Process POST requests BEFORE any output is sent
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    todoSpeichern($db);
    todoBearbeiten($db);
    todoLoeschen($db);
    todoStatus($db);
    todoPermanentLoeschen($db);
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
</head>
<body>
    <nav>
        <a class="homebutton" href="../index.html">🏠</a>
        <a href="../notes/notizen.php">Notizen</a>
        <a href="../todo/todo.php">To-Do</a>
        <a href="../event/event.php">Event</a>
        <a href="../user/login.php">Login</a> 
    </nav>

    <div class="todo-form">
        <form method="POST" action="">
            <h2>Neues To-Do erstellen</h2>
            <input type="text" name="todo_titel" placeholder="Titel" required>
            <textarea name="todo_inhalt" placeholder="Inhalt" required></textarea>
            <button type="submit" name="neue_todo">Hinzufügen</button>
            <a href="todo.edit.php" class="bearbeiten_knopf">Bearbeiten</a>
        </form>
    </div>
    
   
    <div class="todo-container">  <!-- To-Do Listen Anzeigungscontainer -->
        
        <div class="todo-zustand">
            <div class="todo-ueberschrift">To-Do</div>
            <?php
            
            // Alle Todos die den Status 1 haben also ganz links sind anzeigen.
            $stmt = $db->prepare("SELECT * FROM todo WHERE userid = :userid AND todo_status = 1 ORDER BY todo_erstellt DESC");
            $stmt->bindParam(':userid', $_SESSION['sesid']);
            $stmt->execute();
            
            while ($todo = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo '<div class="todo-zeug" data-id="' . $todo['todoid'] . '">';
                echo '<div class="todo-titel">' . $todo['todo_titel'] . '</div>';
                echo '<div class="todo-inhalt">' . $todo['todo_inhalt'] . '</div>';
                echo '<div class="todo-zeit">Erstellt: ' . $todo['todo_erstellt'] . '</div>';
                echo '<div class="todo-knopf">';
                echo '<form method="POST" style="display: inline;">';
                echo '<input type="hidden" name="todoid" value="' . $todo['todoid'] . '">';
                echo '<input type="hidden" name="status" value="2">';
                echo '<button type="submit" name="todo_status" class="bearbeiten_knopf">→</button>';
                echo '</form>';
                echo '<form method="POST" style="display: inline;">';
                echo '<input type="hidden" name="todoid" value="' . $todo['todoid'] . '">';
                echo '<button type="submit" name="todo_loeschen" class="loesch_knopf">Löschen</button>';
                echo '</form>';
                echo '</div>';
                echo '</div>';
            }
            ?>
        </div>
        
        <!-- Spalte 2: In Bearbeitung -->
        <div class="todo-zustand">
            <div class="todo-ueberschrift">In Bearbeitung</div>
            <?php
            // Todos mit Status 2 abrufen
            $stmt = $db->prepare("SELECT * FROM todo WHERE userid = :userid AND todo_status = 2 ORDER BY todo_erstellt DESC");
            $stmt->bindParam(':userid', $_SESSION['sesid']);
            $stmt->execute();
            
            while ($todo = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo '<div class="todo-zeug" data-id="' . $todo['todoid'] . '">';
                echo '<div class="todo-titel">' . $todo['todo_titel'] . '</div>';
                echo '<div class="todo-inhalt">' . $todo['todo_inhalt'] . '</div>';
                echo '<div class="todo-zeit">Bearbeitet: ' . $todo['todo_erstellt'] . '</div>';
                echo '<div class="todo-knopf">';
                echo '<form method="POST" style="display: inline;">';
                echo '<input type="hidden" name="todoid" value="' . $todo['todoid'] . '">';
                echo '<input type="hidden" name="status" value="3">';
                echo '<button type="submit" name="todo_status" class="bearbeiten_knopf">→</button>';
                echo '</form>';
                echo '<form method="POST" style="display: inline;">';
                echo '<input type="hidden" name="todoid" value="' . $todo['todoid'] . '">';
                echo '<button type="submit" name="todo_loeschen" class="loesch_knopf">Löschen</button>';
                echo '</form>';
                echo '</div>';
                echo '</div>';
            }
            ?>
        </div>
        
        <!-- Spalte 3: Erledigt -->
        <div class="todo-zustand">
            <div class="todo-ueberschrift">Erledigt</div>
            <?php
            // Todos mit Status 3 abrufen
            $stmt = $db->prepare("SELECT * FROM todo WHERE userid = :userid AND todo_status = 3 ORDER BY todo_erstellt DESC");
            $stmt->bindParam(':userid', $_SESSION['sesid']);
            $stmt->execute();
            
            while ($todo = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo '<div class="todo-zeug" data-id="' . $todo['todoid'] . '">';
                echo '<div class="todo-titel">' . $todo['todo_titel'] . '</div>';
                echo '<div class="todo-inhalt">' . $todo['todo_inhalt'] . '</div>';
                echo '<div class="todo-zeit">Erledigt: ' . $todo['todo_erstellt'] . '</div>';
                echo '<div class="todo-knopf">';
                echo '<form method="POST" style="display: inline;">';
                echo '<input type="hidden" name="todoid" value="' . $todo['todoid'] . '">';
                echo '<input type="hidden" name="status" value="1">';
                echo '<button type="submit" name="todo_status" class="bearbeiten_knopf">↺</button>';
                echo '</form>';
                echo '<form method="POST" style="display: inline;">';
                echo '<input type="hidden" name="todoid" value="' . $todo['todoid'] . '">';
                echo '<button type="submit" name="todo_loeschen" class="loesch_knopf">Löschen</button>';
                echo '</form>';
                echo '</div>';
                echo '</div>';
            }
            ?>
        </div>
        
        <!-- Spalte 4: Gelöscht -->
        <div class="todo-zustand">
            <div class="todo-ueberschrift">Papierkorb</div>
            <?php
            // Todos mit Status 4 abrufen (gelöscht)
            $stmt = $db->prepare("SELECT * FROM todo WHERE userid = :userid AND todo_status = 4 ORDER BY todo_erstellt DESC LIMIT 10");
            $stmt->bindParam(':userid', $_SESSION['sesid']);
            $stmt->execute();
            
            while ($todo = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo '<div class="todo-zeug" data-id="' . $todo['todoid'] . '">';
                echo '<div class="todo-titel">' . $todo['todo_titel'] . '</div>';
                echo '<div class="todo-inhalt">' . $todo['todo_inhalt'] . '</div>';
                echo '<div class="todo-zeit">Gelöscht: ' . $todo['todo_erstellt'] . '</div>';
                echo '<div class="todo-knopf">';

                // Knopf Für Wiederherstelen
                echo '<form method="POST" style="display: inline;">';
                echo '<input type="hidden" name="todoid" value="' . $todo['todoid'] . '">';
                echo '<input type="hidden" name="status" value="1">';
                echo '<button type="submit" name="todo_status" class="bearbeiten_knopf">Wiederherstellen</button>';
                echo '</form>';


                // Knopf Löschen
                echo '<form method="POST" style="display: inline;">';
                echo '<input type="hidden" name="todoid" value="' . $todo['todoid'] . '">';
                echo '<button type="submit" name="todo_permanent_loeschen" class="loesch_knopf">Endgültig löschen</button>';
                echo '</form>';
                echo '</div>';
                echo '</div>';
            }
            ?>
        </div>
    </div>

</body>
</html>