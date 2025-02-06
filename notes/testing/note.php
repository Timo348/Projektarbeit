<?php
// Beipsiel ohne Datenbank verknüpfung
// userid, todoid, todotitel, todoinhalt, todostatus, todo_erstellt

/*
Funktion um userid zu bekommen und davon dann die berechtigten ToDos zu bekommen
UML = Benutzer(string userid) : todoid
*/

/*
Tabellensystem für ToDos damit die als Liste angezeigt werden
4 Spalten: 1 = Offen 2 = In Bearbeitung 3 = Erledigt 4 = Archiviert
(5 die man nicht sieht (gelöscht))
*/

/*
Irgendwo ein Knopf Implementieren um eine Neue Todo zu erstellen (führt Funktion aus),
die dann in die Datenbank geschrieben wird mit:
Titel, Id, Userid, inhalt, status, erstellt
*/

// Test Funktion von Todos mit Beispielinhalt:

$test_todoid = 1;
$test_todotitel = "Test ToDo";
$test_todoinhalt = "Das ist ein Test ToDo";
$test_todostatus = "offen";
$test_todo_erstellt = "2021-06-01";





?>