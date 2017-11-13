// Initialize

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

// Fill user information
$.ajax({
	url : "./data/applicationLayer.php",
	type : "POST",
	dataType : "json",
	data : 	{
				"action" : "PROFILE"
			},
	ContentType : "application/json",

	success : function(profile) {
		$("#name").text(profile.fName + " " + profile.lName);

		$("#username").text(profile.username);

		$("#email").text(profile.email);
	},
	error : function(errorMessage) {
		alert(errorMessage.statusText);
	}
});

updateOceans();

// Events

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

	var oceanID = $(this).parents('.oceanItem').attr('name');

	$.ajax({
		url : "./data/applicationLayer.php",
		type : "POST",
		dataType : "json",
		data : 	{
					"action" : "BOOKMARK",
					"ocean" : oceanID
				},
		ContentType : "application/json",

		success : function(bookmark) {
			updateOceans();
		},
		error : function(errorMessage) {
			alert(errorMessage.statusText);
		}
	});
});

// Click to unbookmark
$(document).on('click', 'i[name="bookmarked"]', function() {
	var oceanID = $(this).parents('.oceanItem').attr('name');

	$.ajax({
		url : "./data/applicationLayer.php",
		type : "POST",
		dataType : "json",
		data : 	{
					"action" : "UNBOOKMARK",
					"ocean" : oceanID
				},
		ContentType : "application/json",

		success : function(unbookmark) {
			updateOceans();
		},
		error : function(errorMessage) {
			alert(errorMessage.statusText);
		}
	});
});

// Click to paint/unpaint
$(document).on('click', 'div[name="paintCounter"]', function() {
	
	var icon = $(this).find('i');
	var counter = icon.next();

	if(counter.hasClass("paint")) {
		var oceanID = $(this).parents('.oceanItem').attr('name');

		$.ajax({
			url : "./data/applicationLayer.php",
			type : "POST",
			dataType : "json",
			data : 	{
						"action" : "PAINT",
						"ocean" : oceanID
					},
			ContentType : "application/json",

			success : function(unpaint) {
				updateOceans();
			},
			error : function(errorMessage) {
				alert(errorMessage.statusText);
			}
		});
	}
	else if(counter.hasClass("painted")) {
		var oceanID = $(this).parents('.oceanItem').attr('name');

		$.ajax({
			url : "./data/applicationLayer.php",
			type : "POST",
			dataType : "json",
			data : 	{
						"action" : "UNPAINT",
						"ocean" : oceanID
					},
			ContentType : "application/json",

			success : function(unpaint) {
				updateOceans();
			},
			error : function(errorMessage) {
				alert(errorMessage.statusText);
			}
		});
	}
});

// Click to open chat
$(document).on('click', 'div[name="chatToggler"]', function() {

	// Fill messages-----------------

	$(this).parents('div.oceanItem').find('div[name="chatWindow"]').toggleClass('hide');
});

// Click new ocean
$(document).on('click', '#newOcean', createOcean);
function createOcean() {

	// If fields are not empty
	if($('textarea[name="newOceanSummary"]').val() != "" && $('#newOceanTags').val() != "") {

		// Obtain information
		var content = $('textarea[name="newOceanSummary"]').val();
		var sTags = $('#newOceanTags').val();
		var tags = sTags.split('#');
		tags = tags.slice(1);
		var jsonToSend;

		// Add bookmark to that ocean or not
		if($('#BookmarkNewOcean').prop('checked')) {
			jsonToSend = {
							"content" : content,
							"tags" : tags,
							"bookmark" : "true",
							"action" : "CREATE_OCEAN"
						 };
		}
		else {
			jsonToSend = {
							"content" : content,
							"tags" : tags,
							"bookmark" : "false",
							"action" : "CREATE_OCEAN"
						 };
		}

		// Send to database
		$.ajax({
			url : "./data/applicationLayer.php",
			type : "POST",
			dataType : "json",
			data : 	jsonToSend,
			ContentType : "application/json",

			success : function(reply) {
				if(reply.MESSAGE == "SUCCESS") {
					updateOceans();
					$('textarea[name="newOceanSummary"]').val("");
					$('#newOceanTags').val("");
					alert("New ocean created successfully!");
				}
			},
			error : function(errorMessage) {
				alert(errorMessage.statusText);
			}
		});
	}
	else {
		alert("Remember to write a summary of the new ocean and it's tags!");
	}
}

// Change search bar
$(document).on('change', '#searchBar', updateHomeOceans);

// Change search bar
$(document).on('change', '#searchBarBook', updateBookmarksOceans);

// Change search bar
$(document).on('change', '#searchBarPaint', updatePaintedOceans);

