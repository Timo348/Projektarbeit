# Temporäre Notizen markdown datei

## Login
Session Starten(keine ahnung was es genau macht aber ist das wenn man einmal eingeloggt ist man eingeloggt bleibt (glaube ich))
Quelle:  https://www.php-einfach.de/experte/php-codebeispiele/loginscript/  
für Zeile 10-15
https://www.php.net/manual/en/pdostatement.fetch.php
neue variabel $result erstellen und dort einen fetch machen um werte als array auszugeben (mit print_r)
ES FUNKTUINIERTE ALLES NICHT EGAL WAS VAR DUMP BAM BUM BIM ABER DIE REQUEST METHODE WAR NICHT AUF POST ICH KÖNNTE WEINEN
Jetzt müssen wir mit einer if abfrage abfragen ob die variabel $result besetzt ist 
und dannach die Variabeln aus dem Array ziehen $datauser = $result['user']; $datapass = $result['password'];
https://www.php.net/manual/en/function.password-verify.php 
Um zu überprüfen ob das passwort übereinstimmt machen wir ejtzt eifnach eine if und so ... kb mehr ich hör auf

## Notizen
Nav rüber kopiert und verlinkt wieder
Class Erstellt für alle Notizen in der Html dann eine div dafür
Der class erstmal den display auf flex gestellt (https://www.w3schools.com/cssref/pr_class_display.php)
die Flex Direction auf row/column
justify content wieder center und align items auch
Noch einen Margin (vorerst 20px Oben)
Nun Noch eine Class erstellen (Notiz) die wird dann für jede Einzelne Notiz benutzt
Neue div erstellt die Notiz heißt, dort erstmal eine Überschrift reingemacht eine form erstellt mit einer textarea (https://www.w3schools.com/tags/tag_textarea.asp) diese auf vorerst auf 30 cols und 10 rows eingestellt
Einen button erstellt um zu speichern
in der css datei für die textarea das resizing ausgestellt