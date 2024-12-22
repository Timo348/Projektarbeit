<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="user.css">
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
    <form class="maincontainer" action="" method="POST">
        <h2>Log In Seite</h2> 
        <input id="username" name="username" type="text" placeholder="Benutzername" required> <br>
        <input id="passwort" name="passwort" type="password" placeholder="Passwort" required> <br>
        <button type="submit">Einloggen</button> <br>

        <p>Noch keinen Account?</p>
        <a href="register.php">Hier geht es zur Registrierung</a>
    </form>


<?php
session_start();
ini_set('display_errors', '1'); 

$db = new PDO('sqlite:C:\xampp\htdocs\Projektarbeit\database\projektdatenbank.db');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 

if ($_SERVER['REQUEST_METHOD'] === 'POST')  { 
    $user = trim($_POST['username']);
    $pass = trim($_POST['passwort']);

    $statement = $db->prepare("SELECT * FROM account WHERE username = :username"); // Wert aus der Tabelle account holen
    $statement->bindParam(':username', $user);
    $statement->execute();

    $result = $statement->fetch(PDO::FETCH_ASSOC); // Tabelle als Array ausgeben
    if ($result) {
        $datapass = $result['password']; 
        if ($datapass == $pass) {
            $_SESSION['sesuser'] = $result['username']; // Session werte vergeben
            $_SESSION['sesid'] = $result['userid']; // Session werte vergeben
            header('Location: ../index.html'); // Weiterleitung zur Startseite (hoffentlich auch mit Session werten)
        }
        else {
            session_abort(); // Session abbrechen wenn Passwort falsch
            echo"Irgendwas ist Falsch"; // Am besten eine richtige Fehlermeldung anzeigen lassen als Pop up
        }
    }
}
?>

</body>
</html>