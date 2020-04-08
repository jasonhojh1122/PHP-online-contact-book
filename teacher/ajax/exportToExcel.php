<?php
	require_once '../../phpexcel/PHPExcel.php';
	require_once '../../loginMySQL.php';
	require_once '../functions.php';
	$conn = connect_mysql($hn, $un, $pw, $dbData);
	
	function num_to_alphabet($num){
		return chr($num+64);
	}
	
	// Create new PHPExcel object
	$objPHPExcel = new PHPExcel();
	$sheetNum = 0;
	
	//weekly announces
	$objPHPExcel->setActiveSheetIndex($sheetNum);
	$objPHPExcel->getActiveSheet()->setTitle('每周公告');
	$startRow = $maxRow = $curRow = 3;
	for ($grade = 3; $grade <= 6; ++$grade){
		$objPHPExcel->getActiveSheet()->setCellValue('A'.($curRow-2), $grade.'年級');
		$objPHPExcel->getActiveSheet()->setCellValue('A'.($curRow-1), '週數');
		for ($week = 1; $week <= 21; ++$week){
			$curRow = $startRow;
			$column = num_to_alphabet($week+1);
			$objPHPExcel->getActiveSheet()->setCellValue($column.($curRow-1), $week);
			$getAnnounce = "SELECT * FROM bulletin WHERE grade = $grade AND week = $week";
			$announces = query_to_result($conn, $getAnnounce);
			while($announce = mysqli_fetch_row($announces)){
				$announceTitle = $announce[1];
				$announceText = $announce[2];
				$href = $announce[3];
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$curRow, '標題');
				$objPHPExcel->getActiveSheet()->setCellValue($column.$curRow, $announceTitle);
				++$curRow;
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$curRow, '內容');
				$objPHPExcel->getActiveSheet()->setCellValue($column.$curRow, $announceText);
				++$curRow;
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$curRow, '連結');
				$objPHPExcel->getActiveSheet()->setCellValue($column.$curRow, $href);
				++$curRow;
			}
			$maxRow = ($curRow > $maxRow) ? $curRow : $maxRow;
		}
		$startRow = $maxRow = $curRow = $maxRow + 5;
	}
	++$sheetNum;
	$objPHPExcel->createSheet($sheetNum);
	
	//weekly homework
	$objPHPExcel->setActiveSheetIndex($sheetNum);
	$objPHPExcel->getActiveSheet()->setTitle('每周作業');
	$startRow = $curRow = 7;
	for ($grade = 3; $grade <= 6; ++$grade){
		$objPHPExcel->getActiveSheet()->setCellValue('A'.($curRow-6), $grade.'年級');
		$objPHPExcel->getActiveSheet()->setCellValue('A'.($curRow-5), '週數');
		$objPHPExcel->getActiveSheet()->setCellValue('A'.($curRow-4), '作業');
		$objPHPExcel->getActiveSheet()->setCellValue('A'.($curRow-3), '詳細');
		$objPHPExcel->getActiveSheet()->setCellValue('A'.($curRow-2), '連結');
		$objPHPExcel->getActiveSheet()->setCellValue('A'.($curRow-1), '答案');
		$getHomework = "SELECT * FROM homework WHERE grade = $grade ORDER BY week ASC";
		$homeworks = query_to_result($conn, $getHomework);
		for ($i = 1; $i <= 21; ++$i) $objPHPExcel->getActiveSheet()->setCellValue(num_to_alphabet($i+1).($curRow-5), $i);
		while ($homework = mysqli_fetch_row($homeworks)){
			$curRow = $startRow;
			$homeworkID = $homework[0];
			$homeworkName = $homework[1];
			$homeworkDescribe = $homework[2];
			$href = $homework[3];
			$answer = $homework[6];
			$week = $homework[7];			
			$column = num_to_alphabet($week + 1);
			$objPHPExcel->getActiveSheet()->setCellValue($column.($curRow-4), $homeworkName);
			$objPHPExcel->getActiveSheet()->setCellValue($column.($curRow-3), $homeworkDescribe);
			$objPHPExcel->getActiveSheet()->setCellValue($column.($curRow-2), $answer);
			$objPHPExcel->getActiveSheet()->setCellValue($column.($curRow-1), $href);
			$getStatus = "SELECT * FROM homework_student WHERE homeworkID = $homeworkID ORDER BY studentID ASC";
			$status = query_to_result($conn, $getStatus);
			while ($student = mysqli_fetch_row($status)){
				$studentID = $student[1];
				$studentAnswer = $student[2];
				$studentName = get_userName_by_ID($conn, $studentID, $dbData);
				$objPHPExcel->getActiveSheet()->setCellValue('A'.$curRow, $studentName);
				if ($studentAnswer == '' or $studentAnswer == NULL) $objPHPExcel->getActiveSheet()->setCellValue($column.$curRow, '0');
				else $objPHPExcel->getActiveSheet()->setCellValue($column.$curRow, $studentAnswer);
				++$curRow;
			}			
		}
		$startRow = $curRow + 9;
		$curRow = $startRow;
	}
	++$sheetNum;
	$objPHPExcel->createSheet($sheetNum);
	
	//parent sign
	$objPHPExcel->setActiveSheetIndex($sheetNum);
	$objPHPExcel->getActiveSheet()->setTitle('家長簽名');
	$startRow = $curRow = 3;
	for ($grade = 3; $grade <= 6; ++$grade){
		$objPHPExcel->getActiveSheet()->setCellValue('A'.($curRow-2), $grade.'年級');
		$objPHPExcel->getActiveSheet()->setCellValue('A'.($curRow-1), '週數');
		for ($i = 1; $i <= 21; ++$i) $objPHPExcel->getActiveSheet()->setCellValue(num_to_alphabet($i+1).($curRow-1), $i);
		$getSign = "SELECT * FROM sign_parent WHERE studentID IN (SELECT userID FROM user WHERE userType = 2 AND grade = $grade)";
		$signs = query_to_result($conn, $getSign);
		while ($sign = mysqli_fetch_row($signs)){
			$studentName = get_userName_by_ID($conn, $sign[0], $dbData);
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$curRow, $studentName);
			for ($i = 1; $i <= 21; ++$i){
				$column = num_to_alphabet($i+1);
				$signTime = $sign[$i];
				$objPHPExcel->getActiveSheet()->setCellValue($column.$curRow, $signTime);
			}
			++$curRow;
		}
		$curRow += 5;
	}
	
	//----- COURSES -----
	$getCourses = "SELECT * FROM course ORDER BY forWho ASC";
	$courses = query_to_result($conn, $getCourses);
	while($course = mysqli_fetch_assoc($courses)){
		//add new sheet
		++$sheetNum;
		$objPHPExcel->createSheet($sheetNum);
		$courseID = $course['courseID'];
		$courseName = $course['courseName'];
		$courseDescribe = $course['courseDescribe'];
		$time = $course['time'];
		$forWho = $course['forWho'];
		$compulsory = $course['compulsory'];
		$compulsory = ($compulsory == 1)? '必修' : '選修';
		switch($forWho){
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
				$forWho = '全體年級';
				break;
		}
		
		$getTeacher = "SELECT name FROM user WHERE userID IN (SELECT userID FROM course_teacher WHERE courseID = $courseID)";
		$teacherName = query_to_result($conn, $getTeacher);
		$teacherName->data_seek(0);
		$teacherName = $teacherName->fetch_array(MYSQLI_NUM);
		$teacherName = $teacherName[0];
		
		//course basic info
		$objPHPExcel->setActiveSheetIndex($sheetNum)
					->setCellValue('A1', '課程名稱')->setCellValue('A2', $courseName)
					->setCellValue('A4', '授課老師')->setCellValue('A5', $teacherName)
					->setCellValue('A7', '課程描述')->setCellValue('A8', $courseDescribe)		
					->setCellValue('A10', '上課時間')->setCellValue('A11', $time)
					->setCellValue('A13', '必/選修')->setCellValue('A14', $compulsory)
					->setCellValue('A16', '上課對象')->setCellValue('A17', $forWho);
					
		//course progress
		$objPHPExcel->getActiveSheet()->setCellValue('C1', '週數');
		for ($i = 2; $i <= 22; ++$i) $objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $i-1);
		$objPHPExcel->getActiveSheet()->setCellValue('D1', '課程進度');
		$getProgress = "SELECT * FROM course_progress WHERE courseID = $courseID";
		$progresses = query_to_result($conn, $getProgress);
		$progresses->data_seek(0);
		$progress = $progresses->fetch_array(MYSQLI_NUM);
		for ($week = 1; $week <= 21; ++$week){
			$row = $week + 1;
			$text = $progress[$week];
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$row, $text);
		}
		
		//students' attendance
		$getAttendance = "SELECT * FROM attendance WHERE courseID = $courseID";
		$attendances = query_to_result($conn, $getAttendance);
		$column = 'D';
		while($attendance = mysqli_fetch_row($attendances)){
			++$column;
			$studentName = get_userName_by_ID($conn, $attendance[0], $dbData);
			$objPHPExcel->getActiveSheet()->setCellValue($column.'1', $studentName);
			for ($i = 2; $i <= 22; ++$i){
				$status = $attendance[$i];
				$objPHPExcel->getActiveSheet()->setCellValue($column.$i, $status);
				//formatting
				switch($status){
					case 0: //present
						$color = '7ea823';
						break;
					case 1: //late
						$color = 'a97f24';
						break;
					case 2: //absent
						$color = 'aa2525';
						break;
					case 3: //off
						$color = '919191';
						break;
					case 4: //holiday
						$color = '000000';
						break;
				}
				$objPHPExcel->getActiveSheet()->getStyle($column.$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
				$objPHPExcel->getActiveSheet()->getStyle($column.$i)->getFill()->getStartColor()->setRGB($color);
			}
		}
		$objPHPExcel->getActiveSheet()->setTitle($courseName);
	
		/* conditional formatting
		$objConditional1 = new PHPExcel_Style_Conditional();
		$objConditional1->setConditionType(PHPExcel_Style_Conditional::CONDITION_CELLIS)
						->setOperatorType(PHPExcel_Style_Conditional::OPERATOR_EQUAL)
						->addCondition('0'); //present
		$objConditional1->getStyle()->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$objConditional1->getStyle()->getFill()->getStartColor()->setRGB(PHPExcel_Style_Color::COLOR_GREEN);
		$objConditional2 = new PHPExcel_Style_Conditional();
		$objConditional2->setConditionType(PHPExcel_Style_Conditional::CONDITION_CELLIS)
						->setOperatorType(PHPExcel_Style_Conditional::OPERATOR_EQUAL)
						->addCondition('1'); //late
		$objConditional2->getStyle()->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$objConditional2->getStyle()->getFill()->getStartColor()->setRGB(PHPExcel_Style_Color::COLOR_YELLOW);
		$objConditional3 = new PHPExcel_Style_Conditional();
		$objConditional3->setConditionType(PHPExcel_Style_Conditional::CONDITION_CELLIS)
						->setOperatorType(PHPExcel_Style_Conditional::OPERATOR_EQUAL)
						->addCondition('2'); //absent
		$objConditional3->getStyle()->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$objConditional3->getStyle()->getFill()->getStartColor()->setRGB(PHPExcel_Style_Color::COLOR_RED);
		$objConditional4 = new PHPExcel_Style_Conditional();
		$objConditional4->setConditionType(PHPExcel_Style_Conditional::CONDITION_CELLIS)
						->setOperatorType(PHPExcel_Style_Conditional::OPERATOR_EQUAL)
						->addCondition('3'); //day off
		$objConditional4->getStyle()->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$objConditional4->getStyle()->getFill()->getStartColor()->setRGB(PHPExcel_Style_Color::COLOR_BLUE);
		$objConditional5 = new PHPExcel_Style_Conditional();
		$objConditional5->setConditionType(PHPExcel_Style_Conditional::CONDITION_CELLIS)
						->setOperatorType(PHPExcel_Style_Conditional::OPERATOR_EQUAL)
						->addCondition('4'); //holiday
		$objConditional5->getStyle()->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$objConditional5->getStyle()->getFill()->getStartColor()->setRGB(PHPExcel_Style_Color::COLOR_BLACK);
		$conditionalStyles = $objPHPExcel->getActiveSheet()->getStyle('E2')->getConditionalStyles();
		array_push($conditionalStyles, $objConditional1);
		array_push($conditionalStyles, $objConditional2);
		array_push($conditionalStyles, $objConditional3);
		array_push($conditionalStyles, $objConditional4);
		array_push($conditionalStyles, $objConditional5);
		$toSet = 'E2:' . $column . '22';
		$objPHPExcel->getActiveSheet()->getStyle($toSet)->setConditionalStyles($conditionalStyles); */
	}
	
	date_default_timezone_set('Asia/Taipei');
	$exportTime = date('Y-m-d');
	$fileName = 'Content-Disposition: attachment;filename='. $exportTime . '.xlsx';
	
	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);
	// Redirect output to a client’s web browser (Excel2007)
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header($fileName); // file name
	header('Cache-Control: max-age=0');
	// If you're serving to IE 9, then the following may be needed
	header('Cache-Control: max-age=1');
	// If you're serving to IE over SSL, then the following may be needed
	header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
	header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
	header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
	header ('Pragma: public'); // HTTP/1.0
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
	exit;
?>