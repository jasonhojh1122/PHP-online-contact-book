<html>
	<head>
		<meta charset="UTF-8" />  <!--MUST HAVE-->
		<link rel="stylesheet" type="text/css" href="../css/update.css">
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
		<script type="text/javascript">
			function del(id) {
				if (confirm("確認刪除，一旦刪除，所有資料連同相關作業，都將如長江般一去不復返")){
					$.ajax({
						type: 'POST',
						url: '../ajax/delCourse.php',
						data: {courseID: id},
						success: function(result) {
							window.location.replace('../courseMy.php')
						}
					});
				}
			}
			function cancel(){
				if (confirm("一旦取消，輸入資訊將遺失")) window.location.replace('../index.php');
			}
		</script>
		<?php
			session_start();

			require_once '../../loginMySQL.php';
			require_once '../functions.php';
			$conn = connect_mysql($hn, $un, $pw, $dbData);

			$courseName = $courseDescribe = $time = $courseNameErr = "";
			$courseID = $_SESSION['courseID'];
			$query = "SELECT * FROM course WHERE courseID = $courseID";
			$result = $conn->query($query);
			if (!$result) echo "存取資料庫發生錯誤: $query<br> " . $conn->error ."<br><br>";
			else{
				$result->data_seek(0);
				$row = $result->fetch_array(MYSQLI_ASSOC);
				$courseName = $row['courseName'];
				$courseDescribe = $row['courseDescribe'];
				$time = $row['time'];
			}

			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				if (empty($_POST["courseName"])) $courseNameErr = "請輸入課程名稱";
				else $courseName = check_input($conn, $_POST["courseName"]);

				$courseDescribe = check_input($conn, $_POST["courseDescribe"]);

				$time = check_input($conn, $_POST["time"]);
			}

			if ($courseNameErr=="" and !empty($_POST["courseName"])){

				$query = "UPDATE course SET courseName = '$courseName', courseDescribe = '$courseDescribe', time = '$time' WHERE courseID = $courseID";
				$result = $conn->query($query);
				if (!$result) echo "更新資料庫錯誤: $query<br> " . $conn->error ."<br><br>";
				else{
					$conn->close();
					$message = "更新課程成功";
					echo "<script type='text/javascript'> alert('$message'); window.location.replace('../courseMy.php');</script>";
				}
			}
		?>
	</head>

	<body>
		<div class="page">
			<h2>更新課程</h2>
			<div class="bulletin">
			<form method="post">
				<label>課程名稱:</label><br>
				<textarea type="text" name="courseName" rows="1" cols="60%"><?php echo $courseName;?></textarea>
				<br><br>

				<label>課程描述:</label><br>
				<textarea type="text" name="courseDescribe" rows="10" cols="60%"><?php echo $courseDescribe;?></textarea>
				<br><br>

				<label>上課時間:</label><br>
				<textarea type="text" name="time" rows="1" cols="60%"><?php echo $time;?></textarea>
				<br><br>

				<input class="button" type="submit" value="更新課程"/>
				<br><br>
			</form>
			<button class="button" onclick="cancel()">取消</button>
			<button class="button" type="button" onclick="del(<?php echo $courseID?>)">刪除課程</button>
			</div>
		</div>
	</body>

</html>

