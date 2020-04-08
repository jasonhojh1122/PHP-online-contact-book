<?php 
	session_start(); 
	require_once "../loginMySQL.php";
	require_once "./HRMyHomeworkFuncs.php";
	$conn = connect_mysql($hn, $un, $pw, $dbData);
	$userID = $_SESSION['userID'];
?>
<!DOCTYPE html>
<html>
<head>
	<title> Student's Homework </title>
	<meta charset="UTF-8" />
	<link rel="stylesheet" type="text/css" href="style/HRMyHomework.css">
	<link type="text/css" rel="stylesheet" href="../style/sideBar.css">
	<!-- Google Fonts -->
	<link href='https://fonts.googleapis.com/css?family=Schoolbell' rel='stylesheet'>
	<!-- Ajax -->
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script type="text/javascript" src="js/HRMainPage.js"></script>
</head>
<body>
	<!-- Background -->
	<div class="bg"><img src='style/BG/blackboard.jpg'></div>
	<!-- SideBar -->
	<?php
		$dir = '../userData/head/'. $userID.'.png';
		$default = '../userData/head/default.jpg';
		echoSideBar($userID,$dir,$default);
	?>
	<div>
		<span class="open" onclick="sideBar_open()"> ☰ </span>
	</div>
	<!-- Title of my homework -->
	<div id="MyHWTitle">My Homework</div>
	<?php
		for($j=0 ; $j < $_SESSION['StuNum'] ; ++$j){
			echoHWlist($conn,$_SESSION['Students'][$j],$_SESSION['Grade'],$_SESSION['week']);
		}	
	?>
</body>
</html>