<?php
	# Header files
	header('Content-type: application/json');
	header('Accept: application/json');
	require_once __DIR__ . '/dataLayer.php';
	
	# Save action in a variable
	$action = $_POST["action"];

	# Action filtering
	switch ($action) {

		case "LOGIN":
			loginService();
			break;

		case "LOGOUT":
			logoutService();
			break;

		case "REDIRECT":
			redirectService();
			break;
		
		default:
			# code...
			break;
	}

	# Error function REWRITE
	function genericErrorFunction($errorCode) {
		switch($errorCode) {
			case "500" : header("HTTP/1.1 500 Bad connection, portal down");
						 die("The server is down, we couldn't stablish the data base connection.");
						 break;

			case "406" : header("HTTP/1.1 406 User not found.");
						 die("Wrong credentials provided.");
						 break;

			case "409" : header("HTTP/1.1 409 Username provided already exists, please provide a new one.");
						die("Username provided already exists.");
						break;
			default:
			# code...
			break;
		}
	}

	# Encryption function
	function getEncryptedPassword($password) {
		# For encryption purposes
		$key = pack('H*', "B374A26A71490437AA024E4FADD5B497FDFF1A8EA6FF12F6FB65AF2720B59CCF");
		$iv = pack('H*', "7E892875A52C59A3B588306B13C31FBD");

		$password_enc = openssl_encrypt($password, 'aes-256-cbc', $key, 0, $iv);
		$password_enc = $password_enc . ':' . $iv;

	    return $password_enc;
	}

	# Decryption function
	function getDecryptedPassword($encryptedPassword) {
		# For decryption purposes
		$key = pack('H*', "B374A26A71490437AA024E4FADD5B497FDFF1A8EA6FF12F6FB65AF2720B59CCF");

		$parts = explode(':', $encryptedPassword);
		$pass_dec = openssl_decrypt($parts[0], 'aes-256-cbc', $key, 0, $parts[1]);

		return $pass_dec;
	}

	# -------------- Services --------------
	# REWRITE
	function answerFriendRequestService() {
		session_start();
		if(isset($_SESSION['current'])) {
			# Fetch required data
			$currentUID = $_SESSION['current'];
			$senderUsername = $_POST['request_sender'];
			$answer = $_POST['answer'];

			// Get the id of the new friend
			$friendIDRetrival = getUserID($senderUsername);

			if($friendIDRetrival["MESSAGE"] == "SUCCESS") {

				if($answer == "accept") {

					// Add the friend
					$addFriendOutcome = addFriend($currentUID, $friendIDRetrival["UID"]);

					// If insertion was successful...
					if($addFriendOutcome["MESSAGE"] == "SUCCESS") {

						# Remove request from the database
						$removeRequestOutcome = removeRequest($friendIDRetrival["UID"], $currentUID);

						if($removeRequestOutcome["MESSAGE"] == "SUCCESS") {
							$response = array("outcome" => "SUCCESS");

							echo json_encode($response);
						}
						// If removal fails...
						else {
							// Error message
							genericErrorFunction($removeRequestOutcome["MESSAGE"]);
						}
					}
					else {
						genericErrorFunction($addFriendOutcome["MESSAGE"]);
					}
				}
				else if($answer == "reject"){
					# Remove request from the database
					$removeRequestOutcome = removeRequest($friendIDRetrival["UID"], $currentUID);

					if($removeRequestOutcome["MESSAGE"] == "SUCCESS") {
						$response = array("outcome" => "SUCCESS");

						echo json_encode($response);
					}
					// If insertion fails...
					else {
						// Error message
						genericErrorFunction($removeRequestOutcome["MESSAGE"]);
					}
				}
			}
				// If id retrival fails...
			else {
				// Error message
				genericErrorFunction($friendIDRetrival["MESSAGE"]);
			}
		}
		else {
			session_destroy();
			setcookie("PHPSESSID", "", time() - 1, "/", "", 0);
			$response = array("MESSAGE" => "IGNORED");
			echo json_encode($response);
		}
	}
	# REWRITE
	function commentsService() {
		session_start();
		if(isset($_SESSION['current'])) {
			# Fetch required data
			$currentUID = $_SESSION['current'];

			# Launch data layer execution attempt
			$commentsFetched = fetchComments($currentUID);
			
			# If the message is success...
			if($commentsFetched["MESSAGE"] == "SUCCESS") {
				
				# Return profile data
				echo json_encode($commentsFetched['COMMENTS']);
			}
			# If attempt failed...
			else {
				# Error message
				genericErrorFunction($commentsFetched["MESSAGE"]);
			}
		}
		else {
			session_destroy();
			setcookie("PHPSESSID", "", time() - 1, "/", "", 0);
			$response = array("MESSAGE" => "IGNORED");
			echo json_encode($response);
		}
	}
	# REWRITE
	function fillFriendListService() {
		session_start();
		if(isset($_SESSION['current'])) {
			# Fetch required data
			$currentUID = $_SESSION['current'];

			# Launch data layer execution attempt
			$friendsFetched = fetchFriends($currentUID);
			
			# If the message is success...
			if($friendsFetched["MESSAGE"] == "SUCCESS") {
				
				# Return friends data
				echo json_encode($friendsFetched['FRIENDS']);
			}
			# If attempt failed...
			else {
				# Error message
				genericErrorFunction($friendsFetched["MESSAGE"]);
			}
		}
		else {
			session_destroy();
			setcookie("PHPSESSID", "", time() - 1, "/", "", 0);
			$response = array("MESSAGE" => "IGNORED");
			echo json_encode($response);
		}
	}
	# REWRITE
	function fillFriendRequestService() {
		session_start();
		if(isset($_SESSION['current'])) {
			# Fetch required data
			$currentUID = $_SESSION['current'];

			# Launch data layer execution attempt
			$requestsFetched = fetchRequests($currentUID);
			
			# If the message is success...
			if($requestsFetched["MESSAGE"] == "SUCCESS") {
				
				# Return requests data
				echo json_encode($requestsFetched['REQUESTS']);
			}
			# If attempt failed...
			else {
				# Error message
				genericErrorFunction($requestsFetched["MESSAGE"]);
			}
		}
		else {
			session_destroy();
			setcookie("PHPSESSID", "", time() - 1, "/", "", 0);
			$response = array("MESSAGE" => "IGNORED");
			echo json_encode($response);
		}
	}
	# REWRITE
	function fillTextboxRememberUsernameService() {
		if(isset($_COOKIE['remember'])) {
			$response = array("remember" => "SUCCESS",
							  "username" => $_COOKIE['remember']);
		}
		else {
			$response = array("remember" => "FAIL");
		}

		echo json_encode($response);
	}
	
	function loginService() {

		# Fetch data in local variables
		$uName = $_POST["uName"];
		$uPassword = $_POST["uPassword"];

		# Launch data layer execution attempt
		$fetchloginInfoOutcome = fetchloginInfo($uName);

		# If attempt is successful...
		if($fetchloginInfoOutcome["MESSAGE"] == "SUCCESS") {

			# Decrypt real password and compare with input
			$pass_dec = getDecryptedPassword($fetchloginInfoOutcome["PASS_ENC"]);

			if($pass_dec === $uPassword) {

				# Start and set session variables
				session_start();
				$_SESSION['current'] = $fetchloginInfoOutcome["ID"];

				# Return successful message to presentation layer
				$response = array("result" => "$uName logged in successfully");
				echo json_encode($response);
			}
			else {
				# Wrong credentials error message
				genericErrorFunction("406");
			}
		}
		# If attempt failed...
		else {
			# Error message
			genericErrorFunction($fetchloginInfoOutcome["MESSAGE"]);
		}
	}
	
	function logoutService() {
		session_start();
		unset($_SESSION['current']);
		session_destroy();
		setcookie("PHPSESSID", "", time() - 1, "/", "", 0);

		# Return successful message to presentation layer
		$response = array("result" => "SUCCESS");
		echo json_encode($response);
	}
	# REWRITE
	function newCommentService() {
		session_start();
		if(isset($_SESSION['current'])) {
			# Fetch required data
			$currentUID = $_SESSION['current'];
			$comment = addslashes($_POST["comment"]);

			# Attempt to create a new comment
			$newCommentOutcome = attemptCreateComment($currentUID, $comment);

			# If it was successful...
			if ($newCommentOutcome['MESSAGE'] == "SUCCESS") {

				# Get user information to present the new comment
				$userInfo = fetchProfile($currentUID);

				# If information is fetched...
				if ($userInfo['MESSAGE'] == "SUCCESS") {
					# Return profile data
					echo json_encode($userInfo);
				}
				# If fetching information fails...
				else {
					# Error message
					genericErrorFunction($userInfo["MESSAGE"]);
				}
			}
			# If insertion fails...
			else {
				# Error message
				genericErrorFunction($newCommentOutcome["MESSAGE"]);
			}
		}
		else {
			session_destroy();
			setcookie("PHPSESSID", "", time() - 1, "/", "", 0);
			$response = array("MESSAGE" => "IGNORED");
			echo json_encode($response);
		}
	}
	# REWRITE
	function profileService() {
		session_start();
		if(isset($_SESSION['current'])) {
			# Fetch required data
			$currentUID = $_SESSION['current'];

			# Launch data layer execution attempt
			$profileFetched = fetchProfile($currentUID);

			# If the message is success...
			if($profileFetched['MESSAGE'] == "SUCCESS") {
				
				# Return profile data
				echo json_encode($profileFetched);
			}
			# If attempt failed...
			else {
				# Error message
				genericErrorFunction($profileFetched["MESSAGE"]);
			}
		}
		else {
			session_destroy();
			setcookie("PHPSESSID", "", time() - 1, "/", "", 0);
			$response = array("MESSAGE" => "IGNORED");
			echo json_encode($response);
		}
	}
	
	function redirectService() {

		$context = $_POST["context"];

		$response = array("shouldRedirect" => false);

		session_start();
		
		if($context === "home") {
			if(!isset($_SESSION['current'])) {
				$response = array("shouldRedirect" => true);
				session_destroy();
				setcookie("PHPSESSID", "", time() - 1, "/", "", 0);
			}
		}
		else if($context === "index"){
			if(isset($_SESSION['current'])) {
		   		$response = array("shouldRedirect" => true);
			}
			else {
				$response = array("shouldRedirect" => false);
				session_destroy();
				setcookie("PHPSESSID", "", time() - 1, "/", "", 0);
			}
		}

		echo json_encode($response);
	}
	# REWRITE
	function registerService() {

		$uName = $_POST["uName"];
		$uPassword = $_POST["uPassword"];
		$fName = $_POST["fName"];
		$lName = $_POST["lName"];
		$email = $_POST["email"];
		$gender = $_POST["gender"];
		$country = $_POST["country"];

		# Verify that desired username doesn't exists
		$verificationOutcome = verifyUserExistence($uName);

		if($verificationOutcome["MESSAGE"] == "NO") {

			# Encrypt password
			$pass_enc = getEncryptedPassword($uPassword);

			# Attempt to create new user
			$newUserOutcome = attemptCreateUser($uName, $pass_enc, $fName, $lName, $email, $country, $gender);

			if($newUserOutcome["MESSAGE"] == "SUCCESS") {

				# Get new user's id
				$getIdOutcome = getUserID($uName);

				if($getIdOutcome["MESSAGE"] == "SUCCESS") {

					session_start();

					$_SESSION['current'] = $getIdOutcome["UID"];

					$response = array("MESSAGE" => "New user created successfully!");
					
					echo json_encode($response);
				}
				else {
					# Error message
					genericErrorFunction($getIdOutcome["MESSAGE"]);
				}
			}
			# If insertion fails...
			else {
				# Error message
				genericErrorFunction($newUserOutcome["MESSAGE"]);
			}
		}
		# If user exists or query fails...
		else {
			# Error message
			genericErrorFunction($verificationOutcome["MESSAGE"]);
		}
	}
	# REWRITE
	function searchFriendService() {
		session_start();
		if(isset($_SESSION['current'])) {
			# Fetch required data
			$currentUID = $_SESSION['current'];
			$nameOrEmail = $_POST['key'];

			# Launch data layer execution attempt
			$friendsFetched = searchFriends($currentUID, $nameOrEmail);
			
			# If the message is success...
			if($friendsFetched["MESSAGE"] == "SUCCESS") {
				
				# Return possible friends data
				echo json_encode($friendsFetched['FRIENDS']);
			}
			# If attempt failed...
			else {
				# Error message
				genericErrorFunction($friendsFetched["MESSAGE"]);
			}
		}
		else {
			session_destroy();
			setcookie("PHPSESSID", "", time() - 1, "/", "", 0);
			$response = array("MESSAGE" => "IGNORED");
			echo json_encode($response);
		}
	}
	# REWRITE
	function sendFriendRequestService() {
		session_start();
		if(isset($_SESSION['current'])) {
			# Fetch required data
			$currentUID = $_SESSION['current'];
			$possibleFriendUser = $_POST['possible_Friend'];

			// Get the id of the new friend
			$friendIDRetrival = getUserID($possibleFriendUser);

			if($friendIDRetrival["MESSAGE"] == "SUCCESS") {

				// Add the friend
				$sendRequestOutcome = attemptCreateFriendRequest($currentUID, $friendIDRetrival["UID"]);

				// If insertion was successful...
				if($sendRequestOutcome["MESSAGE"] == "SUCCESS") {

					$response = array("outcome" => "SUCCESS");

					echo json_encode($response);
				}
				else {
					genericErrorFunction($sendRequestOutcome["MESSAGE"]);
				}
			}
				// If id retrival fails...
			else {
				// Error message
				genericErrorFunction($friendIDRetrival["MESSAGE"]);
			}
		}
		else {
			session_destroy();
			setcookie("PHPSESSID", "", time() - 1, "/", "", 0);
			$response = array("MESSAGE" => "IGNORED");
			echo json_encode($response);
		}
	}
?>