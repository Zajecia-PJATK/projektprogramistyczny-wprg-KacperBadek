<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
</head>
<body>
<h2>Rejestracja</h2>
<form method="post">
    Imie: <input type="text" name="imie"><br><br>
    Nazwisko: <input type="text" name="nazwisko"><br><br>
    E-mail: <input type="email" name="email"><br><br>
    Hasło: <input type="password" name="haslo"><br><br>
    Nr.telefonu: <input type="text" name="telefon"><br><br>
    <button type="submit" name="register">Zarejestruj</button>
</form>

<?php
$name_regex = "/^[A-Z][a-z]+$/";
$email_regex = "/^[^.].(([a-zA-Z0-9\.\-\!\#\$\%\&\'\*\+\/\=\?\^\_\`\{\|\}\~])(?!\.\.)){1,64}@{1}[a-zA-Z0-9\-]{1,255}\.[a-zA-Z]{2,3}(\.[a-zA-Z]{2,3})?$/";
$telefon_regex = "/^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{3,6}$/";

$imie = $_POST['imie'];
$nazwisko = $_POST['nazwisko'];
$email = $_POST['email'];
$haslo = $_POST['haslo'];
$telefon = $_POST['telefon'];

if (isset($_POST['register'])) {

    if (!preg_match($name_regex, $imie)) echo "<br>Podano błędne imie!";
    else if (!preg_match($name_regex, $nazwisko)) echo "<br>Podano błędne nazwisko!";
    else if (!preg_match($email_regex, $email)) echo "<br>Podano błędny email!";
    else if (empty($haslo)) echo "<br>Hasło nie może być puste!";
    else if (!preg_match($telefon_regex, $telefon)) echo "<br>Podano błędny nr.telefonu!";

    else {
        $db = new mysqli('localhost', 'root', '', 'sklep');
        if ($db->connect_errno) {
            die("Błąd połączenia z bazą danych!");
        }
        $query = "SELECT Email FROM `users` WHERE Email LIKE '$email'";

        if ($db->query($query)->num_rows > 0) {
            die("Podany e-mail jest już w użyciu!");
        }

        $hash = password_hash($haslo, PASSWORD_DEFAULT);

        $query = "INSERT INTO users (Imie, Nazwisko, Email, Hasło, NrTelefonu) VALUES ('$imie', '$nazwisko', '$email', '$hash', '$telefon')";

        if ($db->query($query)) {
            echo "<br>Zarejestrowano!";
        } else echo printf("Błąd: %s<br />", $db->error);

        $db->close();
    }
}
?>

<?php
$db = new mysqli('localhost', 'root', '', 'sklep');
if ($db->connect_errno) {
    die("Błąd połączenia z bazą danych!");
}

$registered_users = $db->query("SELECT COUNT(UserID) AS total FROM `users`");
$data = $registered_users->fetch_assoc();
echo "<h3>Ilośc zarejestrowanych użytkowników: " . $data['total'] . "</h3>";

$db->close();
?>

</body>
</html>