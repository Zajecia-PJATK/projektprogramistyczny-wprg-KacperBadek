<?php
session_start();
if (!$_SESSION['purchase']) {
    header('Location: ../strona_glowna/sklep_internetowy.php');
}
$_SESSION['koszyk'] = [];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Potwierdzenie zamówienia</title>
</head>
<body>
<div style="text-align: center">
    <h2>Dziękujemy za zakupy w naszym sklepie!</h2>
    <h3>Potwierdzenie zamówienia zostało wysłane na twój e-mail!</h3>
    <a href="../strona_glowna/sklep_internetowy.php">Powrót na strone główną</a>
</div>


</body>
</html>
