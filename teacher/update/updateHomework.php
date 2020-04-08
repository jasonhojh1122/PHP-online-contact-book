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
						url: '../ajax/delHomework.php',
						data: {homeworkID: id},
						success: function(result) {
							window.location.replace('../homeworkMy.php')
						}
					});
				}
			}
			function cancel(){
				if (confirm("一旦取消，更新資訊將遺失")) window.location.replace('../index.php');
			}
		</script>
		<?php
			session_start();

			require_once '../../loginMySQL.php';
			require_once '../functions.php';
			$conn = connect_mysql($hn, $un, $pw, $dbData);

			$homeworkName = $homeworkDescribe = $answer = $homeworkNameErr = $homeworkDescribeErr = $homeworkTypeErr = "";
			$homeworkID = $_SESSION['homeworkID'];
			$query = "SELECT * FROM homework WHERE homeworkID = $homeworkID";
			$result = $conn->query($query);
			if (!$result) echo "存取資料庫發生錯誤: $query<br> " . $conn->error ."<br><br>";
			else{
				$result->data_seek(0);
				$row = $result->fetch_array(MYSQLI_ASSOC);
				$homeworkName = $row['homeworkName'];
				$homeworkDescribe = $row['homeworkDescribe'];
				$answer = $row['answer'];
				$link = $row['href'];
				$image = $row['image'];
			}

			if ($_SERVER["REQUEST_METHOD"] == "POST") {

				if (empty($_POST["homeworkName"])) $homeworkNameErr = "請輸入作業名稱";
				else $homeworkName = check_input($conn, $_POST["homeworkName"]);

				if (empty($_POST["homeworkDescribe"])) $homeworkDescribeErr = "請輸入作業描述";
				else $homeworkDescribe = check_input($conn, $_POST["homeworkDescribe"]);

				if (empty($_POST["answer"])) $answer = "";
				else $answer = check_input($conn, $_POST["answer"]);
				
				if (isset($_POST["link"])) $link = check_input($conn, $_POST["link"]);
				else $link="";
				}

			if ($homeworkNameErr=="" and $homeworkDescribeErr=="" and !empty($_POST["homeworkName"]) and !empty($_POST["homeworkDescribe"])){

				$query = "UPDATE homework SET homeworkName='$homeworkName', homeworkDescribe='$homeworkDescribe', href='$link', answer='$answer' WHERE homeworkID = $homeworkID";
				$result = query_to_result($conn, $query);
				if ($_FILES['image']['tmp_name'] != "") {
					$dir = '../../userData/homeworkPic/';
					$name = $homeworkID . '.' . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
					$path = $dir.$name;
					move_uploaded_file($_FILES['image']['tmp_name'], $path);
					$query = "UPDATE homework SET image = '$path' WHERE homeworkID = $homeworkID";
					$result = query_to_result($conn, $query);
				}
				$conn->close();
				$message = '更新作業成功';
				echo "<script type='text/javascript'> alert('$message'); window.location.replace('../index.php');</script>";				
			}
		?>
	</head>

	<body>
		<div class="page">
			<h2>更新作業</h2>
			<div class="bulletin">
			<form method="post" enctype="multipart/form-data">
			
				<h3>作業名稱：</h3>
				<input class='input' type="text" name="homeworkName" value="<?php echo $homeworkName;?>" />
				<span class="error"><?php echo $homeworkNameErr;?></span>
				<br><br>

				<h3>作業描述：</h3>
				<textarea class='input' type="text" name="homeworkDescribe" rows="10" cols="60%"><?php echo $homeworkDescribe;?></textarea>
				<span class="error"><?php echo $homeworkDescribeErr;?></span>
				<br><br>
				
				<h3>作業答案：</h3>
				<textarea class='input' type="text" name="answer" rows="3" cols="60%"><?php echo $answer;?></textarea>
				<br><br>

				<h3 id='linkLabel'>連結網址：</h3>
				<input class='input' id='linkInput' type='text' name='link' value="<?php echo $link;?>" />
			
				<h3 id='imgLabel'>更新圖片：</h3>
				<input class='input' id='imgInput' type='file' name='image' accept='image/*'></input>
				
				<input class="button" type="submit" value="更新課程"/>
				<br><br>
			</form>
			<br><br>
			
			<button class="button" onclick="cancel()">取消</button>
			<button class="button" type="button" onclick="del(<?php echo $homeworkID?>)">刪除作業</button>
			</div>
		</div>
	</body>

</html>

