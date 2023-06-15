<?php
include "../includy/header.php";
?>

<div id="srodek">
    <h2>Nasze produkty</h2>

    <div id="lista">

        <?php
        try {
            $db = new PDO("mysql:host=localhost;dbname=sklep", 'root', '');
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $query = "SELECT id_produkt, nazwa, cena, opis, zdjecie FROM produkty";
            $result = $db->query($query);

            //echo "<ul>";
            if ($result->rowCount() > 0) {
                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                    echo "<div id='product'>";
                    echo '<img src="../zdjecia_produktow/' . $row['zdjecie'] . '" alt="Product Image" width = "200" height = "200">';
                    echo "<div class='product-info'>";
                    echo '<a href="../oferta/sklep_produkt.php?id=' . $row['id_produkt'] . '">' . $row["nazwa"] . '</a><br>';
                    echo $row["cena"] . "zł";
                    echo "</div>";
                    echo "</div>";
                }
            } else echo "0 results";
            // echo "</ul>";
            $db = null;
        } catch (PDOException $e) {
            die("Błąd połączenia z bazą danych: " . $e->getMessage());
        }
        ?>
    </div>
</div>

<footer>
    <h3>All rights reserved by ©me</h3>
</footer>

</body>
</html>