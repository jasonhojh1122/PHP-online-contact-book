<html>
	<head>
		<title>更新點名表</title>
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
					url: 'ajax/sessionAddAttendanceWeek.php',
					data: {attendanceWeek: week},
					success: function(result) {
						window.location.replace('attendance.php')
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
			<h2>更新點名表</h2>
			<div class="bulletin">
				<h3>0:準時, 1:遲到, 2:未到, 3:請假, 4:節日</h3>
				<form method="post">
					<?php
						// choose week first
						if (!isset($_SESSION['attendanceWeek'])){
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
								if ($i == (int)$_SESSION['attendanceWeek']) echo "<option value='$i' selected='selected'>$i</option>";
								else echo "<option value='$i'>$i</option>";
							}
							echo "</select>";
							echo "<label class='week'>週</label><br><br>";
							
							$week = $_SESSION['attendanceWeek'];
							$courseOrderAll = array();
							$studentOrderAll = array();
							echo "<form>";
							// go through different grade
							for ($grade = 3; $grade <= 6; ++$grade){
								if ($grade == 3) $forWho = array(1, 5, 7);
								else if ($grade == 4) $forWho = array(2, 5, 7);
								else if ($grade == 5) $forWho = array(3, 6, 7);
								else if ($grade == 6) $forWho = array(4, 6, 7);
								$getCourse = "SELECT courseID, courseName, compulsory FROM course WHERE forWho IN (" . implode(',',$forWho) . ") ORDER BY compulsory DESC";
								$courses = $conn->query($getCourse);
								$getStudent = "SELECT userID, name FROM user WHERE grade = $grade AND userType = 2";
								$students = $conn->query($getStudent);
								$courseOrder = array();
								$studentOrder = array();
								echo "<label class='grade'>$grade 年級</label><br>";
								echo "<table><tr><th class='studentName'>學生</th>"; // create table
								// go through courses
								while ($course = mysqli_fetch_assoc($courses)){
									$courseName = $course['courseName'];
									$courseID = $course['courseID'];
									$courseOrder[] = $courseID;
									echo "<th>$courseName</th>";  // create column in table
								}
								echo "</tr>";
								// go through students
								while ($student = mysqli_fetch_assoc($students)){
									$studentID = $student['userID'];
									$studentName = $student['name'];
									$studentOrder[] = $studentID;
									echo "<tr>";
									echo "<td class='studentName'>$studentName</td>";
									foreach($courseOrder as $courseID){
										if(check_in_course($conn, $studentID, $courseID)){ // check if student has take the course
											$getStatus = "SELECT `$week` FROM attendance WHERE studentID = $studentID AND courseID = $courseID";
											$getStatus = $conn->query($getStatus);
											$getStatus->data_seek(0);
											$status = $getStatus->fetch_array(MYSQLI_NUM);
											$status = $status[0];
											$name = $studentID . '_' . $courseID;
											echo "<td><input class='tableInput' name='$name' type='text' value=$status /></td>";
										}
										else echo "<td></td>";				
									}
									echo "</tr>";
								}
								echo "</table>";
								$studentOrderAll[$grade] = $studentOrder;
								$courseOrderAll[$grade] = $courseOrder;
							}
							echo "<input name='week' type='hidden' value=$week />";
							echo "<br><input class='submitButton' type='submit' value='更新點名表' /><br><br>";
							echo "</form>";
						}
					?>
				</form>
				<?php
					if ($_SERVER["REQUEST_METHOD"] == "POST") {
						$week = $_POST['week'];
						for ($grade = 3; $grade <= 6; ++$grade){
							foreach($studentOrderAll[$grade] as $studentID){
								foreach($courseOrderAll[$grade] as $courseID) {
									$name = $studentID . '_' . $courseID;
									if (isset($_POST[$name])){
										$status = (int)check_input($conn, $_POST[$name]);
										$query = "UPDATE attendance SET `$week` = $status WHERE studentID = $studentID AND courseID = $courseID";
										$result = query_to_result($conn, $query);
									}
								}
							}
						}
						$conn->close();
						$message = "更新點名表成功";
						unset($_SESSION["attendanceWeek"]);
						echo "<script type='text/javascript'> alert('$message'); window.location.replace('index.php');</script>";
					}
				?>
				<button class="button" onclick="cancel()">取消</button>
			</div>
		</div>
	</body>

</html>
