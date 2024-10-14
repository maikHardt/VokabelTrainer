<?php
@session_start();
if(isset($_SESSION['nutzer'])) {
	unset($_SESSION['nutzer']);
	header('Location:loginformular.php');
	exit;
}
?>