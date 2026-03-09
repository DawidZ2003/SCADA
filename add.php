<?php 
// Włączenie ścisłego typowania i rozpoczęcie sesji
declare(strict_types=1); 
session_start();  

// Sprawdzenie czy użytkownik jest zalogowany
if (!isset($_SESSION['loggedin'])) {     
    header('Location: logowanie.php'); // przekierowanie do strony logowania
    exit(); // zatrzymanie dalszego wykonywania skryptu
}  

// Sprawdzenie czy wszystkie wymagane dane zostały wysłane z formularza
if (
    isset($_POST['x1'], $_POST['x2'], $_POST['x3'], $_POST['x4'], $_POST['x5'],
          $_POST['pozar'], $_POST['zalanie'], $_POST['wlamanie'],
          $_POST['czujnik_co'], $_POST['wentylacja'])
    &&
    $_POST['x1'] !== "" && $_POST['x2'] !== "" &&
    $_POST['x3'] !== "" && $_POST['x4'] !== "" &&
    $_POST['x5'] !== ""
) {

    // Dane do połączenia z bazą danych
    $dbhost = "127.0.0.1";
    $dbuser = "dm81079_z12a";
    $dbpassword = "Dawidek7003#";
    $dbname = "dm81079_z12a";

    // Nawiązanie połączenia z bazą danych
    $conn = mysqli_connect($dbhost, $dbuser, $dbpassword, $dbname);

    // Sprawdzenie czy połączenie się powiodło
    if (!$conn) {
        die("Błąd połączenia z bazą danych");
    }

    // Pobranie danych liczbowych z formularza i konwersja na float
    $x1 = floatval($_POST['x1']);
    $x2 = floatval($_POST['x2']);
    $x3 = floatval($_POST['x3']);
    $x4 = floatval($_POST['x4']);
    $x5 = floatval($_POST['x5']);

    // Pobranie danych tekstowych (np. stan czujników)
    $pozar = $_POST['pozar'];
    $zalanie = $_POST['zalanie'];
    $wlamanie = $_POST['wlamanie'];
    $czujnik_co = $_POST['czujnik_co'];
    $wentylacja = $_POST['wentylacja'];

    // Zapytanie SQL do dodania danych do tabeli "pomiary"
    $sql = "INSERT INTO pomiary
            (x1,x2,x3,x4,x5,pozar,zalanie,wlamanie,czujnik_co,wentylacja)
            VALUES (?,?,?,?,?,?,?,?,?,?)";

    // Przygotowanie zapytania (prepared statement)
    $stmt = mysqli_prepare($conn, $sql);

    // Powiązanie parametrów z zapytaniem
    // d = double (liczba zmiennoprzecinkowa)
    // s = string (tekst)
    mysqli_stmt_bind_param(
        $stmt,
        "ddddssssss",
        $x1,
        $x2,
        $x3,
        $x4,
        $x5,
        $pozar,
        $zalanie,
        $wlamanie,
        $czujnik_co,
        $wentylacja
    );

    // Wykonanie zapytania
    if (mysqli_stmt_execute($stmt)) {
        // Jeśli zapis się udał – przekierowanie na stronę główną
        header("Location: index.php");
        exit();
    } else {
        // Jeśli zapis się nie udał
        echo "Błąd zapisu danych";
    }

    // Zamknięcie zapytania i połączenia z bazą
    mysqli_stmt_close($stmt);
    mysqli_close($conn);

} else {
    // Komunikat jeśli nie wszystkie pola formularza zostały wypełnione
    echo "Wszystkie pola muszą być wypełnione";
}
?>