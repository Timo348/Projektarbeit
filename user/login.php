<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
</head>
<body>
    <form action="">
        <h2>Login</h2>
        <label for="username">Gib deinen Nutzernamen ein:</label>
        <input id="usrnm" name="usrnm" type="text" required>

        <label for="passwd">Gib dein Passwort ein:</label>
        <input id="pswd" name="passwd" type="password" required>

        <button type="submit">Log in</button>

        <label for="noaccount">Noch keinen Account?</label>
        <a href="register.php">Hier geht es zur Registrierung</a>
    </form>

    <?php
    $username = $_REQUEST['usrnm'] ?? '';
    $password = $_REQUEST['passwd'] ?? '';

    if ($username == '') {
        echo"Bitte geben sie ihr Benutzernamen ein";
    }
    if ($password == '') {
        echo"Bitte geben sie ihr Passwort ein";
    }
    
// Funktion um zu Überprüfen ob Login und Passwort Korrekt sind
// Funktion um eingelogt zu werden
    
    ?>
</body>
</html>
