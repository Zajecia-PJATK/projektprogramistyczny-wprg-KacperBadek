<?php
include 'ProduktKoszyk.php';
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
        echo "<a href='koszyk.php'>Koszyk:" . $basketCount . " produktów</a>"
        ?>
    </div>
</div>