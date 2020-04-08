<html>
	<head>
		<meta charset="UTF-8" />  <!--MUST HAVE-->
		<link rel="stylesheet" type="text/css" href="../css/update.css">
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
		<script type="text/javascript">
			function del(id) {
				if (confirm("確認刪除，一旦刪除，所有相關資料都將如長江般一去不復返")){
					$.ajax({
						type: 'POST',
						url: '../ajax/delAnnounce.php',
						data: {announceID: id},
						success: function(result) {
							window.location.replace('../index.php')
						}
					});
				}
			}
			function cancel(){
				if (confirm("一旦取消，輸入資訊將遺失")) window.location.replace('index.php');
			}
		</script>
		<?php
			session_start();
			require_once '../../loginMySQL.php';
			require_once '../functions.php';
			$conn = connect_mysql($hn, $un, $pw, $dbData);

			$announceTitle = $announceDescribe = $announceTitleErr = $announceTextErr = "";

			$announceID = $_SESSION['announceID'];
			$query = "SELECT * FROM bulletin WHERE announceID = $announceID";
			$result = $conn->query($query);
			if (!$result) echo "存取資料庫發生錯誤: $query<br> " . $conn->error ."<br><br>";
			else{
				$result->data_seek(0);
				$row = $result->fetch_array(MYSQLI_ASSOC);
				$announceTitle = $row['announceTitle'];
				$announceDescribe = $row['announceDescribe'];
				$link = $row['href'];
				$image = $row['image'];
			}

			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				if (empty($_POST["announceTitle"])) $announceTitleErr = "請輸入公告標題";
				else $announceTitle = check_input($conn, $_POST["announceTitle"]);

				if (!empty($_POST["announceDescribe"])) $announceDescribe = check_input($conn, $_POST["announceDescribe"]);

				if (isset($_POST["link"])) $link = check_input($conn, $_POST["link"]);
				else $link="";
			}

			if ($announceTitleErr=="" and !empty($_POST["announceTitle"])){
				$query = "UPDATE bulletin SET announceTitle = '$announceTitle', announceDescribe = '$announceDescribe', href = '$link' WHERE announceID = $announceID";
				$result = query_to_result($conn, $query);
				if ($_FILES['image']['tmp_name'] != "") {
					$dir = '../../userData/bulletinPic/';
					$name = $announceID . '.' . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
					$path = $dir.$name;
					move_uploaded_file($_FILES['image']['tmp_name'], $path);
					$query = "UPDATE bulletin SET image = '$path' WHERE announceID = $announceID";
					$result = $conn->query($query);
					if (!$result) echo "寫入資料庫發生錯誤: $query<br> " . $conn->error ."<br><br>";
					else{
						$conn->close();
						$message = '更新公告成功';
						echo "<script type='text/javascript'> alert('$message'); window.location.replace('../index.php');</script>";
					}
				}
				else{
					$conn->close();
					$message = '更新公告成功';
					echo "<script type='text/javascript'> alert('$message'); window.location.replace('../index.php');</script>";
				}
			}
		?>

	</head>

	<body>
		<div class ="page">
			<h2>更新公告</h2>
			<div class="bulletin">
			<form method="post" enctype="multipart/form-data">
				<h3>公告：</h3>
				<input type="text" name="announceTitle" value="<?php echo $announceTitle; ?>" />
				<span class="error"><?php echo $announceTitleErr;?></span>
				<br><br>

				<h3>詳細內容：</h3>
				<textarea type="text" name="announceDescribe" rows="10" cols="60%"><?php echo $announceDescribe;?></textarea>
				<br><br>

				<h3>連結網址：</h3>
				<input id='linkInput' type='text' name='link'></input><br><br>

				<h3>新增圖片：</h3>
				<input id='imgInput' type='file' name='image' accept='image/*'></input><br><br>

				<br><input class="button" type="submit" value="更新公告" />
			</form>
			<br>
			<button class="button" onclick="cancel()">取消</button>
			<button class="button" type="button" onclick="del(<?php echo $announceID?>)">刪除公告</button>
			</div>
		</div>
	</body>
</html>
