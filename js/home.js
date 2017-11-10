// Redirect
$.ajax({
	url : "./data/applicationLayer.php",
	type : "POST",
	dataType : "json",
	data : 	{
				"context" : "home",
				"action" : "REDIRECT"
			},
	ContentType : "application/json",
	success : function(redirectAnswer) {
		if(redirectAnswer.shouldRedirect) {
			$(location).attr("href", "index.html");
		}
	},
	error : function(errorMessage) {
		alert(errorMessage.statusText);
	}
});

// Logout
$("#logout_button").click(function() {
	$.ajax({
		url : "./data/applicationLayer.php",
		type : "POST",
		dataType : "json",
		ContentType : "application/json",
		data : { "action" : "LOGOUT" },
		success : function(message) {
			$(location).attr("href", "index.html");
		},
		error : function(errorMessage) {
			alert(errorMessage.statusText);
		}
	});
});

// Click in menu bar
$("#mainMenu > li").click(function() {
	if($(this).attr("class") != "Logout") {
		$("li.selected").removeClass("selected");

		var current = $(this).attr("class");

		$(this).addClass("selected");

		$("section.selected").removeClass("selected").addClass("notSelected");

		$("#" + current).removeClass("notSelected").addClass("selected");
	}
});