// Change switch order home
$(document).on('change', '#sortDeepnessSwitch', updateHomeOceans);

// Change switch order bookmarks
$(document).on('change', '#sortDeepnessSwitchBook', updateBookmarksOceans);

// Change search order painted
$(document).on('change', '#sortDeepnessSwitchPaint', updatePaintedOceans);

// Funcion para actualizar los oceanos mostrados
function updateOceans() {
	updateHomeOceans();
	updateBookmarksOceans();
	updatePaintedOceans();
}

// Funcion para actualizar los oceanos de home
function updateHomeOceans() {

	// Obtener tags para buscar
	var sTags = $('#searchBar').val();
	var tags = sTags.split('#');
	tags = tags.slice(1);
	var sortBy;

	// Obtener orden de aparicion
	if($('#sortDeepnessSwitch').prop('checked')) {
		sortBy = "paintNum";
	}
	else {
		sortBy = "O.ID";	
	}

	$.ajax({
		url : "./data/applicationLayer.php",
		type : "POST",
		dataType : "json",
		data : 	{
					"action" : "OCEANS",
					"section" : "Home",
					"tags" : tags,
					"sort" : sortBy
				},
		ContentType : "application/json",
		success : function(oceans) {

			$("#oceans").html("");
			var newHtml = "";

			if(oceans.length > 0) {
				for(var i = 0; i < oceans.length; i++) {
					newHtml += '<div name="' + oceans[i].id + '" class="oceanItem">';
					newHtml += '<div class="row align-middle align-right">';
					newHtml += '<div class="columns shrink offset-right-mini align-icons">';
					newHtml += '<div class=" icon row align-middle align-center">';

					if(oceans[i].bookmarked == "true") {
						newHtml += '<i name="bookmark" class="material-icons align-icons hide">bookmark_border</i>';
						newHtml += '<i name="bookmarked" class="material-icons align-icons yellowColor">bookmark</i>';
					}
					else {
						newHtml += '<i name="bookmark" class="material-icons align-icons">bookmark_border</i>';
						newHtml += '<i name="bookmarked" class="material-icons align-icons yellowColor hide">bookmark</i>';
					}
					
					newHtml += '</div>';
					newHtml += '</div>';
					newHtml += '<div class="columns offset-right-mini">';
					newHtml += '<p>';
					newHtml += '<strong name="summary">';
					newHtml += oceans[i].summary;
					newHtml += '</strong>';
					newHtml += '<span class="dateOcean">';
					newHtml += '- ' + oceans[i].timetag;
					newHtml += '</span>';
					newHtml += '</p>';
					newHtml += '<p name="oceanTags">';

					for(var t = 0; t < oceans[i].tags.length; t++) {
						newHtml += '#' + oceans[i].tags[t] + ' ';
					}

					newHtml += '</p>';
					newHtml += '</div>';

					newHtml += '<div class="columns shrink offset-right-mini">';

					if(oceans[i].painted == "true") {
						newHtml += '<div name="paintCounter" class="icon row align-middle align-center offset-bottom-mini yellowColor">';
						newHtml += '<i class="material-icons">brush</i>';
						newHtml += '<span name="counter" class="painted">';
					}
					else {
						newHtml += '<div name="paintCounter" class="icon row align-middle align-center offset-bottom-mini">';
						newHtml += '<i class="material-icons">brush</i>';
						newHtml += '<span name="counter" class="paint">';
						
					}

					newHtml += oceans[i].paintNum;
					newHtml += '</span>';
					newHtml += '</div>';
					newHtml += '<div name="chatToggler" class="icon row align-middle align-center">';
					newHtml += '<i name="chat" class="material-icons align-icons">chat</i>';
					newHtml += '<span name="msgNum">';
					newHtml += oceans[i].numMessages;
					newHtml += '</span>';
					newHtml += '</div>';
					newHtml += '</div>';
					newHtml += '</div>';

					newHtml += '<div class="row align-center">';
					newHtml += '<div name="chatWindow" class="columns small-8 hide">';
					newHtml += '<div class="chatHeader row align-center">';
					newHtml += '<h4>Messages</h4>';
					newHtml += '</div>';
					newHtml += '<hr>';
					newHtml += '<div name="messages">';

					newHtml += '</div>';
					newHtml += '<hr>';
					newHtml += '<div name="newMassage" class="newMessage row align-middle">';
					newHtml += '<div class="columns">';
					newHtml += '<input type="text" placeholder="Post a message...">';
					newHtml += '</div>';
					newHtml += '<div class="columns shrink">';
					newHtml += '<input type="submit" class="button" name="newMsg" Value="Post">';
					newHtml += '</div>';
					newHtml += '</div>';
					newHtml += '</div>';
					newHtml += '</div>';
					newHtml += '</div>';
				}
			}
			else {
				newHtml += '<p class="noContent">There are no oceans with those tags.</p>';
			}

			$("#oceans").html(newHtml);
		},
		error : function(errorMessage) {
			alert(errorMessage.statusText);
		}
	});
}

