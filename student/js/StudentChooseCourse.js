/* Call rechoose.php and rechoose course. Will delete courses choosed in database.*/
function rechoose() {
	$.ajax({
		type: 'POST',
		url: 'rechoose.php',
		success: function() {
			window.location.replace('StudentChooseCourse.php')
		}
	});
}
/* Click on a row of list to choose course */
function choose(x){
	var check = document.getElementById(x);
	if(check.checked==false){
		check.checked=true;				
	}
	else{
		check.checked=false;
	}
}
/* Side Bar open or close */
function sideBar_open() {
    document.getElementById("StuSideBar").style.display = "block";
}
function sideBar_close() {
    document.getElementById("StuSideBar").style.display = "none";
}
function showC(x){
	if(	document.getElementById(x).style.display == "none"){
		document.getElementById(x).style.display = "block";		
	}
	else{
		document.getElementById(x).style.display = "none";	
	}
}