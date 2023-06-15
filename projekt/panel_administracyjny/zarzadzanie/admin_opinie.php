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

<h2>Opinie</h2>
<?php

try {
    $db = new PDO("mysql:host=localhost;dbname=sklep", 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $query = "SELECT * FROM opinie";
    $result = $db->query($query);

    if ($result->rowCount() > 0) {
        echo "<table>";
        echo "<tr>";
        echo "<th>id opinii</th>";
        echo "<th>id produktu</th>";
        echo "<th>id uzytkownika</th>";
        echo "<th>opinia</th>";
        echo "<th>liczba gwiazdek</th>";
        echo "<th>data wystawienia opinii</th>";
        echo "</tr>";
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>" . $row['id_opinia'] . "</td>";
            echo "<td>" . $row['id_produkt'] . "</td>";
            echo "<td>" . $row['id_uzytkownik'] . "</td>";
            echo "<td>" . $row['opinia'] . "</td>";
            echo "<td>" . $row['liczba_gwiazdek'] . "</td>";
            echo "<td>" . $row['data_wystawienia_opinii'] . "</td>";
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