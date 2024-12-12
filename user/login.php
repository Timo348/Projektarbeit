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
    <a href="../notes/notizen.php">Notizen</a>
    <a href="../todo/todo.php">To-Do</a>
    <a href="../event/event.php">Event-Countdown</a>
    <a href="../user/login.php">Login</a>
</nav>
    <form class="maincontainer" action="">
        <h2>Log In Seite</h2> 
        <label for="username">Bitte gib deinen Benutzernamen ein:</label>
        <input id="username" name="username" type="text" placeholder="Benutzername" required> <br>

        <label for="password">Bitte gib dein Passwort ein:</label>
        <input id="passwort" name="passwort" type="password" placeholder="Passwort" required> <br>

        <button type="submit">Einloggen</button> <br>

        <p>Noch keinen Account?</p>
        <a href="register.php">Hier geht es zur Registrierung</a>
    </form>
</body>
</html>