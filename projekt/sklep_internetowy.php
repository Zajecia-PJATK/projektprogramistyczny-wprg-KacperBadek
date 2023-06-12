<?php
include "header.php";
?>

<div id="srodek">
    <div id="srodek-lewo">
        <h2>Filtry</h2>
    </div>

    <div id="lista">
        <h2>Nasze produkty</h2>

        <?php

        try {
            $db = new PDO("mysql:host=localhost;dbname=sklep", 'root', '');
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $query = "SELECT id_produkt, nazwa, cena, opis, zdjecie FROM produkty";
            $result = $db->query($query);

            if (!$result) {
                die("Błąd zapytania: " . $db->errorInfo()[2]);
            }

            echo "<ul>";
            if ($result->rowCount() > 0) {
                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                    echo '<a href="sklep_produkt.php?id=' . $row['id_produkt'] . '">' . $row["nazwa"] . '</a> ' . $row["cena"] . 'zł<br>';
                }
            } else echo "0 results";
            echo "</ul>";
            $db = null;
        } catch (PDOException $e) {
            die("Błąd połączenia z bazą danych: " . $e->getMessage());
        }
        ?>
    </div>
</div>

<footer id="footer">
    <h3>All rights reserved by ©me</h3>
</footer>

</body>
</html>