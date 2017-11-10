<?php
	
	# REWRITE
	# Function to initialize database connection
	function databaseConnection() {
		# Save configuration in local variables
		$servername = "localhost";
		$username = "root";
		$password = "root";
		$dbname = "navychart";

		# Obtain connection reference or null (if it failed)
		$conn = new mysqli($servername, $username, $password, $dbname);

		# Return $conn
		if ($conn -> connect_error)
		{
			return null;
		}
		else
		{
			return $conn;
		}
	}

	# -------------- Services --------------

	# REWRITE
	# Attempt to add a friend
	function addFriend($currentUID, $friendID) {

		# Obtain null or actual reference of connection
		$connection = databaseConnection();

		# If connection exists...
		if($connection != null) {

			# Query forumlation
			$sql = "INSERT INTO Friends (user1_ID, user2_ID)
					VALUES ('$currentUID', '$friendID')";

			# Query execution
			$friendAdded = $connection -> query($sql);

			# If frienship is created successfully...
			if ($friendAdded === true) {

				$response = array("MESSAGE" => "SUCCESS");
				
				# Cerrar la conexion a base de datos
				$connection -> close();
				# Regresar respuesta
				return $response;
			}
			# Return an error message if database insertion failed
			else {
				return array("MESSAGE" => "500");
			}
		}
		# Return an error message if connection is null
		else {
			return array("MESSAGE" => "500");
		}
	}

	# Attempt to create new user function
	function attemptCreateUser($uName, $uPassword, $fName, $lName, $email) {

		# Obtain null or actual reference of connection
		$connection = databaseConnection();

		# If connection exists...
		if($connection != null) {

			# Query forumlation
			$sql = "INSERT INTO Users(username, passwrd, fName, lName, email)
					VALUES ('$uName', '$uPassword', '$fName', '$lName', '$email')";

			# Query execution
			$userCreated = $connection -> query($sql);

			# If user is created successfully...
			if ($userCreated === true) {

				$response = array("MESSAGE" => "SUCCESS");
				
				# Cerrar la conexion a base de datos
				$connection -> close();
				# Regresar respuesta
				return $response;
			}
			# Return an error message if database insertion failed
			else {
				return array("MESSAGE" => "500");
			}
		}
		# Return an error message if connection is null
		else {
			return array("MESSAGE" => "500");
		}
	}

	# REWRITE
	# Attempt to create a comment
	function attemptCreateComment($currentUID, $comment) {
		# Obtain null or actual reference of connection
		$connection = databaseConnection();

		# If connection exists...
		if($connection != null) {

			# Query forumlation
			$sql = "INSERT INTO Comments(user_ID, content)
					VALUES ('$currentUID', '$comment')";

			# Query execution
			$commentCreated = $connection -> query($sql);

			# If user is created successfully...
			if ($commentCreated === true) {

				$response = array("MESSAGE" => "SUCCESS");
				
				# Cerrar la conexion a base de datos
				$connection -> close();
				# Regresar respuesta
				return $response;
			}
			# Return an error message if database insertion failed
			else {
				return array("MESSAGE" => "500");
			}
		}
		# Return an error message if connection is null
		else {
			return array("MESSAGE" => "500");
		}
	}

	# REWRITE
	# Attempt to create a friend request
	function attemptCreateFriendRequest($currentUID, $possibleFriendID) {
		# Obtain null or actual reference of connection
		$connection = databaseConnection();

		# If connection exists...
		if($connection != null) {

			# Query forumlation
			$sql = "INSERT INTO Requests(sender_ID, response_ID)
					VALUES ('$currentUID', '$possibleFriendID')";

			# Query execution
			$requestCreated = $connection -> query($sql);

			# If friend request is created successfully...
			if ($requestCreated === true) {

				$response = array("MESSAGE" => "SUCCESS");
				
				# Cerrar la conexion a base de datos
				$connection -> close();
				# Regresar respuesta
				return $response;
			}
			# Return an error message if database insertion failed
			else {
				return array("MESSAGE" => "500");
			}
		}
		# Return an error message if connection is null
		else {
			return array("MESSAGE" => "500");
		}
	}

	# Attempt to login function
	function fetchloginInfo($userName) {
		# Obtain null or actual reference of connection
		$connection = databaseConnection();

		# If connection exists...
		if($connection != null) {

			# Query forumlation
			$sql = "SELECT ID, passwrd
					FROM Users
					WHERE username = '$userName'";

			# Query execution
			$queryResult = $connection -> query($sql);

			# If query is successful
			if($queryResult -> num_rows > 0) {
				# Fetching if single result
				$user = $queryResult -> fetch_assoc();
				$response = array("ID" => $user["ID"],
								  "MESSAGE" => "SUCCESS",
								  "PASS_ENC" => $user["passwrd"]);
				
				# Cerrar la conexion a base de datos
				$connection -> close();
				# Regresar respuesta
				return $response;
			}
			# Return an error message for invalid credentials
			else {
				$connection -> close();
				return array("MESSAGE" => "406");
			}
		}
		# Return an error message if connection is null
		else {
			return array("MESSAGE" => "500");
		}
	}

	# REWRITE
	# Attempt to fetch comments avaiable for a user
	function fetchComments($currentUID) {
		# Obtain null or actual reference of connection
		$connection = databaseConnection();

		# If connection exists...
		if($connection != null) {

			# Query forumlation
			$sql = "SELECT fName, lName, username, email, content
					FROM Comments AS C INNER JOIN Users AS U
	                ON C.user_ID = U.ID
					WHERE C.user_ID = $currentUID
					OR C.user_ID IN (SELECT F.user1_ID
					                 FROM Friends AS F
					                 WHERE F.user2_ID = $currentUID
					                 UNION
					                 SELECT F.user2_ID
					                 FROM Friends AS F
					                 WHERE F.user1_ID = $currentUID)
					ORDER BY C.ID";

			# Query execution
			$queryResult = $connection -> query($sql);

			$comments = array();

			# If information was fetch successfully...
			if ($queryResult != null) {
				if ($queryResult -> num_rows > 0) {

					# Fetching various comments if any
					while ($row = $queryResult -> fetch_assoc()) {
				 		$comments[] = array("fName" => $row["fName"],
				 							"lName" => $row["lName"],
				 						    "username" => $row["username"],
				 						    "email" => $row["email"],
				 						    "content" => $row["content"]);
					}
				}

				$response = array("MESSAGE" => "SUCCESS",
								  "COMMENTS" => $comments);

				# Cerrar la conexion a base de datos
				$connection -> close();
				# Regresar respuesta
				return $response;
			}
			# Return an error message if query failed
			else {
				return array("MESSAGE" => "500");
			}
		}
		# Return an error message if connection is null
		else {
			return array("MESSAGE" => "500");
		}
	}

	# REWRITE
	# Attempt to fetch the friend list of a user
	function fetchFriends($currentUID) {
		# Obtain null or actual reference of connection
		$connection = databaseConnection();

		# If connection exists...
		if($connection != null) {

			# Query forumlation
			$sql = "SELECT fName, lName, username, email
					FROM Friends AS F INNER JOIN Users AS U
	                ON F.user1_ID = U.ID
					WHERE F.user2_ID = $currentUID
					UNION
					SELECT fName, lName, username, email
					FROM Friends AS F INNER JOIN Users AS U
	                ON F.user2_ID = U.ID
					WHERE F.user1_ID = $currentUID
					ORDER BY fName";

			# Query execution
			$queryResult = $connection -> query($sql);

			$friends = array();

			# If information was fetch successfully...
			if ($queryResult != null) {
				if ($queryResult -> num_rows > 0) {

					# Fetching various friends if any
					while ($row = $queryResult -> fetch_assoc()) {
				 		$friends[] = array("fName" => $row["fName"],
				 							"lName" => $row["lName"],
				 						    "username" => $row["username"],
				 						    "email" => $row["email"]);
					}
				}

				$response = array("MESSAGE" => "SUCCESS",
								  "FRIENDS" => $friends);

				# Cerrar la conexion a base de datos
				$connection -> close();
				# Regresar respuesta
				return $response;
			}
			# Return an error message if query failed
			else {
				return array("MESSAGE" => "500");
			}
		}
		# Return an error message if connection is null
		else {
			return array("MESSAGE" => "500");
		}
	}

	# REWRITE
	# Attempt to fetch friendship requests of a user
	function fetchRequests($currentUID) {
		# Obtain null or actual reference of connection
		$connection = databaseConnection();

		# If connection exists...
		if($connection != null) {

			# Query forumlation
			$sql = "SELECT R.ID, fName, lName, username, email
					FROM Requests AS R INNER JOIN Users AS U
	                ON R.sender_ID = U.ID
					WHERE R.response_ID = $currentUID
					ORDER BY R.ID";

			# Query execution
			$queryResult = $connection -> query($sql);

			$requests = array();

			# If information was fetch successfully...
			if ($queryResult != null) {
				if ($queryResult -> num_rows > 0) {

					# Fetching various requests if any
					while ($row = $queryResult -> fetch_assoc()) {
				 		$requests[] = array("fName" => $row["fName"],
				 							"lName" => $row["lName"],
				 						    "username" => $row["username"],
				 						    "email" => $row["email"]);
					}
				}

				$response = array("MESSAGE" => "SUCCESS",
								  "REQUESTS" => $requests);

				# Cerrar la conexion a base de datos
				$connection -> close();
				# Regresar respuesta
				return $response;
			}
			# Return an error message if query failed
			else {
				return array("MESSAGE" => "500");
			}
		}
		# Return an error message if connection is null
		else {
			return array("MESSAGE" => "500");
		}
	}

	# REWRITE
	# Get profile information from a user
	function fetchProfile($currentUID) {
		# Obtain null or actual reference of connection
		$connection = databaseConnection();

		# If connection exists...
		if($connection != null) {

			# Query forumlation
			$sql = "SELECT fName, lName, username, email, 	  gender, country
					FROM Users
					WHERE ID = '$currentUID'";

			# Query execution
			$queryResult = $connection -> query($sql);

			# If information was fetch successfully...
			if ($queryResult -> num_rows > 0) {

				# Fetching single result
				$row = $queryResult -> fetch_assoc();
				$response = array("MESSAGE" => "SUCCESS",
								  "fName" => $row["fName"],
							  	  "lName" => $row["lName"],
							  	  "username" => $row["username"],
							  	  "email" => $row["email"],
							  	  "gender" => $row["gender"],
							  	  "country" => $row["country"]);
				
				# Cerrar la conexion a base de datos
				$connection -> close();
				# Regresar respuesta
				return $response;
			}
			# Return an error message if query failed
			else {
				return array("MESSAGE" => "500");
			}
		}
		# Return an error message if connection is null
		else {
			return array("MESSAGE" => "500");
		}
	}

	# Get user's id in the database
	function getUserID($uName) {
		# Obtain null or actual reference of connection
		$connection = databaseConnection();

		# If connection exists...
		if($connection != null) {

			# Query forumlation
			$sql = "SELECT ID
					FROM Users
					WHERE username = '$uName'";

			# Query execution
			$userId = $connection -> query($sql);

			# If user is created successfully...
			if ($userId -> num_rows > 0) {

				# Fetching single result
				$row = $userId -> fetch_assoc();
				$response = array("MESSAGE" => "SUCCESS",
								  "UID" => $row["ID"]);
				
				# Cerrar la conexion a base de datos
				$connection -> close();
				# Regresar respuesta
				return $response;
			}
			# Return an error message if database query
			else {
				return array("MESSAGE" => "500");
			}
		}
		# Return an error message if connection is null
		else {
			return array("MESSAGE" => "500");
		}
	}

	# Verify that a user exists
	function verifyUserExistence($uName) {
		# Obtain null or actual reference of connection
		$connection = databaseConnection();

		# If connection exists...
		if($connection != null) {

			# Query forumlation
			$sql = "SELECT username
					FROM Users
					WHERE username = '$uName'";

			# Query execution
			$result = $connection -> query($sql);

			# If user is created successfully...
			if ($result -> num_rows == 0) {

				$response = array("MESSAGE" => "NO");
				
				# Cerrar la conexion a base de datos
				$connection -> close();
				# Regresar respuesta
				return $response;
			}
			# Return an error message if database insertion failed
			else {
				return array("MESSAGE" => "409");
			}
		}
		# Return an error message if connection is null
		else {
			return array("MESSAGE" => "500");
		}
	}

	# REWRITE
	# Attempt to remove a request from the database
	function removeRequest($sender_ID, $response_ID) {
		# Obtain null or actual reference of connection
		$connection = databaseConnection();

		# If connection exists...
		if($connection != null) {

			# Query forumlation
			$sql = "DELETE FROM Requests
					WHERE $sender_ID = sender_ID
					AND $response_ID = response_ID";

			# Query execution
			$requestRemoval = $connection -> query($sql);

			# If request was removed successfully...
			if ($requestRemoval == true) {

				$response = array("MESSAGE" => "SUCCESS");
				
				# Cerrar la conexion a base de datos
				$connection -> close();
				# Regresar respuesta
				return $response;
			}
			# Return an error message if database deletion failed
			else {
				return array("MESSAGE" => "500");
			}
		}
		# Return an error message if connection is null
		else {
			return array("MESSAGE" => "500");
		}
	}

	# REWRITE
	# Attempt to search for new friends
	function searchFriends($currentUID, $nameOrEmail) {
		# Obtain null or actual reference of connection
		$connection = databaseConnection();

		# If connection exists...
		if($connection != null) {

			# Query forumlation to find friends who have already sent you a request
			$sql1 = "SELECT fName, lName, username, email, R.ID
					FROM Users AS U INNER JOIN REQUESTS AS R
					ON (U.ID = R.sender_ID AND R.response_ID = $currentUID)
					WHERE U.ID NOT IN (SELECT F.user1_ID
					                   FROM Friends AS F
					                   WHERE F.user2_ID = $currentUID
					                   UNION
					                   SELECT F.user2_ID
					                   FROM Friends AS F
					                   WHERE F.user1_ID = $currentUID)
					AND (username REGEXP '^$nameOrEmail'
						 OR
						 email REGEXP '^$nameOrEmail')
					ORDER BY fName";

			# Query forumlation to find friends who didn't send you a request
			$sql2 = "SELECT fName, lName, username, email, R.ID
					FROM Users AS U LEFT JOIN REQUESTS AS R
					ON ($currentUID = R.sender_ID AND U.ID = R.response_ID)
					WHERE U.ID NOT IN (SELECT F.user1_ID
					                   FROM Friends AS F
					                   WHERE F.user2_ID = $currentUID
					                   UNION
					                   SELECT F.user2_ID
					                   FROM Friends AS F
					                   WHERE F.user1_ID = $currentUID
					                   UNION
					                   SELECT R.sender_ID
					                   FROM Requests AS R
					                   WHERE R.response_ID = $currentUID)
					AND (username REGEXP '^$nameOrEmail'
						 OR
						 email REGEXP '^$nameOrEmail')
					AND U.ID != $currentUID
					ORDER BY fName";

			# Queries execution
			$query1Result = $connection -> query($sql1);
			$query2Result = $connection -> query($sql2);

			$friends = array();

			# If information was fetch successfully...
			if ($query1Result != null && $query2Result != null) {
				
				if ($query1Result -> num_rows > 0) {
					# Fetching various friends if any
					while ($row = $query1Result -> fetch_assoc()) {
						$friends[] = array("fName" => $row["fName"],
				 						   "lName" => $row["lName"],
  				 						   "username" => $row["username"],
				 						   "email" => $row["email"],
				 						   "request" => "answer");
					}
				}

				if ($query2Result -> num_rows > 0) {

					# Fetching various friends if any
					while ($row = $query2Result -> fetch_assoc()) {
						if($row["ID"] != "") {
							$friends[] = array("fName" => $row["fName"],
				 							   "lName" => $row["lName"],
  				 						       "username" => $row["username"],
				 						       "email" => $row["email"],
				 						       "request" => "sent");
						}
						else {
							$friends[] = array("fName" => $row["fName"],
				 							   "lName" => $row["lName"],
  				 						       "username" => $row["username"],
				 						       "email" => $row["email"],
				 						       "request" => "add");
						}
					}
				}

				$response = array("MESSAGE" => "SUCCESS",
								  "FRIENDS" => $friends);

				# Cerrar la conexion a base de datos
				$connection -> close();
				# Regresar respuesta
				return $response;
			}
			# Return an error message if query failed
			else {
				return array("MESSAGE" => "500");
			}
		}
		# Return an error message if connection is null
		else {
			return array("MESSAGE" => "500");
		}
	}

?>