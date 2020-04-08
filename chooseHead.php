<?php session_start();?>
<html>
<head>
	<meta charset="UTF-8" CONTENT="NO-CACHE"/>
	<title> 更新大頭照 </title>
	<link href="./node_modules/croppie/croppie.css" rel="stylesheet" type="text/css">
	<link href="./style/uploadHead.css" rel="stylesheet" type="text/css">
	<script src="https://cdn.bootcss.com/jquery/1.11.2/jquery.min.js"></script>
	<script type="text/javascript" src="./node_modules/croppie/croppie.js"></script>
	<script type="text/javascript">
		$( document ).ready(function() {
			var $uploadCrop;

			function readFile(input) {
				if (input.files && input.files[0]) {
					var reader = new FileReader();          
					reader.onload = function (e) {
						$uploadCrop.croppie('bind', {
							url: e.target.result
						});
						$('.upload-demo').addClass('ready');
					}           
					reader.readAsDataURL(input.files[0]);
				}
			}

			$uploadCrop = $('#upload-demo').croppie({
				viewport: {
					width: 200,
					height: 200,
					type: 'circle'
				},
				boundary: {
					width: 300,
					height: 300
				}
			});

			$('#upload').on('change', function () { readFile(this); });
			$('.upload-result').on('click', function (ev) {
				$uploadCrop.croppie('result', {
					type: 'canvas',
					size: 'original'
				}).then(function (resp) {
					$('#imagebase64').val(resp);
					$('#form').submit();
				});
			});

		});
	</script>
</head>
<body>
	<div id="NewHeadbg"><img src="style/BG/blackboard.jpg"></div>
	<form action="uploadHead.php" id="form" method="post">
		<div id="upload-demo" style="height:350; margin-top:20px;"></div>
		<input type="hidden" id="imagebase64" name="imagebase64">
<!--	<?php 
			/*
			$headurl = 'userData/head/'.$_SESSION['userID'].'.png'; 
			*/
		?>
		<script type="text/javascript">
			document.getElementsByClassName('cr-image')[0].src = <?php /*echo*/ $headurl;?>;
		</script>
-->
		<label class="chooseFile"><input type="file" id="upload" value=" " style="display: none;"></label>
		<button class="upload-result">上傳</button>
	</form>
</body>
</html>