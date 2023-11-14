<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Logowanie</title>
</head>
<body>
<h2>Logowanie</h2>
<form method="post">
    E-mail: <input type="email" name="email"><br><br>
    Hasło: <input type="password" name="haslo"><br><br>
    <button type="submit" name="login">Zaloguj</button>
</form>
<br>
Nie masz konta? <a href="../rejestracja/rejestracja.php">Zarejestruj się</a>

<?php
$email_regex = "/^[^.].(([a-zA-Z0-9\.\-\!\#\$\%\&\'\*\+\/\=\?\^\_\`\{\|\}\~])(?!\.\.)){1,64}@{1}[a-zA-Z0-9\-]{1,255}\.[a-zA-Z]{2,3}(\.[a-zA-Z]{2,3})?$/";

$email = $_POST['email'];
$haslo = $_POST['haslo'];

if (isset($_POST['login'])) {

    if (!preg_match($email_regex, $email)) echo "<br>Podano błędny email!";
    else if (empty($haslo)) echo "<br>Hasło nie może być puste!";

    else {
        try {
            $db = new PDO("mysql:host=localhost;dbname=sklep", 'root', '');
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $query = "SELECT hash_haslo, id_uzytkownik FROM uzytkownicy WHERE email = :email";

            $result = $db->prepare($query);
            $result->bindParam(':email', $email);
            $result->execute();

            if ($result->rowCount() > 0) {
                $row = $result->fetch(PDO::FETCH_ASSOC);
                if (password_verify($haslo, $row['hash_haslo'])) {
                    $_SESSION['loggedIn'] = true;
                    $_SESSION['userId'] = $row['id_uzytkownik'];
                    $db = null;
                    header('Location: ../strona_glowna/sklep_internetowy.php');
                } else {
                    echo "<br>Błędny login lub hasło!";
                }
            } else {
                echo "<br>Konto nie istnieje!";
            }
            $db = null;
        } catch (PDOException $e) {
            die("Błąd połączenia z bazą danych: " . $e->getMessage());
        }
    }
}

?>
</body>
</html>