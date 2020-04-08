<html>
	<head>
		<title>備份與刪除</title>
		<meta charset="UTF-8" CONTENT="NO-CACHE"/>
		<link rel="stylesheet" type="text/css" href="css/add.css">
		<link type="text/css" rel="stylesheet" href="css/sidebar.css">
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
		<script src="script/sidebar.js"></script>		
		<script src="script/add.js"></script>
		<script type='text/javascript'>
			function ex() {
				window.location.replace('exportToExcel.php');
			}
			function del() {
				if (confirm("確認清空課程、公告、作業等內容嗎？")){
					$.ajax({
						type: 'POST',
						url: 'ajax/del.php',
						success: function(result) {
							alert('清空成功');
							window.location.replace('index.php');
						}
					});
				}
			}
			function add() {
				if (confirm("確認要將年級加一、刪除畢業學生資料嗎？")){
					$.ajax({
						type: 'POST',
						url: 'ajax/addOne.php',
						success: function(result) {
							alert('成功');
							window.location.replace('index.php');
						}
					});
				}
			}			
		</script>
		
		<?php
			session_start();
			require_once '../loginMySQL.php';
			require_once 'functions.php';
			$userID = $_SESSION['userID'];
			$dir = '../userData/head/'. $userID.'.png';
			$default = '../userData/head/default.jpg';
			echoSideBar($userID,$dir,$default);
			$conn = connect_mysql($hn, $un, $pw, $dbData);
		?>
	</head>

	<body>
		<div id="page">
			<h2>備份與刪除</h2>
			<div class="bulletin">
			<button class="button" onclick="ex()">備份資料</button><br><br>
			<button class="button" onclick="del()">清空資料</button><br><br>
			<button class="button" onclick="add()">年級加一</button><br><br>
			<button class="button" onclick="cancel()">取消</button><br><br>
			</div>
		</div>
	</body>

</html>
