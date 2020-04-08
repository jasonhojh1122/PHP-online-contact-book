<?php
	require_once '../../loginMySQL.php';
	require_once '../functions.php';
	$conn = connect_mysql($hn, $un, $pw, $dbData);
		
	for ($grade = 6; $grade >= 3; --$grade){
		$toSet = $grade + 1;
		if ($grade == 6) $query = "DELETE FROM user WHERE userType = 2 AND grade = 6";
		else $query = "UPDATE user SET grade = $toSet WHERE grade = $grade";
		$result = query_to_result($conn, $query);		
	}	
?>