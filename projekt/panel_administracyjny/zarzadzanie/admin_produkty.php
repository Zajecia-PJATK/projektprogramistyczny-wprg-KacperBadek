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
<h2>Produkty</h2>
<!--TABLICA PRODUKTÓW-->
<?php
try {
    $db = new PDO("mysql:host=localhost;dbname=sklep", 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $query = "SELECT produkty.*, kategorie.nazwa_kategoria FROM kategorie INNER JOIN produkty ON kategorie.id_kategoria = produkty.id_kategoria ORDER BY produkty.id_produkt";
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
        echo "<th>nazwa kategorii</th>";
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
            echo "<td>" . $row['nazwa_kategoria'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else echo "<h3>Brak danych</h3>";
    $db = null;
} catch (PDOException $e) {
    die("Błąd połączenia z bazą danych: " . $e->getMessage());
}

?>

<h2>Edycja produktów</h2>
<form method="post">
    <!--EDYCJA PRODUKTÓW-->
    <?php
    //GUZIK EDYCJI
    if (isset($_POST['action'])) {

        try {
            $db = new PDO("mysql:host=localhost;dbname=sklep", 'root', '');
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            foreach ($_POST['action'] as $index => $action) {

                if (!empty($_POST['nazwa'][$index]) && !empty($_POST['cena'][$index]) && is_numeric($_POST['cena'][$index]) &&
                    $_POST['cena'][$index] > 0 && !empty($_POST['zdjecie'][$index]) && !empty($_POST['stan'][$index]) && $_POST['stan'][$index] >= 0 &&
                    !empty($_POST['kategoria'][$index]) && is_numeric($_POST['kategoria'][$index]) &&
                    $_POST['kategoria'][$index] > 0) {

                    $query = "UPDATE produkty SET nazwa = :nazwa, cena = :cena, opis = :opis, zdjecie = :zdjecie, stan_magazynu = :stan, id_kategoria = :kategoria WHERE id_produkt = :id";
                    $result = $db->prepare($query);
                    $result->bindParam(':nazwa', $_POST['nazwa'][$index]);
                    $result->bindParam(':cena', $_POST['cena'][$index]);
                    $result->bindParam(':opis', $_POST['opis'][$index]);
                    $result->bindParam(':zdjecie', $_POST['zdjecie'][$index]);
                    $result->bindParam(':stan', $_POST['stan'][$index]);
                    $result->bindParam(':kategoria', $_POST['kategoria'][$index]);
                    $result->bindParam(':id', $index);

                    if ($result->execute()) echo "Edytowano!";
                    else echo $result->errorInfo()[2];

                } else {
                    echo "Błędne dane!";
                    return;
                }
            }

            $db = null;
        } catch (PDOException $e) {
            die("Błąd połączenia z bazą danych: " . $e->getMessage());
        }
        header('Refresh: 0');
    }
    //FORMULARZ EDYCJI
    try {
        $db = new PDO("mysql:host=localhost;dbname=sklep", 'root', '');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = "SELECT * FROM produkty";
        $queryList = "SELECT id_kategoria, nazwa_kategoria FROM kategorie";
        $result = $db->query($query);
        $resultList = $db->query($queryList);
        $categories = $resultList->fetchAll(PDO::FETCH_ASSOC);

        if ($result->rowCount() > 0) {
            echo "<table>";
            echo "<tr>";
            echo "<th>ID</th>";
            echo "<th>Edycja</th>";
            echo "</tr>";
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $counter = 1;
                echo "<tr>";
                echo "<td>" . $row['id_produkt'] . "</td>";
                echo "<td>
                  nazwa: <input type='text' name='nazwa[" . $row['id_produkt'] . "]' value='" . $row['nazwa'] . "'>
                  cena: <input type='number' name='cena[" . $row['id_produkt'] . "]' value='" . $row['cena'] . "'>
                  opis: <textarea name='opis[" . $row['id_produkt'] . "]'>" . $row['opis'] . "</textarea>
                  zdjęcie: <input type='text' name='zdjecie[" . $row['id_produkt'] . "]' value='" . $row['zdjecie'] . "'>
                  stan magazynu: <input type='number' min='0' name='stan[" . $row['id_produkt'] . "]' value='" . $row['stan_magazynu'] . "'>";

                echo " kategoria: <select name='kategoria[" . $row['id_produkt'] . "]' required>";
                foreach ($categories as $category) {
                    if ($counter == 1) echo "<option value='" . $category['id_kategoria'] . "' selected>";
                    else  echo "<option value='" . $category['id_kategoria'] . "'>";
                    echo $category['nazwa_kategoria'];
                    echo "</option>";
                    $counter++;
                }
                echo "</select>";

                echo "<button type='submit' name='action[" . $row['id_produkt'] . "]'>Edytuj</button>
                  </td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<h3>Brak danych</h3>";
        }
        $db = null;
    } catch (PDOException $e) {
        die("Błąd połączenia z bazą danych: " . $e->getMessage());
    }
    ?>
</form>

<h2>Dodawanie produktów</h2>
<form method="post" enctype="multipart/form-data">
    <fieldset>
        Nazwa: <input type="text" name="nazwa" required><br><br>
        Cena: <input type="number" name="cena" min="1" value="1" required><br><br>
        Opis: <textarea name="opis" required></textarea><br><br>
        Zdjęcie: <input type="file" name="zdjecie" accept="image/jpeg, image/png" required><br><br>
        Stan magazynu: <input type="number" name="stan" min="0" value="0" required><br><br>
        <?php listKategorie(); ?><br><br>
        <button type="submit" name="add">Dodaj</button>
    </fieldset>
</form>
<!--DODAWANIE PRODUKTÓW-->
<?php
if (isset($_POST['add'])) {

    if (!empty($_POST['nazwa']) && !empty($_POST['cena']) && is_numeric($_POST['cena']) &&
        $_POST['cena'] > 0 && !empty($_FILES['zdjecie']['name']) && !empty($_POST['stan']) && is_numeric($_POST['stan']) &&
        $_POST['stan'] >= 0 && !empty($_POST['kategoria']) && is_numeric($_POST['kategoria']) &&
        $_POST['kategoria'] > 0) {

        try {
            $db = new PDO("mysql:host=localhost;dbname=sklep", 'root', '');
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $query = "INSERT INTO PRODUKTY(nazwa, cena, opis, zdjecie, stan_magazynu, id_kategoria) VALUES(:nazwa, :cena, :opis, :zdjecie, :stan, :kategoria)";
            $result = $db->prepare($query);
            $result->bindParam(':nazwa', $_POST['nazwa']);
            $result->bindParam(':cena', $_POST['cena']);
            $result->bindParam(':opis', $_POST['opis']);
            $result->bindParam(':zdjecie', $_FILES['zdjecie']['name']);
            $result->bindParam(':stan', $_POST['stan']);
            $result->bindParam(':kategoria', $_POST['kategoria']);

            $targetDirectory = "../../zdjecia_produktow/";
            $targetFile = $targetDirectory . basename($_FILES["zdjecie"]["name"]);
            $uploadSuccess = move_uploaded_file($_FILES["zdjecie"]["tmp_name"], $targetFile);

            if ($uploadSuccess) echo "File uploaded successfully!";
            else echo "Error uploading file.";

            if ($result->execute()) echo "Dodano!";
            else echo $result->errorInfo()[2];

            $db = null;
        } catch (PDOException $e) {
            die("Błąd połączenia z bazą danych: " . $e->getMessage());
        }
    } else echo "<br>Błędne dane!";
}

function listKategorie()
{
    try {
        $db = new PDO("mysql:host=localhost;dbname=sklep", 'root', '');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $queryList = "SELECT id_kategoria, nazwa_kategoria FROM kategorie";
        $resultList = $db->query($queryList);
        $counter = 1;

        echo " kategoria: <select name='kategoria' required>";
        while ($rowList = $resultList->fetch(PDO::FETCH_ASSOC)) {

            if ($counter == 1) echo "<option value='" . $rowList['id_kategoria'] . "' selected>";
            else  echo "<option value='" . $rowList['id_kategoria'] . "'>";
            echo $rowList['nazwa_kategoria'];
            echo "</option>";
            $counter++;
        }
        echo "</select>";
    } catch (PDOException $e) {
        die("Błąd połączenia z bazą danych: " . $e->getMessage());
    }
}

?>
</body>
</html>