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
<h2>Kody rabatowe</h2>
<form method="post">
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
            echo "<th>obniżka</th>";
            echo "<th>edycja kodów</th>";
            echo "<th>usunięcie kodu</th>";
            echo "</tr>";
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td>" . $row['id_kod'] . "</td>";
                echo "<td>" . $row['kod'] . "</td>";
                echo "<td>" . $row['obnizka_procent'] . "%</td>";
                echo "<td>
                  kod: <input type='text' name='kod[" . $row['id_kod'] . "]' value='" . $row['kod'] . "'>
                  obniżka: <input type='number' name='obnizka[" . $row['id_kod'] . "]' value='" . $row['obnizka_procent'] . "'>%
                  <button type='submit' name='action[" . $row['id_kod'] . "]' value='edit'>Edytuj</button>
                  </td>";
                echo "<td><button name='action[" . $row['id_kod'] . "]' value='delete'>Usuń</button></td>";
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

            if ($action == 'edit') {

                if (!empty($_POST['kod'][$index]) && !empty($_POST['obnizka'][$index]) && is_numeric($_POST['obnizka'][$index]) &&
                    $_POST['obnizka'][$index] > 0 && $_POST['obnizka'][$index] < 100) {

                    $query = "UPDATE kody_rabatowe SET kod = :kod, obnizka_procent = :obnizka WHERE id_kod = :id";
                    $result = $db->prepare($query);
                    $result->bindParam(':kod', $_POST['kod'][$index]);
                    $result->bindParam(':obnizka', $_POST['obnizka'][$index]);
                    $result->bindParam(':id', $index);

                    if ($result->execute()) echo "Edytowano!";
                    else echo $result->errorInfo()[2];
                } else echo "Błędne dane!";

            } elseif ($action == 'delete') {
                $query = "DELETE FROM kody_rabatowe WHERE id_kod = :id";
                $result = $db->prepare($query);
                $result->bindParam(':id', $index);

                if ($result->execute()) echo "Edytowano!";
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

<h2>Dodaj kod rabatowy</h2>
<form method="post">
    Kod: <input type="text" name="kod"><br><br>
    Obniżka: <input type="number" name="obnizka"><br><br>
    <button type="submit" name="add">Dodaj</button>
</form>

<?php

if (isset($_POST['add'])) {
    if (!empty($_POST['kod']) && !empty($_POST['obnizka']) && is_numeric($_POST['obnizka']) &&
        $_POST['obnizka'] > 0 && $_POST['obnizka'] < 100) {
        try {
            $db = new PDO("mysql:host=localhost;dbname=sklep", 'root', '');
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $query = "INSERT INTO kody_rabatowe(kod, obnizka_procent) VALUES(:kod, :obnizka)";
            $result = $db->prepare($query);
            $result->bindParam(':kod', $_POST['kod']);
            $result->bindParam(':obnizka', $_POST['obnizka']);

            if ($result->execute()) echo "Dodano!";
            else echo $result->errorInfo()[2];
        } catch (PDOException $e) {
            die("Błąd połączenia z bazą danych: " . $e->getMessage());
        }
        header('Refresh: 0');
    } else echo "Błędne dane!";
}

?>

</body>
</html>