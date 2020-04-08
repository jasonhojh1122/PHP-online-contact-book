<html>
	<head>
		<title>更新班導</title>
		<meta charset="UTF-8" />  <!--MUST HAVE-->
		<link rel="stylesheet" type="text/css" href="css/add.css">
		<link type="text/css" rel="stylesheet" href="css/sidebar.css">
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
		<script src="script/sidebar.js"></script>
		<script type="text/javascript">
			function cancel(){
				if (confirm("一旦取消，輸入資訊將遺失")) window.location.replace('index.php');
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
			<h2>更新班導</h2>
			<div class="bulletin">
				<form method="post">
					<?php
						$studentOrderAll = array();
						// go through grade				
						for ($grade = 3; $grade <= 6; ++$grade){
							// get student
							$query = "SELECT userID, name FROM user WHERE userType = 2 AND grade = $grade";
							$students = query_to_result($conn, $query);
							
							// get homeroom teacher
							$query = "SELECT userID, name FROM user WHERE userType = 3 AND grade = $grade";
							$teachers = query_to_result($conn, $query);
							$homeroom = array();
							while ($teacher = mysqli_fetch_row($teachers))
								$homeroom[$teacher[0]] = $teacher[1];
							$studentOrder = array();
							
							echo "<label class='grade'>$grade 年級</label><br>";
							echo "<table><tr><th>學生</th><th>班導</th></tr>"; // create table
							
							// go through students
							while ($student = mysqli_fetch_assoc($students)){
								$studentID = $student['userID'];
								$studentName = $student['name'];
								$studentOrder[] = $studentID;
								
								// get current homeroom
								$getCurHomeroom = "SELECT teacherID FROM homeroom_student WHERE studentID = $studentID";
								$curHomeroom = query_to_result($conn, $getCurHomeroom);
								$curHomeroom -> data_seek(0);
								$curHomeroom = $curHomeroom -> fetch_array(MYSQLI_NUM);
								$curHomeroom = $curHomeroom[0];
								$curHomeroom = ($curHomeroom == NULL) ? 0 : $curHomeroom;
								echo "<tr>";
								echo "<td>$studentName</td>";
								echo "<td><select class='input' name='$studentID'>";
								foreach($homeroom as $teacherID => $teacherName){
									if ($curHomeroom != 0 and $teacherID == $curHomeroom) echo "<option value='$teacherID' selected='selected'>$teacherName</option>";
									else echo "<option value='$teacherID'>$teacherName</option>";
								}
								echo "</select></td>";
								echo "</tr>";
							}
							echo "</table>";
							$studentOrderAll[$grade] = $studentOrder;
						}
						echo "<br><input class='submitButton' type='submit' value='更新班導' /><br><br>";
					?>
				</form>
				<?php
					if ($_SERVER["REQUEST_METHOD"] == "POST") {
						for ($grade = 3; $grade <= 6; ++$grade){
							foreach($studentOrderAll[$grade] as $studentID){
								$teacherID = (int)check_input($conn, $_POST[$studentID]);
								$query = "UPDATE homeroom_student SET teacherID = $teacherID WHERE studentID = $studentID";
								$result = query_to_result($conn, $query);
							}
						}
						$conn->close();
						$message = "更新班導";
						echo "<script type='text/javascript'> alert('$message'); window.location.replace('index.php');</script>";
					}
				?>
				<button class="button" onclick="cancel()">取消</button>
			</div>
		</div>
	</body>

</html>
