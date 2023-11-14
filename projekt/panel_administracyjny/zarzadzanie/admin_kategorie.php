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
<h2>Kategorie</h2>
<form method="post">
    <?php

    try {
        $db = new PDO("mysql:host=localhost;dbname=sklep", 'root', '');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = "SELECT * FROM kategorie";
        $result = $db->query($query);

        if ($result->rowCount() > 0) {
            echo "<table>";
            echo "<tr>";
            echo "<th>id kategorii</th>";
            echo "<th>nazwa</th>";
            echo "<th>id rodzica kategorii</th>";
            echo "<th>edycja kategorii</th>";
            echo "</tr>";
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td>" . $row['id_kategoria'] . "</td>";
                echo "<td>" . $row['nazwa_kategoria'] . "</td>";
                echo "<td>" . $row['id_rodzic_kategoria'] . "</td>";
                echo "<td>
                  nazwa: <input type='text' name='nazwa[" . $row['id_kategoria'] . "]' value='" . $row['nazwa_kategoria'] . "'>
                  id rodzica: <input type='number' name='rodzic[" . $row['id_kategoria'] . "]' value='" . $row['id_rodzic_kategoria'] . "'>
                  <button type='submit' name='action[" . $row['id_kategoria'] . "]'>Edytuj</button>
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

            if (empty($_POST['rodzic'][$index]) || $_POST['rodzic'][$index] == 0) $_POST['rodzic'][$index] = null;

            if (!empty($_POST['nazwa'][$index]) && !is_numeric($_POST['nazwa'][$index]) && is_numeric($_POST['rodzic'][$index]) &&
                $_POST['rodzic'][$index] > 0 || !empty($_POST['nazwa'][$index]) && !is_numeric($_POST['nazwa'][$index]) && is_null($_POST['rodzic'][$index])) {

                $query = "UPDATE kategorie SET nazwa_kategoria = :nazwa, id_rodzic_kategoria = :rodzic WHERE id_kategoria = :id";
                $result = $db->prepare($query);
                $result->bindParam(':nazwa', $_POST['nazwa'][$index]);
                $result->bindParam(':rodzic', $_POST['rodzic'][$index]);
                $result->bindParam(':id', $index);

                if ($result->execute()) echo "Edytowano!";
                else echo "Błąd!";
            } else echo "Błędne dane!";
        }
        $db = null;
    } catch (PDOException $e) {
        die("Błąd połączenia z bazą danych: " . $e->getMessage());
    }
    header('Refresh: 0');
}
?>

<h2>Dodaj kategorie</h2>
<form method="post">
    Nazwa: <input type="text" name="nazwa"><br><br>
    Id rodzica: <input type="number" name="rodzic"><br><br>
    <button type="submit" name="add">Dodaj</button>
</form>

<?php
if (isset($_POST['add'])) {

    if (empty($_POST['rodzic']) || $_POST['rodzic'] == 0) $_POST['rodzic'] = null;


    if (!empty($_POST['nazwa']) && !is_numeric($_POST['nazwa']) && is_numeric($_POST['rodzic']) &&
        $_POST['rodzic'] > 0 || !empty($_POST['nazwa']) && !is_numeric($_POST['nazwa']) && is_null($_POST['rodzic'])) {
        try {
            $db = new PDO("mysql:host=localhost;dbname=sklep", 'root', '');
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $query = "INSERT INTO kategorie(nazwa_kategoria, id_rodzic_kategoria) VALUES(:nazwa, :rodzic)";
            $result = $db->prepare($query);
            $result->bindParam(':nazwa', $_POST['nazwa']);
            $result->bindParam(':rodzic', $_POST['rodzic']);

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