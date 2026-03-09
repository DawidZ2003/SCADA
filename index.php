<?php 
// Włączenie ścisłego typowania oraz rozpoczęcie sesji użytkownika
declare(strict_types=1); 
session_start(); 

// Sprawdzenie czy użytkownik jest zalogowany
if (!isset($_SESSION['loggedin'])) {
    header('Location: logowanie.php'); // przekierowanie do strony logowania
    exit();
}

// Połączenie z bazą danych MySQL
$conn = mysqli_connect("127.0.0.1", "dm81079_z12a", "Dawidek7003#", "dm81079_z12a");

// Sprawdzenie czy połączenie z bazą się powiodło
if (!$conn) {
    die("Błąd połączenia z bazą danych: " . mysqli_connect_error());
}

// Pobranie adresu IP użytkownika oraz informacji o przeglądarce
$ipaddress = $_SERVER['REMOTE_ADDR'];
$user_agent = $_SERVER['HTTP_USER_AGENT'];


// ---------------------------------------------------
// Pobieranie danych o urządzeniu użytkownika z JS
// ---------------------------------------------------

$screen = $_POST['screen'] ?? null; // rozdzielczość ekranu
$window_size = $_POST['window_size'] ?? null; // rozmiar okna przeglądarki
$colors = $_POST['colors'] ?? null; // liczba kolorów ekranu
$cookie_enabled = isset($_POST['cookie_enabled']) ? (int)$_POST['cookie_enabled'] : null; // czy cookies są włączone
$java_enabled = isset($_POST['java_enabled']) ? (int)$_POST['java_enabled'] : null; // czy Java jest włączona
$language = $_POST['language'] ?? null; // język przeglądarki
$ajax = isset($_POST['ajax']) ? true : false; // czy zapytanie zostało wykonane przez AJAX


// ---------------------------------------------------
// Funkcja analizująca przeglądarkę użytkownika
// ---------------------------------------------------

function parse_browser($ua) {
    $browser = "-";
    $version = "-";
    $platform = "-";

    // Rozpoznawanie systemu operacyjnego
    if (stripos($ua, "Windows") !== false) $platform = "Windows";
    elseif (stripos($ua, "Mac") !== false) $platform = "Mac OS";
    elseif (stripos($ua, "Linux") !== false) $platform = "Linux";
    elseif (stripos($ua, "Android") !== false) $platform = "Android";
    elseif (stripos($ua, "iPhone") !== false || stripos($ua, "iPad") !== false) $platform = "iOS";

    // Rozpoznawanie przeglądarki i jej wersji
    if (preg_match('/OPR\/([0-9\.]+)/i', $ua, $matches)) { $browser="Opera"; $version=$matches[1]; }
    elseif (preg_match('/MSIE ([0-9\.]+)/i', $ua, $matches)) { $browser="Internet Explorer"; $version=$matches[1]; }
    elseif (preg_match('/Trident\/7.0;.*rv:([0-9\.]+)/i', $ua, $matches)) { $browser="Internet Explorer"; $version=$matches[1]; }
    elseif (preg_match('/Edge\/([0-9\.]+)/i', $ua, $matches)) { $browser="Edge"; $version=$matches[1]; }
    elseif (preg_match('/Chrome\/([0-9\.]+)/i', $ua, $matches)) { $browser="Chrome"; $version=$matches[1]; }
    elseif (preg_match('/Firefox\/([0-9\.]+)/i', $ua, $matches)) { $browser="Firefox"; $version=$matches[1]; }
    elseif (preg_match('/Safari\/([0-9\.]+)/i', $ua, $matches) && stripos($ua,"Chrome")===false) { $browser="Safari"; $version=$matches[1]; }

    return "$browser $version ($platform)";
}


// ---------------------------------------------------
// Funkcja pobierająca lokalizację na podstawie IP
// (korzysta z API ipinfo.io)
// ---------------------------------------------------

function ip_details($ip) {
    $json = @file_get_contents("http://ipinfo.io/{$ip}/geo");
    if ($json === false) return null;
    return json_decode($json);
}


