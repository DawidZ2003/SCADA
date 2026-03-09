<?php

// Dane potrzebne do połączenia z bazą danych MySQL
$dbhost="127.0.0.1";
$dbuser="dm81079_z12a";
$dbpassword="Dawidek7003#";
$dbname="dm81079_z12a";

// Nawiązanie połączenia z bazą danych
$polaczenie = mysqli_connect($dbhost,$dbuser,$dbpassword,$dbname);

// Wykonanie zapytania pobierającego wszystkie pomiary
$rezultat = mysqli_query($polaczenie,
"SELECT * FROM pomiary ORDER BY id ASC");

// Tablica, w której będą przechowywane dane do wykresu
$dane = [];

// Pobieranie kolejnych wierszy z bazy danych
while($wiersz = mysqli_fetch_assoc($rezultat)){

    // Dodanie danych do tablicy w formacie wymaganym przez Google Charts
    $dane[] = [
        intval($wiersz['id']), 
        floatval($wiersz['x1']),
        floatval($wiersz['x2']),
        floatval($wiersz['x3']), 
        floatval($wiersz['x4']),
        floatval($wiersz['x5']) 
    ];
}

// ---------------------------------------------------
// Alternatywna wersja dla sterownika Arduino
// ---------------------------------------------------

// while($wiersz = mysqli_fetch_assoc($rezultat)){
//     $dane[] = [
//         intval($wiersz['id']),
//         floatval($wiersz['v0']),
//         floatval($wiersz['v1']),
//         floatval($wiersz['v2']),
//         floatval($wiersz['v3']),
//         floatval($wiersz['v4'])
//     ];
// }

// Zamknięcie połączenia z bazą danych
mysqli_close($polaczenie);

// Zwrócenie danych w formacie JSON (np. dla JavaScript i wykresu)
echo json_encode($dane);

?>