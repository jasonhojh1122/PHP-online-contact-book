/* Change page of Undone Homework list. Call changepage.php */
function redirect_mainpage(id) {
	$.ajax({
		type: 'POST',
		url: 'changepage.php',
		data: {unHWPage: id},
		success: function(result) {
		window.location.replace('HRMainPage.php')
		}
	});
}
/* Choose user head */
function chooseHead() {
		window.open('../chooseHead.php', '上傳大頭照', config='height=450,width=400,scrollbars=no');
}
/* Side Bar open or close */
function sideBar_open() {
    document.getElementById("StuSideBar").style.display = "block";
}
function sideBar_close() {
    document.getElementById("StuSideBar").style.display = "none";
}
/* Show Modal */
function ShowModal(x,y){
	var modal = document.getElementById(x);
	var span = document.getElementById(y);
	// When the user clicks the button, open the modal 
	modal.style.display = "block";
	// When the user clicks on <span> (x), close the modal
	span.onclick = function() {
	    modal.style.display = "none";
	};
	// When the user clicks anywhere outside of the modal, close it
	window.onclick = function(event) {
	    if (event.target == modal) {
	    	modal.style.display = "none";
	    }
	};
}

function showC(x){
	var y = document.getElementById(x);
	if(	y.style.display == "none"){
		y.style.display = "block";		
	}
	else{
		y.style.display = "none";
	}
}

function SHOW(){
	var drop = document.getElementsByClassName('dropdown-content')[0];
	if(	drop.style.display == "none"){
		drop.style.display = "block";		
	}
	else{
		drop.style.display = "none";
	}	
}

/* Student My Course */
function info(x){
	var element = document.getElementById(x);
	if(element.style.display=="none"){
		element.style.display="table";
	}
	else{
		element.style.display="none";					
	}
}