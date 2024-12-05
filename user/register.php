<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>
<body>
<form action="">
        <h2>Registrierung</h2>
        <label for="username">Gib deinen Nutzernamen ein:</label>
        <input id="usrnm" name="usrnm" type="text" required>

        <label for="passwd">Gib dein Passwort ein:</label>
        <input id="pswd" name="passwd" type="password" required>

        <button type="submit">Registerstrierung Abschließen! :D</button>  
</form>
<?php

$db = 'C:\xampp\htdocs\Projektarbeit\database\projekt.sqlite';
$pdo = new \PDO($db);


$username = $_REQUEST['usrnm'] ?? '';
$password = $_REQUEST['passwd'] ?? '';

if ($username == '') {
    echo"Bitte erstellen sie ein Benutzernamen";
}
if ($password == '') {
    echo"Bitte geben sie ein Valides Passwort ein";
}
// Funktion um Daten in die Datenbank einzutragen

?>
</body>
</html>