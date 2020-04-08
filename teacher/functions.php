<?php
	// require_once '../loginMySQL.php';
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
	
	function get_userName_by_ID($conn, $ID, $dbData){
		mysqli_select_db($conn, $dbData);
		$query = "SELECT name FROM user WHERE userID = $ID";
		$result = $conn->query($query);
		$name = $result->fetch_array(MYSQLI_NUM);
		$name = $name[0];
		return $name;
	}
	
	function get_courseName_by_ID($conn, $ID){
		$query = "SELECT courseName FROM course WHERE courseID = $ID";
		$result = $conn->query($query);
		$name = $result->fetch_array(MYSQLI_NUM);
		$name = $name[0];
		return $name;
	}
	
	function get_maxID($conn, $table){
		if ($table == "course") $tosearch = "courseID";
		else if ($table == "bulletin") $tosearch = "announceID";
		else if ($table == "homework") $tosearch = "homeworkID";
		else if ($table == "user") $tosearch = "userID";
		$query = "SELECT max($tosearch) FROM $table";
		$result = $conn->query($query);
		if (!$result) echo "存取資料庫發生錯誤: $query<br> " . $conn->error ."<br><br>";
		$result->data_seek(0);
		$id = $result->fetch_array(MYSQLI_NUM);
		$id = $id[0];
		return $id;
	}
	
	function get_user_grade($conn, $userID){
		$query = "SELECT grade FROM user WHERE userID = $userID";
		$result = $conn->query($query);
		$result->data_seek(0);
		$grade = $result->fetch_array(MYSQLI_NUM);
		$grade = (int)$grade[0];
		return $grade;
	}

	function query_to_result($conn, $query){
		$result = $conn->query($query);
		if (!$result){
			echo "存取資料庫發生錯誤: $query<br> " . $conn->error ."<br><br>";
			$conn->close();
			return false;
		}
		else return $result;
	}

	function echoSideBar($userID,$dir,$default){
		echo <<< sideBar
			<div id="sidebar">
				<div class = "toggle-btn" onclick="toggleSidebar()">
					<span></span>
					<span></span>
					<span></span>
				</div>
				<div class="container">
sideBar;
					if (count(glob($dir)) > 0) echo "<img class='userHead' src=$dir>";
					else echo "<img class='userHead' src=$default>";

		echo <<< sideBar
					<img class="uploadPen" src="../style/Icon/edit.png" title="上傳大頭照" alt="上傳大頭照" style="height:25px;width:25px;" onclick="chooseHead()">
				</div>
				<ul class="sidebar_menu">
					<li><a href="index.php" target = "_self">首頁</a></li>
					<li><a>每週作業</a>
						<ul>
							<li><a href="courseProgress.php" target = "_self">課程進度</a></li>
							<li><a href="attendance.php" target = "_self">出席狀況</a></li>
							<li><a href="bulletinAdd.php" target = "_self">每週公告</a></li>
							<li><a href="homeworkAdd.php" target = "_self">每週作業</a></li>
							<li><a href="homeworkMy.php" target = "_self">作業批改</a></li>
							
						</ul>
					</li>
					<li><a>期初作業</a>
						<ul>
							<li><a href="courseAdd.php" target = "_self">新增課程</a></li>
							<li><a href="addMember.php" target = "_self">新增成員</a></li>
							<li><a href="homeroomNstudent.php" target = "_self">班導配對</a></li>
						</ul>
					</li>
					<li><a>詳細資料</a>
						<ul>
							<li><a href="courseMy.php" target = "_self">課程</a></li>
							<li><a href="bulletinMy.php" target = "_self">公告</a></li>
						</ul>
					</li>
					<li><a>系統</a>
						<ul>
							<li><a href="setting.php" target = "_self">基本設定</a></li>							
							<li><a href="delNExport.php" target = "_self">備份刪除</a></li>
						</ul>
					</li>
					<a href="../logout.php" onclick="return confirm('一旦登出，所有未儲存的資料都將如長江般一去不復返')" target = "_top"><img class="logoutButton" src="../style/Icon/logout.png" title="登出" alt="登出"></a>
				</ul>
			</div>
sideBar;
	}
	
	function echo_course($conn, $query, $dbData){
		$result = query_to_result($conn, $query);
		$rows = $result->num_rows;
		if ($rows == 0) echo "<label>尚未建立任何課程</label>";
		else{
			for ($j = 0; $j < $rows; ++$j){
				$result->data_seek($j);
				$course = $result->fetch_array(MYSQLI_ASSOC);
				$courseID = $course['courseID'];
				$courseName = $course['courseName'];
				$courseDescribe = str_replace("\n", "<br>", $course['courseDescribe']);
				$time = $course['time'];
				$compulsory = $course['compulsory'];
				
				$toGetTeacherID = "SELECT teacherID FROM course_teacher WHERE courseID = $courseID";
				$getTeacherID = $conn->query($toGetTeacherID);
				$getTeacherID->data_seek(0);
				$teacherID = $getTeacherID->fetch_array(MYSQLI_NUM);
				$teacherID = $teacherID[0];
				$grade = get_user_grade($conn, $teacherID);
				
				$teacherName = get_userName_by_ID($conn, $teacherID, $dbData);
				$forWho = $course['forWho'];
				switch ($forWho) {
					case 1:
						$forWho = '三年級';
						break;
					case 2:
						$forWho = '四年級';
						break;
					case 3:
						$forWho = '五年級';
						break;
					case 4:
						$forWho = '六年級';
						break;
					case 5:
						$forWho = '中年級';
						break;
					case 6:
						$forWho = '高年級';
						break;
					case 7:
						$forWho = '全體';
						break;
				}							
				echo <<< END
					<div class="cube" onclick="show_more('$courseID')">
						<label class="left">[$teacherName] $courseName</label>
						<button class="right" type="button" onclick="update_course($courseID)">更新課程</button>
					</div>
					
					<div class="detail" id="$courseID" style="display:none">
						<h3>課程描述：</h3>
							<lable class='info'>$courseDescribe</label><br>
						<h3>上課時間：</h3>
							<lable class='info'>$time</label><br>
						<h3>課程對象：</h3>
							<lable class='info'>$forWho</label><br>
END;
				if ($compulsory == 1) echo "<h3>必修課</h3>";
				else echo "<h3>選修課</h3>";
				
				echo "<h3>課程進度：</h3>";
				$getProgress = "SELECT * FROM course_progress WHERE courseID = $courseID";
				$p = query_to_result($conn, $getProgress);
				$p->data_seek(0);
				$p = $p->fetch_array(MYSQLI_NUM);
				for($i = 1; $i<=21; ++$i){		
					$progress = $p[$i];
					if ($progress != NULL) echo "<lable class='info'>第 $i 周進度：</label> <label> $progress </label><br>";
				}
				
				echo "<h3>修課學生</h3>";
				if ($compulsory == 1) $getStudent = "SELECT userID FROM user WHERE userType = 2 and grade = $grade";
				else $getStudent = "SELECT studentID FROM course_student WHERE courseID = $courseID";			
				
				$students = query_to_result($conn, $getStudent);
				while($student = mysqli_fetch_row($students)){
					$studentID = $student[0];
					$studentName = get_userName_by_ID($conn, $studentID, $dbData);
					echo "<div class='contain'>";
						echo "<label class='info'>$studentName</label>";
						if ($compulsory == 0) echo "<button class='submitButton' onclick='del_stu($studentID, $courseID)'>退選</button>";					
					echo "</div><br>";
				}
				echo "</div>";
			}
		}
	}

	function echo_course_progress($conn, $query, $week, $dbData, $courseOrder){
		$result = query_to_result($conn, $query);
		while($course = mysqli_fetch_assoc($result)){
			$courseID = $course['courseID'];
			$courseName = $course['courseName'];
			
			$toGetProgress = "SELECT `$week` FROM course_progress WHERE courseID = $courseID";
			$getProgress = $conn->query($toGetProgress);
			$getProgress->data_seek(0);
			$progress = $getProgress->fetch_array(MYSQLI_NUM);
			$progress = $progress[0];
			if ($progress == NULL) $progress = '';
			$courseOrder[$courseID] = $progress;
			echo "<div class='contain'>";
			echo "<label class='left'>$courseName</label> <input class='right' type='text' name='$courseID' value='$progress'></input><br>";
			echo "</div>";
		}
		return $courseOrder;
	}
	function check_in_course($conn, $studentID, $courseID){
		$query = "SELECT * FROM course_student WHERE studentID = $studentID AND courseID = $courseID";
		$result = $conn->query($query);
		if ($result->num_rows == 0){
			$query = "SELECT compulsory FROM course WHERE courseID = $courseID";
			$result = $conn->query($query);
			$result->data_seek(0);
			$row = $result->fetch_array(MYSQLI_NUM);
			$row = $row[0];
			if ($row == 1) return 1;
			else return 0;			
		}
		else return 1;
		
	}