// ---------------------------------------------------
// Zapis danych użytkownika do bazy danych
// ---------------------------------------------------

if($screen && $window_size){
    $stmt = $conn->prepare("INSERT INTO goscieportalu 
        (ipaddress, browser, screen_resolution, window_size, colors, cookie_enabled, java_enabled, language) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param("ssssiiis", $ipaddress, $user_agent, $screen, $window_size, $colors, $cookie_enabled, $java_enabled, $language);
    $stmt->execute();
}


// ---------------------------------------------------
// Obsługa zapytania AJAX – zwracanie danych w formacie JSON
// ---------------------------------------------------

if($ajax){

    // Zapytanie pobierające ostatnie wejście z każdego IP
    // oraz liczbę wejść z tego adresu
    $query = "
        SELECT g1.*, cnt.iloscWejsc
        FROM goscieportalu g1
        INNER JOIN (
            SELECT ipaddress, MAX(datetime) AS max_date
            FROM goscieportalu
            GROUP BY ipaddress
        ) g2 ON g1.ipaddress = g2.ipaddress AND g1.datetime = g2.max_date
        INNER JOIN (
            SELECT ipaddress, COUNT(*) AS iloscWejsc
            FROM goscieportalu
            GROUP BY ipaddress
        ) cnt ON g1.ipaddress = cnt.ipaddress
        ORDER BY cnt.iloscWejsc DESC
    ";

    $result = $conn->query($query);
    $data = [];

    // Przetwarzanie wyników
    while($row = $result->fetch_assoc()){
        $geo = ip_details($row['ipaddress']);

        // Tworzenie informacji o lokalizacji
        $row['lokalizacja'] = ($geo) ? ($geo->country ?? "-").", ".($geo->region ?? "-").", ".($geo->city ?? "-") : "-";

        // współrzędne geograficzne
        $row['lok'] = $geo->loc ?? "-";

        // link do map Google
        $row['mapLink'] = ($row['lok'] != "-") ? "<a href='https://www.google.pl/maps/place/".$row['lok']."' target='_blank'>LINK</a>" : "-";

        // parsowanie przeglądarki
        $row['browser'] = parse_browser($row['browser']);

        $data[] = $row;
    }

    // Zwrócenie danych w formacie JSON
    echo json_encode($data);
    exit;
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
	<meta charset="utf-8">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    /* Styl dla tabeli odwiedzających */
    table#visitorTable {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 60px; /* odstęp od stopki */
        background-color: #fff8e7; /* kremowe tło całej tabeli */
    }

    #visitorTable th, #visitorTable td {
        border: 1px solid #c8bca8; /* delikatne linie */
        padding: 8px;
    }

    /* lekki efekt naprzemiennych wierszy */
    #visitorTable tr:nth-child(even) {
        background-color: #fff2d5; /* jaśniejszy kremowy */
    }

    #visitorTable th {
        background-color: #7a6749; /* ciemniejszy odcień brązu dla kontrastu */
        color: #fff8e7;
        text-align: center;
    }

    main {
        padding: 20px;
    }
    
    /* Responsywność tabeli */
.table-responsive {
    border-radius: 10px;
    overflow-x: auto;
}

/* Mniejsze czcionki na telefonach */
@media (max-width: 768px) {
    #visitorTable {
        font-size: 13px;
    }

    #visitorTable th,
    #visitorTable td {
        padding: 6px;
        white-space: nowrap;
    }

    main {
        padding: 10px;
    }
}

/* Bardzo małe ekrany */
@media (max-width: 480px) {
    #visitorTable {
        font-size: 12px;
    }

    #visitorTable th,
    #visitorTable td {
        padding: 4px;
    }
}
</style>
</head>

<body class="d-flex flex-column min-vh-100">
    	<?php include "navbar.php"; ?>

