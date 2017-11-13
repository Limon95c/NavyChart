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

		case "REGISTER":
			registerService();
			break;

		case "PROFILE":
			profileService();
			break;

		case "CREATE_OCEAN":
			newOceanService();
			break;

		case "OCEANS":
			fillOceansService();
			break;

		case "BOOKMARK":
			bookmarkService();
			break;

		case "UNBOOKMARK":
			unbookmarkService();
			break;

		case "UNPAINT":
			unpaintService();
			break;

		case "PAINT":
			paintService();
			break;

		default:
			# code...
			break;
	}

	# Error function
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
	
	function fillOceansService() {
		session_start();
		if(isset($_SESSION['current'])) {

			# Fetch required data
			$currentUID = $_SESSION['current'];
			$section = $_POST['section'];

			if(isset($_POST['tags'])) {
				$tags = $_POST['tags'];
			}
			else {
				$tags = array();
			}
			$order = $_POST['sort'];

			# Launch data layer execution attempt
			$oceansFetched = fetchOceans($currentUID, $section, $tags, $order);
			
			# If the message is success...
			if($oceansFetched["MESSAGE"] == "SUCCESS") {
				
				# Return friends data
				echo json_encode($oceansFetched['oceans']);
			}
			# If attempt failed...
			else {
				# Error message
				genericErrorFunction($oceansFetched["MESSAGE"]);
			}
		}
		else {
			session_destroy();
			setcookie("PHPSESSID", "", time() - 1, "/", "", 0);
			$response = array("MESSAGE" => "IGNORED");
			echo json_encode($response);
		}
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
	
	function newOceanService() {

		session_start();
		if(isset($_SESSION['current'])) {
			# Fetch required data
			$creator = $_SESSION['current'];
			$content = addslashes($_POST["content"]);
			$tags = $_POST["tags"];
			$bookmark = $_POST["bookmark"];

			# Attempt to create a new ocean
			$newOceanOutcome = attemptCreateOcean($content);

			# If it was successful...
			if ($newOceanOutcome['MESSAGE'] == "SUCCESS") {

				# Get the new ocean id
				$getLastOceanIDOutcome = getLastOceanID();

				# If it was successful...
				if($getLastOceanIDOutcome["MESSAGE"] == "SUCCESS") {

					# Retrieve id
					$oceanID = $getLastOceanIDOutcome["ID"];

					# Create array of non-existent tags
					$newTags = array();
					foreach($tags as &$tag) {

						# Check if tag already exists
						$tagExistence = verifyTagExistence($tag);

						# If it didn't exist
						if($tagExistence["MESSAGE"] == "NO") {
							# Add to array of non-existent tags
							$newTags[] = $tag;
						}
					}

					# Create new tags if any
					if(!empty($newTags)) {
						$newTagsOutcome = attemptCreateTags($newTags);

						if($newTagsOutcome['MESSAGE'] != "SUCCESS") {
							# Error message
							genericErrorFunction($newTagsOutcome["MESSAGE"]);
						}
					}

					# Get array of ids from those tags
					$tagsIDs = array();
					foreach($tags as &$tag) {
						$getTagIDOutcome = getTagID($tag);

						if($getTagIDOutcome["MESSAGE"] == "SUCCESS") {
							$tagsIDs[] = $getTagIDOutcome["ID"];
						}
						else {
							# Error message
							genericErrorFunction($getTagIDOutcome["MESSAGE"]);
						}
					}

					# Relate tags to ocean
					foreach($tagsIDs as &$tagID) {
						$relateTagOceanOutcome = relateTagToOcean($tagID, $oceanID);

						if($relateTagOceanOutcome["MESSAGE"] != "SUCCESS") {
							# Error message
							genericErrorFunction($relateTagOceanOutcome["MESSAGE"]);
						}
					}

					# Paint ocean by creator
					$paintOceanOutcome = paintOcean($creator, $oceanID);

					if($paintOceanOutcome["MESSAGE"] != "SUCCESS") {
						# Error message
						genericErrorFunction($paintOceanOutcome["MESSAGE"]);
					}

					# Bookmark ocean if indicated
					if($bookmark == "true") {
						$bookmarkOceanOutcome = bookmarkOcean($creator, $oceanID);

						if($bookmarkOceanOutcome["MESSAGE"] != "SUCCESS") {
							# Error message
							genericErrorFunction($bookmarkOceanOutcome["MESSAGE"]);
						}
					}

					$response = array("MESSAGE" => "SUCCESS");
					
					echo json_encode($response);
				}
				else {
					# Error message
					genericErrorFunction($getLastOceanIDOutcome["MESSAGE"]);
				}
			}
			# If insertion fails...
			else {
				# Error message
				genericErrorFunction($newOceanOutcome["MESSAGE"]);
			}
		}
		else {
			session_destroy();
			setcookie("PHPSESSID", "", time() - 1, "/", "", 0);
			$response = array("MESSAGE" => "IGNORED");
			echo json_encode($response);
		}
	}
	
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
	
	function registerService() {

		$uName = $_POST["uName"];
		$uPassword = $_POST["uPassword"];
		$fName = $_POST["fName"];
		$lName = $_POST["lName"];
		$email = $_POST["email"];

		# Verify that desired username doesn't exists
		$verificationOutcome = verifyUserExistence($uName);

		if($verificationOutcome["MESSAGE"] == "NO") {

			# Encrypt password
			$pass_enc = getEncryptedPassword($uPassword);

			# Attempt to create new user
			$newUserOutcome = attemptCreateUser($uName, $pass_enc, $fName, $lName, $email);

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

	function bookmarkService() {
		session_start();
		if(isset($_SESSION['current'])) {
			# Fetch required data
			$currentUID = $_SESSION['current'];
			$oceanID = $_POST['ocean'];

			# Launch data layer execution attempt
			$bookmarkOceanOutcome = bookmarkOcean($currentUID, $oceanID);

			# If the message is success...
			if($bookmarkOceanOutcome['MESSAGE'] == "SUCCESS") {
				
				# Return profile data
				echo json_encode($bookmarkOceanOutcome);
			}
			# If attempt failed...
			else {
				# Error message
				genericErrorFunction($bookmarkOceanOutcome["MESSAGE"]);
			}
		}
		else {
			session_destroy();
			setcookie("PHPSESSID", "", time() - 1, "/", "", 0);
			$response = array("MESSAGE" => "IGNORED");
			echo json_encode($response);
		}
	}

	function paintService() {
		session_start();
		if(isset($_SESSION['current'])) {
			# Fetch required data
			$currentUID = $_SESSION['current'];
			$oceanID = $_POST['ocean'];

			# Launch data layer execution attempt
			$paintOceanOutcome = paintOcean($currentUID, $oceanID);

			# If the message is success...
			if($paintOceanOutcome['MESSAGE'] == "SUCCESS") {
				
				# Return profile data
				echo json_encode($paintOceanOutcome);
			}
			# If attempt failed...
			else {
				# Error message
				genericErrorFunction($paintOceanOutcome["MESSAGE"]);
			}
		}
		else {
			session_destroy();
			setcookie("PHPSESSID", "", time() - 1, "/", "", 0);
			$response = array("MESSAGE" => "IGNORED");
			echo json_encode($response);
		}
	}

	function unbookmarkService() {
		session_start();
		if(isset($_SESSION['current'])) {
			# Fetch required data
			$currentUID = $_SESSION['current'];
			$oceanID = $_POST['ocean'];

			# Launch data layer execution attempt
			$unbookmarkOceanOutcome = unbookmarkOcean($currentUID, $oceanID);

			# If the message is success...
			if($unbookmarkOceanOutcome['MESSAGE'] == "SUCCESS") {
				
				# Return profile data
				echo json_encode($unbookmarkOceanOutcome);
			}
			# If attempt failed...
			else {
				# Error message
				genericErrorFunction($unbookmarkOceanOutcome["MESSAGE"]);
			}
		}
		else {
			session_destroy();
			setcookie("PHPSESSID", "", time() - 1, "/", "", 0);
			$response = array("MESSAGE" => "IGNORED");
			echo json_encode($response);
		}
	}

	function unpaintService() {
		session_start();
		if(isset($_SESSION['current'])) {
			# Fetch required data
			$currentUID = $_SESSION['current'];
			$oceanID = $_POST['ocean'];

			# Launch data layer execution attempt
			$unpaintOceanOutcome = unpaintOcean($currentUID, $oceanID);

			# If the message is success...
			if($unpaintOceanOutcome['MESSAGE'] == "SUCCESS") {
				
				# Return profile data
				echo json_encode($unpaintOceanOutcome);
			}
			# If attempt failed...
			else {
				# Error message
				genericErrorFunction($unpaintOceanOutcome["MESSAGE"]);
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