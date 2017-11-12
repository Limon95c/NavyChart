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
$(document).on('click', "#logout_button", function() {
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
$(document).on('click', "#mainMenu > li", function() {
	if($(this).attr("class") != "Logout") {
		$("li.selected").removeClass("selected");

		var current = $(this).attr("class");

		$(this).addClass("selected");

		$("section.selected").removeClass("selected").addClass("notSelected").addClass("hide");

		$("#" + current).removeClass("notSelected").removeClass("hide").addClass("selected");
	}
});

// Click to bookmark
$(document).on('click', 'i[name="bookmark"]', function() {
	// Remove the hollow icon
	$(this).addClass('hide');
	// Add filled icon
	$(this).next('i[name="bookmarked"]').removeClass('hide');
});

// Click to unbookmark
$(document).on('click', 'i[name="bookmarked"]', function() {
	// Remove the filled icon
	$(this).addClass('hide');
	// Add hollow icon
	$(this).prev('i[name="bookmark"]').removeClass('hide');
});

// Click to paint/unpaint
$(document).on('click', 'div[name="paintCounter"]', function() {
	
	var icon = $(this).find('i');
	var counter = icon.next('span');

	// Paint
	if(counter.attr('name') == "paint") {

		// Pintar de amarillo
		$(this).addClass('yellowColor');
		counter.attr('name', 'painted');
	}

	//Unpaint
	else if(counter.attr('name') == "painted") {

		// Quitar color amarillo
		$(this).removeClass('yellowColor');
		counter.attr('name', 'paint');
	}

	/*
	// Paint parent
	$(this).addClass('yellowColor');

	var element = $(this);
	var spanElem;

	// Change name
	if(element.prop("tagName").toLowerCase() == "i") {
		element.name.attr('name', 'painted');
		spanElem = element.next('span');
	}
	else {
		element.prev('i').name.attr('name', 'painted');
		spanElem = element;
	}

	// Increment value in span
	var actual = parseInt(spanElem.text());
	actual = actual + 1;
	element.text(actual);
	*/
});
