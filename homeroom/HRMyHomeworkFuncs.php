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
					<li><a href="HRMainPage.php" target = "_self">首頁</a></li>
					<li><a href="HRAnnouncement.php" target = "_self">過去公告</a></li>	
					<li><a href="HRMyHomework.php" target = "_self">學生作業</a></li>
					<li><a href="HREditProfile.php" target = "_self">設定</a></li>
					<li><a href="../logout.php" style="cursor: pointer;" target = "_top" onclick="return confirm('R U SURE?')">登出</a></li>
				</ul>	
			</div>
sideBar;
	}

	function echoHWlist($conn,$userID,$grade,$week){
		$getName = "SELECT name FROM user WHERE userID = $userID";
		$getName = $conn->query($getName);
		$getName->data_seek(0);
		$Name = $getName->fetch_array(MYSQLI_NUM);
		$Name = $Name[0];
		echo<<<title
			<div style="clear: both; height: 20px;"></div>
			<div style="width:88.6%;margin-top: 20px;margin-left:5.7%">
				<span style="color:white;font-family:'Noto Sans TC',sans-serif; font-weight: 500; font-size:20px;margin-left:5.7%;">$Name</span>
				<table class="mytable">
					<tr style="background-color: #555;font-weight: 500;">
						<td width="10%" align="center"> 週 </td>
						<td width="25%" align="center">作 業 名 稱</td>
						<td width="48%" align="center">作 業 說 明</td>
						<td width="17%" align="center">狀 態</td>
					</tr>			
title;
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
		}
		echo<<<title
			</table>
		</div>
title;
	}

?>