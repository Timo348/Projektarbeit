# Temporäre Notizen markdown datei

## Login und Register
nav bar eingefügt wie bei login
nav.css mit link tag verknüpft wie bei login
alle classes übernommen von dem login
Klasse maincontainer zur form hinzugefügt
labels wieder entfernt da wir diese nicht benötigen
placeholder geschrieben damit man weiß was worein muss
Erster fehler: echo geht außerhalb vom maincontainer daher statt nur der form die class maincontainer geben eine div erstellt die auch das php skript miteinbehält
Dies Funktionert auch.


## Home Button
Grund warum home button (kein weg zum button)
neuen a tag erstmal erstellt wo erstmal noch H steht aber wir ein Icon benutzen wollen später
a tag in einer eigenen class damit er Links sortiert ist und nicht neben den Anderen a tags
genau wie bei nav a farbe text align decoration eingestellt, nur größe auf 60x60 gestellt
problem: ist immernoch zu nah an den anderen Elementen da wir es am linken bildschirmrand haben wollen
Fehlerbehebung:
https://developer.mozilla.org/en-US/docs/Web/CSS/position
https://www.w3schools.com/css/css_positioning.asp
Durch Quellen weiß position vom Homebutton auf Absolute stellen
Jetzt ist er Mittig im nav also einen Margin right einstellen 
Margin top noch überarbeiten da er kleiner als die anderen Elemente ist somit nicht mittig ist (diesen auf 20px)
Online nach Icon/Emoji Gesucht
Für Emoji entschieden da einfacher