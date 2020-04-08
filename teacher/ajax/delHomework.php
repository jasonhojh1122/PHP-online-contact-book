<?php
	require_once '../../loginMySQL.php';
	require_once '../../functions.php';
	$homeworkID = $_POST['homeworkID'];

	$conn = connect_mysql($hn, $un, $pw, $dbData);

	$query = "DELETE FROM homework WHERE homeworkID = $homeworkID";
	$result =  $conn->query($query);
	
	$query = "DELETE FROM homework_student WHERE homeworkID = $homeworkID";
	$result =  $conn->query($query);
?>