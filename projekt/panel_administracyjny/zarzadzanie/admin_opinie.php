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
<h2>Opinie</h2>
<form method="post">
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
            echo "<th>edycja opinii</th>";
            echo "<th>usunięcie opinii</th>";
            echo "</tr>";
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td>" . $row['id_opinia'] . "</td>";
                echo "<td>" . $row['id_produkt'] . "</td>";
                echo "<td>" . $row['id_uzytkownik'] . "</td>";
                echo "<td>" . $row['opinia'] . "</td>";
                echo "<td>" . $row['liczba_gwiazdek'] . "</td>";
                echo "<td>" . $row['data_wystawienia_opinii'] . "</td>";
                echo "<td>
                  opinia: <textarea name='opinia[" . $row['id_opinia'] . "]'>" . $row['opinia'] . " </textarea>
                  liczba gwiazdek: <input type='number' min='1' max='5' name='gwiazdki[" . $row['id_opinia'] . "]' value='" . $row['liczba_gwiazdek'] . "'>
                  data wystawienia opinii: <input type='date' name='data[" . $row['id_opinia'] . "]' value='" . $row['data_wystawienia_opinii'] . "'>
                  <button type='submit' name='action[" . $row['id_opinia'] . "]' value='edit'>Edytuj</button>
                  </td>";
                echo "<td><button type='submit' name='action[" . $row['id_opinia'] . "]' value='delete'>Usuń</button></td>";
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

function checkmydate($date)
{
    $tempDate = explode('-', $date);
    return checkdate($tempDate[1], $tempDate[2], $tempDate[0]);
}

if (isset($_POST['action'])) {

    try {
        $db = new PDO("mysql:host=localhost;dbname=sklep", 'root', '');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        foreach ($_POST['action'] as $index => $action) {

            if ($action == 'edit') {

                if (!empty($_POST['opinia'][$index]) && !empty($_POST['gwiazdki'][$index]) && is_numeric($_POST['gwiazdki'][$index])
                    && $_POST['gwiazdki'][$index] > 0 && $_POST['gwiazdki'][$index] < 6 && checkmydate($_POST['data'][$index])) {
                    $query = "UPDATE opinie SET opinia = :opinia, liczba_gwiazdek = :gwiazdki, data_wystawienia_opinii = :data WHERE id_opinia = :id";
                    $result = $db->prepare($query);
                    $result->bindParam(':opinia', $_POST['opinia'][$index]);
                    $result->bindParam(':gwiazdki', $_POST['gwiazdki'][$index]);
                    $result->bindParam(':data', $_POST['data'][$index]);
                    $result->bindParam(':id', $index);

                    if ($result->execute()) echo "Edytowano!";
                    else echo $result->errorInfo()[2];
                } else echo "Błędne dane!";

            } elseif ($action == 'delete') {
                $query = "DELETE FROM opinie WHERE id_opinia = :id";
                $result = $db->prepare($query);
                $result->bindParam(':id', $index);

                if ($result->execute()) echo "Usunięto!";
                else echo $result->errorInfo()[2];
            }
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