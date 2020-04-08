<?php
	require_once '../loginMySQL.php';
	function connect_mysql($hn, $un, $pw, $dbData){
		$conn = new mysqli($hn, $un, $pw, $dbData);
		if (!$conn) die("Could not connect: " . mysql_error());
		else mysqli_query($conn, "SET NAMES 'UTF8'");
		return $conn;
	}

	function echoSideBar($userID,$dir,$default){
		echo <<<sideBar
			<div id="StuSideBar" class="sidebar" style="display:none;">
				<div><span onclick="sideBar_close()" class="close" style="margin-right:10px;"> &times;</span></div>
				<div style="clear:both;"></div>
				<div class="container">
sideBar;
					if (count(glob($dir)) > 0) echo "<img class='userHead' src=$dir>";
					else echo "<img class='userHead' src=$default>";
					
				echo <<<sideBar
					<img class="uploadPen" src="../style/Icon/edit.png" title="上傳大頭照" alt="上傳大頭照" style="height:20px;width:20px;margin-top:35px;" onclick="chooseHead()">
				</div>		
				<ul class="sidebar_menu">
					<li><a href="StudentMainPage.php" target = "_self">首頁</a></li>
					<li><a href="StudentAnnouncement.php" target = "_self">過去公告</a></li>	
					<li><a onclick="showC('C')">課程</a>
						<ul id="C" style="display:none;">
							<li><a href="StudentMyCourse.php" target = "_self">我的課程</a></li>
							<li><a href="StudentChooseCourse.php" target = "_self">選課作業</a></li>
						</ul>
					</li>
					<li><a href="StudentMyHomework.php" target = "_self">我的作業</a></li>
					<li><a href="StudentEditProfile.php" target = "_self">設定</a></li>
					<li><a href="../logout.php" style="cursor: pointer;" target = "_top" onclick="return confirm('R U SURE?')">登出</a></li>
				</ul>	
			</div>
sideBar;
	}

	function echoHWlist($conn,$userID,$grade,$week){
		$query = "SELECT * FROM homework WHERE grade = $grade ORDER BY week DESC";
		$result = $conn->query($query);
		$rows = $result->num_rows;
		for($i = 0; $i < $rows;++$i){
			$result->data_seek($i);
			$h = $result->fetch_array(MYSQLI_ASSOC);
			$homeworkID = $h['homeworkID'];
			$homeworkName = $h['homeworkName'];
			$homeworkDescribe = str_replace("\n", '<br>', $h['homeworkDescribe']);
			$hweek = $h['week'];
			$hanswer = $h['answer'];
			$href = $h['href'];
			$path = $h['image'];
			$stat = "SELECT * FROM homework_student WHERE studentID = $userID AND homeworkID = $homeworkID";
			$stat = $conn->query($stat);
			$stat->data_seek(0);
			$S = $stat->fetch_array(MYSQLI_ASSOC);
			$stuAns = $S['studentAnswer'];
			$status = $S['status'];
			$spanID = $homeworkID.'s';
			if ($status == 1) {
				$status='已完成';
				echo <<<table
					<div id='$homeworkID' class='modal'>
						<div class='modal-content'>
							<span id=$spanID class='close'>&times;</span>
							<p style="text-align:left;"">
								解答: $hanswer<br>
								我的答案: $stuAns<br>
								作業說明: $homeworkDescribe
							</p>
						</div>
					</div>
					<tr class="myhw" onclick="ShowModal($homeworkID,'$spanID')">
						<td align="center"> $hweek </td>
						<td align="center">$homeworkName</td>
						<td>$homeworkDescribe</td>
						<td align="center">$status</td>
					</tr>
table;
			}
			else {
				$status = '未完成';
				echo <<<table
					<div id='$homeworkID' class='modal'>
						<div class='modal-content'>
							<span id=$spanID class='close'>&times;</span>
							<p style="text-align:left;"">
								解答: $hanswer<br>
								我的答案: $stuAns<br>
								作業說明: $homeworkDescribe
								<form method="post">
								<input type='textarea' name="$hweek" placeholder="請輸入答案..." required>
								<span><input id="sendAns" type="submit" value=""></span>
								</form>
							</p>
						</div>
					</div>
					<tr class="myhw" onclick="ShowModal($homeworkID,'$spanID')">
						<td align="center"> $hweek </td>
						<td align="center">$homeworkName</td>
						<td>$homeworkDescribe</td>
						<td align="center">$status</td>
					</tr>
table;
			}
		}
	}

?>