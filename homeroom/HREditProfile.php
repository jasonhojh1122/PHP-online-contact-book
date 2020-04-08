<?php 
	session_start(); 
	require_once "../loginMySQL.php";
	require_once "./HRMainPageFuncs.php";
	$conn = connect_mysql($hn, $un, $pw, $dbData);
	$userID = $_SESSION['userID'];
?>
<!DOCTYPE html>
<html>
<head>
	<title>Edit Profile</title>
	<meta charset="UTF-8"/>
	<link rel="stylesheet" type="text/css" href="style/HREditProfile.css">
	<link type="text/css" rel="stylesheet" href="../style/sideBar.css">
	<!-- Google Fonts -->
	<link href='https://fonts.googleapis.com/css?family=Schoolbell' rel='stylesheet'>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script type="text/javascript" src="js/HRMainPage.js"></script>
</head>
<body>
	<!-- Background -->
	<div class="bg"><img src="style/BG/blackboard.jpg"/></div>
	<!-- SideBar -->
	<?php
		$dir = '../userData/head/'. $userID.'.png';
		$default = '../userData/head/default.jpg';
		echoSideBar($userID,$dir,$default);
	?>
	<div>
		<span class="open" onclick="sideBar_open()"> ☰ </span>
	</div>
	<!-- Title of edit profile -->
	<div id="EditTitle">Edit Profile</div>
	<div style="height: 40px;clear:both;"></div>
	<!-- Form of changing password -->
	<form method="post"> 
		<div id="EditContent">
			<label>Enter Old Password :</label>
			<input type="password" name="OldPassword"/> <br/>
			<label>Enter New Password :</label>
			<input type="password" name="NewPassword"/> <br/>	
			<label>Confirm New Password :</label>
			<input type="password" name="ConfirmPassword"/>
		</div>
		<div>
			<input id="okbutton" type="submit" value="">
		</div>
	</form>
	<?php
		if (isset($_POST['OldPassword'],$_POST['NewPassword'],$_POST['ConfirmPassword'])){
			$psw = "SELECT password FROM user WHERE userID = $userID";
			$password = $conn->query($psw);
			if (!$password) echo "存取資料庫發生錯誤: $query<br> " . $conn->error ."<br><br>";
			else{
				$password -> data_seek(0);
				$password = $password -> fetch_array(MYSQLI_NUM);
				$password = $password[0];
				if ( $password == $_POST['OldPassword'] ){
					if($_POST['OldPassword'] !== $_POST['NewPassword']){
						if ($_POST['NewPassword'] == $_POST['ConfirmPassword']) {
							check_input($conn, $_POST['NewPassword']);
							$NewPassword = $_POST['NewPassword'];
							$newpsw = "UPDATE user SET password = '$NewPassword' WHERE userID = $userID";
							$conn->query($newpsw);
						}
						else{
							echo" <script> alert('「確認新密碼」寫入發生錯誤!'); </script>";						
						}	
					}
					else{
						echo" <script> alert('舊密碼與新密碼不得相同!'); </script>";				
					}
				}
				else{
					echo" <script> alert('舊密碼錯誤!'); </script>";
				}		
			}			
		}
	?>
</body>
</html>