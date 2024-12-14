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
<<<<<<< HEAD
=======
<nav>
    <a href="../notes/notizen.php">Notizen</a>
    <a href="../todo/todo.php">To-Do</a>
    <a href="../event/event.php">Event-Countdown</a>
    <a href="../user/login.php">Login</a>
</nav>
<div class="maincontainer">
>>>>>>> 39f19abd82e44640d31d5e439b6a4071455bbaea
<form action="" method="POST">
        <h2>Registrierung</h2>
        <input id="usrnm" name="usrnm" type="text" placeholder="Benutzernamen" required>

<<<<<<< HEAD
        <label for="passwd">Gib dein Passwort ein:</label>
        <input id="passwd" name="passwd" type="password" required>
=======
        <input id="passwd" name="passwd" type="password" placeholder="Passwort" required>
>>>>>>> 39f19abd82e44640d31d5e439b6a4071455bbaea

        <button type="submit">Registerstrierung Abschließen! :D</button>
</form>
<?php
ini_set('display_errors', '1');
<<<<<<< HEAD

// Verbindung zur SQLite-Datenbank herstellen
    $db = new PDO('sqlite:C:\xampp\htdocs\Projektarbeit\database\projektdatenbank.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
=======
>>>>>>> 39f19abd82e44640d31d5e439b6a4071455bbaea


    $db = new PDO('sqlite:C:\xampp\htdocs\Projektarbeit\database\projektdatenbank.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($_SERVER['REQUEST_METHOD'] === 'POST')  {
    $username = trim($_REQUEST['usrnm']);
    $password = trim($_REQUEST['passwd']);

    try {
        $statement = $db->prepare("INSERT INTO account (username, password) VALUES (:username, :password)");
        $statement->bindParam(':username', $username);
        $statement->bindParam(':password', $password);
        $statement->execute();

        echo "Registrierung war erfolgreich! Sie können sich jetzt anmelden.";
<<<<<<< HEAD
    } catch(PDOException $e){
        // Fehler behandeln, z.B. wenn der Benutzername schon existiert
        if ($e->getCode()=='23000'){
=======
    } catch(PDOException $e)    {
        // Fehler behandeln, z.B. wenn der Benutztername schon existiert
        if ($e->getCode()=='23000') {
>>>>>>> 39f19abd82e44640d31d5e439b6a4071455bbaea
            echo "Benutzername ist bereits registriert.";
        } 
        else    {
            echo "Ein Fehler ist aufgetreten :/". $e->getMessage();
        }
    }
<<<<<<< HEAD

=======
}
>>>>>>> 39f19abd82e44640d31d5e439b6a4071455bbaea
?>
</div>
</body>
</html>