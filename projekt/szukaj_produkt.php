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
        if (isset($_SESSION['szukanyProdukt']) && isset($_SESSION['kategoria'])) {
            try {
                $db = new PDO("mysql:host=localhost;dbname=sklep", 'root', '');
                $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $szukajParam = '%' . $_SESSION['szukanyProdukt'] . '%';
                if($_SESSION['kategoria'] != 0){
                    $kategoria = $_SESSION['kategoria'];
                    $query = "SELECT id_produkt, nazwa, cena, opis, zdjecie FROM produkty WHERE (nazwa LIKE :szukajParam OR opis LIKE :szukajParam OR cena LIKE :szukajParam) AND id_kategoria = :kategoria ";
                } else {
                    $query = "SELECT id_produkt, nazwa, cena, opis, zdjecie FROM produkty WHERE nazwa LIKE :szukajParam OR opis LIKE :szukajParam OR cena LIKE :szukajParam";
                }
                $result = $db->prepare($query);
                $result->bindParam('kategoria', $kategoria);
                $result->bindValue(':szukajParam', $szukajParam, PDO::PARAM_STR);
                $result->execute();

                echo "<ul>";
                if ($result->rowCount() > 0) {
                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                        echo '<a href="sklep_produkt.php?id=' . $row['id_produkt'] . '">' . $row["nazwa"] . '</a> ' . $row["cena"] . 'zł<br>';
                    }
                } else echo "Brak wyników";
                echo "</ul>";
                $db = null;
            } catch (PDOException $e) {
                die("Błąd połączenia z bazą danych: " . $e->getMessage());
            }
        } else echo "Brak wyników";
        ?>
    </div>
</div>

<footer id="footer">
    <h3>All rights reserved by ©me</h3>
</footer>

</body>
</html>

