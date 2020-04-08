<?php
	require_once '../../loginMySQL.php';
	require_once '../functions.php';
	$delStuID = $_POST['delStuID'];
	$delCourseID = $_POST['delCourseID'];

	$conn = connect_mysql($hn, $un, $pw, $dbData);
	
	$query = "DELETE FROM course_student WHERE courseID = $delCourseID AND studentID = $delStuID";
	$result = $conn->query($query);
	$query = "DELETE FROM attendance WHERE courseID = $delCourseID AND studentID = $delStuID";
	$result = $conn->query($query);
?>