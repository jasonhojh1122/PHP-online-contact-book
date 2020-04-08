<html>
	<head>
		<meta charset="UTF-8" CONTENT="NO-CACHE"/>
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
			function update_homework(id) {
				$.ajax({
					type: 'POST',
					url: 'ajax/sessionAddHomeworkID.php',
					data: {homeworkID: id},
					success: function(result) {
						window.location.replace('update/updateHomework.php')
					}
				});
			}
			function mark_as_finished(stuID,homID) {
				$.ajax({
					type: 'POST',
					url: 'ajax/setAsFinished.php',
					data: {studentID: stuID, homeworkID: homID},
					success: function(result) {
						window.location.replace('homeworkMy.php')
					}
				});
			}
			function check_all(obj,cName){ 
			var checkboxs = document.getElementsByName(cName); 
			for(var i=0;i<checkboxs.length;i++){checkboxs[i].checked = obj.checked;} 
		}
		</script>
		<?php
			if ($_SERVER["REQUEST_METHOD"] == "POST") {
				require_once '../loginMySQL.php';
				require_once 'functions.php';
				$conn = connect_mysql($hn, $un, $pw, $dbData);
				
				$hwID = $_POST['homeworkID'];
				foreach ($_POST[$hwID] as $value){
					$studentID = (int)$value;
					$query = "UPDATE homework_student SET status = 1 WHERE studentID = $studentID AND homeworkID = $hwID";
					$result = $conn->query($query);
					if (!$result) echo "更新資料庫錯誤: $query<br> " . $conn->error ."<br><br>";
				}
				$conn->close();
				$message = "設定成功";
				echo "<script type='text/javascript'> alert('$message'); window.location.replace('homeworkMy.php');</script>";
			}
		?>
	</head>

	<body>
		<div id="page">
			<h2>我的作業</h2>
			<div class="bulletin">
				<?php
					session_start();
					require_once '../loginMySQL.php';
					require_once 'functions.php';
					$userID = $_SESSION['userID'];
					echoSideBar($userID, '../userData/head/'. $userID.'.png', '../userData/head/default.jpg');
					$conn = connect_mysql($hn, $un, $pw, $dbData);

					$userID = $_SESSION['userID'];
					$grade = get_user_grade($conn, $userID);
					$query = "SELECT * FROM homework WHERE grade = $grade ORDER BY week";
					$allHomeworks = $conn->query($query);
					if (!$allHomeworks) echo "存取資料庫發生錯誤: $query<br> " . $conn->error ."<br><br>";
					$rows = $allHomeworks->num_rows;
					if ($rows == 0) echo "尚未建立任何作業";
					else{
						// go through homeworks
						while($homework = mysqli_fetch_assoc($allHomeworks)){
							$homeworkID = $homework['homeworkID'];
							$homeworkName = $homework['homeworkName'];
							$homeworkDescribe = str_replace("\n", "<br>", $homework['homeworkDescribe']);
							$href = $homework['href'];
							$image = $homework['image'];
							$answer = $homework['answer'];
							$week = $homework['week'];
							
							echo "<h3>第 $week 週</h3>";
							
							$query = "SELECT * FROM homework_student WHERE homeworkID = $homeworkID AND status = 0";
							$result = $conn->query($query);
							$unfinishNum = $result->num_rows;

							$query = "SELECT * FROM homework_student WHERE homeworkID = $homeworkID ORDER BY status ASC";
							$students = $conn->query($query);
							$studentNum = $students->num_rows;

							echo <<< END
							<div class="cube" onclick="show_more('$homeworkID')">
END;
							if ($unfinishNum == 0) echo "<div class='greenBox'></div>";
							else echo "<div class='redBox'></div>";
							echo <<< END
									<label class="left">$homeworkName</label>
									<button class="right" type="button" onclick="update_homework($homeworkID)">更新作業</button>
							</div>

							<div class="detail" id="$homeworkID" style="display:none">
									<h3>作業描述：</h3> <label class='info'>$homeworkDescribe</label> <br>
END;
							if ($image != '') echo "<img class='img' src=$image /><br>";
							if ($href != '') echo "<h3><a href=$href>相關連結</a></h3> <label>";
							if ($answer != '') echo "<h3>作業答案：</h3><label class='info'>$answer</label> <br>";
							
							$name = $homeworkID . '[]';
							$name = "'" . $name . "'";
							
							echo "<br><br><form method='POST' action='homeworkMy.php'>";
								echo "<input type='hidden' name='homeworkID' value='$homeworkID'>";
								echo <<< END
									<input type="checkbox" name="all" onclick="check_all(this, $name)" />
									<div class='contain'>
										<div class='greenBoxSmall'></div>
										<label class='leftSmall'>全選</label>
									</div><br>
END;
							// go through students
							while ($student = mysqli_fetch_assoc($students)){
								$studentID = $student['studentID'];
								$status = $student['status'];
								$answer = $student['studentAnswer'];
								
								$studentName = get_userName_by_ID($conn, $studentID, $dbData);
								echo "<input type='checkbox' name=$name value='$studentID'></input>";
								echo "<div class='contain'>";
									if ($status == 0)      echo "<div class='redBoxSmall'></div>";
									else if ($status == 1) echo "<div class='greenBoxSmall'></div>";
									echo "<label class='leftSmall'>$studentName</label><label class='leftSmall'>答案：$answer</label>";
								echo "</div><br>";
							}
								echo "<input class='submitButton' type='submit' value='設為完成'/>";
							echo "</form><br>";
					echo "</div>";
						}
					}
				?>

			</div>
		</div>
	</body>
</html>
