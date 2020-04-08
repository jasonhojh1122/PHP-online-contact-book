<?php
	session_start();
	require_once "../loginMySQL.php";
	require_once "./StudentChooseCourseFuncs.php";

	$userID = $_SESSION['userID'];
	$conn = connect_mysql($hn, $un, $pw, $dbData);
	$query = "DELETE FROM course_student WHERE studentID = $userID";
	$result = mysqli_query($conn, $query);
?>