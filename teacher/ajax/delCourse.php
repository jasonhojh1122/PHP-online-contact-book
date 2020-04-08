<?php
	require_once '../../loginMySQL.php';
	require_once '../../functions.php';
	$courseID = $_POST['courseID'];

	$conn = connect_mysql($hn, $un, $pw, $dbData);
	
	$query = "DELETE FROM course WHERE courseID = $courseID";
	$result = $conn->query($query);
	
	$query = "DELETE FROM course_teacher WHERE courseID = $courseID";
	$result = $conn->query($query);
	
	$query = "DELETE FROM course_student WHERE courseID = $courseID";
	$result = $conn->query($query);
	
		$query = "DELETE FROM course_progress WHERE courseID = $courseID";
	$result = $conn->query($query);
?>