<?php
	
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

	# Attempt to create an ocean
	function attemptCreateOcean($content) {
		# Obtain null or actual reference of connection
		$connection = databaseConnection();

		# If connection exists...
		if($connection != null) {

			# Query forumlation
			$sql = "INSERT INTO Oceans(summary)
					VALUES ('$content')";

			# Query execution
			$oceanCreated = $connection -> query($sql);

			# If user is created successfully...
			if ($oceanCreated === true) {

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

	# Attempt to create new tags
	function attemptCreateTags($tags) {
		# Obtain null or actual reference of connection
		$connection = databaseConnection();

		# If connection exists...
		if($connection != null) {

			# Query forumlation
			$sql = "INSERT INTO Tags(tag) VALUES ";
			
			$first = true;
			foreach($tags as &$tag){
				if($first) {
					$sql .= "('$tag')";
					$first = false;
				}
				else {
					$sql .= ", ('$tag')";
				}
			}
			$sql .= ";";

			# Query execution
			$tagsCreated = $connection -> query($sql);

			# If user is created successfully...
			if ($tagsCreated === true) {

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

	# Bookmark an ocean
	function bookmarkOcean($user_ID, $ocean_ID) {
		# Obtain null or actual reference of connection
		$connection = databaseConnection();

		# If connection exists...
		if($connection != null) {

			# Query forumlation
			$sql = "INSERT INTO Bookmarks(user_ID, ocean_ID)
					VALUES ('$user_ID', '$ocean_ID');";

			# Query execution
			$result = $connection -> query($sql);

			# If user is created successfully...
			if ($result === true) {

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

	# Unbookmark an ocean
	function unbookmarkOcean($user_ID, $ocean_ID) {
		# Obtain null or actual reference of connection
		$connection = databaseConnection();

		# If connection exists...
		if($connection != null) {

			# Query forumlation
			$sql = "DELETE FROM Bookmarks
					WHERE user_ID = $user_ID AND ocean_ID = $ocean_ID";

			# Query execution
			$result = $connection -> query($sql);

			# If user is created successfully...
			if ($result === true) {

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

	# Unbookmark an ocean
	function unpaintOcean($user_ID, $ocean_ID) {
		# Obtain null or actual reference of connection
		$connection = databaseConnection();

		# If connection exists...
		if($connection != null) {

			# Query forumlation
			$sql = "DELETE FROM Painted
					WHERE user_ID = $user_ID AND ocean_ID = $ocean_ID";

			# Query execution
			$result = $connection -> query($sql);

			# If user is created successfully...
			if ($result === true) {

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

	# Attempt to fetch oceans
	function fetchOceans($currentUID, $section, $tags, $order) {
		# Obtain null or actual reference of connection
		$connection = databaseConnection();

		# If connection exists...
		if($connection != null) {

			# Query forumlation
			$sql = "SELECT O.ID AS id, summary, timetag, COUNT(O.ID) AS paintNum
			FROM Oceans AS O INNER JOIN Painted As P
			ON O.ID = P.ocean_ID
			GROUP BY O.ID
			ORDER BY $order DESC";

			# Query execution
			$queryResult = $connection -> query($sql);

			$initialOceans = array();

			# If information was fetch successfully...
			if ($queryResult != null) {
				if ($queryResult -> num_rows > 0) {

					# Fetching various oceans if any
					while ($row = $queryResult -> fetch_assoc()) {
						$initialOceans[] = array("id" => $row["id"],
							"summary" => $row["summary"],
							"timetag" => $row["timetag"],
							"paintNum" => $row["paintNum"]);
					}
				}

				# Add the related tags
				foreach ($initialOceans as &$ocean) {

					$tagsArray = array();
					$temp = $ocean["id"];
					# Query forumlation
					$tagsSql = "SELECT tag
					FROM Oceans AS O INNER JOIN TagsToOceans AS TaTO INNER JOIN Tags As T
					ON O.ID = TaTO.ocean_ID AND TaTO.tag_ID = T.ID
					WHERE O.ID = $temp;";

					# Query execution
					$tagsResult = $connection -> query($tagsSql);

					if ($tagsResult != null) {
						if ($tagsResult -> num_rows > 0) {
							# Fetching various oceans if any
							while ($row = $tagsResult -> fetch_assoc()) {
								$tagsArray[] = $row["tag"];
							}
						}
					}

					$ocean["tags"] = $tagsArray;
				}

				if(!empty($tags)) {
					# Filter by searched tags
					$filteredOceans = $initialOceans;

					foreach ($tags as &$tagItem) {
						$thisTagOceans = $filteredOceans;
						$filteredOceans = array();
						foreach ($thisTagOceans as &$ocean) {
							if (in_array($tagItem, $ocean["tags"])) {
								echo $ocean["tags"];
								$filteredOceans[] = $ocean;
							}
						}
					}

					$initialOceans = $filteredOceans;
				}

				# Add Bookmarked
				foreach ($initialOceans as &$ocean) {

					# Query forumlation
					$temp = $ocean["id"];

					$bookSql = "SELECT B.user_ID
					FROM Oceans AS O INNER JOIN Bookmarks AS B
					ON O.ID = B.ocean_ID
					WHERE B.user_ID = $currentUID
					AND $temp = O.ID";

					# Query execution
					$bookResult = $connection -> query($bookSql);

					if ($bookResult != null) {
						if ($bookResult -> num_rows > 0) {
							$ocean["bookmarked"] = "true";
						}
						else {
							$ocean["bookmarked"] = "false";
						}
					}
				}

				# Add Painted
				foreach ($initialOceans as &$ocean) {

					# Query forumlation
					$temp = $ocean["id"];
					$paintSql = "SELECT P.user_ID
					FROM Oceans AS O INNER JOIN Painted AS P
					ON O.ID = P.ocean_ID
					WHERE P.user_ID = $currentUID
					AND $temp = O.ID";

					# Query execution
					$paintResult = $connection -> query($paintSql);

					if ($paintResult != null) {
						if ($paintResult -> num_rows > 0) {
							$ocean["painted"] = "true";
						}
						else {
							$ocean["painted"] = "false";
						}
					}
				}

				# Num messages
				foreach ($initialOceans as &$ocean) {

					# Query forumlation
					$temp = $ocean["id"];

					$msgSql = "SELECT COUNT(O.ID) AS num
							   FROM Oceans AS O INNER JOIN Messages AS Mes
							   ON O.ID = Mes.ocean_ID
							   WHERE $temp = O.ID;";

					# Query execution
					$msgResult = $connection -> query($msgSql);

					if ($msgResult != null) {
						if ($msgResult -> num_rows > 0) {
							$ocean["numMessages"] = "0";
						}
						else {
							$ocean["numMessages"] = "0";
						}
					}
					else {
						$ocean["numMessages"] = "0";
					}
				}

				$finalOceans = array();

				if($section == "Bookmarked") {
					foreach ($initialOceans as &$ocean) {
						if($ocean["bookmarked"] == "true") {
							$finalOceans[] = $ocean;
						}
					}
				}
				else if($section == "Painted") {
					foreach ($initialOceans as &$ocean) {
						if($ocean["painted"] == "true") {
							$finalOceans[] = $ocean;
						}
					}
				}
				else {
					$finalOceans = $initialOceans;
				}

				$response = array("MESSAGE" => "SUCCESS",
					"oceans" => $finalOceans);

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

	# Get profile information from a user
	function fetchProfile($currentUID) {
		# Obtain null or actual reference of connection
		$connection = databaseConnection();

		# If connection exists...
		if($connection != null) {

			# Query forumlation
			$sql = "SELECT fName, lName, username, email
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
							  	  "email" => $row["email"]);
				
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

	# Get the ID of the last ocean created
	function getLastOceanID() {
		# Obtain null or actual reference of connection
		$connection = databaseConnection();

		# If connection exists...
		if($connection != null) {

			# Query forumlation
			$sql = "SELECT ID
					FROM Oceans
					ORDER BY ID DESC LIMIT 1";

			# Query execution
			$result = $connection -> query($sql);

			# If query was successful
			if ($result -> num_rows > 0) {

				# Fetching single result
				$row = $result -> fetch_assoc();
				$response = array("MESSAGE" => "SUCCESS",
								  "ID" => $row["ID"]);
				
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

	# Get tag ID
	function getTagID($tag) {
		# Obtain null or actual reference of connection
		$connection = databaseConnection();

		# If connection exists...
		if($connection != null) {

			# Query forumlation
			$sql = "SELECT ID
					FROM Tags
					WHERE tag = '$tag'";

			# Query execution
			$result = $connection -> query($sql);

			# If tag is created successfully...
			if ($result -> num_rows > 0) {

				# Fetching single result
				$row = $result -> fetch_assoc();
				$response = array("MESSAGE" => "SUCCESS",
								  "ID" => $row["ID"]);
				
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

	# Paint an ocean
	function paintOcean($user_ID, $ocean_ID) {
		# Obtain null or actual reference of connection
		$connection = databaseConnection();

		# If connection exists...
		if($connection != null) {

			# Query forumlation
			$sql = "INSERT INTO Painted(user_ID, ocean_ID)
					VALUES ('$user_ID', '$ocean_ID');";

			# Query execution
			$result = $connection -> query($sql);

			# If user is created successfully...
			if ($result === true) {

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

	# Relate tag to ocean
	function relateTagToOcean($tag_ID, $ocean_ID) {
		# Obtain null or actual reference of connection
		$connection = databaseConnection();

		# If connection exists...
		if($connection != null) {

			# Query forumlation
			$sql = "INSERT INTO TagsToOceans(tag_ID, ocean_ID)
					VALUES ('$tag_ID', '$ocean_ID');";

			# Query execution
			$result = $connection -> query($sql);

			# If user is created successfully...
			if ($result === true) {

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

	# Verify that a tag exists
	function verifyTagExistence($tag) {
		# Obtain null or actual reference of connection
		$connection = databaseConnection();

		# If connection exists...
		if($connection != null) {

			# Query forumlation
			$sql = "SELECT tag
					FROM Tags
					WHERE tag = '$tag'";

			# Query execution
			$result = $connection -> query($sql);

			# If tag wasn't found...
			if ($result -> num_rows == 0) {

				# Return no as an answer
				$response = array("MESSAGE" => "NO");
				
				# Cerrar la conexion a base de datos
				$connection -> close();
				# Regresar respuesta
				return $response;
			}
			# Return yes if tag already existed
			else {
				$response = array("MESSAGE" => "YES");
				
				# Cerrar la conexion a base de datos
				$connection -> close();
				# Regresar respuesta
				return $response;
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

			# If user didn't exist...
			if ($result -> num_rows == 0) {

				# Return no as an answer
				$response = array("MESSAGE" => "NO");
				
				# Cerrar la conexion a base de datos
				$connection -> close();
				# Regresar respuesta
				return $response;
			}
			# Return yes if user already existed
			else {
				$response = array("MESSAGE" => "YES");
				
				# Cerrar la conexion a base de datos
				$connection -> close();
				# Regresar respuesta
				return $response;
			}
		}
		# Return an error message if connection is null
		else {
			return array("MESSAGE" => "500");
		}
	}

?>