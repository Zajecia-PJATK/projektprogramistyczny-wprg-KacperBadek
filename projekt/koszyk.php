<?php
include 'ProduktKoszyk.php';
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Koszyk</title>
</head>
<body>

<h2>Produkty w koszyku</h2>

<div>
    <ul>
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
                            echo $row['nazwa'];
                            echo " Ilość: " . $item->quantity;
                            echo " Cena: " . $row['cena'] * $item->quantity . "zł<br>";
                            echo "<button name='action[$index]'>Usuń</button>";
                            echo "</div>";
                            $price += $row['cena'] * $item->quantity;
                        }
                    }

                    $db = null;
                } catch (PDOException $e) {
                    die("Błąd połączenia z bazą danych: " . $e->getMessage());
                }
                echo "Wspólna wartość produktów: " . $price . "zł ";
                echo "<button name='payment'>PŁATNOŚĆ</button>";
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
?>

</body>
</html>