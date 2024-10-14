<?php
@session_start();
$nutzername=isset($_GET['nutzername']) ? $_GET['nutzername'] : null;
$passwort=isset($_GET['passwort']) ? $_GET['passwort'] : null;

if(empty($nutzername) || empty($passwort)) {
	$_SESSION['login_fehler']="Login falsch!";
	header('Location: loginformular.php');
	exit;
}

require_once '../sql/db.php';
$stmt=$db->prepare("SELECT * FROM `benutzer` WHERE nutzername=?");
$stmt->bind_param('s',$nutzername);
$stmt->execute();
$result=$stmt->get_result();
$nutzer=$result->fetch_object();
$result->free();
if(!$nutzer) {
	//nicht gefunden
	$_SESSION['login_fehler']="Login falsch!";
	header('Location: loginformular.php');
	exit;
}
if(!password_verify($passwort,$nutzer->passworthash)) {
	$_SESSION['login_fehler']="Login falsch!";
	header('Location: loginformular.php');
	exit;
}
$_SESSION['nutzer']=$nutzer;
header('Location:../index.php');
exit;
?>