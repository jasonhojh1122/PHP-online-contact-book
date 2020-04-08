function show_more(id) {
	var x = document.getElementById(id);
	if (x.style.display === "none") x.style.display = "block";
	else x.style.display = "none";
}
function redirect_to_page(id) {
	$.ajax({
		type: 'POST',
		url: 'ajax/sessionAddMainPageButtonID.php',
		data: {announcePage: id},
		success: function(result) {
			window.location.replace('index.php')
		}
	});
}