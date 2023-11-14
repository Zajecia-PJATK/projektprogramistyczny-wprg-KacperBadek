<?php
session_start();

if ($_SESSION['loggedIn']) {
    try {
        $db = new PDO("mysql:host=localhost;dbname=sklep", 'root', '');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = "SELECT rodzaj_klienta FROM uzytkownicy WHERE id_uzytkownik = :id";
        $result = $db->prepare($query);
        $result->bindParam(':id', $_SESSION['userId']);
        $result->execute();

        if ($result->rowCount() > 0) {
            $row = $result->fetch(PDO::FETCH_ASSOC);
            if ($row['rodzaj_klienta'] != "admin") {
                header('Location: ../strona_glowna/sklep_internetowy.php');
            }
        }
        $db = null;
    } catch (PDOException $e) {
        die("Błąd połączenia z bazą danych: " . $e->getMessage());
    }
} else header('Location: ../strona_glowna/sklep_internetowy.php');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Panel administracyjny</title>
</head>
<body>
<h2>Panel Administracyjny</h2>

<form method="post">
    Tabela: <select name="choice">
        <option value="zarzadzanie/admin_zamowienia.php" selected>Zamówienia</option>
        <option value="zarzadzanie/admin_reklamacje.php">Reklamacje</option>
        <option value="zarzadzanie/admin_uzytkownicy.php">Użytkownicy</option>
        <option value="zarzadzanie/admin_produkty.php">Produkty</option>
        <option value="zarzadzanie/admin_opinie.php">Opinie</option>
        <option value="zarzadzanie/admin_kategorie.php">Kategorie</option>
        <option value="zarzadzanie/admin_kody_rabatowe.php">Kody rabatowe</option>
    </select>
    <button type="submit" name="button">zarządzaj</button>
</form>

<br><a href="../strona_glowna/sklep_internetowy.php">Powrót na strone główną</a>

<?php
if (isset($_POST['button'])) {
    $path = $_POST['choice'];
    header('Location: ' . $path);
}
?>

</body>
</html>