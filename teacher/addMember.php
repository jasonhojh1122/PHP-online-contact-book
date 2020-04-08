<html>
	<head>
		<title>新增成員</title>
		<meta charset="UTF-8" CONTENT="NO-CACHE"/>
		<link rel="stylesheet" type="text/css" href="css/add.css">
		<link type="text/css" rel="stylesheet" href="css/sidebar.css">
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
		<script src="script/sidebar.js"></script>		
		<script src="script/add.js"></script>

		<?php
			session_start();
			require_once '../loginMySQL.php';
			require_once 'functions.php';
			$userID = $_SESSION['userID'];
			$dir = '../userData/head/'. $userID.'.png';
			$default = '../userData/head/default.jpg';
			echoSideBar($userID,$dir,$default);
			$conn = connect_mysql($hn, $un, $pw, $dbData);
			$num = $annual = $grade = $numErr = $annualErr = $gradeErr = "";
		?>
	</head>

	<body>
		<div id="page">
			<h2>新增成員</h2>
			<div class="bulletin">
			<?php
				if (isset($_POST["numSub"])){
					if (empty($_POST["num"])) $numErr = "請輸入人數";
					else $num = (int)check_input($conn, $_POST["num"]);
					
					if (empty($_POST["annual"])) $annualErr = "請輸入第幾屆";
					else $annual = (int)check_input($conn, $_POST["annual"]);
					
					if (empty($_POST["grade"])) $annualErr = "請輸入年級";
					else $grade = (int)check_input($conn, $_POST["grade"]);			
				}
			?>
			<h3>請使用阿拉伯數字</h3>
			<h3>使用者類別 資優班老師：1, &nbsp&nbsp&nbsp 學生：2, &nbsp&nbsp&nbsp&nbsp 班導師：3</h3>
			<form method="POST">
				<label class='week'>新增</label>
				<input class='input' name="num" type="text" value="<?php echo $num;?>" />
				<label class='week'>位</label>
				<span class="error"><?php echo $numErr;?></span><br><br>
				
				<label class='week'>第</label>
				<input class='input' name="annual" type="text" value="<?php echo $annual;?>" />
				<label class='week'>屆</label>
				<span class="error"><?php echo $annualErr;?></span><br><br>
				
				<input class='input' name="grade" type="text" value="<?php echo $grade;?>" />
				<label class='week'>年級</label>
				<span class="error"><?php echo $gradeErr;?></span><br><br>				
				
				<input class='submitButton' name="numSub" type="submit" value="確認" />
			</form>
			<br>
			<?php
				if ($numErr=="" and $annualErr=="" and $gradeErr=="" and !empty($_POST["num"]) and !empty($_POST["annual"]) and !empty($_POST["grade"])){
					echo "<form method='POST'><table>";
					echo "<tr> <th></th> <th>姓名</th> <th>使用者類型</th> <th>年級</th> </tr>";
					for ($i = 1; $i <= $num; ++$i){
						$name = 'name'  . $i;
						$type = 'type'  . $i;
						$gd   = 'grade' . $i;
						echo <<< END
						<tr name="$i">
							<td> $i </td>
							<td> <input name="$name" type="text" /> </td>
							<td> <input name="$type" type="text" value=2 /> </td>
							<td> <input name="$gd" type="text" value=$grade /> </td>
						</tr>
END;
					}
					echo "</table>";
					echo "<input name='num' type='hidden' value=$num>";
					echo "<input name='annual' type='hidden' value=$annual>";
					echo "<input class='submitButton' name='dataSub' type='submit' value='新增使用者' /><br>";
					echo "</form>";
				}
				if (isset($_POST["dataSub"])){
					$num = (int)check_input($conn, $_POST["num"]);
					$annual = (int)check_input($conn, $_POST["annual"]);
					// get alreay created account
					$query = "SELECT max(`account`) FROM `user` WHERE `account` DIV 100 = $annual";
					$result = $conn->query($query);
					$result -> data_seek(0);
					$maxAccount = $result -> fetch_array(MYSQLI_NUM);
					$maxAccount = $maxAccount[0];
					$maxAccount = ($maxAccount == NULL) ? 0 : $maxAccount % 100;
					$c = 1;
					for ($i = 1; $i <= $num; ++$i){
						$name = 'name'  . $i;
						$type = 'type'  . $i;
						$gd   = 'grade' . $i;
						$userName = check_input($conn, $_POST[$name]);
						$userType = (int)check_input($conn, $_POST[$type]);
						$userGrade = (int)check_input($conn, $_POST[$gd]);
						if ($userName=="" or $userType=="" or $userGrade=="") continue;
						else{
							if (($c + $maxAccount) < 10) $account = $annual . "0" . ($c + $maxAccount);
							else $account = $annual . ($c + $maxAccount);
							$query = "INSERT INTO user (account, password, name, userType, grade) VALUES ('$account', '$account', '$userName', $userType, $userGrade)";
							$result = $conn->query($query);
							$userID = get_maxID($conn, 'user');
							if ($userType == 2){
								$query = "INSERT INTO sign_parent (studentID) VALUES ($userID)";
								$result = $conn->query($query);		
								$query = "INSERT INTO warning (studentID) VALUES ($userID)";
								$result = $conn->query($query);
								$query = "INSERT INTO homeroom_student (studentID) VALUES ($userID)";
								$result = $conn->query($query);
							}
							else if ($userType == 3){
								$query = "INSERT INTO sign_homeroom (teacherID) VALUES ($userID)";
								$result = $conn->query($query);
							}
							$c++;
						}
					}
				}
			?>
			<button class="button" onclick="cancel()">取消</button>
			</div>
		</div>
	</body>

</html>
