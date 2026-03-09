<?php

$dbhost="127.0.0.1";
$dbuser="dm81079_z12a";
$dbpassword="Dawidek7003#";
$dbname="dm81079_z12a";

// Nawiązanie połączenia z bazą
$polaczenie = mysqli_connect($dbhost, $dbuser, $dbpassword, $dbname);

// Sprawdzenie połączenia
if (!$polaczenie) {
    die(json_encode(["error" => "Błąd połączenia: " . mysqli_connect_error()]));
}

// Pobranie ostatniego rekordu z tabeli 'pomiary'
$rezultat = mysqli_query($polaczenie, "SELECT * FROM pomiary ORDER BY id DESC LIMIT 1");

if ($rezultat && mysqli_num_rows($rezultat) > 0) {
    $wiersz = mysqli_fetch_assoc($rezultat);

    // Przygotowanie danych JSON dla SCADA
    $json = [
        "x1" => floatval($wiersz['x1']),
        "x2" => floatval($wiersz['x2']),
        "x3" => floatval($wiersz['x3']),
        "x4" => floatval($wiersz['x4']),
        "x5" => floatval($wiersz['x5']),
        "pozar" => $wiersz['pozar'],
        "zalanie" => $wiersz['zalanie'],
        "wlamanie" => $wiersz['wlamanie'],
        "czujnik_co" => $wiersz['czujnik_co'],
        "wentylacja" => $wiersz['wentylacja']
    ];

    // Zwrócenie JSON
    echo json_encode($json);
} else {
    // Gdy brak danych
    echo json_encode(["error" => "Brak rekordów w bazie"]);
}

// Zamknięcie połączenia
mysqli_close($polaczenie);
?>