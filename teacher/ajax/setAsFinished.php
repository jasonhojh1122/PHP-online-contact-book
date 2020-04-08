<?php
	require_once '../../loginMySQL.php';
	require_once '../../functions.php';
	$studentID = $_POST['studentID'];
	$homeworkID = $_POST['homeworkID'];

	$conn = connect_mysql($hn, $un, $pw, $dbData);
	
	$query = "UPDATE homework_and_student SET homeworkStatus = 1 WHERE userID = $studentID AND homeworkID = $homeworkID";
	$result = $conn->query($query);
?>