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
beim body noch die Hintergrund farbe eingestellt, für alles noch die font family size und farbe
Eine Border eingestellt damit man sehen kann wo was ist gerade von den divs her
Nochmals zwei notizen copy pastet um zu sehen wir es immoment aussehen würde
Dadurch da in spalten aufgeteilt die flex direction auf row festellt
Die Notizen als "block" wieder dargestellt sodass jede Notiz ihr eigenes feld bekommt (dies mit backgroudn color und Margin erhöt damit die Notizen nicht press aneinander sind)
Funktioniert nicht wirklich, die Textarea ist deutlich größer als die notizen textarea height und width auf 150px
Border weggemacht um anzuschauen wie es aussieht (sehr unzufrieden muss noch sehr viel gemacht werden (siehe Screenshot (momentan noch aufm desktop)))
Margin wieder runtergestellt
noch mehr Notizen hinzugefügt um zu schauen wie sich die Notizen verhalten
Notizen wieder etwas größer gemacht 
Textarea auch etwas größer gemacht
dadurch da die textareas nicht mittig sind margin left unf rifht auf auto gestellt
Hat nicht funktioniert 
Daher erstmal von den Notizen einen Border radius gegeben von 10px 
Textarea auch einen Border radius gegeben von 5px
die border aber unsichtbar gemacht
dadurch da margin left und right nicht funktioniert hat margin manuell einstellen (Ganze Notiz hat 380px die häfte davon sind 190px das minus die Textare width sind 10px also 10px margin left)
Rechnung ging nicht auf 10px sind zu wenig
mit ausprobieren herausgefunden es sind zwichen 25 und 30 px