<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="user.css">
    <link rel="stylesheet" href="../nav.css">
</head>
<body>
    <!--
<nav>
    <a href="../notes/notizen.php">Notizen</a>
    <a href="../todo/todo.php">To-Do</a>
    <a href="../event/event.php">Event-Countdown</a>
    <a href="../user/login.php">Login</a>
</nav>
    <form class="maincontainer" action="">
        <h2>Log In Seite</h2> 
        <input id="username" name="username" type="text" placeholder="Benutzername" required> <br>
        <input id="passwort" name="passwort" type="password" placeholder="Passwort" required> <br>
        <button type="submit">Einloggen</button> <br>

        <p>Noch keinen Account?</p>
        <a href="register.php">Hier geht es zur Registrierung</a>
    </form>
-->
<?php
ini_set('display_errors', '1');


$db = new PDO('sqlite:C:\xampp\htdocs\Projektarbeit\database\projektdatenbank.db');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($_SERVER['REQUEST_METHOD'] === 'POST')  {
    $username = trim($_REQUEST['username']);
    $password = trim($_REQUEST['passwort']);

    $statement = $db->prepare("SELECT * FROM account WHERE user = :username");
    $result = $statement->fetch(PDO::FETCH_ASSOC); // Holt die erste Zeile als assoziatives Array
    $statement->bindParam(':username', $datausername);
    $statement->bindParam(':password', $datapassword);
    $statement->bindParam(':userid', $dataid);
    $statement->execute();
    echo"$datapassword $dataid $datausername";
}
    ?>
</body>
</html>