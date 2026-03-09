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
<title>Formularz pomiarów</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="d-flex flex-column min-vh-100">

<!-- NAVBAR -->
<?php include "navbar.php"; ?>

<!-- TREŚĆ -->
<main class="flex-grow-1 container mt-5">

<div class="card shadow-sm mx-auto" style="max-width:500px;">
<div class="card-body">

<h4 class="mb-4 text-center">Dodaj pomiar</h4>

<form method="POST" action="add.php">

<div class="mb-3 d-flex align-items-center">
  <label class="form-label me-3 mb-0" style="width:40px;">x1:</label>
  <input type="number" name="x1" class="form-control" min="-50" max="100" required>
</div>

<div class="mb-3 d-flex align-items-center">
  <label class="form-label me-3 mb-0" style="width:40px;">x2:</label>
  <input type="number" name="x2" class="form-control" min="-50" max="100" required>
</div>

<div class="mb-3 d-flex align-items-center">
  <label class="form-label me-3 mb-0" style="width:40px;">x3:</label>
  <input type="number" name="x3" class="form-control" min="-50" max="100" required>
</div>

<div class="mb-3 d-flex align-items-center">
  <label class="form-label me-3 mb-0" style="width:40px;">x4:</label>
  <input type="number" name="x4" class="form-control" min="-50" max="100" required>
</div>

<div class="mb-3 d-flex align-items-center">
  <label class="form-label me-3 mb-0" style="width:40px;">x5:</label>
  <input type="number" name="x5" class="form-control" min="-50" max="100" required>
</div>

<div class="mb-3 d-flex align-items-center">
  <label class="form-label me-3 mb-0">Pożar:</label>
  <div class="form-check form-check-inline mb-0">
    <input class="form-check-input" type="radio" name="pozar" value="tak" required>
    <label class="form-check-label">Tak</label>
  </div>
  <div class="form-check form-check-inline mb-0">
    <input class="form-check-input" type="radio" name="pozar" value="nie">
    <label class="form-check-label">Nie</label>
  </div>
</div>

<div class="mb-3 d-flex align-items-center">
  <label class="form-label me-3 mb-0">Zalanie:</label>
  <div class="form-check form-check-inline mb-0">
    <input class="form-check-input" type="radio" name="zalanie" value="tak" required>
    <label class="form-check-label">Tak</label>
  </div>
  <div class="form-check form-check-inline mb-0">
    <input class="form-check-input" type="radio" name="zalanie" value="nie">
    <label class="form-check-label">Nie</label>
  </div>
</div>

<div class="mb-3 d-flex align-items-center">
  <label class="form-label me-3 mb-0">Włamanie:</label>
  <div class="form-check form-check-inline mb-0">
    <input class="form-check-input" type="radio" name="wlamanie" value="tak" required>
    <label class="form-check-label">Tak</label>
  </div>
  <div class="form-check form-check-inline mb-0">
    <input class="form-check-input" type="radio" name="wlamanie" value="nie">
    <label class="form-check-label">Nie</label>
  </div>
</div>

<div class="mb-3 d-flex align-items-center">
  <label class="form-label me-3 mb-0">Czujnik CO:</label>
  <div class="form-check form-check-inline mb-0">
    <input class="form-check-input" type="radio" name="czujnik_co" value="tak" required>
    <label class="form-check-label">Tak</label>
  </div>
  <div class="form-check form-check-inline mb-0">
    <input class="form-check-input" type="radio" name="czujnik_co" value="nie">
    <label class="form-check-label">Nie</label>
  </div>
</div>

<div class="mb-3 d-flex align-items-center">
  <label class="form-label me-3 mb-0" style="width:120px;">Wentylacja:</label>
  <select name="wentylacja" class="form-select" required>
    <option value="Wyłączona">Wyłączona</option>
    <option value="Włączona - stopień 1">Włączona - stopień 1</option>
    <option value="Włączona - stopień 2">Włączona - stopień 2</option>
  </select>
</div>

<div class="text-center">
<button type="submit" class="btn btn-success w-100">Dodaj</button>
</div>

</form>

</div>
</div>

</main>

<!-- FOOTER -->
<?php include "footer.php"; ?>

</body>
</html>