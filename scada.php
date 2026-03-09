<?php 
// Włączenie ścisłego typowania oraz rozpoczęcie sesji użytkownika
declare(strict_types=1);
session_start();

// Sprawdzenie czy użytkownik jest zalogowany
// Jeśli nie – następuje przekierowanie do strony logowania
if (!isset($_SESSION['loggedin'])) {
    header('Location: logowanie.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="UTF-8">
<title>SCADA</title>

<!-- Biblioteka Bootstrap do stylowania strony -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Biblioteka Google Charts do tworzenia wykresów -->
<script src="https://www.gstatic.com/charts/loader.js"></script>

<script>

// Załadowanie biblioteki wykresów Google
google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(startChart);

let chart;

// Funkcja startowa uruchamiana po załadowaniu biblioteki
// Inicjalizuje wykres i ustawia automatyczne odświeżanie danych
function startChart(){
    chart = new google.visualization.LineChart(document.getElementById('chart_div'));
    drawChart();
    updateTemperatures();

    // Odświeżanie danych co 2 sekundy
    setInterval(drawChart, 2000);
    setInterval(updateTemperatures, 2000);
}

// ---------------------------------------------------
// Funkcja pobierająca aktualne dane z czujników
// ---------------------------------------------------
function updateTemperatures() {

fetch("ostatni_pomiar.php")
.then(response => response.json())
.then(data => {

    // Aktualizacja wyświetlanych temperatur
    document.getElementById("t1").innerHTML = "X1=" + data.x1 + "°C";
    document.getElementById("t2").innerHTML = "X2=" + data.x2 + "°C";
    document.getElementById("t3").innerHTML = "X3=" + data.x3 + "°C";
    document.getElementById("t4").innerHTML = "X4=" + data.x4 + "°C";
    document.getElementById("t5").innerHTML = "X5=" + data.x5 + "°C";

    // Obsługa alarmu pożaru
    const fire = document.getElementById("fire");
    if (data.pozar?.toLowerCase() === "tak") {
        fire.src = "ogien.png";
        fire.classList.add("fire-anim");
    } else {
        fire.src = "gasnica.png";
        fire.classList.remove("fire-anim");
    }

    // Sterowanie animacją wentylatora
    const ventilator = document.getElementById("ventilator");
    ventilator.classList.remove("scada-spin-slow","scada-spin-fast");
    if (data.wentylacja === "Włączona - stopień 1") ventilator.classList.add("scada-spin-slow");
    else if (data.wentylacja === "Włączona - stopień 2") ventilator.classList.add("scada-spin-fast");

    // Obsługa alarmu zalania
    const water = document.getElementById("water");
    data.zalanie === "tak" ? water.classList.add("water-anim") : water.classList.remove("water-anim");

    // Obsługa alarmu włamania
    const burglar = document.getElementById("burglar");
    data.wlamanie === "tak" ? burglar.classList.add("burglar-anim") : burglar.classList.remove("burglar-anim");

    // Obsługa czujnika CO
    const coSensor = document.getElementById("co");
    data.czujnik_co === "tak" ? coSensor.classList.add("co-anim") : coSensor.classList.remove("co-anim");
});
}

// ---------------------------------------------------
// Funkcja rysująca wykres temperatur
// ---------------------------------------------------
function drawChart(){

fetch("wykres3_dane.php")
.then(response => response.json())
.then(dane => {

    // Tworzenie tablicy danych do wykresu
    let dataArray = [['Pomiar','x1','x2','x3','x4','x5']];
    dane.forEach(row => dataArray.push(row));

    // Konwersja danych do formatu Google Charts
    let data = google.visualization.arrayToDataTable(dataArray);

    // Opcje wyglądu wykresu
    let options = {
        title: 'Wykres pomiarów',
        curveType: 'function',
        legend: { position: 'right' },
        pointSize: 5,
        width: '100%',
        height: '100%'
    };

    // Rysowanie wykresu
    chart.draw(data, options);
});
}

// Ponowne rysowanie wykresu przy zmianie rozmiaru okna
window.addEventListener("resize", drawChart);

</script>

<style>

/* Ogólny styl głównej sekcji strony */
main{padding:15px;}

/* Kontener wizualizacji SCADA */
.scada-container{position:relative;width:100%;max-width:1100px;margin:auto;}

/* Obraz tła (np. schemat serwerowni) */
.scada-bg{width:100%;height:auto;display:block;}

/* Ikony urządzeń i pola temperatur */
.scada-icon,.temp{
    position:absolute;width:5%;min-width:35px;height:auto;
    padding:3px 6px;font-size:14px;border-radius:6px;text-align:center;
}

/* Wygląd wyświetlanych temperatur */
.temp{
    background:black;
    color:#00ff00;
    font-weight:bold;
    min-width:70px;
    white-space:nowrap;
    padding:4px 10px;
    border-radius:6px;
    text-align:center;
}

/* Responsywność – dopasowanie do różnych ekranów */
@media (max-width:1200px){.scada-icon,.temp{width:6%;font-size:13px;}}
@media (max-width:768px){.scada-icon,.temp{width:8%;font-size:12px;}}
@media (max-width:480px){.scada-icon,.temp{width:10%;font-size:11px;}}

/* Animacje używane w wizualizacji SCADA */
@keyframes spinSlow{from{transform:rotate(0deg);}to{transform:rotate(360deg);}}
@keyframes spinFast{from{transform:rotate(0deg);}to{transform:rotate(360deg);}}
@keyframes pulseFire{0%{transform:scale(1);opacity:1;}50%{transform:scale(1.2);opacity:.8;}100%{transform:scale(1);opacity:1;}}
@keyframes pulseWater{0%{transform:scale(1);}50%{transform:scale(1.2);}100%{transform:scale(1);}}
@keyframes pulseAlarm{0%{transform:scale(1);}50%{transform:scale(1.2);}100%{transform:scale(1);}}
@keyframes pulseCO{0%{transform:scale(1);}50%{transform:scale(1.2);}100%{transform:scale(1);}}

/* Klasy animacji */
.scada-spin-slow{animation:spinSlow 4s linear infinite;}
.scada-spin-fast{animation:spinFast 1s linear infinite;}
.fire-anim{animation:pulseFire 1s infinite;}
.water-anim{animation:pulseWater 1s infinite;}
.burglar-anim{animation:pulseAlarm 1s infinite;}
.co-anim{animation:pulseCO 1s infinite;}

/* Kontener wykresu */
#chart_div{width:100%;height:600px;}
@media (min-width:1400px){#chart_div{height:700px;}}

</style>
</head>

<body class="d-flex flex-column min-vh-100">

<!-- Menu nawigacyjne strony -->
<?php include "navbar.php"; ?>

<main class="flex-grow-1 container-fluid mt-3">

<!-- Tytuł strony -->
<h3 class="mb-4">SCADA</h3>

<!-- Główna sekcja z wizualizacją systemu i wykresem -->
<div class="row g-5 align-items-stretch">

  <!-- Wizualizacja systemu SCADA (schemat serwerowni + czujniki) -->
  <div class="col-12 col-xl-6 d-flex">
    <div class="scada-container flex-grow-1">
      <img src="server_room.svg" class="scada-bg">

      <!-- Wyświetlane temperatury czujników -->
      <div id="t1" class="temp" style="top:28%; left:28%"></div>
      <div id="t2" class="temp" style="top:52%; left:67%"></div>
      <div id="t3" class="temp" style="top:78%; left:53%"></div>
      <div id="t4" class="temp" style="top:26%; left:50%"></div>
      <div id="t5" class="temp" style="top:50%; left:35%"></div>

      <!-- Ikony urządzeń i czujników -->
      <img id="ventilator" src="ventilator.png" class="scada-icon" style="top:43%; left:12%;">
      <img id="fire" src="gasnica.png" class="scada-icon" style="top:95%; left:28%;">
      <img id="water" src="pipe.svg" class="scada-icon" style="top:60%; left:55%;">
      <img id="burglar" src="wlamanie.png" class="scada-icon" style="top:55%; left:85%;">
      <img id="co" src="czujnik_co.png" class="scada-icon" style="top:60%; left:35%;">
    </div>
  </div>

  <!-- Wykres temperatur -->
  <div class="col-12 col-xl-6 d-flex">
    <div id="chart_div" class="flex-grow-1"></div>
  </div>

</div>

</main>

<!-- Stopka strony -->
<?php include "footer.php"; ?>

</body>
</html>