<?php declare(strict_types=1);
session_start();
if (!isset($_SESSION['loggedin'])) {
    header('Location: logowanie.php');
    exit();
}

$dbhost = "127.0.0.1";
$dbuser = "dm81079_z12a";
$dbpassword = "Dawidek7003#";
$dbname = "dm81079_z12a";

$polaczenie = mysqli_connect($dbhost, $dbuser, $dbpassword, $dbname);

if (!$polaczenie) {
    die("Błąd połączenia z bazą danych");
}

$rezultat = mysqli_query($polaczenie, "SELECT * FROM pomiary");
?>

<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="UTF-8">
<title>Tabela pomiarów</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="d-flex flex-column min-vh-100">

<!-- NAVBAR -->
<?php include "navbar.php"; ?>

<!-- TREŚĆ -->
<main class="flex-grow-1 container mt-4">

<div class="card shadow-sm">
<div class="card-body">

<h4 class="mb-4">Tabela pomiarów</h4>

<div class="table-responsive">
<table class="table table-bordered table-hover text-center">

<thead class="table-dark">
<tr>
<th>id</th>
<th>x1</th>
<th>x2</th>
<th>x3</th>
<th>x4</th>
<th>x5</th>
<th>Pożar</th>
<th>Zalanie</th>
<th>Włamanie</th>
<th>Czujnik CO</th>
<th>Wentylacja</th>
<th>Data/Godzina</th>
</tr>
</thead>

<tbody>
<?php
while ($wiersz = mysqli_fetch_assoc($rezultat)) {
    echo "<tr>";
    echo "<td>".$wiersz['id']."</td>";
    echo "<td>".$wiersz['x1']."</td>";
    echo "<td>".$wiersz['x2']."</td>";
    echo "<td>".$wiersz['x3']."</td>";
    echo "<td>".$wiersz['x4']."</td>";
    echo "<td>".$wiersz['x5']."</td>";
    echo "<td>".$wiersz['pozar']."</td>";
    echo "<td>".$wiersz['zalanie']."</td>";
    echo "<td>".$wiersz['wlamanie']."</td>";
    echo "<td>".$wiersz['czujnik_co']."</td>";
    echo "<td>".$wiersz['wentylacja']."</td>";
    echo "<td>".$wiersz['datetime']."</td>";
    echo "</tr>";
}
?>
</tbody>
</table>
</div>

</div>
</div>

</main>

<!-- FOOTER -->
<?php include "footer.php"; ?>

</body>
</html>

<?php mysqli_close($polaczenie); ?>