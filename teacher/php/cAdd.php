
<?php
	session_start();
	require_once '../loginMySQL.php';
	require_once 'functions.php';
	$conn = connect_mysql($hn, $un, $pw, $dbData);

	$courseName = $courseDescribe = $courseTime = $forWho = "";
	$courseNameErr = $courseDescribeErr = "";

	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		if (empty($_POST["courseName"])) $courseNameErr = "請輸入課程名稱";
		else {
			$courseName = check_input($conn, $_POST["courseName"]);
			$query = "SELECT courseID FROM course WHERE courseName = '$courseName'";
			$result = $conn->query($query);
			if ($result->num_rows != 0) $courseNameErr = "課程名稱重複";
			elseif (mb_strlen($courseName,"utf-8") > 50) $courseNameErr = "超過上限50個字";
		}

		if (empty($_POST["courseDescribe"])) $courseDescribeErr = "請輸入課程描述";
		else {
			$courseDescribe = check_input($conn, $_POST["courseDescribe"]);
			if (mb_strlen($courseDescribe,"utf-8") > 20000) $courseDescribeErr = "超過上限20000字";
		}
		
		$courseTime = check_input($conn, $_POST["courseTime"]);

		$forWho = (int)check_input($conn, $_POST["forWho"]);
	}

	if ($courseNameErr=="" and $courseDescribeErr=="" and !empty($_POST["courseName"]) and !empty($_POST["courseDescribe"]) and !empty($_POST["courseTime"])){
		date_default_timezone_set('Asia/Taipei');
		$summitTime = date('Y-m-d H:i:s');

		$query = "INSERT INTO course(courseName, courseDescribe, courseTime, lastEditTime, forWho) VALUES" . "('$courseName', '$courseDescribe', '$courseTime', '$summitTime', $forWho)";
		$result = $conn->query($query);
		if (!$result) echo "寫入資料庫發生錯誤: $query<br> " . $conn->error ."<br><br>";

		$query = "SELECT max(courseID) FROM course";
		$result = $conn->query($query);
		if (!$result) echo "存取資料庫發生錯誤: $query<br> " . $conn->error ."<br><br>";
		$result->data_seek(0);
		$id = $result->fetch_array(MYSQLI_NUM);
		$id = $id[0];
		$userID = $_SESSION['userID'];
		$query = "INSERT INTO course_and_teacher(userID, courseID) VALUES" . "($userID, $id)";
		$result = $conn->query($query);
		if (!$result) echo "寫入資料庫發生錯誤: $query<br> " . $conn->error ."<br><br>";
		else{
			$conn->close();
			$message = "新增課程成功";
			echo "<script type='text/javascript'> alert('$message'); window.location.replace('../courseMy.php');</script>";
		}
	}
?>