<main class="flex-grow-1 container mt-4">
			<h2>Odwiedzający portal</h2>
            <div class="table-responsive">
                <table id="visitorTable" class="table table-bordered align-middle">
                <tr>
                    <th>Data</th><th>Adres IP</th><th>Lokalizacja</th>
                    <th>Współrzędne</th><th>Mapy Google</th>
                    <th>Przeglądarka</th><th>Ekran</th><th>Okno</th>
                    <th>Kolory</th><th>Ciasteczka</th><th>Java</th>
                    <th>Język</th><th>Ilość wejść z IP</th>
                </tr>
                <?php
                $query = "
                    SELECT g1.*, cnt.iloscWejsc
                    FROM goscieportalu g1
                    INNER JOIN (
                        SELECT ipaddress, MAX(datetime) AS max_date
                        FROM goscieportalu
                        GROUP BY ipaddress
                    ) g2 ON g1.ipaddress = g2.ipaddress AND g1.datetime = g2.max_date
                    INNER JOIN (
                        SELECT ipaddress, COUNT(*) AS iloscWejsc
                        FROM goscieportalu
                        GROUP BY ipaddress
                    ) cnt ON g1.ipaddress = cnt.ipaddress
                    ORDER BY cnt.iloscWejsc DESC
                ";
                $result = $conn->query($query);
                while($row = $result->fetch_assoc()):
                    $geo = ip_details($row['ipaddress']);
                    $lokalizacja = ($geo) ? ($geo->country ?? "-").", ".($geo->region ?? "-").", ".($geo->city ?? "-") : "-";
                    $lok = $geo->loc ?? "-";
                    $mapLink = ($lok != "-") ? "<a href='https://www.google.pl/maps/place/$lok' target='_blank'>LINK</a>" : "-";
                ?>
                <tr>
                    <td><?= $row['datetime'] ?></td>
                    <td><?= $row['ipaddress'] ?></td>
                    <td><?= $lokalizacja ?></td>
                    <td><?= $lok ?></td>
                    <td><?= $mapLink ?></td>
                    <td><?= parse_browser($row['browser']) ?></td>
                    <td><?= $row['screen_resolution'] ?></td>
                    <td><?= $row['window_size'] ?></td>
                    <td><?= $row['colors'] ?></td>
                    <td><?= $row['cookie_enabled'] == 1 ? 'true' : 'false' ?></td>
                    <td><?= $row['java_enabled'] == 1 ? 'true' : 'false' ?></td>
                    <td><?= $row['language'] ?></td>
                    <td><?= $row['iloscWejsc'] ?></td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>
	</main>	

	<?php include "footer.php"; ?>

<script>
document.addEventListener("DOMContentLoaded", function() {
    var data = {
        screen: screen.width + "x" + screen.height,
        window_size: window.innerWidth + "x" + window.innerHeight,
        colors: screen.colorDepth,
        cookie_enabled: navigator.cookieEnabled ? 1 : 0,
        java_enabled: navigator.javaEnabled() ? 1 : 0,
        language: navigator.language,
        ajax: 1
    };

    var params = Object.keys(data).map(key => key + "=" + encodeURIComponent(data[key])).join("&");

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onload = function() {
        if(xhr.status === 200){
            var allData = JSON.parse(xhr.responseText);
            var table = document.getElementById("visitorTable");
            while(table.rows.length > 1){ table.deleteRow(1); }

            allData.forEach(function(row){
                var tr = table.insertRow();
                tr.innerHTML = `
                    <td>${row.datetime ?? '-'}</td>
                    <td>${row.ipaddress}</td>
                    <td>${row.lokalizacja}</td>
                    <td>${row.lok}</td>
                    <td>${row.mapLink}</td>
                    <td>${row.browser}</td>
                    <td>${row.screen_resolution}</td>
                    <td>${row.window_size}</td>
                    <td>${row.colors}</td>
                    <td>${row.cookie_enabled == 1 ? 'true' : 'false'}</td>
                    <td>${row.java_enabled == 1 ? 'true' : 'false'}</td>
                    <td>${row.language}</td>
                    <td>${row.iloscWejsc}</td>
                `;
            });
        }
    };
    xhr.send(params);
});
</script>
</body>
</html>
