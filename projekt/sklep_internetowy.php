<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Sklep</title>
    <link rel="stylesheet" href="strona_glowna.css">
</head>
<body>

<div id="gora">

    <div id="logo">

    </div>

    <div id="szukaj">
        <input type="text" placeholder="czego szukasz?">
        <input type="button" value="szukaj">
    </div>

    <div id="login">
        <?php
        echo "<form method='post'>";
        if(isset($_SESSION['loggedIn'])){
            echo "<input type='submit' name='logoff' value='Wyloguj się'>";
        } else{
            echo "<a href='logowanie.php'>Zaloguj się</a>";
        }
        echo "</form>";

        if(isset($_POST['logoff'])){
            unset($_SESSION['loggedIn']);
            unset($_SESSION['userId']);
            header("Refresh:0");
        }

        ?>
    </div>

    <div id="rejestracja">
        <a href="rejestracja.php">Zarejestruj się</a>
    </div>

    <div id="koszyk">
        <?php
        if (!isset($_SESSION['koszyk'])) {
            $_SESSION['koszyk'] = [];
        }
        echo "<a href='koszyk.php'>Koszyk:" . count($_SESSION['koszyk']) . " produktów</a>"
        ?>
    </div>

</div>

<div id="srodek">
    <div id="srodek-lewo">
        <h2>Filtry</h2>
    </div>

    <div id="lista">
        <h2>Nasze produkty</h2>

        <?php

        $db = new mysqli('localhost', 'root', '', 'sklep');
        if ($db->connect_errno) {
            die("Błąd połączenia z bazą danych!");
        }

        $query = "SELECT id_produkt, nazwa, cena, opis, zdjecie FROM produkty";
        $result = $db->query($query);

        if (!$result) {
            die("Błąd zapytania: " . $db->error);
        }

        echo "<ul>";
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<a href="sklep_produkt.php?id=' . $row['id_produkt'] . '">' . $row["nazwa"] . '</a> ' . $row["cena"] . 'zł<br>';
            }
        } else echo "0 results";
        echo "</ul>";
        $db->close();

        ?>

    </div>
</div>

<footer id="footer">
    <h3>All rights reserved by ©me</h3>
</footer>

</body>
</html>