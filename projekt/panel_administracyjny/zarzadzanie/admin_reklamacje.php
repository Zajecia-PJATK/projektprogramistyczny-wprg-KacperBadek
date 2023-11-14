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
<a href="../admin.php">Panel administracyjny</a>
<h2>Reklamacje</h2>
<?php

try {
    $db = new PDO("mysql:host=localhost;dbname=sklep", 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $query = "SELECT * FROM reklamacje";
    $result = $db->query($query);

    if ($result->rowCount() > 0) {
        echo "<table>";
        echo "<tr>";
        echo "<th>id reklamacji</th>";
        echo "<th>id produktu</th>";
        echo "<th>id uzytkownika</th>";
        echo "<th>typ reklamacji</th>";
        echo "<th>data zlozenia reklamacji</th>";
        echo "</tr>";
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>" . $row['id_reklamacja'] . "</td>";
            echo "<td>" . $row['id_produkt'] . "</td>";
            echo "<td>" . $row['id_uzytkownik'] . "</td>";
            echo "<td>" . $row['typ_reklamacji'] . "</td>";
            echo "<td>" . $row['data_zlozenia_reklamacji'] . "</td>";
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