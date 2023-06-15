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

<h2>Kody rabatowe</h2>
<?php

try {
    $db = new PDO("mysql:host=localhost;dbname=sklep", 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $query = "SELECT * FROM kody_rabatowe";
    $result = $db->query($query);

    if ($result->rowCount() > 0) {
        echo "<table>";
        echo "<tr>";
        echo "<th>id kodu</th>";
        echo "<th>kod</th>";
        echo "<th>procent obniżki</th>";
        echo "</tr>";
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>" . $row['id_kod'] . "</td>";
            echo "<td>" . $row['kod'] . "</td>";
            echo "<td>" . $row['obnizka_procent'] . "</td>";
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