<?php
	error_reporting(E_ALL);
	ini_set('display_errors', TRUE);
	ini_set('display_startup_errors', TRUE);
	
	function num_to_alphabet($num){
		return chr($num+64);
	}
	
	require_once '../../phpexcel/PHPExcel.php';
	require_once '../../loginMySQL.php';
	require_once '../functions.php';
	$conn = connect_mysql($hn, $un, $pw, $dbData);
	
	$query = "TRUNCATE TABLE attendance";
	$result = query_to_result($conn, $query);
	$query = "TRUNCATE TABLE bulletin";
	$result = query_to_result($conn, $query);
	$query = "TRUNCATE TABLE bulletin_teacher";
	$result = query_to_result($conn, $query);
	$query = "TRUNCATE TABLE course";
	$result = query_to_result($conn, $query);
	$query = "TRUNCATE TABLE course_progress";
	$result = query_to_result($conn, $query);
	$query = "TRUNCATE TABLE course_student";
	$result = query_to_result($conn, $query);
	$query = "TRUNCATE TABLE course_teacher";
	$result = query_to_result($conn, $query);
	$query = "TRUNCATE TABLE homework";
	$result = query_to_result($conn, $query);
	$query = "TRUNCATE TABLE homework_student";
	$result = query_to_result($conn, $query);
	$query = "TRUNCATE TABLE sign_homeroom";
	$result = query_to_result($conn, $query);
	$query = "TRUNCATE TABLE sign_parent";
	$result = query_to_result($conn, $query);
	$query = "TRUNCATE TABLE warning";
	$result = query_to_result($conn, $query);
?>