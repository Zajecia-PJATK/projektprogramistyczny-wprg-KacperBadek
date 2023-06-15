<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Panel administracyjny</title>
    <link rel="stylesheet" href="..\..\css\panel_administracyjny.css">
</head>
<body>

<h2>Produkty</h2>
<?php

try {
    $db = new PDO("mysql:host=localhost;dbname=sklep", 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $query = "SELECT * FROM produkty";
    $result = $db->query($query);

    if ($result->rowCount() > 0) {
        echo "<table>";
        echo "<tr>";
        echo "<th>id produktu</th>";
        echo "<th>nazwa</th>";
        echo "<th>cena</th>";
        echo "<th>opis</th>";
        echo "<th>zdjecie</th>";
        echo "<th>stan magazynu</th>";
        echo "<th>id kategorii</th>";
        echo "</tr>";
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>" . $row['id_produkt'] . "</td>";
            echo "<td>" . $row['nazwa'] . "</td>";
            echo "<td>" . $row['cena'] . "</td>";
            echo "<td>" . $row['opis'] . "</td>";
            echo "<td>" . $row['zdjecie'] . "</td>";
            echo "<td>" . $row['stan_magazynu'] . "</td>";
            echo "<td>" . $row['id_kategoria'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else echo "<h3>Brak danych</h3>";
    $db = null;
} catch (PDOException $e) {
    die("Błąd połączenia z bazą danych: " . $e->getMessage());
}

?>
</body>
</html>