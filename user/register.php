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
    <a class="homebutton" href="../index.html">🏠</a>
    <a href="../notes/notizen.php">Notizen</a>
    <a href="../todo/todo.php">To-Do</a>
    <a href="../event/event.php">Timer</a>
    <a href="../user/login.php">Login</a>
    <a class="Ausloggen" href="../logout.php"><img src="../images/logout.png" alt=""> </a>
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

try {
    $db = new PDO('sqlite:' . __DIR__ . '/../database/projektdatenbank.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Datenbankverbindung fehlgeschlagen: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST')  {
    $username = trim($_POST['usrnm']);
    $password = password_hash(trim($_POST['passwd']), PASSWORD_BCRYPT);

    try {
        $statement = $db->prepare("INSERT INTO account (username, password) VALUES (:username, :password)");
        $statement->bindParam(':username', $username);
        $statement->bindParam(':password', $password);
        $statement->execute();

        echo "Registrierung war erfolgreich! Sie können sich jetzt anmelden.";
    } catch(PDOException $e)    {
        // Fehler behandeln, z.B. wenn der Benutzername schon existiert
        if ($e->getCode() == '23000') {
            echo "Benutzername ist bereits registriert.";
        } else {
            echo "Ein Fehler ist aufgetreten: " . $e->getMessage();
        }
    }
}
?>
</div>
</body>
</html>
