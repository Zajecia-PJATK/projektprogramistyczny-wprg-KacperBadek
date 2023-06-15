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
<h2>Zamówienia</h2>
<form method="post">
    <?php

    try {
        $db = new PDO("mysql:host=localhost;dbname=sklep", 'root', '');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = "SELECT * FROM zamowienia";
        $result = $db->query($query);

        if ($result->rowCount() > 0) {
            echo "<table>";
            echo "<tr>";
            echo "<th>id zamowienia</th>";
            echo "<th>id adresu</th>";
            echo "<th>id uzytkownika</th>";
            echo "<th>typ platnosci</th>";
            echo "<th>zaplacona suma</th>";
            echo "<th>dane kontaktowe</th>";
            echo "<th>data zamowienia</th>";
            echo "<th>stan zamowienia</th>";
            echo "<th>Edycja stanu zamowienia</th>";
            echo "</tr>";
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td>" . $row['id_zamowienia'] . "</td>";
                echo "<td>" . $row['id_adres'] . "</td>";
                echo "<td>" . $row['id_uzytkownik'] . "</td>";
                echo "<td>" . $row['typ_platnosci'] . "</td>";
                echo "<td>" . $row['zaplacona_suma'] . "</td>";
                echo "<td>" . $row['dane_kontaktowe'] . "</td>";
                echo "<td>" . $row['data_zamowienia'] . "</td>";
                echo "<td>" . $row['stan_zamowienia'] . "</td>";
                echo "<td>
                  <select name='stan[" . $row['id_zamowienia'] . "]'>
                  <option value='zrealizowane' selected>zrealizowane</option>
                  <option value='w_trakcie_realizacji'>w trakcie realizacji</option></select> 
                  <button type='submit' name='action[" . $row['id_zamowienia'] . "]'>edytuj</button>
                  </td>";

                echo "</tr>";
            }
            echo "</table>";
        } else echo "<h3>Brak danych</h3>";
        $db = null;
    } catch (PDOException $e) {
        die("Błąd połączenia z bazą danych: " . $e->getMessage());
    }
    ?>
</form>

<?php

if (isset($_POST['action'])) {

    try {
        $db = new PDO("mysql:host=localhost;dbname=sklep", 'root', '');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        foreach ($_POST['action'] as $index => $action) {

            $query = "UPDATE zamowienia SET stan_zamowienia = :stan WHERE id_zamowienia = :id";
            $result = $db->prepare($query);
            $result->bindParam(':stan', $_POST['stan'][$index]);
            $result->bindParam(':id', $index);

            if($result->execute()) echo "Edytowano!";
            else echo "Błąd!";
        }
        $db = null;
    } catch (PDOException $e) {
        die("Błąd połączenia z bazą danych: " . $e->getMessage());
    }
    header('Refresh: 0');
}
?>

</body>
</html>