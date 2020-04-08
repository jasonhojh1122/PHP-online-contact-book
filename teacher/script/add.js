function cancel(){
	if (confirm("一旦取消，輸入資訊將遺失")) window.location.replace('index.php');
}
function addHref(){
	document.getElementById("linkLabel").style.display = "block";
	document.getElementById("linkInput").style.display = "block";
}
function addPic(){
	document.getElementById("imgLabel").style.display = "block";
	document.getElementById("imgInput").style.display = "block";
}