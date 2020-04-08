<?php 
	session_start(); 
	require_once "../loginMySQL.php";
	require_once "./StudentChooseCourseFuncs.php";
	$conn = connect_mysql($hn, $un, $pw, $dbData);
	$userID = $_SESSION['userID'];
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8" />  <!-- MUST HAVE -->
	<link type="text/css" rel="stylesheet" href="style/StudentChooseCourse.css">
	<link type="text/css" rel="stylesheet" href="../style/sideBar.css">
	<!-- Google Fonts -->
	<link href='https://fonts.googleapis.com/css?family=Schoolbell' rel='stylesheet'>
	<!-- Ajax -->
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script type="text/javascript" src="js/StudentChooseCourse.js"></script>
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
	<!-- Title of choose course -->
	<span id="courseTitle"> Choose Course </span>
	<!-- Form of courses user choosed -->
	<form method="post">
	<?php
		echoChoose($conn,$userID,$_SESSION['Grade']);
		if(isset($_POST["submit"])){
			if(!empty($_POST["check_list"])){
				foreach($_POST["check_list"] as $selected){
					$courseID = (int)($selected);
					/* Insert choosed course to database */
					$query = "INSERT INTO course_student (studentID, courseID) VALUES ($userID, $courseID)";
					$result = mysqli_query($conn, $query);
					if (!$result) echo "存取資料庫發生錯誤: $query<br> " . $conn->error ."<br><br>";
				}
				$conn->close();
				$message = '選課成功';
				/* Alert msg and redirect to Main Page */
				echo "<script type='text/javascript'> alert('$message'); window.location.replace('StudentMainPage.php');</script>";
			}
		}	
	?>
</body>
</html>