// Funcion para actualizar los oceanos de bookmarks
function updateBookmarksOceans() {
	// Obtener tags para buscar
	var sTags = $('#searchBarBook').val();
	var tags = sTags.split('#');
	tags = tags.slice(1);
	var sortBy;

	// Obtener orden de aparicion
	if($('#sortDeepnessSwitchBook').prop('checked')) {
		sortBy = "paintNum";
	}
	else {
		sortBy = "O.ID";	
	}

	$.ajax({
		url : "./data/applicationLayer.php",
		type : "POST",
		dataType : "json",
		data : 	{
					"action" : "OCEANS",
					"section" : "Bookmarked",
					"tags" : tags,
					"sort" : sortBy
				},
		ContentType : "application/json",
		success : function(oceans) {

			$("#oceansBook").html("");
			var newHtml = "";

			if(oceans.length > 0) {
				for(var i = 0; i < oceans.length; i++) {
					newHtml += '<div name="' + oceans[i].id + '" class="oceanItem">';
					newHtml += '<div class="row align-middle align-right">';
					newHtml += '<div class="columns shrink offset-right-mini align-icons">';
					newHtml += '<div class=" icon row align-middle align-center">';

					if(oceans[i].bookmarked == "true") {
						newHtml += '<i name="bookmark" class="material-icons align-icons hide">bookmark_border</i>';
						newHtml += '<i name="bookmarked" class="material-icons align-icons yellowColor">bookmark</i>';
					}
					else {
						newHtml += '<i name="bookmark" class="material-icons align-icons">bookmark_border</i>';
						newHtml += '<i name="bookmarked" class="material-icons align-icons yellowColor hide">bookmark</i>';
					}
					
					newHtml += '</div>';
					newHtml += '</div>';
					newHtml += '<div class="columns offset-right-mini">';
					newHtml += '<p>';
					newHtml += '<strong name="summary">';
					newHtml += oceans[i].summary;
					newHtml += '</strong>';
					newHtml += '<span class="dateOcean">';
					newHtml += '- ' + oceans[i].timetag;
					newHtml += '</span>';
					newHtml += '</p>';
					newHtml += '<p name="oceanTags">';

					for(var t = 0; t < oceans[i].tags.length; t++) {
						newHtml += '#' + oceans[i].tags[t] + ' ';
					}

					newHtml += '</p>';
					newHtml += '</div>';

					newHtml += '<div class="columns shrink offset-right-mini">';

					if(oceans[i].painted == "true") {
						newHtml += '<div name="paintCounter" class="icon row align-middle align-center offset-bottom-mini yellowColor">';
						newHtml += '<i class="material-icons">brush</i>';
						newHtml += '<span name="counter" class="painted">';
					}
					else {
						newHtml += '<div name="paintCounter" class="icon row align-middle align-center offset-bottom-mini">';
						newHtml += '<i class="material-icons">brush</i>';
						newHtml += '<span name="counter" class="paint">';
						
					}

					newHtml += oceans[i].paintNum;
					newHtml += '</span>';
					newHtml += '</div>';
					newHtml += '<div name="chatToggler" class="icon row align-middle align-center">';
					newHtml += '<i name="chat" class="material-icons align-icons">chat</i>';
					newHtml += '<span name="msgNum">';
					newHtml += oceans[i].numMessages;
					newHtml += '</span>';
					newHtml += '</div>';
					newHtml += '</div>';
					newHtml += '</div>';

					newHtml += '<div class="row align-center">';
					newHtml += '<div name="chatWindow" class="columns small-8 hide">';
					newHtml += '<div class="chatHeader row align-center">';
					newHtml += '<h4>Messages</h4>';
					newHtml += '</div>';
					newHtml += '<hr>';
					newHtml += '<div name="messages">';

					newHtml += '</div>';
					newHtml += '<hr>';
					newHtml += '<div name="newMassage" class="newMessage row align-middle">';
					newHtml += '<div class="columns">';
					newHtml += '<input type="text" placeholder="Post a message...">';
					newHtml += '</div>';
					newHtml += '<div class="columns shrink">';
					newHtml += '<input type="submit" class="button" name="newMsg" Value="Post">';
					newHtml += '</div>';
					newHtml += '</div>';
					newHtml += '</div>';
					newHtml += '</div>';
					newHtml += '</div>';
				}
			}
			else {
				newHtml += '<p class="noContent">There are no oceans with those tags.</p>';
			}

			$("#oceansBook").html(newHtml);
		},
		error : function(errorMessage) {
			alert(errorMessage.statusText);
		}
	});
}

