<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="register.css">
    <link rel="stylesheet" href="../nav.css">
</head>
<body>
<nav>
    <a href="../notes/notizen.php">Notizen</a>
    <a href="../todo/todo.php">To-Do</a>
    <a href="../event/event.php">Timer</a>
    <a href="../user/login.php">Login</a>
</nav>
<div class="maincontainer">
<form action="" method="POST">
        <h2>Registrierung</h2>
        <input id="usrnm" name="usrnm" type="text" placeholder="Benutzernamen" required>

        <input id="passwd" name="passwd" type="password" placeholder="Passwort" required>

        <button type="submit">Registerstrierung Abschließen! :D</button>
</form>
<?php
ini_set('display_errors', '1');


    $db = new PDO('sqlite:C:\xampp\htdocs\Projektarbeit\database\projektdatenbank.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($_SERVER['REQUEST_METHOD'] === 'POST')  {
    $username = trim($_POST['usrnm']);
    $password = trim($_POST['passwd']);

    try {
        $statement = $db->prepare("INSERT INTO account (username, password) VALUES (:username, :password)");
        $statement->bindParam(':username', $username);
        $statement->bindParam(':password', $password);
        $statement->execute();

        

        echo "Registrierung war erfolgreich! Sie können sich jetzt anmelden.";
    } catch(PDOException $e)    {
        // Fehler behandeln, z.B. wenn der Benutztername schon existiert
        if ($e->getCode()=='23000') {
            echo "Benutzername ist bereits registriert.";
        } 
        else    {
            echo "Ein Fehler ist aufgetreten :/". $e->getMessage();
        }
    }
}
?>
</div>
</body>
</html>