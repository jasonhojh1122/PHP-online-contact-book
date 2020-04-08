<?php 
	session_start(); 
	require_once "../loginMySQL.php";
	require_once "./StudentMyCourseFuncs.php";
	$conn = connect_mysql($hn, $un, $pw, $dbData);
	$userID = $_SESSION['userID'];
?>
<!DOCTYPE html>
<html>
<head>
	<title> My Course </title>
	<meta charset="UTF-8" /> 
	<link rel="stylesheet" type="text/css" href="style/StudentMyCourse.css">
	<link rel="stylesheet" type="text/css" href="../style/sideBar.css">
	<!-- Google Fonts -->
	<link href='https://fonts.googleapis.com/css?family=Schoolbell' rel='stylesheet'>
	<!-- Show or hide course information -->
	<script type="text/javascript" src="js/StudentMainPage.js"></script>
</head>
<body>
	<!-- Background -->
	<div class="bg"><img src="style/BG/blackboard.jpg"></div>
	<!-- SideBar -->
	<?php
		$dir = '../userData/head/'. $userID.'.png';
		$default = '../userData/head/default.jpg';
		echoSideBar($userID,$dir,$default);
	?>
	<div>
		<span class="open" onclick="sideBar_open()"> ☰ </span>
	</div>
	<!-- Title of my course -->
	<div id="courseTitle"> My Course </div>
	<div style="clear: both; height: 20px;"></div>
	<?php
		echoMyCourse($conn, $_SESSION['Grade'],$_SESSION['week'], $userID);
	?>
</body>
</html>