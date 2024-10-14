<?php 
@session_start();

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Einloggen</title>
</head>
<body>
    <div>
        <h3>Beispiel Konten:</h3>
    </div>
    <div>
        <a href="../Loginfunktion/login.php?nutzername=benutzer1&passwort=passwort123">benutzer1</a><br />
        <a href="../Loginfunktion/login.php?nutzername=benutzer2&passwort=geheim321">benutzer2</a><br />
        <a href="../Loginfunktion/login.php?nutzername=benutzer3&passwort=meinPasswort">benutzer3</a><br />
        <a href="../Loginfunktion/login.php?nutzername=benutzer4&passwort=sicher123">benutzer4</a><br />
        <a href="../Loginfunktion/login.php?nutzername=benutzer5&passwort=123456789">benutzer5</a><br />
<?php
if(isset($_SESSION['login_fehler'])) {
	echo $_SESSION['login_fehler'];
	unset($_SESSION['login_fehler']);
}
?>
    </div>
</body>
</html>