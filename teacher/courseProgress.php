<html>
	<head>
		<title>更新課程進度</title>
		<meta charset="UTF-8" />  <!--MUST HAVE-->
		<link rel="stylesheet" type="text/css" href="css/add.css">
		<link type="text/css" rel="stylesheet" href="css/sidebar.css">
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
		<script src="script/sidebar.js"></script>
		<script type="text/javascript">
			function cancel(){
				if (confirm("一旦取消，輸入資訊將遺失")) window.location.replace('index.php');
			}
			function goToWeek(){
				var week = document.getElementById("week").value;
				$.ajax({
					type: 'POST',
					url: 'ajax/sessionAddProgressWeek.php',
					data: {progressWeek: week},
					success: function(result) {
						window.location.replace('courseProgress.php')
					}
				});
			}
		</script>
	</head>

	<body>
		<?php
			session_start();
			require_once '../loginMySQL.php';
			require_once 'functions.php';
			$userID = $_SESSION['userID'];
			$dir = '../userData/head/'. $userID.'.png';
			$default = '../userData/head/default.jpg';
			echoSideBar($userID,$dir,$default);
			$conn = connect_mysql($hn, $un, $pw, $dbData);
		?>

		<div id="page">
			<h2>更新課程進度</h2>
			<div class="bulletin">
				<form method="post">
					<?php
						if (!isset($_SESSION['progressWeek'])){
							echo "<label class='week'>第</label>";
							echo "<select id='week' onchange='goToWeek()'>";
							echo "<option value='0'>?</option>";
							for ($i = 1; $i <= 21; ++$i) echo "<option value='$i'>$i</option>";
							echo "</select>";
							echo "<label class='week'>週</label><br>";
							echo "<label>請先選擇周次</label>";
						}
						else{
							echo "<label class='week'>第</label>";
							echo "<select id='week' onchange='goToWeek()'>";
							echo "<option value='0'>?</option>";
							for ($i = 1; $i <= 21; ++$i){
								if ($i == (int)$_SESSION['progressWeek']) echo "<option value='$i' selected='selected'>$i</option>";
								else echo "<option value='$i'>$i</option>";
							}
							echo "</select>";
							echo "<label class='week'>週</label><br>";
							
							$week = (int)$_SESSION['progressWeek'];
							$courseOrder = array();

							echo "<h3>我的課程</h3><br>";
							$query = "SELECT courseID, courseName, forWho, compulsory FROM course WHERE courseID IN (SELECT courseID FROM course_teacher WHERE teacherID = $userID) ORDER BY forWho DESC, compulsory DESC";
							$courseOrder = echo_course_progress($conn, $query, $week, $dbData, $courseOrder);

							echo "<br><h3>其他老師的課程</h3><br>";
							$query = "SELECT courseID, courseName, forWho, compulsory FROM course WHERE courseID IN (SELECT courseID FROM course_teacher WHERE teacherID != $userID) ORDER BY forWho DESC, compulsory DESC";
							$courseOrder = echo_course_progress($conn, $query, $week, $dbData, $courseOrder);
							
							echo "<br><input class='submitButton' type='submit' value='更新進度' /><br><br>";
						}
					?>
				</form>
				<?php	
					if ($_SERVER["REQUEST_METHOD"] == "POST") {
						foreach($courseOrder as $courseID => $oldProgress) {
							$progress = check_input($conn, $_POST[$courseID]);
							$query = "UPDATE course_progress SET `$week` = '$progress' WHERE courseID = $courseID";
							$result = query_to_result($conn, $query);
						}
						$conn->close();
						$message = "更新進度成功";
						unset($_SESSION["progressWeek"]);
						echo "<script type='text/javascript'> alert('$message'); window.location.replace('index.php');</script>";
					}
				?>
				<button class="button" onclick="cancel()">取消</button>
			</div>
		</div>
	</body>

</html>
