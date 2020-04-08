<?php
	$fileCount=count($_FILES['UploadHW']['name']);
	for ($i=0;$i<$fileCount;$i++){
		if ($_FILES['UploadHW']['error'][$i] === UPLOAD_ERR_OK){
		/*	echo '檔案名稱: ' . $_FILES['UploadHW']['name'][$i].'<br/>';
			echo '檔案類型: ' . $_FILES['UploadHW']['type'][$i].'<br/>';
			echo '檔案大小: ' . ($_FILES['UploadHW']['size'][$i] / 1024).' KB<br/>';
			echo '暫存名稱: ' . $_FILES['UploadHW']['tmp_name'][$i]. '<br/>';*/
			if (file_exists('UploadedHW/' . $_FILES['UploadHW']['name'][$i])){
				echo "<script>alert('檔案已存在。'); window.location.href='StudentMyHomework.php';</script>";
			}
			else{
				$file=$_FILES['UploadHW']['tmp_name'][$i];
				$dest='UploadedHW/'.$_FILES['UploadHW']['name'][$i];
				move_uploaded_file($file, $dest);
				echo "<script>alert('檔案上傳成功。'); window.location.href='StudentMyHomework.php';</script>";		
			}
		} else{
			echo '發生錯誤代碼: ' . $_FILES['UploadHW']['error'].'<br/>';
		}	
	}
?>