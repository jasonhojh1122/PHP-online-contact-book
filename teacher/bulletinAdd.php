<html>
	<head>
		<title>更新每周公告</title>
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

			$week = $announceTitle = $announceDescribe = $link = $announceTitleErr = "";

			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				if (empty($_POST["announceTitle"])) $announceTitleErr = "請輸入公告標題";
				else $announceTitle = check_input($conn, $_POST["announceTitle"]);

				if (!empty($_POST["announceDescribe"])) $announceDescribe = check_input($conn, $_POST["announceDescribe"]);
				
				if (isset($_POST["link"])) $link = check_input($conn, $_POST["link"]);
				else $link="";
				
				$week = (int)$_POST["week"];
			}

			if ($announceTitleErr=="" and !empty($_POST["announceTitle"])){
				date_default_timezone_set('Asia/Taipei');
				$lastEditTime = date('Y-m-d H:i:s');
				$grade = get_user_grade($conn, $userID);

				$query = "INSERT INTO bulletin(announceTitle, announceDescribe, href, image, grade, week) VALUES ('$announceTitle', '$announceDescribe', '$link', '', $grade, $week)";
				$result = query_to_result($conn, $query);
				$announceID = get_maxID($conn, 'bulletin');
				$query = "INSERT INTO bulletin_teacher VALUES ($announceID, $userID)";
				$result = query_to_result($conn, $query);
				if ($_FILES['image']['tmp_name'] != "") {
					
					$dir = '../userData/bulletinPic/';
					$name = $announceID . '.' . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
					$path = $dir.$name;
					move_uploaded_file($_FILES['image']['tmp_name'], $path);
					$query = "UPDATE bulletin SET image = '$path' WHERE announceID = $announceID";
					$result = $conn->query($query);
					if (!$result) echo "寫入資料庫發生錯誤: $query<br> " . $conn->error ."<br><br>";
					else{
						$conn->close();
						$message = '新增公告成功';
						echo "<script type='text/javascript'> alert('$message'); window.location.replace('index.php');</script>";
					}
				}
				else{
					$conn->close();
					$message = '新增公告成功';
					echo "<script type='text/javascript'> alert('$message'); window.location.replace('index.php');</script>";
				}
			}
		?>
	</head>

	<body>
		<div id="page">
			<h2>新增本周公告</h2>
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
				
				<h3>本周公告：(必填)</h3>
				<input class='input' type="text" name="announceTitle" value="<?php echo $announceTitle;?>"/>
				<span class="error"><?php echo $announceTitleErr;?></span>
				<br><br>
				
				<h3>詳細內容：</h3>
				<textarea class='input' type="text" name="announceDescribe" rows="10" cols="60%"><?php echo $announceDescribe;?></textarea>
				<br><br>
				
				<h3>連結網址：</h3>
				<input class='input' id='linkInput' type='text' name='link' value="<?php echo $link;?>" /><br><br>
				
				<h3>新增圖片：</h3>
				<input class='input' id='imgInput' type='file' name='image' accept='image/*'></input><br><br>
				
				<input class="submitButton" type="submit" value="新增公告" />
			</form>
			<br><button class="button" onclick="cancel()">取消</button>
			</div>
		</div>
	</body>

</html>
