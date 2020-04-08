<html>
	<head>
		<meta charset="UTF-8" />  <!--MUST HAVE-->
		<link type="text/css" rel="stylesheet" href="css/my.css">
		<link type="text/css" rel="stylesheet" href="css/sidebar.css">
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
		<script src="script/sidebar.js"></script>
		<script type="text/javascript">
			function show_more(id) {
				var x = document.getElementById(id);
				if (x.style.display === "none") x.style.display = "block";
				else x.style.display = "none";
			}
			function update(id) {
				$.ajax({
					type: 'POST',
					url: 'ajax/sessionAddAnnounceID.php',
					data: {announceID: id},
					success: function(result) {
						window.location.replace('update/updateAnnounce.php')
					}
				});
			}
		</script>
	</head>
	
	<body>
		<div id="page">
			<h2>我的公告</h2>
			<div class="bulletin">
			<?php 
				session_start();
				require_once '../loginMySQL.php';
				require_once "functions.php";
				$userID = $_SESSION['userID'];
				$dir = '../userData/head/'. $userID.'.png';
				$default = '../userData/head/default.jpg';
				echoSideBar($userID,$dir,$default);
				$conn = connect_mysql($hn, $un, $pw, $dbData);
				
				for ($i = 21; $i > 0; --$i){
					$query = "SELECT * FROM bulletin WHERE week = $i ORDER BY grade";
					$result = query_to_result($conn, $query);
					if ($result->num_rows > 0){
						echo "<h3>第 $i 週</h3>";
						while($announce = mysqli_fetch_assoc($result)){
							$grade = $announce['grade'];
							$announceID = $announce['announceID'];
							$announceTitle = $announce['announceTitle'];
							$announceDescribe = $announce['announceDescribe'];
							$href = $announce['href'];
							$image = $announce['image'];
							echo <<< END
							<div class="cube" onclick="show_more('$announceID')">
								<label class="left">[$grade 年級] $announceTitle</label>
								<button class="right" type="button" onclick="update($announceID)">更新公告</button>
							</div>
							
							<div class="detail" id="$announceID" style="display:none">
END;
							if ($announceDescribe != ''){
								echo "<h3>詳細內容：</h3>";
								echo "<label class='info'>$announceDescribe</label><br>";
							}
							if ($image != ''){
								echo "<img class='img' src=$image><br>";
							}
							if ($href != ''){
								echo "<h3><a href=$href target='_blank'>相關連結</a></h3><br>";
							}
							echo "</div>";
						}
					}
				}
			?>
			</div>
		</div>
	</body>

</html>

