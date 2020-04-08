<?php 
	session_start(); 
	require_once "../loginMySQL.php";
	require_once "./HRMainPageFuncs.php";
	$conn = connect_mysql($hn, $un, $pw, $dbData);
	$userID = $_SESSION['userID'];
?>
<!DOCTYPE html>
<html>
<head>
	<title>History Announcements</title>
	<meta charset="UTF-8" content="NO-CACHE" />
	<link rel="stylesheet" type="text/css" href="style/HRMainPage.css">
	<link type="text/css" rel="stylesheet" href="../style/sideBar.css">
	<!-- Google Fonts -->
	<link href='https://fonts.googleapis.com/css?family=Schoolbell' rel='stylesheet'>
	<!-- Ajax -->
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script type="text/javascript" src="js/HRMainPage.js"></script>
</head>
<body>
	<!-- Background -->		
	<div class="bg"><img src="style/BG/blackboard.jpg"></div>
	<!-- SideBar & Wanrning From Teacher -->	
	<?php
		$dir = '../userData/head/'. $userID.'.png';
		$default = '../userData/head/default.jpg';
		echoSideBar($userID,$dir,$default);
	?>
	<div>
		<span class="open" onclick="sideBar_open()"> ☰ </span>
	</div>

	<!-- Choose week -->
	<div class="dropdown">
		<button class="dropbtn" onclick="SHOW()">選擇週</button>
		<div class="dropdown-content" style="display: none;">
			<?php
				for($i = 1 ; $i < $_SESSION['week'] ; ++$i){
					$x = $_SESSION['week'] - $i;
		    		echo "<a href='HRAnnouncement.php?chosenWeek=$x' >第 $x 週</a>";
				}
			?>
		</div>
	</div>
	<?php
		if(isset($_GET['chosenWeek'])==false){
			$_GET['chosenWeek'] = $_SESSION['week'];		
		}
	?>
	<div class="title"> Week <?php echo $_GET['chosenWeek'];?> </div>
	<?php
		if($_SERVER['REQUEST_METHOD']=="POST"){
			if(isset($_POST['Signed'])){
				date_default_timezone_set('Asia/Taipei');
				$time = date("Y-m-d H:i:s");
				UpdateSignA($conn,$time,$_GET['chosenWeek'],$userID,$_POST['Signed']);
			}				
		}
		signEnableA($conn,$_GET['chosenWeek'],$userID);
	?>
	<div style="height: 20px;clear:both;"></div>
	<!-- Style for input[type=select] -->
	<script type="text/javascript" src="style/selection.js"></script>
	<!-- Announcement 公告 -->
	<div class="bulletin" >
		<table style="border-collapse: collapse; width: 100%;">
			<?php
				echoBulletin($conn,$_GET['chosenWeek'],$_SESSION['Grade']);
			?>
		</table>
	</div>
	<!-- 課程進度 -->
	<?php
		for($j=0 ; $j < $_SESSION['StuNum'] ; ++$j)
			echoCourseProg($conn,$_GET['chosenWeek'],$_SESSION['Grade'],$userID);
	?>
</body>
</html>