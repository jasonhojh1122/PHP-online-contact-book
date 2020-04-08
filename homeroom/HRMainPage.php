<?php 
	session_start(); 
	require_once "../loginMySQL.php";
	require_once "./HRMainPageFuncs.php";
	$conn = connect_mysql($hn, $un, $pw, $dbData);
	$userID = $_SESSION['userID'];
	$getStu = "SELECT studentID FROM homeroom_student WHERE teacherID = $userID";
	$getStu = $conn->query($getStu);
	$_SESSION['StuNum'] = $getStu->num_rows;
	for($i=0; $i < $_SESSION['StuNum']; ++$i){
		$getStu ->data_seek($i);
		$Stu = $getStu->fetch_array(MYSQLI_NUM);
		$_SESSION['Students'][$i] = $Stu[0];
	}
	$StuGrade = $_SESSION['Students'][0];
	$getGrade = "SELECT grade FROM user WHERE userID = $StuGrade";
	$getGrade = $conn->query($getGrade);
	$getGrade->data_seek(0);
	$getGrade = $getGrade->fetch_array(MYSQLI_NUM);
	$_SESSION['Grade'] = $getGrade[0];
	$getWeek = "SELECT week FROM bulletin WHERE 1 ORDER BY week DESC";
	$getWeek = $conn->query($getWeek);
	$getWeek ->data_seek(0);
	$GetWeek = $getWeek->fetch_array(MYSQLI_NUM);	
	$_SESSION['week'] = $GetWeek[0];
?>
<!DOCTYPE html>
<html>
<head>
	<title>Homeroom's Main Page</title>
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
		<?php
			if($_SERVER['REQUEST_METHOD']=="POST"){
				if(isset($_POST['Signed'])){
					date_default_timezone_set('Asia/Taipei');
					$time = date("Y-m-d H:i:s");
					UpdateSign($conn,$time,$_SESSION['week'],$userID,$_POST['Signed']);
				}				
			}
			systemWarn($conn,$_SESSION['week'],$userID);
			signEnable($conn,$_SESSION['week'],$userID);
		?>
	</div>
	<div style="height: 20px;clear:both;"></div>
	<!-- Announcement 公告 -->
	<div class="bulletin" >
		<table style="border-collapse:collapse;width: 100%;">
			<?php
				echoBulletin($conn,$_SESSION['week'],$_SESSION['Grade']);
			?>
		</table>
	</div>
	<!-- 課程進度 -->
	
	<?php
		for($j=0 ; $j < $_SESSION['StuNum'] ; ++$j){
			echoCourseProg($conn,$_SESSION['week'],$_SESSION['Grade'],$_SESSION['Students'][$j]);
		}
	?>
	<!-- Title of Week Homework -->
	<div class="weekHW">
		<?php
			echoWeekHW($conn,$userID,$_SESSION['Grade'],$_SESSION['week']);
		?>
	</div>
	<!-- Input google calendar -->
	<div id="Calendar">
		<iframe src="https://calendar.google.com/calendar/embed?src=8tjb29onfdbphn4k9bpuboedto%40group.calendar.google.com&ctz=Asia%2FTaipei" style="border: 0" width="800" height="600" frameborder="0" scrolling="no"></iframe>
	</div>	

</body>
</html>