<?php
include 'ProduktKoszyk.php';
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Zamówienie</title>
</head>
<body>

<h2>Dane odbiorcy przesyłki</h2>

<form method="post">
    <?php
    $userId = $_SESSION['userId'];
    try {
        $db = new PDO("mysql:host=localhost;dbname=sklep", 'root', '');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = "SELECT imie, nazwisko, email FROM uzytkownicy WHERE id_uzytkownik = :id";
        $result = $db->prepare($query);
        $result->bindParam(':id', $userId);
        $result->execute();

        if ($result->rowCount() > 0) {
            $row = $result->fetch(PDO::FETCH_ASSOC);

            echo "Imię i Nazwisko: " . $row['imie'] . " " . $row['nazwisko'] . "<br><br>";
            $email = $row['email'];
        }

        $db = null;
    } catch (PDOException $e) {
        die("Błąd połączenia z bazą danych: " . $e->getMessage());
    }
    ?>

    Miasto: <input type="text" name="miasto" required><br><br>
    Ulica: <input type="text" name="ulica" required><br><br>
    Kod pocztowy: <input type="text" name="kod" required><br><br>
    Nr domu: <input type="number" name="dom" required><br><br>
    Nr mieszkania: <input type="number" name="mieszkanie"><br><br>
    Nr telefonu: <input type="text" name="telefon" required>

    <h2>Metoda płatności</h2>

    <input type="radio" name="typPlatnosci" value="karta_płatnicza" required> Karta płatnicza<br>
    <input type="radio" name="typPlatnosci" value="Google_Pay"> Google pay<br>
    <input type="radio" name="typPlatnosci" value="BLIK"> BLIK<br>
    <input type="radio" name="typPlatnosci" value="Przelew"> Przelew<br>

    <h2>Podusmowanie</h2>

    <?php
    try {
        $db = new PDO("mysql:host=localhost;dbname=sklep", 'root', '');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = "SELECT cena FROM produkty WHERE id_produkt = :produktId";
        $price = 0;

        foreach ($_SESSION['koszyk'] as $item) {
            $produktId = $item->id;

            $result = $db->prepare($query);
            $result->bindParam(':produktId', $produktId);
            $result->execute();

            if ($result->rowCount() > 0) {
                $row = $result->fetch(PDO::FETCH_ASSOC);
                $price += $row['cena'] * $item->quantity;
            }
        }
        $db = null;
    } catch (PDOException $e) {
        die("Błąd połączenia z bazą danych: " . $e->getMessage());
    }

    echo "Wspólna wartość produktów: <b>" . $price . " zł</b><br><br>";


    ?>
    <button type="submit" name="zamow">Zamów</button>

</form>

<?php
$kod_regex = '/\d{2}-\d{3}/';
$telefon_regex = '/^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{3,6}$/';

