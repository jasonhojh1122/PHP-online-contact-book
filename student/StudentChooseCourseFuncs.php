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

	function echoChoose($conn,$userID,$grade){
		$query = "SELECT chooseCourse FROM config";
		$result = mysqli_query($conn, $query);
		$result->data_seek(0);
		$row = $result->fetch_array(MYSQLI_NUM);
		$cnf_chooseCourse = $row[0];				
		/* Time for choosing is over */
		if ($cnf_chooseCourse == 0) echo <<<span
			<div style="clear:both;height:40px;"></div>
			<span style="color:white; margin-left:5.7%;margin-top:40px;font-size:20px;font-family:'Noto Sans TC',sans-serif;">選課時間已過.</span>
span;
		else{
			/* Check if any course is choosed */
			$query = "SELECT * FROM course_student WHERE studentID = $userID";
			$result = mysqli_query($conn, $query);
			/* Haven't chosen yet */				
			if(!$result || $result->num_rows ==0){
				/* Classes are provided for 7 types of groups consisting of students in the same or diiferent grades. Type 1: Grade 3. Type 2: Grade 4. Type 3: Grade 5. Type 4: Grade 6. Type 5: Grade 3 and 4. Type 6: Grade 5 and 6. Type 7: All students. */	
				if ($grade == 3) $courseArr = array(1,5,7);
				else if ($grade == 4) $courseArr = array(2,5,7);
				else if ($grade == 5) $courseArr = array(3,6,7);
				else if ($grade == 6) $courseArr = array(4,6,7);
				/* Select compulsory courses for user */
				$query = "SELECT * FROM course WHERE forWho IN (" . implode(',',$courseArr) . ") AND compulsory=1";
				$result = mysqli_query($conn, $query);
				if (!$result || $result->num_rows == 0) echo "無必修課程";
				else{
					$COMcourseNum = $result->num_rows;
					echo <<<compul
						<span><input id="ok-icon" name="submit" type="submit" value=""></input></span>
						<div style="clear: both;"></div>
						<div style="width: 94%;margin-left: 3%;margin-top: 70px;">
							<table class="course">
								<tr style="background-color: #aaa; color:#000; font-weight: bold;">
									<td width="20%" align="center">課 程 名 稱</td>
									<td width="50%" align="center">課 程 簡 介</td>
									<td width="20%" align="center">上 課 時 間</td>
									<td width="10%" align="center">必 修</td>
								</tr>
compul;
					for ($j = 0; $j < $COMcourseNum; ++$j){
						$result->data_seek($j);
						$row = $result->fetch_array(MYSQLI_ASSOC);
						$courseID = $row['courseID'];
						$courseName = $row['courseName'];
						$courseDescribe = str_replace("\n", "<br>", $row['courseDescribe']);
						$time = $row['time'];
						/* Apply template in the following html file */
						echo <<<compul
							<tr>
								<td align="center">$courseName</td>
								<td align="left">$courseDescribe</td>
								<td align="center">$time</td>
								<td align="center">必 修</td>
							</tr>
compul;
					}
					echo"
							</table>
						</div>";
				}
				/* Select not compulsory open courses available for user */
				$query = "SELECT * FROM course WHERE forWho IN (" . implode(',',$courseArr) . ") AND compulsory = 0";
				$result = mysqli_query($conn, $query);
				if (!$result || $result->num_rows == 0) echo "無選修課程";
				else{
					$courseNum = $result->num_rows;
					echo <<<xcompul
						<div style="clear: both;"></div>
						<div style="width: 94%;margin-left: 3%;margin-top: 70px;">
							<table class="course">
								<tr style="background-color: #aaa; color:#000; font-weight: bold;">
									<td width="20%" align="center">課 程 名 稱</td>
									<td width="50%" align="center">課 程 簡 介</td>
									<td width="20%" align="center">上 課 時 間</td>
									<td width="10%" align="center">選 修</td>
								</tr>
xcompul;
					for ($j = 0; $j < $courseNum; ++$j){
						$result->data_seek($j);
						$row = $result->fetch_array(MYSQLI_ASSOC);
						$courseID = $row['courseID'];
						$courseName = $row['courseName'];
						$courseDescribe = str_replace("\n", "<br>", $row['courseDescribe']);
						$courseTime = $row['time'];
						$id = "course" . (string)$j;
						/* Apply template in the following html file */
						$html = file_get_contents("templateChooseCourse.html");
						$html = str_replace("{{courseName}}", $courseName, $html);
						$html = str_replace("{{courseID}}", $courseID, $html);
						$html = str_replace("{{id}}", $id, $html);
						$html = str_replace("{{courseDescribe}}", $courseDescribe, $html);
						$html = str_replace("{{courseTime}}", $courseTime, $html);
						echo $html;
					}
					echo <<<isd
							</table>
						</div>
					</from>
isd;
				}
			}
			/* Already chosen */
			else{
				echo <<<choosed
				<div style='clear:both;'></div>
				<div style="color:white; margin-left:5.7%;margin-top:40px;font-size:20px;font-family:'Noto Sans TC',sans-serif;">已完成選課，點擊按鈕以重新選課</div>
				<button id="re" onclick='rechoose()'> </button>
choosed;
			}	
		}
	}

?>