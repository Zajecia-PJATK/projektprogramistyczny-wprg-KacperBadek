<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Rejestracja</title>
</head>
<body>
<h2>Rejestracja</h2>
<form method="post">
    Imie: <input type="text" name="imie"><br><br>
    Nazwisko: <input type="text" name="nazwisko"><br><br>
    E-mail: <input type="email" name="email"><br><br>
    Hasło: <input type="password" name="haslo"><br><br>
    <button type="submit" name="register">Zarejestruj</button>
</form>

<?php
$name_regex = "/^[A-Z][a-z]+$/";
$email_regex = "/^[^.].(([a-zA-Z0-9\.\-\!\#\$\%\&\'\*\+\/\=\?\^\_\`\{\|\}\~])(?!\.\.)){1,64}@{1}[a-zA-Z0-9\-]{1,255}\.[a-zA-Z]{2,3}(\.[a-zA-Z]{2,3})?$/";

$imie = $_POST['imie'];
$nazwisko = $_POST['nazwisko'];
$email = $_POST['email'];
$haslo = $_POST['haslo'];

if (isset($_POST['register'])) {

    if (!preg_match($name_regex, $imie)) echo "<br>Podano błędne imie!";
    else if (!preg_match($name_regex, $nazwisko)) echo "<br>Podano błędne nazwisko!";
    else if (!preg_match($email_regex, $email)) echo "<br>Podano błędny email!";
    else if (empty($haslo)) echo "<br>Hasło nie może być puste!";

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

            $query = "INSERT INTO uzytkownicy (imie, nazwisko, email, hash_haslo, rodzaj_klienta) VALUES (:imie, :nazwisko, :email, :hash, 'klient')";

            $result = $db->prepare($query);
            $result->bindParam(':imie', $imie);
            $result->bindParam(':nazwisko', $nazwisko);
            $result->bindParam(':email', $email);
            $result->bindParam(':hash', $hash);

            if ($result->execute()) {
                echo "<br>Zarejestrowano!";
            } else echo printf("Błąd: %s<br>", $result->errorInfo()[2]);

            $db = null;
        } catch (PDOException $e) {
            die("Błąd połączenia z bazą danych: " . $e->getMessage());
        }
    }
}
?>

Masz już konto? <a href="logowanie.php">Zaloguj się</a>
</body>
</html>