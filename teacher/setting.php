<html>
	<head>
		<meta charset="UTF-8" />
		<link rel="stylesheet" type="text/css" href="css/update.css">
		<link type="text/css" rel="stylesheet" href="css/sidebar.css">
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
		<script src="script/sidebar.js"></script>
		<script type="text/javascript">
			function cancel(){
				if (confirm("一旦取消，輸入資訊將遺失")) window.location.replace('index.php');
			}
			function addAttendance(){
				$.ajax({
					type: 'POST',
					url: 'ajax/addAttendance.php',
					success: function(result) {
						alert("更新成功");
						window.location.replace('setting.php')
					}
				});			
			}
		</script>
		<?php
			session_start();
			require_once '../loginMySQL.php';
			require_once 'functions.php';
			$conn = connect_mysql($hn, $un, $pw, $dbData);
			$userID = $_SESSION['userID'];
			$query = "SELECT * FROM config";
			$result = $conn->query($query);
			$result->data_seek(0);
			$row = $result->fetch_array(MYSQLI_NUM);
			$chooseCourse = $row[0];

			$oldPassword = $newPassword = $oldPasswordErr = $newPasswordErr = "";
			
			$query = "SELECT signPass FROM config";
			$result = query_to_result($conn, $query);
			$result->data_seek(0);
			$signPass = $result->fetch_array(MYSQLI_NUM);
			$signPass = $signPass[0];

			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				if (!empty($_POST["oldPassword"])){
					$oldPassword = check_input($conn, $_POST["oldPassword"]);
					$check = $oldPassword;
					$query = "SELECT account FROM user WHERE userID = $userID AND password = '$check'";
					$result = $conn->query($query);
					if ($result->num_rows == 0) $oldPasswordErr = "舊密碼錯誤";
					else{
						$newPassword = check_input($conn, $_POST["newPassword"]);
						if (empty($_POST["newPassword"])) $newPasswordErr = "請輸入新密碼";
						else{
							if (!preg_match("/^[0-9A-Za-z]*$/",$newPassword)) $newPasswordErr = "只接受大小寫英文字母或數字";
							elseif (strlen($newPasswordErr) > 15) $newPasswordErr = "超過上限15字元";
						}
					}
				}
				$chooseCourse = (int)check_input($conn, $_POST["chooseCourse"]);
				$signPass = check_input($conn, $_POST['signPass']);

				if (empty($_POST["oldPassword"]) and empty($_POST["newPassword"])){
					$query = "UPDATE config SET chooseCourse = $chooseCourse, signPass = '$signPass'";
					$result = $conn->query($query);
					$conn->close();
					$message = "設定成功";
					echo "<script type='text/javascript'> alert('$message'); window.location.replace('index.php');</script>";
				}
				else if ($oldPasswordErr=="" and $newPasswordErr=="" and !empty($_POST["oldPassword"]) and !empty($_POST["newPassword"])){
					$input = $newPassword;
					$query = "UPDATE user SET password = '$input' WHERE userID = $userID";
					$result = $conn->query($query);
					$query = "UPDATE config SET chooseCourse = $chooseCourse, signPass = '$signPass'";
					$result = $conn->query($query);
					$conn->close();
					$message = "設定成功";
					echo "<script type='text/javascript'> alert('$message'); window.location.replace('index.php');</script>";
				}
			}
		?>
	</head>

	<body>
		<?php
			$userID = $_SESSION['userID'];
			$dir = '../userData/head/'. $userID.'.png';
			$default = '../userData/head/default.jpg';
			echoSideBar($userID,$dir,$default);
		?>
		<div id="page">
			<h2>設定</h2>
			<div class="bulletin">
			<form method="post">
				<h3>重設密碼</h3>
				<label>舊密碼:</label>
				<input type="text" name="oldPassword" value="<?php echo $oldPassword ?>" >
				<span class="error"><?php echo $oldPasswordErr;?></span>
				<br><br>

				<label>新密碼:</label>
				<input type="text" name="newPassword" value="<?php echo $newPassword ?>" >
				<span class="error"><?php echo $newPasswordErr;?></span>
				<br><br><br>

				<h3>選課系統</h3>
				<select name="chooseCourse">
					<option value="1" <?php if($chooseCourse==1) echo "selected='selected'";?>>開放</option>
					<option value="0" <?php if($chooseCourse==0) echo "selected='selected'";?>>關閉</option>
				</select>
				<br><br><br>
				
				<h3>簽到驗證碼</h3>
				<input type="text" name="signPass" value="<?php echo $signPass ?>" >
				<br><br><br>
				
				<input class="button" type="submit" value="設定"> <br><br>
			</form>
			<button class="button" onclick="addAttendance()">更新點名表</button>(1.選課結束2.選課學生異動，請更新課堂點名表) <br><br>
			<button class="button" onclick="cancel()">取消</button>
			</div>
		</div>
	</body>

</html>