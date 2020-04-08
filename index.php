<html>
	<head>
		<title>Login Page</title>
		<style>.error {color: #FF0000;}</style>
		<meta charset="UTF-8" />
		<?php
			// redirect to different user page
			function head($userType){
				if($userType == 1) header("Location: teacher/index.php");
				elseif($userType == 2) header("Location: student/StudentMainPage.php");
				elseif($userType == 3) header("Location: homeroom/HRMainPage.php");
			}
		?>
	</head>

	<body>
		<?php
			session_start();
			require_once 'loginMySQL.php';
			require_once 'functions.php';
			$conn = connect_mysql($hn, $un, $pw, $dbData);
			
			// if has already login, redirect to user main page
			if (isset($_SESSION['userID'])){
				$userID = $_SESSION['userID'];
				$query = "SELECT userType FROM user WHERE userID = $userID";
				$result = $conn->query($query);
				$result->data_seek(0);
				$userType = $result->fetch_array(MYSQLI_NUM);
				$userType = $userType[0];
				head($userType);
			}

			else{
				$account = $password = $accountErr = $passwordErr = "";

				if ($_SERVER["REQUEST_METHOD"] == "POST") {
					if (empty($_POST["account"])) $accountErr = "請輸入帳號";
					else {
						$account = check_input($conn, $_POST["account"]);
						$query = "SELECT password FROM user WHERE account = '$account'";
						$result = $conn->query($query);
						if ($result->num_rows == 0) $accountErr = "此帳號不存在";

						if (empty($_POST["password"])) $passwordErr = "請輸入密碼";
						else{
							$password = check_input($conn, $_POST["password"]);
							$result->data_seek(0);
							$correctPassword = $result->fetch_array(MYSQLI_NUM);
							if ($password != $correctPassword[0]) $passwordErr = "密碼錯誤";
							else{
								$query = "SELECT * FROM user WHERE account = '$account'";
								$user = $conn->query($query);
								$user->data_seek(0);
								$userInfo = $user->fetch_array(MYSQLI_NUM);

								$_SESSION['userID'] = $userInfo[0];
								$_SESSION['userName'] = $userInfo[3];
								$userType = $userInfo['userType'];
								head($userType);
							}
						}
					}
				}
			}
		?>
		<h2>民生國小資優班電子聯絡簿</h2>
		<form method="post", id="login-form", autocomplete="nope">
			<label>帳號:</label>
			<input type="text" name="account" value="<?php echo $account ?>" >
			<span class="error"><?php echo $accountErr;?></span>
			<br><br>
			<label>密碼:</label>
			<input type="password" name="password" value="<?php echo $password ?>" >
			<span class="error"><?php echo $passwordErr;?></span>
			<br><br>
			<input type="submit" value="登入">
		</form>
		<br><br><br><br><br><br>
		<p> Version 1.0.2 18.10.01 </p>
		<p> Credit to J. Ho, K. Wang</p>
		<p> Contact us b07902129@ntu.edu.tw</p>
	</body>
</html>