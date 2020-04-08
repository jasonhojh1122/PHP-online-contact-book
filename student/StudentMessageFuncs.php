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
					<li><a href="StudentMessage.php" target = "_self">訊息</a></li>
					<li><a href="StudentEditProfile.php" target = "_self">設定</a></li>
					<li><a href="../logout.php" style="cursor: pointer;" target = "_top" onclick="return confirm('R U SURE?')">登出</a></li>
				</ul>	
			</div>
sideBar;
	}

	function getTeacher($conn){
		$T = "SELECT userID FROM user WHERE userType = 1";
		$Teachers = $conn -> query($T);
		if (!$Teachers || $Teachers->num_rows==0) echo "<span style='color:white;'>沒有老師</span>";
		else{
			$Num = $Teachers->num_rows;
			for($i = 0; $i < $Num; ++$i){
				$Teachers -> data_seek($i);
				$Teacher = $Teachers -> fetch_array(MYSQLI_NUM);
				$Teacher = $Teacher[0];
				$tdir = '../userData/head/'. $Teacher .'.png';
				$default = '../userData/head/default.jpg';
				$IDtoName = "SELECT name FROM user WHERE userID = $Teacher";
				$IDtoName = $conn->query($IDtoName);
				$IDtoName -> data_seek(0);
				$Name = $IDtoName -> fetch_array(MYSQLI_NUM);
				$Name = $Name[0];										
				if (count(glob($tdir))>0) {
					echo <<<isg
					<a href="StudentMessage.php?Name=$Name&teacherID=$Teacher"><input type='image' src=$tdir></a>
isg;
				}
				else {
					echo <<<ish
					<a href="StudentMessage.php?Name=$Name&teacherID=$Teacher"><input type='image' src=$default></a>
ish;
				}
			}
		}
	}

	function getMsgData($studentID,$teacherID,$connM){
		$table = $teacherID.'_'.$studentID;
		$Msg = "SELECT * FROM $table WHERE 1 ORDER BY id";
		$Msg = mysqli_query($connM, $Msg);
		if (!$Msg) echo "存取資料庫發生錯誤: $Msg<br> " . $connM->error ."<br><br>";
		else {
			$Rmsg = "SELECT message FROM $table WHERE userID = -2";
			$Rmsg = mysqli_query($connM, $Rmsg);	
			$Rmsg ->data_seek(0);
			$ReadNum = $Rmsg->fetch_array(MYSQLI_NUM);
			$ReadNum = $ReadNum[0];
			$MsgNum = $Msg->num_rows;
			$MsgNum = $MsgNum - 2;
			if($MsgNum<=0){
				echo <<< nomsg
					<span style="margin-left:20px;color:white;font-family:'Noto Sans TC',sans-serif;">No Message.</span>
nomsg;
			}
			else{
				if($ReadNum < $MsgNum){
					for($i=2; $i < $ReadNum+2; ++$i){
						$Msg -> data_seek($i);
						$Message = $Msg -> fetch_array(MYSQLI_ASSOC);
						$user = $Message['userID'];
						$content = $Message['message'];
						$time = $Message['time'];
						$isImg = $Message['isImage'];
						list($date,$t)=explode(" ",$time);
						$time = $date."<br>".$t;
						if($user==$studentID&&$isImg==0){
							echo <<<stumsg
								<span class="studentMsg">$content</span>
								<span class="MsgTime" style="text-align:right;float:right;">$time</span>
								<div style="clear:both;"></div>
stumsg;
						}
						elseif($user==$teacherID&&$isImg==0){
							$tdir = '../userData/head/'. $user .'.png';
							if(count(glob($tdir))>0) $tdir = '../userData/head/'. $user .'.png';
							else $tdir = '../userData/head/default.jpg';
							echo <<<tmsg
								<img class='msgHead' src=$tdir>
								<span class="teacherMsg">$content</span>
								<span style="text-align:left;float:left;" class="MsgTime">$time</span>
								<div style="clear:both;"></div>
tmsg;
						}
						elseif($user==$studentID&&$isImg==1){
							echo <<<stuimg
								<img class="MsgImg" src=$content style="float:right;"></img>
								<span class="MsgTime" style="text-align:right;float:right;">$time</span>
								<div style="clear:both;"></div>
stuimg;
						}
						elseif($user==$teacherID&&$isImg==1){
							$tdir = '../userData/head/'. $user .'.png';
							if(count(glob($tdir))>0) $tdir = '../userData/head/'. $user .'.png';
							else $tdir = '../userData/head/default.jpg';
							echo <<<timg
								<img class='msgHead' src=$tdir>
								<img class="MsgImg" src=$content style="float:left;"></img>
								<span class="MsgTime" style="text-align:left;float:left;">$time</span>
								<div style="clear:both;"></div>
timg;
						}								
					}
					echo "<div style='text-align:center;margin-top: 20px;margin-bottom: 20px;'><span class='borderline'>- - - - - - 以下為未讀訊息 - - - - - -</span></div>";
					for($j=$ReadNum+2; $j < $MsgNum+2; ++$j){
						$Msg -> data_seek($j);
						$Message = $Msg -> fetch_array(MYSQLI_ASSOC);
						$user = $Message['userID'];
						$content = $Message['message'];
						$time = $Message['time'];
						$isImg = $Message['isImage'];
						list($date,$t)=explode(" ",$time);
						$time = $date."<br>".$t;
						if($user==$studentID&&$isImg==0){
							echo <<<stumsg
								<span class="studentMsg">$content</span>
								<span class="MsgTime" style="text-align:right;float:right;">$time</span>
								<div style="clear:both;"></div>
stumsg;
						}
						elseif($user==$teacherID&&$isImg==0){
							$tdir = '../userData/head/'. $user .'.png';
							if(count(glob($tdir))>0) $tdir = '../userData/head/'. $user .'.png';
							else $tdir = '../userData/head/default.jpg';
							echo <<<tmsg
								<img class='msgHead' src=$tdir>
								<span class="teacherMsg">$content</span>
								<span style="text-align:left;float:left;" class="MsgTime">$time</span>
								<div style="clear:both;"></div>
tmsg;
						}
						elseif($user==$studentID&&$isImg==1){
							echo <<<stuimg
								<img class="MsgImg" src=$content style="float:right;"></img>
								<span class="MsgTime" style="text-align:right;float:right;">$time</span>
								<div style="clear:both;"></div>
stuimg;
						}
						elseif($user==$teacherID&&$isImg==1){
							$tdir = '../userData/head/'. $user .'.png';
							if(count(glob($tdir))>0) $tdir = '../userData/head/'. $user .'.png';
							else $tdir = '../userData/head/default.jpg';
							echo <<<timg
								<img class='msgHead' src=$tdir>
								<img class="MsgImg" src=$content style="float:left;"></img>
								<span class="MsgTime" style="text-align:left;float:left;">$time</span>
								<div style="clear:both;"></div>
timg;
						}
					}
					$UpdateRead = "UPDATE $table SET `message` = $MsgNum WHERE userID = -2";
					mysqli_query($connM, $UpdateRead);		
				}
				else{
					for($i=2; $i < $MsgNum+2; ++$i){
						$Msg -> data_seek($i);
						$Message = $Msg -> fetch_array(MYSQLI_ASSOC);
						$user = $Message['userID'];
						$content = $Message['message'];
						$time = $Message['time'];
						$isImg = $Message['isImage'];
						list($date,$t)=explode(" ",$time);
						$time = $date."<br>".$t;
						if($user==$studentID&&$isImg==0){
							echo <<<stumsg
								<span class="studentMsg">$content</span>
								<span class="MsgTime" style="text-align:right;float:right;">$time</span>
								<div style="clear:both;"></div>
stumsg;
						}
						elseif($user==$teacherID&&$isImg==0){
							$tdir = '../userData/head/'. $user .'.png';
							if(count(glob($tdir))>0) $tdir = '../userData/head/'. $user .'.png';
							else $tdir = '../userData/head/default.jpg';
							echo <<<tmsg
								<img class='msgHead' src=$tdir>
								<span class="teacherMsg">$content</span>
								<span style="text-align:left;float:left;" class="MsgTime">$time</span>
								<div style="clear:both;"></div>
tmsg;
						}
						elseif($user==$studentID&&$isImg==1){
							echo <<<stuimg
								<img class="MsgImg" src=$content style="float:right;"></img>
								<span class="MsgTime" style="text-align:right;float:right;">$time</span>
								<div style="clear:both;"></div>
stuimg;
						}
						elseif($user==$teacherID&&$isImg==1){
							$tdir = '../userData/head/'. $user .'.png';
							if(count(glob($tdir))>0) $tdir = '../userData/head/'. $user .'.png';
							else $tdir = '../userData/head/default.jpg';
							echo <<<timg
								<img class='msgHead' src=$tdir>
								<img class="MsgImg" src=$content style="float:left;"></img>
								<span class="MsgTime" style="text-align:left;float:left;">$time</span>
								<div style="clear:both;"></div>
timg;
						}
					}
				}
			}
		}	
	}

	

?>