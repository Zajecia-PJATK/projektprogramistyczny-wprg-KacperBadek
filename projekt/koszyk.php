<?php
include "header.php";
?>
<!DOCTYPE html>
<html>
<head>
    <title>Koszyk</title>
</head>
<body>

<h2>Produkty w koszyku</h2>

<div>
    <ul style="list-style: none">
        <form method="post">
            <?php

            if (empty($_SESSION['koszyk'])) echo "<h3>Twój koszyk jest pusty!</h3>";
            else {
                try {
                    $db = new PDO("mysql:host=localhost;dbname=sklep", 'root', '');
                    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                    $query = "SELECT id_produkt, nazwa, cena, opis, zdjecie FROM produkty WHERE id_produkt = :id";
                    $price = 0;

                    foreach ($_SESSION['koszyk'] as $index => $item) {
                        $id = $item->id;

                        $result = $db->prepare($query);
                        $result->bindParam(':id', $id);
                        $result->execute();

                        if ($result->rowCount() > 0) {
                            $row = $result->fetch(PDO::FETCH_ASSOC);
                            echo "<div>";
                            echo "<li>";
                            echo $row['nazwa'];
                            echo " Ilość: " . $item->quantity;
                            echo " Cena: " . $row['cena'] * $item->quantity . "zł<br>";
                            echo "<button name='action[$index]'>Usuń</button>";
                            echo "</li>";
                            echo "</div>";
                            $price += $row['cena'] * $item->quantity;
                        }
                    }

                    $db = null;
                } catch (PDOException $e) {
                    die("Błąd połączenia z bazą danych: " . $e->getMessage());
                }
                echo "Wspólna wartość produktów: <b>" . $price . "zł</b> ";
                if($_SESSION['loggedIn']){
                    echo "<br><br><button type='submit' name='payment'>PŁATNOŚĆ</button>";
                } else{
                    echo "<br><a href='logowanie.php'>Zaloguj się</a>" . ", aby zamówić!";
                }

            }

            ?>
        </form>
    </ul>
</div>

<?php
if (isset($_POST['action'])) {
    foreach ($_POST['action'] as $index => $action) {
        array_splice($_SESSION['koszyk'], $index, 1);
    }
    header("Refresh:0");
}

if(isset($_POST['payment'])){
    header('Location: zamowienie.php');
}
?>

</body>
</html>