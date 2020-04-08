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

	function echoMyCourse($conn,$grade,$week,$userID){
		if ($grade == 3) $courseArr = array(1,5,7);
		else if ($grade == 4) $courseArr = array(2,5,7);
		else if ($grade == 5) $courseArr = array(3,6,7);
		else if ($grade == 6) $courseArr = array(4,6,7);
		$getCC = "SELECT * FROM course WHERE forWho IN (" . implode(',',$courseArr) . ") AND compulsory = 1";
		$getCC = $conn->query($getCC);
		$getCCNum = $getCC->num_rows;
		for( $j = 0 ; $j < $getCCNum ; ++$j){
			$getCC->data_seek($j);
			$CCI = $getCC->fetch_array(MYSQLI_ASSOC);
			$courseID = $CCI['courseID'];
			$courseName = $CCI['courseName'];
			$courseTime = $CCI['time'];
			$lastEditTime = $CCI['lastEditTime'];
			$courseDescribe = str_replace("\n", "<br>", $CCI['courseDescribe']);
			$getT = "SELECT name FROM user WHERE userID IN (SELECT teacherID FROM course_teacher WHERE courseID = $courseID)";
			$getT = $conn->query($getT);
			$getT->data_seek(0);
			$courseTeacher = $getT->fetch_array(MYSQLI_NUM);
			$courseTeacher = $courseTeacher[0];
			echo <<<course
				<div style='width: 94%;margin-left: 3%;margin-top: 40px;border:1.5px solid #ccc;clear:both;'>
				<table class="course">
					<tr style="background-color: #aaa; color:#000; font-weight: bold;">
						<td width="10%" align="center">授 課 時 間</td>
						<td width="10%" align="center">課 程 類 型</td>
						<td width="15%" align="center">課 程 名 稱</td>
						<td width="37%" align="center">課 程 說 明</td>
						<td width="13%" align="center">授 課 老 師</td>
						<td width="15%" align="center">最 後 更 新</td>
					</tr>
					<tr onclick="info('$j')" style="cursor: pointer;">
						<td align="center">$courseTime</td>
						<td align="center">必 修</td>
						<td align="center">$courseName</td>
						<td align="left">$courseDescribe</td>
						<td align="center">$courseTeacher</td>
						<td align="center">$lastEditTime</td>
					</tr>
				</table>
				</div>
course;
		}	
		$getC = "SELECT courseID FROM course_student WHERE studentID = $userID";
		$getC = $conn->query($getC);
		$getCNum = $getC->num_rows;
		for( $i = 0 ; $i < $getCNum ; ++$i){
			$getC->data_seek($i);
			$courseID = $getC->fetch_array(MYSQLI_NUM);
			$courseID = $courseID[0];
			$getT = "SELECT name FROM user WHERE userID IN (SELECT teacherID FROM course_teacher WHERE courseID = $courseID)";
			$getT = $conn->query($getT);
			$getT->data_seek(0);
			$courseTeacher = $getT->fetch_array(MYSQLI_NUM);
			$courseTeacher = $courseTeacher[0];
			$getCI = "SELECT * FROM course WHERE courseID = $courseID";
			$getCI = $conn->query($getCI);
			$getCI->data_seek(0);
			$courseInf = $getCI->fetch_array(MYSQLI_ASSOC);
			$courseName = $courseInf['courseName'];
			$courseTime = $courseInf['time'];
			$lastEditTime = $courseInf['lastEditTime'];
			$courseDescribe = str_replace("\n", "<br>", $courseInf['courseDescribe']);
			echo <<<course
				<div style='width: 94%;margin-left: 3%;margin-top: 40px;border:1.5px solid #ccc;clear:both;'>
				<table class="course">
					<tr style="background-color: #aaa; color:#000; font-weight: bold;">
						<td width="10%" align="center">授 課 時 間</td>
						<td width="10%" align="center">課 程 類 型</td>
						<td width="15%" align="center">課 程 名 稱</td>
						<td width="37%" align="center">課 程 說 明</td>
						<td width="13%" align="center">授 課 老 師</td>
						<td width="15%" align="center">最 後 更 新</td>
					</tr>
					<tr onclick="info('$i')" style="cursor: pointer;">
						<td align="center">$courseTime</td>
						<td align="center">選 修</td>
						<td align="center">$courseName</td>
						<td align="left">$courseDescribe</td>
						<td align="center">$courseTeacher</td>
						<td align="center">$lastEditTime</td>
					</tr>
				</table>
				</div>
course;
		}
	}

?>