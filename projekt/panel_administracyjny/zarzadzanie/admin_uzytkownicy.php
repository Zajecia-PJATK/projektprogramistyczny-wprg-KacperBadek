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
<h2>Użytkownicy</h2>
<form method="post">
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
            echo "<th>rodzaj użytkownika</th>";
            echo "<th>edycja użytkownika</th>";
            echo "</tr>";
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td>" . $row['id_uzytkownik'] . "</td>";
                echo "<td>" . $row['imie'] . "</td>";
                echo "<td>" . $row['nazwisko'] . "</td>";
                echo "<td>" . $row['email'] . "</td>";
                echo "<td>" . $row['rodzaj_klienta'] . "</td>";
                echo "<td>
                  imie: <input type='text' name='imie[" . $row['id_uzytkownik'] . "]' value='" . $row['imie'] . "'>
                  nazwisko: <input type='text' name='nazwisko[" . $row['id_uzytkownik'] . "]' value='" . $row['nazwisko'] . "'>
                  email: <input type='email' name='email[" . $row['id_uzytkownik'] . "]' value='" . $row['email'] . "'>
                  hasło: <input type='password' name='haslo[" . $row['id_uzytkownik'] . "]'>
                  rodzaj użytkownika:  <select name='rodzaj[" . $row['id_uzytkownik'] . "]'>
                  <option value='klient' selected>klient</option>
                  <option value='admin'>admin</option></select> 
                  <button type='submit' name='action[" . $row['id_uzytkownik'] . "]'>Edytuj</button>
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
$name_regex = "/^[A-Z][a-z]+$/";
$email_regex = "/^[^.].(([a-zA-Z0-9\.\-\!\#\$\%\&\'\*\+\/\=\?\^\_\`\{\|\}\~])(?!\.\.)){1,64}@{1}[a-zA-Z0-9\-]{1,255}\.[a-zA-Z]{2,3}(\.[a-zA-Z]{2,3})?$/";

if (isset($_POST['action'])) {

    try {
        $db = new PDO("mysql:host=localhost;dbname=sklep", 'root', '');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        foreach ($_POST['action'] as $index => $action) {

            if (preg_match($name_regex, $_POST['imie'][$index]) && preg_match($name_regex, $_POST['nazwisko'][$index]) &&
                preg_match($email_regex, $_POST['email'][$index]) && !empty($_POST['rodzaj'][$index])) {

                if (empty($_POST['haslo'][$index])) $query = "UPDATE uzytkownicy SET imie = :imie, nazwisko = :nazwisko, email = :email, rodzaj_klienta = :rodzaj WHERE id_uzytkownik = :id";
                else {
                    $query = "UPDATE uzytkownicy SET imie = :imie, nazwisko = :nazwisko, email = :email, hash_haslo = :haslo , rodzaj_klienta = :rodzaj WHERE id_uzytkownik = :id";
                    $haslo = password_hash($_POST['haslo'], PASSWORD_DEFAULT);
                }

                $result = $db->prepare($query);
                $result->bindParam(':imie', $_POST['imie'][$index]);
                $result->bindParam(':nazwisko', $_POST['nazwisko'][$index]);
                $result->bindParam(':email', $_POST['email'][$index]);
                if (!empty($_POST['haslo'][$index])) $result->bindParam(':haslo', $haslo);
                $result->bindParam(':rodzaj', $_POST['rodzaj'][$index]);
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

<h2>Dodaj użytkownika</h2>

<form method="post">
    Imie: <input type="text" name="imie"><br><br>
    Nazwisko: <input type="text" name="nazwisko"><br><br>
    E-mail: <input type="email" name="email"><br><br>
    Hasło: <input type="password" name="haslo"><br><br>
    Rodzaj użytkownika:<select name="rodzaj">
        <option value="klient" selected>klient</option>
        <option value="admin">admin</option>
    </select><br><br>
    <button type="submit" name="add">Dodaj</button>
</form>

<?php
$imie = $_POST['imie'];
$nazwisko = $_POST['nazwisko'];
$email = $_POST['email'];
$haslo = $_POST['haslo'];

if (isset($_POST['add'])) {

    if (!preg_match($name_regex, $imie)) echo "<br>Podano błędne imie!";
    else if (!preg_match($name_regex, $nazwisko)) echo "<br>Podano błędne nazwisko!";
    else if (!preg_match($email_regex, $email)) echo "<br>Podano błędny email!";
    else if (empty($haslo)) echo "<br>Hasło nie może być puste!";
    else if (!(($_POST['rodzaj'] == 'klient') || ($_POST['rodzaj'] == 'admin'))) echo "<br>Zły rodzaj użytkownika!";

    else {
        try {
            $db = new PDO("mysql:host=localhost;dbname=sklep", 'root', '');
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $query = "SELECT email FROM uzytkownicy WHERE email = :email";
            $result = $db->prepare($query);
            $result->bindParam(':email', $email);
            $result->execute();

            if ($result->rowCount() > 0) {
                die("Podany e-mail jest już w użyciu!");
            }

            $hash = password_hash($haslo, PASSWORD_DEFAULT);

            $query = "INSERT INTO uzytkownicy (imie, nazwisko, email, hash_haslo, rodzaj_klienta) VALUES (:imie, :nazwisko, :email, :hash, :rodzaj)";

            $result = $db->prepare($query);
            $result->bindParam(':imie', $imie);
            $result->bindParam(':nazwisko', $nazwisko);
            $result->bindParam(':email', $email);
            $result->bindParam(':hash', $hash);
            $result->bindParam(':rodzaj', $_POST['rodzaj']);

            if ($result->execute()) {
                echo "<br>Dodano!";
            } else echo printf("Błąd: %s<br>", $result->errorInfo()[2]);

            $db = null;
        } catch (PDOException $e) {
            die("Błąd połączenia z bazą danych: " . $e->getMessage());
        }
    }
}
?>

</body>
</html>