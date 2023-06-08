<?php
session_start();
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

<?php
$db = new mysqli('localhost', 'root', '', 'sklep');
if ($db->connect_errno) {
    die("Błąd połączenia z bazą danych!");
}

$query = "SELECT id_produkt, nazwa, cena, opis, zdjecie FROM produkty WHERE id_produkt = '$id'";
$result = $db->query($query);

if (!$result) {
    die("Błąd zapytania: " . $db->error);
}

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
       echo 'Produkt: ' . $row['nazwa'].  ' ' . $row['cena'] . ' ' . $row["opis"] . ' ' . '<img src="zdjecia_produktow/' . $row['zdjecie'] . '" alt="Product Image">';
    }
} else echo "0 results";

$db->close();
?>




<footer id="footer">
    <h3>All rights reserved by ©me</h3>
</footer>

</body>
</html>
