<?php
	require_once 'loginMySQL.php';
	function check_input($conn, $string){
		$string = trim($string);
		return htmlentities(mysql_fix_string($conn, $string));
	}
	
	function mysql_fix_string($conn, $string){
		if (get_magic_quotes_gpc()) $string = stripcslashes($string);
		$string = mysqli_real_escape_string($conn, $string);
		return $string;
	}
	
	function connect_mysql($hn, $un, $pw, $dbData){
		$conn = new mysqli($hn, $un, $pw, $dbData);
		if (!$conn) die("Could not connect: " . mysql_error());
		else mysqli_query($conn, "SET NAMES 'UTF8'");
		return $conn;
	}
?>