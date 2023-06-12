<?php
include "header.php";
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

<!--OPIS PRODUKTU-->
<?php
try{
$db = new PDO("mysql:host=localhost;dbname=sklep", 'root', '');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$queryOpinion = "SELECT id_produkt, nazwa, cena, opis, zdjecie FROM produkty WHERE id_produkt = :id";
$resultOpinion = $db->prepare($queryOpinion);
$resultOpinion->bindParam(':id', $id);
$resultOpinion->execute();

if ($resultOpinion->rowCount() > 0) {
while ($rowOpinion = $resultOpinion->fetch(PDO::FETCH_ASSOC)) {
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
        $db = null;
        } catch (PDOException $e) {
            die("Błąd połączenia z bazą danych: " . $e->getMessage());
        }
        ?>
        <br><br><br>
        <!--ILOŚĆ SZTUK PRODUKTU W MAGAZYNIE-->
        <form method="post">
            <?php
            try {
                $db = new PDO("mysql:host=localhost;dbname=sklep", 'root', '');
                $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $queryWarehouse = "SELECT stan_magazynu FROM produkty WHERE id_produkt = :id";
                $resultWarehouse = $db->prepare($queryWarehouse);
                $resultWarehouse->bindParam(':id', $id);
                $resultWarehouse->execute();

                if ($resultWarehouse->rowCount() > 0) {
                    $rowWarehouse = $resultWarehouse->fetch(PDO::FETCH_ASSOC);
                    $warehouseState = $rowWarehouse['stan_magazynu'];
                } else $warehouseState = 0;
                $db = null;
            } catch (PDOException $e) {
                die("Błąd połączenia z bazą danych: " . $e->getMessage());
            }
            ?>
            Ilość: <input type="number" name="ilosc" min="1" value="1" max="<?php echo $warehouseState;?>" style="width: 40%">
            <?php echo "z " . $warehouseState . " sztuk"; ?>
            <br><br>
            <button type="submit" name="addBusket">DODAJ DO KOSZYKA</button>
            <button type="submit" name="buyButton">KUP I ZAPŁAĆ</button>
        </form>

        <!--DODANIE PRODUKTÓW DO KOSZYKA-->
        <?php
        $existingIndex = -1;

        if (isset($_POST['addBusket']) && is_numeric($_POST['ilosc']) && $_POST['ilosc'] <= $warehouseState) {
            $obj = new ProduktKoszyk($id, $_POST['ilosc']);

            foreach ($_SESSION['koszyk'] as $index => $value) {
                if ($obj->id === $value->id) {
                    $existingIndex = $index;
                    break;
                }
            }

            if ($existingIndex !== -1) {
                $_SESSION['koszyk'][$existingIndex]->increaseQuantity($obj->quantity);
            } else {
                $_SESSION['koszyk'][] = $obj;
            }
            header('Refresh: 0');
        }

        if (isset($_POST['buyButton']) && is_numeric($_POST['ilosc']) && $_POST['ilosc'] <= $warehouseState) {
            $obj = new ProduktKoszyk($id, $_POST['ilosc']);
            $_SESSION['koszyk'] = [];
            $_SESSION['koszyk'][] = $obj;
            header('Location: zamowienie.php');
        }

        ?>

    </div>
</div>

<!--FORMULARZ DODANIA OPINII DO PRODUKTU-->
<?php
echo "<div style='clear: both'>";
if (!isset($_SESSION['loggedIn'])) {
    echo "<a href='logowanie.php'>Zaloguj się</a>" . ", aby móc dodawać opinie!";
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

    try {
        $stars = intval($_POST['rating']);
        $today = date("Y/m/d");
        $opinion = $_POST['opinion'];
        if (isset($_SESSION['userId'])) $user = $_SESSION['userId'];
        else die("Error");

        $db = new PDO("mysql:host=localhost;dbname=sklep", 'root', '');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $queryAddOpinion = "INSERT INTO opinie (id_produkt, id_uzytkownik, opinia, liczba_gwiazdek, data_wystawienia_opinii) VALUES (:id, :user, :opinion, :stars, '$today')";
        $resultAddOpinion = $db->prepare($queryAddOpinion);
        $resultAddOpinion->bindParam(':id', $id);
        $resultAddOpinion->bindParam(':user', $user);
        $resultAddOpinion->bindParam(':opinion', $opinion);
        $resultAddOpinion->bindParam(':stars', $stars);

        if ($resultAddOpinion->execute()) {
            echo "<br>Dodano opinię!";
        } else {
            echo "Błąd: " . $resultAddOpinion->errorInfo()[2];
        }

        $db = null;
    } catch (PDOException $e) {
        die("Pojebie mnie Błąd połączenia z bazą danych: " . $e->getMessage());
    }
}
?>

<!--LISTA WSZYSTKICH OPINII PRODUKTU-->
<div>
    <h2>Opinie produktu</h2>

    <?php
    echo "<div>";

    try {
        $db = new PDO("mysql:host=localhost;dbname=sklep", 'root', '');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $queryAverage = "SELECT AVG(liczba_gwiazdek) AS srednia FROM opinie WHERE id_produkt = :id";
        $resultAverage = $db->prepare($queryAverage);
        $resultAverage->bindParam(':id', $id);
        $resultAverage->execute();

        if ($resultAverage->rowCount() > 0) {
            $rowAverage = $resultAverage->fetch(PDO::FETCH_ASSOC);
            echo "<h3>Średnia ocena produktu: " . round($rowAverage['srednia'], 2) . "</h3>";
        }
        echo "</div>";


        $queryOpinion = "SELECT liczba_gwiazdek, opinia, id_uzytkownik, data_wystawienia_opinii FROM opinie WHERE id_produkt = :id";
        $resultOpinion = $db->prepare($queryOpinion);
        $resultOpinion->bindParam(':id', $id);
        $resultOpinion->execute();


        echo "<div>";
        echo "<ul style='list-style: none'>";
        if ($resultOpinion->rowCount() > 0) {
            while ($rowOpinion = $resultOpinion->fetch(PDO::FETCH_ASSOC)) {
                echo "<li>";
                $user = $rowOpinion['id_uzytkownik'];
                $queryName = "SELECT imie FROM uzytkownicy WHERE id_uzytkownik = :user";
                $resultName = $db->prepare($queryName);
                $resultName->bindParam(':user', $user);
                $resultName->execute();

                if (!$resultName) {
                    die("Błąd zapytania: " . $db->errorInfo()[2]);
                }
                $rowName = $resultName->fetch(PDO::FETCH_ASSOC);

                echo "Użytkownik: " . $rowName['imie'];
                echo "&nbsp&nbspData:" . $rowOpinion['data_wystawienia_opinii'] . "<br>";
                echo "Ocena: " . $rowOpinion['liczba_gwiazdek'] . "<br>";
                echo "Opinia: " . $rowOpinion['opinia'];
                echo "<li>";
            }
        } else echo "Brak opinii";

        echo "</ul>";
        echo "</div>";
        $db = null;
    } catch (PDOException $e) {
        die("Błąd połączenia z bazą danych: " . $e->getMessage());
    }
    ?>
</div>

<footer id="footer" style="clear:both">
    <h3>All rights reserved by ©me</h3>
</footer>

</body>
</html>
