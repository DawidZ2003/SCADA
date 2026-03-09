<?php declare(strict_types=1);
session_start();
if (!isset($_SESSION['loggedin'])) {
    header('Location: logowanie.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="UTF-8">
<title>Wykres 5 serii</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<script src="https://www.gstatic.com/charts/loader.js"></script>

<script>


google.charts.load('current', {'packages':['corechart']});

google.charts.setOnLoadCallback(startChart);

let chart;



function startChart(){

    // Utworzenie wykresu liniowego w elemencie HTML o id "chart_div"
    chart = new google.visualization.LineChart(
        document.getElementById('chart_div')
    );

    // Pierwsze narysowanie wykresu
    drawChart();

    setInterval(drawChart, 2000);
}


function drawChart(){

// Pobranie danych z pliku PHP w formacie JSON
fetch("wykres3_dane.php")

// Konwersja odpowiedzi na JSON
.then(response => response.json())

// Przetwarzanie danych
.then(dane => {

    // Tablica danych dla Google Charts
    let dataArray = [
        ['Pomiar','x1','x2','x3','x4','x5']
    ];

    // Dodanie kolejnych wierszy
    dane.forEach(row=>{
        dataArray.push(row);
    });

    // Konwersja tablicy do formatu Google Charts
    var data = google.visualization.arrayToDataTable(dataArray);

    // Opcje wykresu
    var options = {
        title: 'Wykres pomiarów', 
        curveType: 'function', 
        legend: { position: 'right' },
        pointSize: 5
    };

    // Narysowanie wykresu na stronie
    chart.draw(data, options);

});

}

</script>

</head>

<body class="d-flex flex-column min-vh-100">

<!-- NAVBAR -->
<?php include "navbar.php"; ?>

<main class="flex-grow-1 container mt-4">
    <h3 class="mb-4">Wykres pomiarów</h3>

    <div class="d-flex justify-content-center">
        <div id="chart_div" style="width:100%; max-width:1000px; height:500px"></div>
    </div>
</main>

<?php include "footer.php"; ?>

</body>
</html>