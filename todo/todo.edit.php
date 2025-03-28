<?php

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
        todoBearbeiten($db);
        header("Location: todo.php");
        exit;
    } elseif (isset($_POST['loeschen'])) {
        todoLoeschen($db);
        header("Location: todo.php");
        exit;
    }
}

$userid = $_SESSION['sesid'];
try {
    $stmt = $db->prepare("SELECT * FROM todo WHERE userid = :userid ORDER BY todo_erstellt DESC");
    $stmt->bindParam(':userid', $userid);
    $stmt->execute();
    $todos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Fehler beim Abrufen der Todos: " . $e->getMessage());
}

function todoBearbeiten(PDO $db) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bearbeiten'])) {
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
            $statement->bindParam(':todoid', $_POST['todoid']);
            $statement->execute();
        } catch (PDOException $e) {
            die("Todo konnte nicht bearbeitet werden: " . $e->getMessage());
        }
    }
}

function todoLoeschen(PDO $db) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['loeschen'])) {
        $userid = $_SESSION['sesid'];
        try {
            $statement = $db->prepare("UPDATE todo SET todo_status = 4 WHERE userid = :userid AND todoid = :todoid");
            $statement->bindParam(':userid', $userid);
            $statement->bindParam(':todoid', $_POST['todoid']);
            $statement->execute();
        } catch (PDOException $e) {
            die("Todo konnte nicht gelöscht werden: " . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Todo Bearbeiten</title>
    <link rel="stylesheet" href="../nav.css">
    <link rel="stylesheet" href="todo.css"> 
</head>
<body>
    <nav>
        <a class="homebutton" href="../index.html">🏠</a>
        <a href="../notes/notizen.php">Notizen</a>
        <a href="todo.php">To-Do</a>
        <a href="../event/event.php">Event</a>
        <a href="../user/login.php">Login</a>
    </nav>

    <div class="todo-container">
    <?php
    foreach ($todos as $todo) {
        echo '<div class="todo-zeug">';
        echo '<form method="post">';
        
        echo '<input type="hidden" name="todoid" value="' . $todo['todoid'] . '">';
        
        echo '<input type="text" name="todo_titel" value="' . htmlspecialchars($todo['todo_titel']) . '">';
        
        echo '<textarea name="todo_inhalt" rows="5">' . htmlspecialchars($todo['todo_inhalt']) . '</textarea>';
        
        echo '<div class="todo-zeit">Erstellt: ' . $todo['todo_erstellt'] . '</div>';

        echo '<div class="todo-buttons">';
        echo '<button type="submit" name="bearbeiten" class="bearbeiten_knopf">Speichern</button>';
        echo '<button type="submit" name="loeschen" class="loesch_knopf">Löschen</button>';
        echo '</div>';
        
        echo '</form>';
        echo '</div>';
    }
    ?>
    </div>
</body>
</html>