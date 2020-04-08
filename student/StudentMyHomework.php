<?php 
	session_start(); 
	require_once "../loginMySQL.php";
	require_once "./StudentMyHomeworkFuncs.php";
	$conn = connect_mysql($hn, $un, $pw, $dbData);
	$userID = $_SESSION['userID'];
?>
<!DOCTYPE html>
<html>
<head>
	<title> My Homework </title>
	<meta charset="UTF-8" />
	<link rel="stylesheet" type="text/css" href="style/StudentMyHomework.css">
	<link type="text/css" rel="stylesheet" href="../style/sideBar.css">
	<!-- Google Fonts -->
	<link href='https://fonts.googleapis.com/css?family=Schoolbell' rel='stylesheet'>
	<!-- Ajax -->
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script type="text/javascript" src="js/StudentMainPage.js"></script>
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
	<div style="clear: both; height: 20px;"></div>
	<div style="width:88.6%;margin-top: 20px;margin-left:5.7%">
		<table class="mytable">
			<tr style="background-color: #555;font-weight: 500;">
				<td width="10%" align="center"> 週 </td>
				<td width="25%" align="center">作 業 名 稱</td>
				<td width="48%" align="center">作 業 說 明</td>
				<td width="17%" align="center">狀 態</td>
			</tr>
		<?php
			echoHWlist($conn,$userID,$_SESSION['Grade'],$_SESSION['week']);
		?>
		</table>
	</div>
</body>
</html>