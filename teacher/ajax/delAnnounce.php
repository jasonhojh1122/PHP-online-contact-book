<?php
	require_once '../../loginMySQL.php';
	require_once '../functions.php';
	$announceID = $_POST['announceID'];

	$conn = connect_mysql($hn, $un, $pw, $dbData);
	
	$query = "DELETE FROM bulletin WHERE announceID = $announceID";
	$result = $conn->query($query);
?>