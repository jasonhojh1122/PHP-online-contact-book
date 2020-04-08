<?php 
	session_start(); 
	require_once "../loginMySQL.php";
	require_once "./StudentMainPageFuncs.php";
	$conn = connect_mysql($hn, $un, $pw, $dbData);
	$userID = $_SESSION['userID'];
	$result = "SELECT * FROM user WHERE userID = $userID";
	$result = $conn->query($result);
	$result ->data_seek(0);
	$getUsrInf = $result->fetch_array(MYSQLI_ASSOC);
	$_SESSION['Name'] = $getUsrInf['name'];
	$_SESSION['Grade'] = $getUsrInf['grade'];
	$getWeek = "SELECT week FROM bulletin WHERE 1 ORDER BY week DESC";
	$getWeek = $conn->query($getWeek);
	$getWeek ->data_seek(0);
	$GetWeek = $getWeek->fetch_array(MYSQLI_NUM);	
	$_SESSION['week'] = $GetWeek[0];
?>
<!DOCTYPE html>
<html>
<head>
	<title>Student's Main Page</title>
	<meta charset="UTF-8" content="NO-CACHE" />
	<link rel="stylesheet" type="text/css" href="style/StudentMainPage.css">
	<link type="text/css" rel="stylesheet" href="../style/sideBar.css">
	<!-- Google Fonts -->
	<link href='https://fonts.googleapis.com/css?family=Schoolbell' rel='stylesheet'>
	<!-- Ajax -->
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script type="text/javascript" src="js/StudentMainPage.js"></script>
</head>
<body>
	<!-- Background -->		
	<!-- <div class="bg"><img src="style/BG/blackboard.jpg"></div> -->
	<!-- SideBar & Wanrning From Teacher -->	
	<?php
		$dir = '../userData/head/'. $userID.'.png';
		$default = '../userData/head/default.jpg';
		echoSideBar($userID,$dir,$default);
	?>
	<div>
		<span class="open" onclick="sideBar_open()"> ☰ </span>
		<?php
			if($_SERVER['REQUEST_METHOD']=="POST"){
				if(isset($_POST['Signed'])){
					date_default_timezone_set('Asia/Taipei');
					$time = date("Y-m-d H:i:s");
					UpdateSign($conn,$time,$_SESSION['week'],$userID,$_POST['Signed']);
				}				
			}
			teacherWarn($conn,$_SESSION['week'],$userID,$_SESSION['Grade']);
			signEnable($conn,$_SESSION['week'],$userID);
		?>
	</div>
	<div style="height: 20px;clear:both;"></div>
	<!-- Announcement 公告 -->
	<div class="bulletin" >
		<table style="border-collapse: collapse; width: 100%;">
			<?php
				echoBulletin($conn,$_SESSION['week'],$_SESSION['Grade']);
			?>
		</table>
	</div>
	<!-- 課程進度 -->
	<div class="courseProg">
		<table style="border-collapse: collapse;width: 100%;">
			<tr style='border-bottom: 1px solid white;font-size:20px;background-color:#444;'>
				<td> ☞ &nbsp; 本週課程 </td>
			</tr>
		</table>
		<table style="border-collapse: collapse;width: 100%;">
			<tr>
				<td align="center" width="25%">課 程</td>
				<td align="center" width="20%">出缺席</td>
				<td align="center" width="55%">本 週 進 度</td>
			</tr>
			<?php
				echoCourseProg($conn,$_SESSION['week'],$_SESSION['Grade'],$userID);
			?>
		</table>
	</div>
	<div style="clear: both;height: 20px;"></div>
	<!-- Title of Week Homework -->
	<div class="weekHW">
		<?php
			echoWeekHW($conn,$userID,$_SESSION['Grade'],$_SESSION['week']);
			echoLastWeekAns($conn,$userID,$_SESSION['Grade'],$_SESSION['week']);
			if($_SERVER['REQUEST_METHOD']=="POST"){
				if(isset($_POST['answer'])){
					UpdateStuAns($conn,$userID,$_SESSION['Grade'],$_SESSION['week'],$_POST['answer']);
				}				
			}
		?>
	</div>
	<!-- Input google calendar -->
	<div id="Calendar">
		<iframe src="https://calendar.google.com/calendar/embed?src=8tjb29onfdbphn4k9bpuboedto%40group.calendar.google.com&ctz=Asia%2FTaipei" style="border: 0" width="800" height="600" frameborder="0" scrolling="no"></iframe>
	</div>	

</body>
</html>