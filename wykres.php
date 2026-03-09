<?php declare(strict_types=1);
session_start();
if (!isset($_SESSION['loggedin'])) {
    header('Location: logowanie.php');
    exit();
}
$data = [
 ['14:30', 5],
 ['14:35', 2],
 ['14:40', 7],
 ['14:45', 3],
 ['14:50', 1]
];
?>

<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="UTF-8">
<title>Wykres</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<script src="https://www.gstatic.com/charts/loader.js"></script>

<script>
google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(drawChart);

function drawChart(){

var data = google.visualization.arrayToDataTable([
 ['Czas', 'Wartość'],
 <?php
 foreach($data as $row){
     echo "['".$row[0]."', ".$row[1]."],";
 }
 ?>
]);

var options = {
 title: 'Przykład wykresu liniowego',
 pointSize: 5,
 legend: { position: 'bottom' }
};

var chart = new google.visualization.LineChart(
    document.getElementById('chart_div')
);

chart.draw(data, options);
}
</script>

</head>

<body class="d-flex flex-column min-vh-100">

<!-- NAVBAR -->
<?php include "navbar.php"; ?>

<!-- TREŚĆ -->
<main class="flex-grow-1 container mt-4">
    <h3 class="mb-4">Wykres</h3>

    <div class="d-flex justify-content-center">
        <div id="chart_div" style="width:100%; max-width:900px; height:500px;"></div>
    </div>
</main>

<!-- STOPKA -->
<?php include "footer.php"; ?>

</body>
</html>