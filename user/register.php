<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>
<body>
<form action="" method="POST">
        <h2>Registrierung</h2>
        <label for="username">Gib deinen Nutzernamen ein:</label>
        <input id="usrnm" name="usrnm" type="text" required>

        <label for="passwd">Gib dein Passwort ein:</label>
        <input id="passwd" name="passwd" type="password" required>

        <button type="submit">Registerstrierung Abschließen! :D</button>
</form>
<?php
ini_set('display_errors', '1');

// Verbindung zur SQLite-Datenbank herstellen
    $db = new PDO('sqlite:C:\xampp\htdocs\Projektarbeit\database\projektdatenbank.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


$username = trim($_REQUEST['usrnm']);
$password = trim($_REQUEST['passwd']);

    try{
        // Benutzer in der Datenbank hinzufügen
        $statement = $db->prepare("INSERT INTO account (username, password) VALUES (:username, :password)");
        $statement->bindParam(':username', $username);
        $statement->bindParam(':password', $password);
        $statement->execute();

        echo "Registrierung war erfolgreich! Sie können sich jetzt anmelden.";
    } catch(PDOException $e){
        // Fehler behandeln, z.B. wenn der Benutzername schon existiert
        if ($e->getCode()=='23000'){
            echo "Benutzername ist bereits registriert.";
        } else{
            echo "Ein Fehler ist aufgetreten :/". $e->getMessage();
        }
    }

?>
</body>
</html>