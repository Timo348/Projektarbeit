# Temporäre Notizen markdown datei

## Register
Session Starten(keine ahnung was es genau macht aber ist das wenn man einmal eingeloggt ist man eingeloggt bleibt (glaube ich))
Datenbank verknüpfung vom Register übernommen, variablen mit post bekommen auch übernommen vom Register
Das Erste Statement übernommen wieder aber dies ist ein Insert into account aber wir wollen diesmal ja keine Informationen in die Datenbank einsetzen sondern welche Rausholen (das Passwort und den User)
Deswegen erstmal alles was in der "" Steht gelöscht
Quelle:  https://www.tutorialspoint.com/sqlite/sqlite_select_query.htm https://www.php-einfach.de/experte/php-codebeispiele/loginscript/  
für Zeile 10-15
in den "" Schreiben wir erstmal ein SELECT dann FROM und die Table in unserem Fall die Table account
Jetzt müssen wir noch definieren was wir von der Table haben möchten
wir möchte den Wert password und userid von der Table haben damit wir abprüfen können ob das Eingegebene Passwort mit dem Eingegebenen übereinstimmt
Nun müssen wir noch Bestimmen von was es das Passwort nehmen soll dies erfolgt dann mit WHERE user = :Variabel name
nun noch ein statement mit bindparam: $statement->bindParam(':user', $username);
dannach execute statement
https://www.php.net/manual/en/pdostatement.fetch.php
neue variabel $result erstellen und dort einen fetch machen um werte als array auszugeben (mit print_r)
Funktioniert nicht, Behebung: parameter bei pindparam auf username ändern da in der tabelle es username heißt und nicht user
