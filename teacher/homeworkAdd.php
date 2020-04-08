<html>
	<head>
		<title>新增每周作業</title>
		<meta charset="UTF-8" />  <!--MUST HAVE-->
		<link rel="stylesheet" type="text/css" href="css/add.css">
		<link type="text/css" rel="stylesheet" href="css/sidebar.css">
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
		<script src="script/sidebar.js"></script>
		<script src="script/add.js"></script>
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

			$homeworkName = $homeworkDescribe = $homeworkAns = $homeworkNameErr = "";
			$week = 1;

			if ($_SERVER["REQUEST_METHOD"] == "POST") {

				if (empty($_POST["homeworkName"])) $homeworkNameErr = "請輸入作業名稱";
				else $homeworkName = check_input($conn, $_POST["homeworkName"]);

				if (!empty($_POST["homeworkDescribe"])) $homeworkDescribe = check_input($conn, $_POST["homeworkDescribe"]);

				if (empty($_POST["homeworkAns"])) $homeworkAns = "";
				else $homeworkAns = check_input($conn, $_POST["homeworkAns"]);
				
				if (isset($_POST["link"])) $link = check_input($conn, $_POST["link"]);
				else $link="";
				
				$week = (int)$_POST["week"];
			}

			if ($homeworkNameErr=="" and !empty($_POST["homeworkName"])){
				$grade = get_user_grade($conn, $userID);

				$query = "INSERT INTO homework(homeworkName, homeworkDescribe, href, image, grade, answer, week) VALUES" . "('$homeworkName', '$homeworkDescribe', '$link', '', $grade, '$homeworkAns', '$week')";
				$result = query_to_result($conn, $query);
				$homeworkID = get_maxID($conn, 'homework');
				if ($_FILES['image']['tmp_name'] != "") {
					$dir = '../userData/homeworkPic/';
					$name = $homeworkID . '.' . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
					$path = $dir.$name;
					move_uploaded_file($_FILES['image']['tmp_name'], $path);
					$query = "UPDATE homework SET image = '$path' WHERE homeworkID = $homeworkID";
					$result = query_to_result($conn, $query);
				}

				$getUser = "SELECT userID FROM user WHERE grade = $grade AND userType = 2";
				$users = $conn->query($getUser);
				while($data = mysqli_fetch_row($users)){
					$studentID = $data[0];
					$query = "INSERT INTO homework_student(homeworkID, studentID, studentAnswer, status) VALUES ($homeworkID, $studentID, '', 0)";
					$result = query_to_result($conn, $query);
				}
				$conn->close();
				$message = '新增作業成功';
				echo "<script type='text/javascript'> alert('$message'); window.location.replace('index.php');</script>";				

			}
		?>

		<div id="page">
			<h2>新增作業</h2>
			<div class="bulletin">
				<form method="post" enctype="multipart/form-data">
					<label class='week'>第</label>
					<select name="week" value=<?php echo $week;?>>
						<option value="1" <?php if($week==1) echo "selected='selected'";?>>1</option>
						<option value="2" <?php if($week==2) echo "selected='selected'";?>>2</option>
						<option value="3" <?php if($week==3) echo "selected='selected'";?>>3</option>
						<option value="4" <?php if($week==4) echo "selected='selected'";?>>4</option>
						<option value="5" <?php if($week==5) echo "selected='selected'";?>>5</option>
						<option value="6" <?php if($week==6) echo "selected='selected'";?>>6</option>
						<option value="7" <?php if($week==7) echo "selected='selected'";?>>7</option>
						<option value="8" <?php if($week==8) echo "selected='selected'";?>>8</option>
						<option value="9" <?php if($week==9) echo "selected='selected'";?>>9</option>
						<option value="10" <?php if($week==10) echo "selected='selected'";?>>10</option>
						<option value="11" <?php if($week==11) echo "selected='selected'";?>>11</option>
						<option value="12" <?php if($week==12) echo "selected='selected'";?>>12</option>
						<option value="13" <?php if($week==13) echo "selected='selected'";?>>13</option>
						<option value="14" <?php if($week==14) echo "selected='selected'";?>>14</option>
						<option value="15" <?php if($week==15) echo "selected='selected'";?>>15</option>
						<option value="16" <?php if($week==16) echo "selected='selected'";?>>16</option>
						<option value="17" <?php if($week==17) echo "selected='selected'";?>>17</option>
						<option value="18" <?php if($week==18) echo "selected='selected'";?>>18</option>
						<option value="19" <?php if($week==19) echo "selected='selected'";?>>19</option>
						<option value="20" <?php if($week==20) echo "selected='selected'";?>>20</option>
						<option value="21" <?php if($week==21) echo "selected='selected'";?>>21</option>
					</select>
					<label class='week'>週</label>
					<br><br>
					
					<h3>作業名稱：(必填)</h3>
					<input class='input' type="text" name="homeworkName" value="<?php echo $homeworkName;?>"/>
					<span class="error"><?php echo $homeworkNameErr;?></span>
					<br><br>

					<h3>作業描述：</h3>
					<textarea class='input' type="text" name="homeworkDescribe" rows="10" cols="60%"><?php echo $homeworkDescribe;?></textarea>
					<br><br>

					<h3>作業答案：</h3>
					<textarea class='input' type="text" name="homeworkAns" rows="3" cols="60%"><?php echo $homeworkAns;?></textarea>
					<br><br>

					<h3 id='linkLabel'>連結網址：</h3>
					<input class='input' id='linkInput' type='text' name='link'></input>
				
					<h3 id='imgLabel'>新增圖片：</h3>
					<input class='input' id='imgInput' type='file' name='image' accept='image/*'></input>
					<br><br>
					
					<input class="submitButton" type="submit" value="新增作業" />
					<br><br>
				</form>
				<button class="button" onclick="cancel()">取消</button>
			</div>
		</div>
	</body>

</html>
