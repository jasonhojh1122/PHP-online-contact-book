<html>
	<head>
		<meta charset="UTF-8" CONTENT="NO-CACHE"/>
		<link rel="stylesheet" type="text/css" href="css/add.css">
		<link type="text/css" rel="stylesheet" href="css/sideBar.css">
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
		<script src="script/index.js"></script>
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
			echoSideBar($userID, '../userData/head/'. $userID.'.png', '../userData/head/default.jpg');
			$conn = connect_mysql($hn, $un, $pw, $dbData);

			$conn = connect_mysql($hn, $un, $pw, $dbData);

			$courseName = $courseDescribe = $time = $forWho = $compulsory = "";
			$courseNameErr = $compulsoryErr = "";

			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				if (empty($_POST["courseName"])) $courseNameErr = "請輸入課程名稱";
				else $courseName = check_input($conn, $_POST["courseName"]);

				if (!empty($_POST["courseDescribe"])) $courseDescribe = check_input($conn, $_POST["courseDescribe"]);

				if (empty($_POST["compulsory"])) $compulsoryErr = "請選擇必修或選修";
				else{
					$compulsory = $_POST["compulsory"];
					$compulsory = ($compulsory == "required" ? 1 : 0);
				}

				$time = check_input($conn, $_POST["time"]);
				$forWho = (int)check_input($conn, $_POST["forWho"]);
			}

			if ($courseNameErr=="" and $compulsoryErr == "" and !empty($_POST["courseName"]) and !empty($_POST["compulsory"])){
				date_default_timezone_set('Asia/Taipei');
				$lastEditTime = date('Y-m-d H:i:s');
				$compulsory = (int)$compulsory;

				$query = "INSERT INTO course(courseName, courseDescribe, time, forWho, compulsory, lastEditTime) VALUES" . "('$courseName', '$courseDescribe', '$time', $forWho, $compulsory, '$lastEditTime')";
				$result = query_to_result($conn, $query);

				$courseID = get_maxID($conn, 'course');

				$userID = $_SESSION['userID'];
				$query = "INSERT INTO course_teacher(courseID, teacherID) VALUES" . "($courseID, $userID)";
				$result = query_to_result($conn, $query);

				$query = "INSERT INTO course_progress (courseID) VALUES ($courseID)";
				$result = query_to_result($conn, $query);
	

				$conn->close();
				$message = "新增課程成功";
				echo "<script type='text/javascript'> alert('$message'); window.location.replace('courseAdd.php');</script>";

			}
		?>
		<div id="page">
			<h2>新增課程</h2>
			<div class="bulletin">
			<form method="post">
				<h3>課程名稱：(必填)</h3>
				<input class='input' type="text" name="courseName" value="<?php echo $courseName;?>"></input>
				<span class="error"><?php echo $courseNameErr;?></span>
				<br><br>

				<h3>課程描述：</h3>
				<textarea class='input' type="text" name="courseDescribe" rows="10" cols="60%"><?php echo $courseDescribe;?></textarea>
				<br><br>

				<h3>上課時間：</h3>
				<input class='input' type="text" name="time"><?php echo $time;?></input>
				<br><br>

				<h3>課程對象：</h3>
				<select class='input' name="forWho" value=<?php echo $forWho;?>>
					<option value="1" <?php if($forWho==1) echo "selected='selected'";?>>三年級</option>
					<option value="2" <?php if($forWho==2) echo "selected='selected'";?>>四年級</option>
					<option value="3" <?php if($forWho==3) echo "selected='selected'";?>>五年級</option>
					<option value="4" <?php if($forWho==4) echo "selected='selected'";?>>六年級</option>
					<option value="5" <?php if($forWho==5) echo "selected='selected'";?>>中年級</option>
					<option value="6" <?php if($forWho==6) echo "selected='selected'";?>>高年級</option>
					<option value="7" <?php if($forWho==7) echo "selected='selected'";?>>全體</option>
				</select>
				<br><br>

				<h3>必/選修</h3>
				<input class='input' type="radio" name="compulsory" value="required" <?php if ($compulsory==1) echo "checked";?>/> 必修
				<input class='input' type="radio" name="compulsory" value="optional" <?php if ($compulsory==0) echo "checked";?>/> 選修
				<span class="error"><?php echo $compulsoryErr;?></span>
				<br><br>

				<input class="submitButton" type="submit" value="新增課程" />
				<br><br>
			</form>
			<button class="button" onclick="cancel()">取消</button>
			</div>
		</div>
	</body>

</html>