if (isset($_POST['zamow']) && !empty($_SESSION['koszyk'])) {

    $mieszkanie = $_POST['mieszkanie'];
    if (empty($mieszkanie)) $mieszkanie = null;

    if (empty($_POST['miasto'])) echo "Nazwa miasta nie może być pusta!";
    elseif (empty($_POST['ulica'])) echo "Nazwa ulicy nie może być pusta!";
    elseif (!preg_match($kod_regex, $_POST['kod'])) echo "Podano zły kod pocztowy!";
    elseif (empty($_POST['dom'])) echo "Numer domu nie może być pusty!";
    elseif (!is_numeric($_POST['dom'])) echo "Podano zły numer domu!";
    elseif (!preg_match($telefon_regex, $_POST['telefon'])) echo "Podano zły numer telefonu!";
    else {

        try {
            $db = new PDO("mysql:host=localhost;dbname=sklep", 'root', '');
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $queryAdress = "SELECT id_adres FROM adres WHERE miasto = :miasto AND ulica = :ulica AND kod_pocztowy = :kod_pocztowy AND nr_domu = :nr_domu AND nr_mieszkania = :nr_mieszkania";
            $resultAdress = $db->prepare($queryAdress);
            $resultAdress->bindParam(':miasto', $_POST['miasto']);
            $resultAdress->bindParam(':ulica', $_POST['ulica']);
            $resultAdress->bindParam(':kod_pocztowy', $_POST['kod']);
            $resultAdress->bindParam(':nr_domu', $_POST['dom']);
            $resultAdress->bindParam(':nr_mieszkania', $mieszkanie);
            $resultAdress->execute();

            $row = $resultAdress->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $adressId = $row['id_adres'];
            } else {
                $queryAdress = "INSERT INTO adres(miasto, ulica, kod_pocztowy, nr_domu, nr_mieszkania) VALUES(:miasto, :ulica, :kod_pocztowy, :nr_domu, :nr_mieszkania)";
                $resultAdress = $db->prepare($queryAdress);
                $resultAdress->bindParam(':miasto', $_POST['miasto']);
                $resultAdress->bindParam(':ulica', $_POST['ulica']);
                $resultAdress->bindParam(':kod_pocztowy', $_POST['kod']);
                $resultAdress->bindParam(':nr_domu', $_POST['dom']);
                $resultAdress->bindParam(':nr_mieszkania', $mieszkanie);

                if (!$resultAdress->execute()) echo printf("Błąd przy dodaniu adresu: %s<br>", $result->errorInfo()[2]);

                $adressId = $db->lastInsertId();
            }

            $today = date("Y-m-d");
            $queryOrder = "INSERT INTO zamowienia(id_adres, id_uzytkownik, typ_platnosci, zaplacona_suma, dane_kontaktowe, data_zamowienia) VALUES(:id_adres, :id_uzytkownik, :typ_platnosci, :zaplacona_suma, :dane_kontaktowe, '$today')";
            $resultOrder = $db->prepare($queryOrder);
            $resultOrder->bindParam(':id_adres', $adressId);
            $resultOrder->bindParam(':id_uzytkownik', $userId);
            $resultOrder->bindParam(':typ_platnosci', $_POST['typPlatnosci']);
            $resultOrder->bindParam(':zaplacona_suma', $price);
            $resultOrder->bindParam(':dane_kontaktowe', $_POST['telefon']);

            if ($resultOrder->execute()) {
                echo "<br>Dodano zamówienie!";
            } else echo printf("Błąd dodania zamówienia: %s<br>", $result->errorInfo()[2]);
            $orderId = $db->lastInsertId();


            $query = "INSERT INTO zamowienia_produkty(id_zamowienia, id_produkt, ilosc) VALUES(:id_zamowienia, :id_produkt, :ilosc)";

            foreach ($_SESSION['koszyk'] as $item) {
                $result = $db->prepare($query);
                $result->bindParam(':id_zamowienia', $orderId);
                $result->bindParam(':id_produkt', $item->id);
                $result->bindParam(':ilosc', $item->quantity);
                $result->execute();
            }

            $querySelect = "SELECT stan_magazynu FROM produkty WHERE id_produkt = :id_produkt";
            $queryUpdate = "UPDATE produkty SET stan_magazynu = :warehouseState WHERE id_produkt = :id_produkt";

            foreach ($_SESSION['koszyk'] as $item) {

                $resultSelect = $db->prepare($querySelect);
                $resultSelect->bindParam(':id_produkt', $item->id);
                $resultSelect->execute();
                $rowSelect = $resultSelect->fetch(PDO::FETCH_ASSOC);
                $wareHouseState = ($rowSelect['stan_magazynu'] - $item->quantity);

                $resultUpdate = $db->prepare($queryUpdate);
                $resultUpdate->bindParam(':warehouseState', $wareHouseState);
                $resultUpdate->bindParam(':id_produkt', $item->id);
                $resultUpdate->execute();
            }
            $db = null;
        } catch (PDOException $e) {
            die("Błąd połączenia z bazą danych: " . $e->getMessage());
        }

        $msg = base64_encode('Dziękujemy za zakupy w naszym sklepie!' . "\r\n\r\n" . 'Twoje zamówienie jest w drodze!');
        $msg = wordwrap($msg, 70);

        $subject = '=?UTF-8?B?' . base64_encode('Potwierdzenie zamówienia') . '?=';

        $from = '=?UTF-8?B?' . base64_encode('Sklep internetowy') . '?= <no-reply@pjwstk.edu.pl>';
        $headers = 'Content-Type: text/plain; charset=utf-8' . "\r\n";
        $headers .= 'Content-Transfer-Encoding: base64' . "\r\n";
        $headers .= 'From: ' . $from . "\r\n";

        mail($email, $subject, $msg, $headers);
        header('Location: zamowienie_potwierdzenie.php');
    }
}

?>

</body>
</html>