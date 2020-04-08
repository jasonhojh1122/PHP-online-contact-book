<?php
	session_start();
	session_unset();
	session_destroy();
	$_SESSION = array();
	setcookie();
	header("Location: index.php");
	exit;
?>