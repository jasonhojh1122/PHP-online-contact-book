<?php
	session_start();
	$userID = $_SESSION['userID'];
    if(isset($_POST['imagebase64'])){
        $data = $_POST['imagebase64'];

        list($type, $data) = explode(';', $data);
        list(, $data)      = explode(',', $data);
        $data = base64_decode($data);
		$imageName = 'userData/head/'.$userID.".png";
        file_put_contents($imageName, $data);
		echo "<script>window.close();</script>";
    }
?>