<?php
session_start();
if (isset($_GET['id'])) $id = $_GET['id'];
else echo "Invalid product ID";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sklep</title>
    <link rel="stylesheet" href="strona_glowna.css">
</head>
<body>

<?php
$db = new mysqli('localhost', 'root', '', 'sklep');
if ($db->connect_errno) {
    die("Błąd połączenia z bazą danych!");
}

$queryOpinion = "SELECT id_produkt, nazwa, cena, opis, zdjecie FROM produkty WHERE id_produkt = '$id'";
$resultOpinion = $db->query($queryOpinion);

if (!$resultOpinion) {
    die("Błąd zapytania: " . $db->error);
}

if ($resultOpinion->num_rows > 0) {
while ($rowOpinion = $resultOpinion->fetch_assoc()) {

?>

<div>
    <div id="photo" style="float: left">
        <?php
        echo '<img src="zdjecia_produktow/' . $rowOpinion['zdjecie'] . '" alt="Product Image">';
        ?>
    </div>

    <div id="product_data" style="float: left">
        <?php
        echo $rowOpinion['nazwa'] . "  <h2>" . $rowOpinion['cena'] . " zł</h2><br>";
        echo $rowOpinion['opis'];
        }
        } else echo "0 results";
        $db->close();
        ?>
        <br><br><br>
        <form method="post">
            <button type="submit" name="addBusket">DODAJ DO KOSZYKA</button>
            <button type="submit" name="buyButton">KUP I ZAPŁAĆ</button>
        </form>

    </div>
</div>

<?php
echo "<div style='clear: both'>";
if (!isset($_SESSION['loggedIn'])) {
    echo "Zaloguj się, aby móc dodawać opinie!";
} else {
    echo '
    <fieldset>
        <legend>Dodaj opinie</legend>
        <form method="post">
            Ilość gwiazdek: 1:<input type="radio" name="rating" value="1" required>
            2:<input type="radio" name="rating" value="2">
            3:<input type="radio" name="rating" value="3">
            4:<input type="radio" name="rating" value="4">
            5:<input type="radio" name="rating" value="5"> <br>
            Opinia: <br>
            <textarea cols="30" rows="10" name="opinion" placeholder="napisz tutaj..." required></textarea><br>
            <button type="submit" name="opinionButton">Dodaj opinię</button>
    </fieldset>
    </form>
';
}
echo "</div>";

if (isset($_POST['opinionButton'])) {
    $stars = intval($_POST['rating']);
    $today = date("Y/m/d");
    $opinion = $_POST['opinion'];
    if (isset($_SESSION['userId'])) $user = $_SESSION['userId'];
    else die("Error");

    $db = new mysqli('localhost', 'root', '', 'sklep');
    if ($db->connect_errno) {
        die("Błąd połączenia z bazą danych!");
    }

    $queryOpinion = "INSERT INTO opinie(`id_produkt`, `id_uzytkownik`, `opinia`, `liczba_gwiazdek`, `data_wystawienia_opinii`) VALUES('$id', '$user', '$opinion', '$stars', '$today')";

    if ($db->query($queryOpinion)) {
        echo "<br>Dodano opinię!";
    } else echo printf("Błąd: %s<br />", $db->error);

    $db->close();
}
?>

<div>
    <h2>Opinie produktu</h2>

    <?php
    echo "<div>";
    $db = new mysqli('localhost', 'root', '', 'sklep');
    if ($db->connect_errno) {
        die("Błąd połączenia z bazą danych!");
    }

    $queryAverage = "SELECT AVG(`liczba_gwiazdek`) AS 'srednia' FROM `opinie` WHERE `id_produkt` = '$id'";
    $resultAverage = $db->query($queryAverage);

    if($resultAverage->num_rows > 0){
        $rowAverage = $resultAverage->fetch_assoc();
        echo "<h3>Średnia ocena produktu: " . round($rowAverage['srednia'], 2) . "</h3>";
    }
    echo "</div>";


    $queryOpinion = "SELECT `liczba_gwiazdek`, `opinia`, `id_uzytkownik`, `data_wystawienia_opinii` FROM `opinie` WHERE `id_produkt` = '$id'";
    $resultOpinion = $db->query($queryOpinion);

    echo "<div>";
    echo "<ul style='list-style: none';>";
    if ($resultOpinion->num_rows > 0) {
        while ($rowOpinion = $resultOpinion->fetch_assoc()) {
            echo "<li>";
            $user = $rowOpinion['id_uzytkownik'];
            $queryName = "SELECT `imie` FROM `uzytkownicy` WHERE `id_uzytkownik` = '$user'";
            $resultName = $db->query($queryName);

            if (!$resultName) {
                die("Błąd zapytania: " . $db->error);
            }
            $rowName = $resultName->fetch_assoc();

            echo "Użytkownik: " . $rowName['imie'];
            echo "&nbsp&nbspData:" . $rowOpinion['data_wystawienia_opinii'] . "<br>";
            echo "Ocena: " . $rowOpinion['liczba_gwiazdek'] . "<br>";
            echo "Opinia: " . $rowOpinion['opinia'];
            echo "<li>";
            echo "<div>";
        }
    } else echo "Brak opinii";


    echo "</ul>";
    $db->close();
    ?>
</div>

<footer id="footer" style="clear:both">
    <h3>All rights reserved by ©me</h3>
</footer>

</body>
</html>
