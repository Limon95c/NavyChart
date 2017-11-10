// Redirect
$.ajax({
	url : "./data/applicationLayer.php",
	type : "POST",
	dataType : "json",
	ContentType : "application/json",
	data : {
				"context" : "index",
				"action" : "REDIRECT"
			},
	success : function(redirectAnswer) {
		if(redirectAnswer.shouldRedirect) {
			$(location).attr("href", "home.html");
		}
	},
	error : function(errorMessage) {
		alert(errorMessage.statusText);
	}
});

/*

// Set remembered username (if cookie is set) in username textbox
$.ajax({
	url: "./data/applicationLayer.php",
	type : "POST",
	dataType : "json",
	ContentType : "application/json",
	data : { "action" : "FILL_TEXTBOX_REMEMBER_USERNAME" },
	success : function(rememberUser) {
		if(rememberUser.remember == "SUCCESS") {
			user.val(rememberUser.username);
		}
	},
	error : function(errorMessage) {
		alert(errorMessage.statusText);
	}
});
*/
// Click actions login
$(document).on('click', "#signIn_button", function() {

	var user = $("#username");
	var password = $("#password");

	if(user.val() != "" && password.val() != "") {
		$.ajax({
			url : "./data/applicationLayer.php",
			type : "POST",
			data : {
						"uName" : user.val(),
						"uPassword" : password.val(),
						"action" : "LOGIN"
					},
			ContentType : "application/json",
			dataType : "json",
			success: function(dataReceived) {
				$(location).attr("href", "home.html");
			},
			error : function(errorMessage) {
				alert(errorMessage.statusText);
			}
		});
	}
	else {
		alert("Remember to fill your information first!");
	}
});

// Click actions register
$(document).on('click', "#register_button", function() {

	var user = $("#newUser");
	var first = $("#newFirst");
	var last = $("#newLast");
	var email = $("#newEmail");
	var password = $("#newPass");
	var confirm = $("#confirmPass");

	var validRegister = $(".registerElement");
	var valid = true;

	// Revisar si los campos estan vacíos
	for(var i = 0; i < 6; i++) {
		if(validRegister.eq(i).val() == "") {
			alert("Please fill the remaining blanks first.")
			valid = false;
		}
	}

	// Si el password no concuerda con la confirmacion, mostrar alerta
	if(valid && password.val() !== confirm.val()) {
		valid = false;
		alert("Password confirmation doesn't match the new password.");
	}

	// Si si es valida la informacion ingresada
	if(valid) {

		// Registrar usuario nuevo
		$.ajax({
			url : "./data/applicationLayer.php",
			type : "POST",
			ContentType : "application/json",
			dataType : "json",
			data : {
						"uName" : user.val(),
						"uPassword" : password.val(),
						"fName" : first.val(),
						"lName" : last.val(),
						"email" : email.val(),
						"action" : "REGISTER"
					},
			success: function(status) {
				alert("New user created successfully!");
				$(location).attr("href", "home.html");
			},
			error : function(errorMessage) {
				alert(errorMessage.statusText);
			}
		});
	}
});