// Funcion para actualizar los oceanos de painted
function updatePaintedOceans() {
	// Obtener tags para buscar
	var sTags = $('#searchBarPaint').val();
	var tags = sTags.split('#');
	tags = tags.slice(1);
	var sortBy;

	// Obtener orden de aparicion
	if($('#sortDeepnessSwitchPaint').prop('checked')) {
		sortBy = "paintNum";
	}
	else {
		sortBy = "O.ID";	
	}

	$.ajax({
		url : "./data/applicationLayer.php",
		type : "POST",
		dataType : "json",
		data : 	{
					"action" : "OCEANS",
					"section" : "Painted",
					"tags" : tags,
					"sort" : sortBy
				},
		ContentType : "application/json",
		success : function(oceans) {

			$("#oceansPaint").html("");
			var newHtml = "";

			if(oceans.length > 0) {
				for(var i = 0; i < oceans.length; i++) {
					newHtml += '<div name="' + oceans[i].id + '" class="oceanItem">';
					newHtml += '<div class="row align-middle align-right">';
					newHtml += '<div class="columns shrink offset-right-mini align-icons">';
					newHtml += '<div class=" icon row align-middle align-center">';

					if(oceans[i].bookmarked == "true") {
						newHtml += '<i name="bookmark" class="material-icons align-icons hide">bookmark_border</i>';
						newHtml += '<i name="bookmarked" class="material-icons align-icons yellowColor">bookmark</i>';
					}
					else {
						newHtml += '<i name="bookmark" class="material-icons align-icons">bookmark_border</i>';
						newHtml += '<i name="bookmarked" class="material-icons align-icons yellowColor hide">bookmark</i>';
					}
					
					newHtml += '</div>';
					newHtml += '</div>';
					newHtml += '<div class="columns offset-right-mini">';
					newHtml += '<p>';
					newHtml += '<strong name="summary">';
					newHtml += oceans[i].summary;
					newHtml += '</strong>';
					newHtml += '<span class="dateOcean">';
					newHtml += '- ' + oceans[i].timetag;
					newHtml += '</span>';
					newHtml += '</p>';
					newHtml += '<p name="oceanTags">';

					for(var t = 0; t < oceans[i].tags.length; t++) {
						newHtml += '#' + oceans[i].tags[t] + ' ';
					}

					newHtml += '</p>';
					newHtml += '</div>';

					newHtml += '<div class="columns shrink offset-right-mini">';

					if(oceans[i].painted == "true") {
						newHtml += '<div name="paintCounter" class="icon row align-middle align-center offset-bottom-mini yellowColor">';
						newHtml += '<i class="material-icons">brush</i>';
						newHtml += '<span name="counter" class="painted">';
					}
					else {
						newHtml += '<div name="paintCounter" class="icon row align-middle align-center offset-bottom-mini">';
						newHtml += '<i class="material-icons">brush</i>';
						newHtml += '<span name="counter" class="paint">';
						
					}

					newHtml += oceans[i].paintNum;
					newHtml += '</span>';
					newHtml += '</div>';
					newHtml += '<div name="chatToggler" class="icon row align-middle align-center">';
					newHtml += '<i name="chat" class="material-icons align-icons">chat</i>';
					newHtml += '<span name="msgNum">';
					newHtml += oceans[i].numMessages;
					newHtml += '</span>';
					newHtml += '</div>';
					newHtml += '</div>';
					newHtml += '</div>';

					newHtml += '<div class="row align-center">';
					newHtml += '<div name="chatWindow" class="columns small-8 hide">';
					newHtml += '<div class="chatHeader row align-center">';
					newHtml += '<h4>Messages</h4>';
					newHtml += '</div>';
					newHtml += '<hr>';
					newHtml += '<div name="messages">';

					newHtml += '</div>';
					newHtml += '<hr>';
					newHtml += '<div name="newMassage" class="newMessage row align-middle">';
					newHtml += '<div class="columns">';
					newHtml += '<input type="text" placeholder="Post a message...">';
					newHtml += '</div>';
					newHtml += '<div class="columns shrink">';
					newHtml += '<input type="submit" class="button" name="newMsg" Value="Post">';
					newHtml += '</div>';
					newHtml += '</div>';
					newHtml += '</div>';
					newHtml += '</div>';
					newHtml += '</div>';
				}
			}
			else {
				newHtml += '<p class="noContent">There are no oceans with those tags.</p>';
			}

			$("#oceansPaint").html(newHtml);
		},
		error : function(errorMessage) {
			alert(errorMessage.statusText);
		}
	});
}