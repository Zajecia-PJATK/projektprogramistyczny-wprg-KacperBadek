<?php
include 'ProduktKoszyk.php';
session_start();
$_SESSION['purchase'] = false;
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
        <a href="sklep_internetowy.php">Strona główna</a>
    </div>

    <div id="szukaj">
        <form method="post">
            <input type="text" placeholder="czego szukasz?" name="searchBar">
            <select name="kategorie">
                <option value="0" selected>Kategorie</option>
                <?php
                try {
                    $db = new PDO("mysql:host=localhost;dbname=sklep", 'root', '');
                    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                    $query = "SELECT id_kategoria, nazwa_kategoria FROM kategorie";
                    $result = $db->query($query);

                    if ($result->rowCount() > 0) {
                        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                            $value = $row['id_kategoria'];
                            echo "<option value='$value'>" . $row['nazwa_kategoria'] . "</option>";
                        }
                    }
                    $db = null;
                } catch (PDOException $e) {
                    die("Błąd połączenia z bazą danych: " . $e->getMessage());
                }
                ?>
            </select>
            <button type="submit" name="search">Szukaj</button>
        </form>

        <?php
        if (isset($_POST['search'])) {
            $_SESSION['szukanyProdukt'] = $_POST['searchBar'];
            $_SESSION['kategoria'] = $_POST['kategorie'];
            header('Location: szukaj_produkt.php');
        }
        ?>

    </div>

    <div id="login">
        <?php
        echo "<form method='post'>";
        if (isset($_SESSION['loggedIn'])) {
            echo "<input type='submit' name='logoff' value='Wyloguj się'>";
        } else {
            echo "<a href='logowanie.php'>Zaloguj się</a>";
        }
        echo "</form>";

        if (isset($_POST['logoff'])) {
            unset($_SESSION['loggedIn']);
            unset($_SESSION['userId']);
            header("Refresh:0");
        }

        ?>
    </div>

    <div id="rejestracja">
        <a href="rejestracja.php">Zarejestruj się</a>
    </div>

    <div id="koszyk" style="float: left margin-right: 10px">
        <?php
        if (!isset($_SESSION['koszyk'])) {
            $_SESSION['koszyk'] = [];
        }

        $basketCount = 0;
        foreach ($_SESSION['koszyk'] as $item) {
            $basketCount += $item->quantity;
        }
        echo "<a href='koszyk.php'>Koszyk: " . $basketCount . "</a>"
        ?>
    </div>

    <div id="admin" style="float: right">
        <?php
        try {
            $db = new PDO("mysql:host=localhost;dbname=sklep", 'root', '');
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $query = "SELECT rodzaj_klienta FROM uzytkownicy WHERE id_uzytkownik = :id";
            $result = $db->prepare($query);
            $result->bindParam(':id', $_SESSION['userId']);
            $result->execute();

            if ($result->rowCount() > 0) {
                $row = $result->fetch(PDO::FETCH_ASSOC);
                if ($row['rodzaj_klienta'] == "admin") {
                    echo "<a href='admin.php'>Panel administracyjny</a>";
                }
            }
            $db = null;
        } catch (PDOException $e) {
            die("Błąd połączenia z bazą danych: " . $e->getMessage());
        }
        ?>
    </div>
</div>