<html>
	<head>
		<meta charset="UTF-8" />  <!--MUST HAVE-->
		<link rel="stylesheet" type="text/css" href="add.css">
		<script type="text/javascript">
			function change_arrage(x) {
				$.ajax({
					type: 'POST',
					url: 'sessionAddFriendArrange.php',
					data: {arrange: x},
					success: function(result) {
						window.location.replace('chatIndex.php')
					}
				});
			}
			function chat(id) {
				$.ajax({
					type: 'POST',
					url: 'sessionAddChatID.php',
					data: {chatID: id},
					success: function(result) {
						window.location.replace('chatRoom.php')
					}
				});
			}
		</script>
	</head>
	<!-- AUTO CREATE MESSAGE FILES -->
	<body>
		<div class="page">
			<h2>訊息</h2>
			<div class="bulletin">
			<button class="button" onclick="change_arrange(1)">時間</button>
			<button class="button" onclick="change_arrange(2)">當屆</button>
			<button class="button" onclick="change_arrange(3)">課程</button>
			<br>
			<?php
				session_start();
				require_once '../loginMySQL.php';
				require_once '../functions.php';
				$userID = $_SESSION['userID'];
				$conn = connect_mysql($hn, $un, $pw, $dbMessage);

				if(!isset($_SESSION['messageArrange']) or $_SESSION['messageArrange'] == 1){
					$friends = array();
					$query = "SHOW TABLES FROM `top secret files`;";
					$result = $conn->query($query);
					while($tableName = mysqli_fetch_row($result))
						$friends[] = $tableName[0];
					$time = array();
					for ($i = 0; $i < count($friends); ++$i){
						$table = $friends[$i];
						$query = "SELECT max(time) FROM `$table`";
						$result = $conn->query($query);
						$lastEditTime = $result->fetch_array(MYSQLI_NUM);
						$lastEditTime = $lastEditTime[0];
						$time[$friends[$i]] = $lastEditTime;
					}
					foreach ($time as $key => $part) {
						$sort[$key] = strtotime($time[$key]);
					}
					array_multisort($sort, SORT_DESC, $time);
					$time = array_keys($time);
					
					mysqli_select_db($conn, $dbData);
					
					for ($i = 0; $i < count($time); ++$i){
						$studentID = (int)(substr($time[$i], 2, 1));
						$query = "SELECT userName FROM user WHERE userID = $studentID";
						$result = $conn->query($query);
						$studentName = $result->fetch_array(MYSQLI_NUM);
						$studentName = $studentName[0];
						echo "<button id='$studentID' onclick='chat($studentID)'>$studentName</button>";
						echo "<br>";
					}
				}
				else if ($_SESSION['messageArrange'] == 2){
				}
				else{
				}
			?>
			


			</div>
		</div>
	</body>

</html>
