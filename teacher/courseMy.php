<html>
	<head>
		<title>所有課程</title>
		<meta charset="UTF-8" CONTENT="NO-CACHE"/>
		<link type="text/css" rel="stylesheet" href="css/my.css">
		<link type="text/css" rel="stylesheet" href="css/sidebar.css">
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
		<script src="script/sidebar.js"></script>
		<script type="text/javascript">
			function show_more(id) {
				var x = document.getElementById(id);
				if (x.style.display === "none") x.style.display = "block";
				else x.style.display = "none";
			}
			function update_course(id) {
				$.ajax({
					type: 'POST',
					url: 'ajax/sessionAddCourseID.php',
					data: {courseID: id},
					success: function(result) {
						window.location.replace('update/updateCourse.php')
					}
				});
			}
			function del_stu(studentID, courseID){
				if (confirm("確認退選，退選後無法重新選課")){
					$.ajax({
						type: 'POST',
						url: 'ajax/delStuFromCourse.php',
						data: {delStuID: studentID, delCourseID: courseID},
						success: function(result) {
							alert('退選成功');
							window.location.replace('courseMy.php');
						}
					});
				}
			}
		</script>		
	</head>
	
	<body>
		<div id="page">
			<h2>所有課程</h2>
			<div class="bulletin">
				<?php 
					session_start();
					require_once '../loginMySQL.php';
					require_once 'functions.php';
					$userID = $_SESSION['userID'];
					$dir = '../userData/head/'. $userID.'.png';
					$default = '../userData/head/default.jpg';
					echoSideBar($userID,$dir,$default);
					$conn = connect_mysql($hn, $un, $pw, $dbData);
					
					echo "<h3>我的課程</h3>";
					$query = "SELECT * FROM course WHERE courseID in (SELECT courseID FROM course_teacher WHERE teacherID = $userID) ORDER BY compulsory DESC, lastEditTime DESC";
					echo_course($conn, $query, $dbData);
					
					echo "<br><h3>其他課程</h3>";
					$query = "SELECT * FROM course WHERE courseID in (SELECT courseID FROM course_teacher WHERE teacherID != $userID) ORDER BY compulsory DESC, lastEditTime DESC";
					echo_course($conn, $query, $dbData);
				?>
			</div>
		</div>
	</body>

</html>

