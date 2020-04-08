<?php 
	session_start(); 
	require_once "../loginMySQL.php";
	require_once "./StudentMessageFuncs.php";
	$conn = connect_mysql($hn, $un, $pw, $dbData);
	$connM = connect_mysql($hn, $un, $pw, $dbMessage);
	$userID = $_SESSION['userID'];
?>
<!DOCTYPE html>
<html>
<head>
	<title> Send Message to Teacher</title>
	<meta charset="UTF-8" />
	<link rel="stylesheet" type="text/css" href="style/StudentMessage.css">
	<link type="text/css" rel="stylesheet" href="../style/sideBar.css">
	<link href='https://fonts.googleapis.com/css?family=Schoolbell' rel='stylesheet'>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script type="text/javascript" src="js/StudentMainPage.js"></script>
	<script type="text/javascript">
		var scrolled = false;
		function updateScroll(){
			if(!scrolled || !scrolledb){
				var element = document.getElementById("MsgContent");
				element.scrollTop = element.scrollHeight;				
			}
		}
		$('#MsgContent').on('scroll',function(){
			scrolled = true;
		});
	</script>
	<script type="text/javascript">
		window.onbeforeunload = confirmExit;
		function confirmExit(){
			$.ajax({
				type: 'POST',
				url: './ClearSession.php',
			});
		}
	</script>
</head>
<body>
	<!-- Background -->		
	<div class="bg"><img src="style/BG/blackboard.jpg"></div>
	<!-- SideBar -->
	<?php
		$dir = '../userData/head/'. $userID.'.png';
		$default = '../userData/head/default.jpg';
		echoSideBar($userID,$dir,$default);
	?>
	<div>
		<span class="open" onclick="sideBar_open()"> ☰ </span>
	</div>	
	<!-- Title of msg -->		
	<div id="MsgTitle">Message</div>
	<!-- Choose teacher to send msg -->
	<div class="MsgTo">
		<?php
			getTeacher($conn);
			if(isset($_GET['Name'])&&isset($_GET['teacherID'])){
				$_SESSION['teacherName'] = $_GET['Name'];
				$_SESSION['teacherID'] = $_GET['teacherID'];
			}
		?>
	</div>
	<div style="clear:both;"></div>
	<!-- Msg frame - toolbar -->	
	<div id="MsgHistory">
		<div id="MsgUpBar">
			<span id="teacherName">
				<?php 
					if(isset($_SESSION['teacherName'])&&$_SESSION['teacherName']!=="0") {
						echo $_SESSION['teacherName'];
					}else {
						echo '請選擇老師';
					}
				?>			
			</span>
			<span id="Tools">
				<input id="search" type="image" style="width:35px;height:35px;" src="style/Icon/search-icon.png" onclick="">				
			</span>
		</div>
		<div style="clear:both;"></div>
	<!-- Msg frame - history msg -->
		<div id="MsgContent">
			<?php
				if(isset($_SESSION['teacherID'])){
					if($_SESSION['teacherID']!=='0'&&$_SESSION['teacherName']!=='0') {
						getMsgData($userID,$_SESSION['teacherID'],$connM); 
						echo "<script>updateScroll();</script>";
					}
				}
			?>
		</div>
	</div>
	<!-- Enter msg -->
	<form method="post">
	<div id="EnterMsg"><input type="textarea" name="chat" placeholder="訊息由此輸入..." maxlength="68"></div>
	<!-- Send msg button -->
	<div><input id="SendMsg" type="submit" value=""></div>
	</form>
	<div><form method="post" id="SendPicForm" enctype="multipart/form-data">
		<label id="SendPic"><input type="file" id="SendPicFile" name="sendPic" style="display: none;" accept="image/*"></label>
		<!--<input type="submit" value="" style="display: none;">-->
	</form></div>
	<script type="text/javascript">
		var imgfile = document.getElementById('SendPicFile');
		imgfile.onchange = function(){
			if(confirm('Send Image ?')){
				var x = document.getElementById('SendPicForm');
				x.submit();
			}
		};
	</script>
	<?php
		if($_SERVER["REQUEST_METHOD"] == "POST"){
			if(isset($_POST['chat'])){
				$table = $_SESSION['teacherID'].'_'.$_SESSION['userID'];
				$teacherID = $_SESSION['teacherID'];
				$Name = $_SESSION['teacherName'];
				$MsgN = "SELECT * FROM $table WHERE 1";
				$MsgN = mysqli_query($connM, $MsgN);
				$MsgNum = $MsgN->num_rows;
				$MsgNum = $MsgNum - 1;
				$chat = check_input($conn,$_POST['chat']);
				$userID = $_SESSION['userID'];
				date_default_timezone_set('Asia/Taipei');
				$time = date("Y-m-d H:i:s");
				$addMsg = "INSERT INTO $table (`userID`,`message`,`time`) VALUES ($userID,'$chat','$time')";	
				mysqli_query($connM, $addMsg);
				$addRead = "UPDATE $table SET `message` = '$MsgNum' WHERE userID = -2";
				mysqli_query($connM, $addRead);
				echo "<script>window.location.href='StudentMessage.php?Name=$Name&teacherID=$teacherID';</script>";
			}
			if(isset($_FILES['sendPic'])){
				if ($_FILES['sendPic']['error'] === UPLOAD_ERR_OK){
					/*echo '檔案名稱: ' . $_FILES['sendPic']['name'].'<br/>';
					echo '檔案類型: ' . $_FILES['sendPic']['type'].'<br/>';
					echo '檔案大小: ' . ($_FILES['sendPic']['size'] / 1024).' KB<br/>';
					echo '暫存名稱: ' . $_FILES['sendPic']['tmp_name']. '<br/>';*/
					$file=$_FILES['sendPic']['tmp_name'];
					$Name = $_SESSION['teacherName'];					
					$teacherID = $_SESSION['teacherID'];
					$userID = $_SESSION['userID'];
					$table = $teacherID.'_'.$userID;
					$path = '../userData/messagePic/'.$table.'/'.$_FILES['sendPic']['name'];
					$MsgN = "SELECT * FROM $table WHERE 1";
					$MsgN = mysqli_query($connM, $MsgN);
					$MsgNum = $MsgN->num_rows;
					$MsgNum = $MsgNum - 1;
					date_default_timezone_set('Asia/Taipei');
					$time = date("Y-m-d H:i:s");
					if (file_exists($path)){
						$addPath = "INSERT INTO $table (`userID`,`message`,`time`,`isImage`) VALUES ($userID,'$path','$time',1)";
						mysqli_query($connM, $addPath);	
						$addRead = "UPDATE $table SET `message` = '$MsgNum' WHERE userID = -2";			
						mysqli_query($connM, $addRead);
					}
					else{
						$file=$_FILES['sendPic']['tmp_name'];
						move_uploaded_file($file, $path);
						$addPath = "INSERT INTO $table (`userID`,`message`,`time`,`isImage`) VALUES ($userID,'$path','$time',1)";
						mysqli_query($connM, $addPath);	
						$addRead = "UPDATE $table SET `message` = '$MsgNum' WHERE userID = -2";			
						mysqli_query($connM, $addRead);
					}
					///$data = file_get_contents($file);
					///$base64 = 'data:image/'.$_FILES['sendPic']['type'].';base64,'.base64_encode($data);
					echo "<script>window.location.href='StudentMessage.php?Name=$Name&teacherID=$teacherID';</script>";
				} else{
					echo '發生錯誤代碼: ' . $_FILES['sendPic']['error'].'<br/>';
				}				
			}
		}
	?>
</body>
</html>