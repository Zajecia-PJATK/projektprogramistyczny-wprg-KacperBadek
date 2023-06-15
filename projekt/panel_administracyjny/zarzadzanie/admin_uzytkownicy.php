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

<h2>Użytkownicy</h2>
<?php

try {
    $db = new PDO("mysql:host=localhost;dbname=sklep", 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $query = "SELECT * FROM uzytkownicy";
    $result = $db->query($query);

    if ($result->rowCount() > 0) {
        echo "<table>";
        echo "<tr>";
        echo "<th>id uzytkownika</th>";
        echo "<th>imie</th>";
        echo "<th>nazwisko</th>";
        echo "<th>e-mail</th>";
        echo "<th>rodzaj klienta</th>";
        echo "</tr>";
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>" . $row['id_uzytkownik'] . "</td>";
            echo "<td>" . $row['imie'] . "</td>";
            echo "<td>" . $row['nazwisko'] . "</td>";
            echo "<td>" . $row['email'] . "</td>";
            echo "<td>" . $row['rodzaj_klienta'] . "</td>";
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