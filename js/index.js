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

// Click actions login
ButtonLogin.click(function() {
	var valid = true;

	for(var i = 0; i < 2; i++) {
		if(validateLogin.eq(i).val() == "") {
			alertsLogin.eq(i).show();
			valid = false;
		}
		else {
			alertsLogin.eq(i).hide();
		}
	}

	if(valid) {

		var remember = $("input[name='remember']").prop('checked');

		if(user.val() != "" && password.val() != "") {
			$.ajax({
				url : "./data/applicationLayer.php",
				type : "POST",
				data : {
							"uName" : user.val(),
							"uPassword" : password.val(),
							"remember" : remember,
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
	}
});

// Click actions register
ButtonRegister.click(function(){
	var valid = true;

	// Revisar si los campos estan vacíos
	for(var i = 0; i < 8; i++) {
		switch (i) {
			case 2:
				//Radio buttons de gender
				if(!$("input[name='gender']:checked").val()) {
					$("#alertGender").show();
					valid = false;
				}
				else {
					$("#alertGender").hide();
				}
				break;
			case 3:
				// Select de country
				if($("#country").prop("selectedIndex") == 0) {
					$("#alertCountry").show();
					valid = false;
				}
				else {
					$("#alertCountry").hide();
				}
				break;
			default:
				if(validateRegister.eq(i).val() == "") {
					alertsRegister.eq(i).show();
					valid = false;
				}
				else {
					alertsRegister.eq(i).hide();
				}
				break;
		}
	}

	// Si el password no concuerda con la confirmacion, mostrar alerta
	if(newPassword.val() !== passwordConfirm.val()) {
		valid = false;
		alert("Password confirmation doesn't match the new password.");
	}

	// Si si es valida la informacion ingresada
	if(valid) {
		if($("input[name='gender']:checked").next().text() == "Masculine")
			var genderLetter = 'M';
		else {
			var genderLetter = 'F';
		}

		// Registrar usuario nuevo
		$.ajax({
			url : "./data/applicationLayer.php",
			type : "POST",
			ContentType : "application/json",
			dataType : "json",
			data : {
						"uName" : newUser.val(),
						"uPassword" : newPassword.val(),
						"fName" : fName.val(),
						"lName" : lName.val(),
						"email" : email.val(),
						"country" : country.find(":selected").text(),
						"gender" : genderLetter,
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