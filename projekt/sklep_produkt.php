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

$query = "SELECT id_produkt, nazwa, cena, opis, zdjecie FROM produkty WHERE id_produkt = '$id'";
$result = $db->query($query);

if (!$result) {
    die("Błąd zapytania: " . $db->error);
}

if ($result->num_rows > 0) {
while ($row = $result->fetch_assoc()) {

?>

<div>
    <div id="photo" style="float: left">
        <?php
        echo '<img src="zdjecia_produktow/' . $row['zdjecie'] . '" alt="Product Image">';
        ?>
    </div>

    <div id="product_data" style="float: left">
        <?php
        echo $row['nazwa'] . "  <h2>" . $row['cena'] . " zł</h2><br>";
        echo $row['opis'];
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


<div id="userOpinion" style="clear: both">
    <fieldset>
        <legend>Dodaj opinie</legend>
    <form method="post">
      Ilość gwiazdek:  1:<input type="radio" name="rating">
        2:<input type="radio" name="rating">
        3:<input type="radio" name="rating">
        4:<input type="radio" name="rating">
        5:<input type="radio" name="rating"> <br>
        Opinia: <br>
        <textarea cols="30" rows="10" name="opinion"></textarea><br>
        <button type="submit" name="opinionButton">Dodaj</button>
    </fieldset>
    </form>
</div>

<div id="listOfOpinions">
    <h2>Opinie produktu</h2>

    <?php
    $db = new mysqli('localhost', 'root', '', 'sklep');
    if ($db->connect_errno) {
        die("Błąd połączenia z bazą danych!");
    }

    $query = "SELECT `liczba_gwiazdek`, `opinia`, `id_uzytkownik`, `data_wystawienia_opinii` FROM `opinie` WHERE `id_produkt` = '$id'";
    $result = $db->query($query);

    if (!$result) {
        die("Błąd zapytania: " . $db->error);
    }

    echo "<ul>";
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo $row['id_uzytkownik'];
            echo $row['liczba_gwiazdek'];
            echo $row['data_wystawienia_opinii'];
            echo $row['opinia'];
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
