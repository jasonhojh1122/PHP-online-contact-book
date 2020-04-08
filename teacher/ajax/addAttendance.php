<?php
	require_once '../../loginMySQL.php';
	require_once '../functions.php';
	$conn = connect_mysql($hn, $un, $pw, $dbData);
	
	// get students' userID
	$query = "SELECT userID FROM user WHERE userType = 2";
	$students = query_to_result($conn, $query);
	while ($student = mysqli_fetch_row($students)){
		$studentID = $student[0];
		if ($studentID == 35) continue;
		// get not compulsory course
		$query = "SELECT courseID FROM course_student WHERE studentID = $studentID";
		$courses = query_to_result($conn, $query);
		while ($course = mysqli_fetch_row($courses)){
			$courseID = $course[0];
			$checkCreated = "SELECT * FROM attendance WHERE studentID = $studentID AND courseID = $courseID";
			$result = query_to_result($conn, $checkCreated);
			if ($result->num_rows != 0) continue;
			else{
				$query = "INSERT INTO attendance (studentID, courseID) VALUES ($studentID, $courseID)";
				$result = query_to_result($conn, $query);
			}
		}
		
		// get compulsory course
		$grade = get_user_grade($conn, $studentID);
		echo $grade;
		if ($grade == 3 or $grade == 4) $grade = array($grade-2, 5, 7); 
		else if ($grade == 5 or $grade == 6) $grade = array($grade-2, 6, 7);
		else continue;
		
		$query = "SELECT courseID FROM course WHERE forWho IN (" . implode(',',$grade) . ") AND compulsory = 1";
		$courses = query_to_result($conn, $query);
		while ($course = mysqli_fetch_row($courses)){
			$courseID = $course[0];
			$checkCreated = "SELECT * FROM attendance WHERE studentID = $studentID AND courseID = $courseID";
			$result = query_to_result($conn, $checkCreated);
			if ($result->num_rows != 0) continue;
			else{
				$query = "INSERT INTO attendance (studentID, courseID) VALUES ($studentID, $courseID)";
				$result = query_to_result($conn, $query);
			}
		}
	}
?>