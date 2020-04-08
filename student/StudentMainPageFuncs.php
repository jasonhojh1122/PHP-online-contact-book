<?php
	require_once '../loginMySQL.php';
	function check_input($conn, $string){
		$string = trim($string);
		return htmlentities(mysql_fix_string($conn, $string));
	}

	function mysql_fix_string($conn, $string){
		if (get_magic_quotes_gpc()) $string = stripcslashes($string);
		$string = mysqli_real_escape_string($conn, $string);
		return $string;
	}

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

	function teacherWarn($conn,$week,$userID,$grade){
		$result = "SELECT * FROM warning WHERE studentID = $userID";
		$result = $conn-> query($result);
		if($result->num_rows > 0){
			$result->data_seek(0);
			$getWarnInf = $result->fetch_array(MYSQLI_ASSOC);
			$warnText = str_replace("\n", "<br>", $getWarnInf['text']);
			if($warnText!=="" && $warnText!==NULL){
				$tID = "SELECT userID FROM user WHERE grade = $grade AND userType = 1";
				$tID = $conn-> query($tID);
				$tID->data_seek(0);
				$teacherID = $tID->fetch_array(MYSQLI_NUM);
				$teacherID = $teacherID[0];
				$N = "SELECT name FROM user WHERE userID = $teacherID";
				$N = $conn-> query($N);
				$N->data_seek(0);
				$getName = $N->fetch_array(MYSQLI_NUM);
				$teacherName = $getName[0];
				echo <<<warn
					<span class="warning">
						✉ &nbsp; $teacherName 說: &nbsp;$warnText
					</span>
warn;
			}

		}
		$result = "SELECT * FROM sign_parent WHERE studentID = $userID";
		$result = $conn-> query($result);
		$result ->data_seek(0);
		$t = $result->fetch_array(MYSQLI_ASSOC);
		$Arr = array();
		for($i = 1; $i < 22; ++$i){
			$signed = $t[$i];
			if($i < $week && $signed==NULL){
				$iname = '第 '.$i.' 週';
				array_push($Arr,$iname);
			}
		}
		$Arr = array_filter($Arr);
		if(!empty($Arr)){
			echo "<span class='warning'> ✉ &nbsp; 系統說: &nbsp;";
			foreach ($Arr as $value) {
				echo $value.',';
			}
			echo " 尚未簽到</span>";
		}
	}

	function signEnable($conn,$week,$userID){
		$result = "SELECT `$week` FROM sign_parent WHERE studentID = $userID";
		$result = $conn-> query($result);
		$result->data_seek(0);
		$result = $result->fetch_array(MYSQLI_NUM);
		$result = $result[0];
		if($result==NULL) {
			echo <<<enable
				<form method="post" id="signForm">
					<label class="SignIn"><input type="submit" id="SignButton" value=""/></label>
					<span><input type="password" name="Signed" placeholder="輸入驗證碼..." required></span>
				</form>
				<span id="SignInHere">Sign in here ☞ &nbsp;</span>
enable;
		}
		else{
			echo <<<disable
				<form method="post" id="signForm">
					<label class="SignIn"><input type="submit" name="Signed" id="SignButton" value="" disabled/></label>
				</form>
				<span id="SignInHere">Signed ✔ &nbsp; </span>
disable;
		}
	}

	function UpdateSign($conn,$time,$week,$userID,$input){
		$input = check_input($conn,$input);
		$sPass = "SELECT `signPass` FROM config";
		$sPass = $conn-> query($sPass);
		$sPass->data_seek(0);
		$sPass = $sPass->fetch_array(MYSQLI_NUM);
		$sPass = $sPass[0];		
		$result = "SELECT `$week` FROM sign_parent WHERE studentID = $userID";
		$result = $conn-> query($result);
		$result->data_seek(0);
		$result = $result->fetch_array(MYSQLI_NUM);
		$result = $result[0];
		if ($input == $sPass){
			if ($result==NULL){
				$sign = "UPDATE `sign_parent` SET `$week` = '$time' WHERE studentID = $userID";
				$conn-> query($sign);
				echo "<script>alert('本週簽到成功 !');window.location.href='StudentMainPage.php';</script>";
			}			
		}
		else{
			echo "<script>alert('驗證碼錯誤 !');window.location.href='StudentMainPage.php';</script>";			
		}

	}
	
	function echoBulletin($conn,$week,$grade){
		$getAnn = "SELECT * FROM bulletin WHERE grade = $grade AND week = $week";
		$getAnn = $conn->query($getAnn);
		$getAnnNum = $getAnn->num_rows;
		if ($getAnnNum==0) {
			echo "<tr style='border-bottom: 1px solid white;font-size:20px;background-color:#444;'><td> ☞ 本週無注意事項 </td></tr>";
		}
		else{
			echo "<tr style='border-bottom: 1px solid white;font-size:20px;background-color:#444;'><td> ☞ &nbsp; 本週公告 </td></tr>";
			for( $i = 0 ; $i < $getAnnNum ; ++$i){
				$getAnn->data_seek($i);
				$Ann = $getAnn->fetch_array(MYSQLI_ASSOC);
				$title = $Ann['announceTitle'];
				$describe = str_replace("\n", "<br>", $Ann['announceDescribe']);
				$href = $Ann['href'];
				$path = $Ann['image'];
				$index = $i+1;
				if($path==NULL && $href==NULL){
					echo"<tr><td> $index. $title: &nbsp; $describe </td></tr>";
				}
				elseif($path!==NULL && $href==NULL){
					echo"<tr>
							<td>
								$index. $title: &nbsp; $describe <br>
								<img src=$path class='bullImg'>
							</td>
						</tr>";
				}
				elseif($path==NULL && $href!==NULL){
					echo"<tr>
							<td>
								$index. $title: &nbsp; $describe  &nbsp;<a href=$href target='_blank'> 點我穿越 </a>
							</td>
						</tr>";
				}
				elseif($path!==NULL && $href!==NULL){
					echo"<tr>
							<td>
								$index. $title: &nbsp; $describe  &nbsp;<a href=$href target='_blank'> 點我穿越 </a> <br>
								<img src=$path class='bullImg'>
							</td>
						</tr>";
				}
			}
		}
	}

	function echoCourseProg($conn,$week,$grade,$userID){
		if ($grade == 3) $courseArr = array(1,5,7);
		else if ($grade == 4) $courseArr = array(2,5,7);
		else if ($grade == 5) $courseArr = array(3,6,7);
		else if ($grade == 6) $courseArr = array(4,6,7);
		$getCC = "SELECT * FROM course WHERE forWho IN (" . implode(',',$courseArr) . ") AND compulsory=1";
		$getCC = $conn->query($getCC);
		$getCCNum = $getCC->num_rows;
		for( $j = 0 ; $j < $getCCNum ; ++$j){
			$getCC->data_seek($j);
			$CCI = $getCC->fetch_array(MYSQLI_ASSOC);
			$courseID = $CCI['courseID'];
			$courseName = $CCI['courseName'];
			$courseTime = $CCI['time'];
			$courseDescribe = str_replace("\n", "<br>", $CCI['courseDescribe']);
			$getT = "SELECT name FROM user WHERE userID IN (SELECT teacherID FROM course_teacher WHERE courseID = $courseID)";
			$getT = $conn->query($getT);
			$getT->data_seek(0);
			$courseTeacher = $getT->fetch_array(MYSQLI_NUM);
			$courseTeacher = $courseTeacher[0];
			$getCCP = "SELECT `$week` FROM course_progress WHERE courseID = $courseID";
			$getCCP = $conn->query($getCCP);
			$getCCP->data_seek(0);
			$courseProgress = $getCCP->fetch_array(MYSQLI_NUM);
			$courseProgress = str_replace("\n", "<br>", $courseProgress[0]);
			$getCCA = "SELECT `$week` FROM attendance WHERE courseID = $courseID AND studentID = $userID";
			$getCCA = $conn->query($getCCA);
			$getCCA->data_seek(0);
			$courseAttendance = $getCCA->fetch_array(MYSQLI_NUM);
			$courseAttendance = $courseAttendance[0];
			if($courseAttendance=='0') $courseAttendance='準時';
			elseif($courseAttendance=='1') $courseAttendance='遲到';
			elseif($courseAttendance=='2') $courseAttendance='請假';
			elseif($courseAttendance=='3') $courseAttendance='缺席';
			elseif($courseAttendance=='4') $courseAttendance='放假';
			$courseDiv = 'course'.$courseID;
			$courseSpanID = 'courseSpan'.$courseID;
			echo <<<inf
				<div id=$courseDiv class="modal">
					<div class="modal-content">
						<span id=$courseSpanID class="close">&times;</span>
						<p>
							授課老師: $courseTeacher<br>
							課程類型: 必修<br>
							上課時間: $courseTime<br>
							課程說明: $courseDescribe
						</p>
					</div>
				</div>
inf;
			echo <<<modal
				<tr onclick="ShowModal('$courseDiv','$courseSpanID')" class='courseUpdate'>
					<td align='center'>$courseName</td>
					<td align='center'>$courseAttendance</td>
					<td>$courseProgress</td>
				</td>
modal;
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
			$courseDescribe = str_replace("\n", "<br>", $courseInf['courseDescribe']);
			$compulsory = $courseInf['compulsory'];
			$getCP = "SELECT `$week` FROM course_progress WHERE courseID = $courseID";
			$getCP = $conn->query($getCP);
			$getCP->data_seek(0);
			$courseProgress = $getCP->fetch_array(MYSQLI_NUM);
			$courseProgress = str_replace("\n", "<br>", $courseProgress[0]);
			$getCA = "SELECT `$week` FROM attendance WHERE courseID = $courseID AND studentID = $userID";
			$getCA = $conn->query($getCA);
			$getCA->data_seek(0);
			$courseAttendance = $getCA->fetch_array(MYSQLI_NUM);
			$courseAttendance = $courseAttendance[0];
			if($courseAttendance=='0') $courseAttendance='準時';
			elseif($courseAttendance=='1') $courseAttendance='遲到';
			elseif($courseAttendance=='2') $courseAttendance='請假';
			elseif($courseAttendance=='3') $courseAttendance='缺席';
			elseif($courseAttendance=='4') $courseAttendance='放假';
			$courseDiv = 'course'.$courseID;
			$courseSpanID = 'courseSpan'.$courseID;
			echo <<<inf
				<div id=$courseDiv class="modal">
					<div class="modal-content">
						<span id=$courseSpanID class="close">&times;</span>
						<p>
							授課老師: $courseTeacher<br>
							課程類型: 選修<br>
							上課時間: $courseTime<br>
							課程說明: $courseDescribe
						</p>
					</div>
				</div>
inf;
			echo <<<modal
				<tr onclick="ShowModal('$courseDiv','$courseSpanID')" class='courseUpdate'>
					<td align='center'>$courseName</td>
					<td align='center'>$courseAttendance</td>
					<td>$courseProgress</td>
				</td>
modal;
		}
	}

	function echoWeekHW($conn,$userID,$grade,$week){
		$getHW ="SELECT * FROM homework WHERE grade=$grade AND week=$week";
		$getHW = $conn ->query($getHW);
		$getHW->data_seek(0);
		$Homework = $getHW ->fetch_array(MYSQLI_ASSOC);
		$homeworkID = $Homework['homeworkID'];
		$homeworkName = $Homework['homeworkName'];
		$homeworkDescribe = str_replace("\n", "<br>", $Homework['homeworkDescribe']);
		$path = $Homework['image'];
		$href = $Homework['href'];
		$N = "SELECT * FROM homework_student WHERE studentID = $userID AND homeworkID = $homeworkID";
		$N = $conn-> query($N);
		if(!$N || $N->num_rows == 0) $ans="";
		else{
			$N->data_seek(0);
			$Ninf = $N->fetch_array(MYSQLI_ASSOC);
			$ans = $Ninf['studentAnswer'];
		}
		$hwDiv = 'homework'.$homeworkID;
		$homeworkSpanID = 'homeworkSpan'.$homeworkID;
		if($path==NULL && $href==NULL){
			echo <<<hw
				<div id=$hwDiv class='modal'>
					<div class='modal-content'>
						<span id=$homeworkSpanID class='close'>&times;</span>
						<p style="text-align:left;">
							作業名稱: $homeworkName<br>
							作業說明: $homeworkDescribe<br>
							我的答案: $ans<br>
							<form method="post">
							<input type='textarea' name="answer" placeholder="請輸入答案..." required>
							<span><input id="sendAns" type="submit" value=""></span>
							</form>
						</p>
					</div>
				</div>
hw;
			echo <<<modal
				<button class="HWbutton1" onclick="ShowModal('$hwDiv','$homeworkSpanID')">本週作業</button>
modal;
		}
		elseif($path!==NULL && $href==NULL){
			echo <<<hw
				<div id=$hwDiv class='modal'>
					<div class='modal-content'>
						<span id=$homeworkSpanID class='close'>&times;</span>
						<p style="text-align:left;">
							作業名稱: $homeworkName<br>
							作業說明: $homeworkDescribe<br>
							<img src=$path class='bullImg'><br>
							我的答案: $ans<br>
							<form method="post">
							<input type='textarea' name="answer" placeholder="請輸入答案..." required>
							<span><input id="sendAns" type="submit" value=""></span>
							</form>
						</p>
					</div>
				</div>
hw;
			echo <<<modal
				<button class="HWbutton1" onclick="ShowModal('$hwDiv','$homeworkSpanID')">本週作業</button>
modal;
		}
		elseif($path==NULL && $href!==NULL){
			echo <<<hw
				<div id=$hwDiv class='modal'>
					<div class='modal-content'>
						<span id=$homeworkSpanID class='close'>&times;</span>
						<p style="text-align:left;">
							作業名稱: $homeworkName<br>
							作業說明: $homeworkDescribe<br>
							連結: <a href=$href target='_blank'>點我穿越</a><br>
							我的答案: $ans<br>
							<form method="post">
							<input type='textarea' name="answer" placeholder="請輸入答案..." required>
							<span><input id="sendAns" type="submit" value=""></span>
							</form>
						</p>
					</div>
				</div>
hw;
			echo <<<modal
				<button class="HWbutton1" onclick="ShowModal('$hwDiv','$homeworkSpanID')">本週作業</button>
modal;
		}
		elseif($path!==NULL && $href!==NULL){
			echo <<<hw
				<div id=$hwDiv class='modal'>
					<div class='modal-content'>
						<span id=$homeworkSpanID class='close'>&times;</span>
						<p style="text-align:left;">
							作業名稱: $homeworkName<br>
							作業說明: $homeworkDescribe<br>
							連結: <a href=$href target='_blank'>點我穿越</a><br>
							<img src=$path class='bullImg'><br>
							我的答案: $ans<br>
							<form method="post">
							<input type='textarea' name="answer" placeholder="請輸入答案..." required>
							<span><input id="sendAns" type="submit" value=""></span>
							</form>
						</p>
					</div>
				</div>
hw;
			echo <<<modal
				<button class="HWbutton1" onclick="ShowModal('$hwDiv','$homeworkSpanID')">本週作業</button>
modal;
		}

	}

	function echoLastWeekAns($conn,$userID,$grade,$week){
		if($week > 1){
			$getAns ="SELECT * FROM homework WHERE grade = $grade AND week = $week - 1 ";
			$getAns = $conn ->query($getAns);
			$getAns->data_seek(0);
			$Answer = $getAns ->fetch_array(MYSQLI_ASSOC);
			$homeworkID = $Answer['homeworkID'];
			$homeworkName = $Answer['homeworkName'];
			$homeworkDescribe = str_replace("\n", "<br>", $Answer['homeworkDescribe']);
			$path = $Answer['image'];
			$href = $Answer['href'];
			$answer = str_replace("\n", "<br>", $Answer['answer']);
			$N = "SELECT * FROM homework_student WHERE studentID = $userID AND homeworkID = $homeworkID";
			$N = $conn-> query($N);
			if(!$N || $N->num_rows == 0) $myans="";
			else{
				$N->data_seek(0);
				$Ninf = $N->fetch_array(MYSQLI_ASSOC);
				$myans = str_replace("\n", "<br>", $Ninf['studentAnswer']);
			}
			$hwDiv = 'Lhomework'.$homeworkID;
			$homeworkSpanID = 'LhomeworkSpan'.$homeworkID;
			if($path==NULL && $href==NULL){
				echo <<<hw
					<div id=$hwDiv class='modal'>
						<div class='modal-content'>
							<span id=$homeworkSpanID class='close'>&times;</span>
							<p style="text-align:left;"">
								作業名稱: $homeworkName<br>
								解答: $answer<br>
								我的答案: $myans<br>
								作業說明: $homeworkDescribe
							</p>
						</div>
					</div>
hw;
				echo <<<modal
					<button class="HWbutton2" onclick="ShowModal('$hwDiv','$homeworkSpanID')">上週作業解答</button>
modal;
			}
			elseif($path!==NULL && $href==NULL){
				echo <<<hw
					<div id=$hwDiv class='modal'>
						<div class='modal-content'>
							<span id=$homeworkSpanID class='close'>&times;</span>
							<p style="text-align:left;"">
								作業名稱: $homeworkName<br>
								解答: $answer<br>
								我的答案: $myans<br>
								作業說明: $homeworkDescribe<br>
								<img src=$path class='bullImg'>
							</p>
						</div>
					</div>
hw;
				echo <<<modal
					<button class="HWbutton2" onclick="ShowModal('$hwDiv','$homeworkSpanID')">上週作業解答</button>
modal;
			}
			elseif($path==NULL && $href!==NULL){
				echo <<<hw
					<div id=$hwDiv class='modal'>
						<div class='modal-content'>
							<span id=$homeworkSpanID class='close'>&times;</span>
							<p style="text-align:left;"">
								作業名稱: $homeworkName<br>
								解答: $answer<br>
								我的答案: $myans<br>
								作業說明: $homeworkDescribe<br>
								連結: <a href=$href target="_blank">點我穿越</a>
							</p>
						</div>
					</div>
hw;
				echo <<<modal
					<button class="HWbutton2" onclick="ShowModal('$hwDiv','$homeworkSpanID')">上週作業解答</button>
modal;
			}
			elseif($path!==NULL && $href!==NULL){
				echo <<<hw
					<div id=$hwDiv class='modal'>
						<div class='modal-content'>
							<span id=$homeworkSpanID class='close'>&times;</span>
							<p style="text-align:left;"">
								作業名稱: $homeworkName<br>
								解答: $answer<br>
								我的答案: $myans<br>
								作業說明: $homeworkDescribe<br>
								連結: <a href=$href target="_blank">點我穿越</a>
								<img src=$path class='bullImg'>
							</p>
						</div>
					</div>
hw;
				echo <<<modal
					<button class="HWbutton2" onclick="ShowModal('$hwDiv','$homeworkSpanID')">上週作業解答</button>
modal;
			}
		}
	}

	function UpdateStuAns($conn,$userID,$grade,$week,$ans){
		$ans = check_input($conn, $ans);
		$result = "SELECT homeworkID FROM homework WHERE grade = $grade AND week = $week";
		$result = $conn-> query($result);
		$result->data_seek(0);
		$H = $result->fetch_array(MYSQLI_NUM);
		$homeworkID = $H[0];
		$N = "SELECT * FROM homework_student WHERE studentID = $userID AND homeworkID = $homeworkID";
		$N = $conn-> query($N);
		if($N->num_rows == 0){
			$upAns = "INSERT INTO `homework_student` (`homeworkID`,`studentID`,`studentAnswer`,`status`) VALUES ($homeworkID,$userID,'$ans',1)";
			$conn-> query($upAns);
			echo "<script> alert('答案提交成功 !');window.location.href='StudentMainPage.php';</script>";
		}
		elseif($N->num_rows > 0){
			$upAns = "UPDATE `homework_student` SET `studentAnswer` = '$ans' WHERE studentID = $userID AND homeworkID = $homeworkID";
			$conn-> query($upAns);
			echo "<script> alert('答案更新成功 !');window.location.href='StudentMainPage.php';</script>";
		}

	}

	function echoLastWeekAnsA($conn,$userID,$grade,$week){
		if($week > 1){
			$getAns ="SELECT * FROM homework WHERE grade = $grade AND week = $week - 1 ";
			$getAns = $conn ->query($getAns);
			$getAns->data_seek(0);
			$Answer = $getAns ->fetch_array(MYSQLI_ASSOC);
			$homeworkID = $Answer['homeworkID'];
			$homeworkName = $Answer['homeworkName'];
			$homeworkDescribe = str_replace("\n", "<br>", $Answer['homeworkDescribe']);
			$answer = str_replace("\n", "<br>", $Answer['answer']);
			$path = $Answer['image'];
			$href = $Answer['href'];
			$N = "SELECT * FROM homework_student WHERE studentID = $userID AND homeworkID = $homeworkID";
			$N = $conn-> query($N);
			if(!$N || $N->num_rows == 0) $myans="";
			else{
				$N->data_seek(0);
				$Ninf = $N->fetch_array(MYSQLI_ASSOC);
				$myans = str_replace("\n", "<br>", $Ninf['studentAnswer']);
			}
			$hwDiv = 'Lhomework'.$homeworkID;
			$homeworkSpanID = 'LhomeworkSpan'.$homeworkID;
			if($path==NULL && $href==NULL){
				echo <<<hw
					<div id=$hwDiv class='modal'>
						<div class='modal-content'>
							<span id=$homeworkSpanID class='close'>&times;</span>
							<p style="text-align:left;"">
								作業名稱: $homeworkName<br>
								解答: $answer<br>
								我的答案: $myans<br>
								作業說明: $homeworkDescribe
							</p>
						</div>
					</div>
hw;
				echo <<<modal
					<button class="HWbutton2" style="margin-left:0px;" onclick="ShowModal('$hwDiv','$homeworkSpanID')">此週作業解答</button>
modal;
			}
			elseif($path!==NULL && $href==NULL){
				echo <<<hw
					<div id=$hwDiv class='modal'>
						<div class='modal-content'>
							<span id=$homeworkSpanID class='close'>&times;</span>
							<p style="text-align:left;"">
								作業名稱: $homeworkName<br>
								解答: $answer<br>
								我的答案: $myans<br>
								作業說明: $homeworkDescribe<br>
								<img src=$path class='bullImg'>
							</p>
						</div>
					</div>
hw;
				echo <<<modal
					<button class="HWbutton2" style="margin-left:0px;" onclick="ShowModal('$hwDiv','$homeworkSpanID')">此週作業解答</button>
modal;
			}
			elseif($path==NULL && $href!==NULL){
				echo <<<hw
					<div id=$hwDiv class='modal'>
						<div class='modal-content'>
							<span id=$homeworkSpanID class='close'>&times;</span>
							<p style="text-align:left;"">
								作業名稱: $homeworkName<br>
								解答: $answer<br>
								我的答案: $myans<br>
								作業說明: $homeworkDescribe<br>
								連結: <a href=$href target="_blank">點我穿越</a>
							</p>
						</div>
					</div>
hw;
				echo <<<modal
					<button class="HWbutton2" style="margin-left:0px;" onclick="ShowModal('$hwDiv','$homeworkSpanID')">此週作業解答</button>
modal;
			}
			elseif($path!==NULL && $href!==NULL){
				echo <<<hw
					<div id=$hwDiv class='modal'>
						<div class='modal-content'>
							<span id=$homeworkSpanID class='close'>&times;</span>
							<p style="text-align:left;"">
								作業名稱: $homeworkName<br>
								解答: $answer<br>
								我的答案: $myans<br>
								作業說明: $homeworkDescribe<br>
								連結: <a href=$href target="_blank">點我穿越</a>
								<img src=$path class='bullImg'>
							</p>
						</div>
					</div>
hw;
				echo <<<modal
					<button class="HWbutton2" style="margin-left:0px;" onclick="ShowModal('$hwDiv','$homeworkSpanID')">此週作業解答</button>
modal;
			}
		}
	}

	function signEnableA($conn,$week,$userID){
		$result = "SELECT `$week` FROM sign_parent WHERE studentID = $userID";
		$result = $conn-> query($result);
		$result->data_seek(0);
		$result = $result->fetch_array(MYSQLI_NUM);
		$result = $result[0];
		if($result==NULL) {
			echo <<<enable
				<form method="post" id="signForm">
					<label class="SignIn"><input type="submit" id="SignButton" value=""/></label>
					<span><input type="text" name="Signed" placeholder="輸入驗證碼..." required></span>
				</form>
				<span id="SignInHere">Not signed yet ☞ &nbsp;</span>
enable;
		}
		else{
			echo <<<disable
				<form method="post" id="signForm">
					<label class="SignIn"><input type="submit" name="Signed" id="SignButton" value="" disabled/></label>
				</form>
				<span id="SignInHere">Signed ✔ &nbsp; </span>
disable;
		}
	}

	function UpdateSignA($conn,$time,$week,$userID,$input){
		$input = check_input($conn, $input); 
		$sPass = "SELECT `signPass` FROM config";
		$sPass = $conn-> query($sPass);
		$sPass->data_seek(0);
		$sPass = $sPass->fetch_array(MYSQLI_NUM);
		$sPass = $sPass[0];
		$result = "SELECT `$week` FROM sign_parent WHERE studentID = $userID";
		$result = $conn-> query($result);
		$result->data_seek(0);
		$result = $result->fetch_array(MYSQLI_NUM);
		$result = $result[0];
		if($input == $sPass){
			if ($result==NULL){
				$sign = "UPDATE `sign_parent` SET `$week` = '$time' WHERE studentID = $userID";
				$conn-> query($sign);
				echo "<script>alert('此週補簽成功 !');window.location.href='StudentAnnouncement.php?chosenWeek=$week';</script>";
			}			
		}
		else{
			echo "<script>alert('驗證碼錯誤 !');window.location.href='StudentAnnouncement.php?chosenWeek=$week';</script>";			
		}
	}

?>