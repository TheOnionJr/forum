<?php

session_start();

$username = "";
$email = "";
$errors = array();  //Created to store error messages in an array

$db = mysqli_connect("localhost", "guest", "", "forum"); //Connect to database


if (isset($_POST['reg_user'])) {
	// Filter_sanitize - http://php.net/manual/en/filter.filters.sanitize.php	
	
	$username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);			//Strip tags
	$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);				//Remove all characters except letters, digits and !#$%&'*+-=?^_`{|}~@.[].
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {							//Check if its a valid email (e.g., against the syntax in RFC 822)
		array_push($errors, "Not a valid email!");								// More info on validate filter http://php.net/manual/en/filter.filters.validate.php
	}
	$password_1 = filter_var($_POST['password_1'], FILTER_SANITIZE_STRING);		//Strip tags
	$password_2 = filter_var($_POST['password_2'], FILTER_SANITIZE_STRING);		//Strip tags

	/*
	$username = mysqli_real_escape_string($db, $_POST['username']);				
	$email = mysqli_real_escape_string($db, $_POST['email']);
	$password_1 = mysqli_real_escape_string($db, $_POST['password_1']);
	$password_2 = mysqli_real_escape_string($db, $_POST['password_2']);
	*/

	//Check if the inputs are empty 
	if (empty($username)) { array_push($errors, "Username is required"); }
	if (empty($email)) { array_push($errors, "Email is required"); }
	if (empty($password_1)) { array_push($errors, "Password is required"); }
	if ($password_1 != $password_2) {
		array_push($errors, "The two passwords do not match");
	}


	$stmt = $db->prepare("SELECT * FROM uUser WHERE uUsername=? OR uEmail=? LIMIT 1");	//Prepare statement
    $stmt->bind_param("ss", $username, $email);											//Bind parameters
    $result = $stmt->execute();															//Executes
    $result = $stmt->get_result();														//Store the result
    $user = mysqli_fetch_assoc($result);												//Fetch the result 
	
	//Check if the username OR email already exist
	if (mysqli_num_rows($result)!=0) {													//If the query returned any rows
		if ($user['uUsername'] === $username) {
			array_push($errors, "Username already exists");
		}
		if ($user['uEmail'] === $email) {
      		array_push($errors, "Email already exists");
    	}
	}
	$stmt->close();																		//Close the connection


	if (count($errors) == 0) {													//If there were no errors
		$options = [											
    		'cost' => 15,
    		'salt' => random_bytes(64),
    	];
    	$salt = $options['salt'];
		$password = password_hash($password_1, PASSWORD_BCRYPT, $options);									//Creates a hashed password with salt
																											//Dont think it is necessary to add the salt to the database as it is not used
		$stmt = $db->prepare("INSERT INTO uUser (uUsername, uEmail, uPassword, uSalt) VALUES(?, ?, ?)");	//Prepeare statement
		$stmt->bind_param("ssss", $username, $email, $password);											//Bind parameters
		$stmt->execute();																					//Execute
		$stmt->close();																						//Close

		$_SESSION['username'] = $username;										//Logs the new registered user inn
	  	$_SESSION['success'] = "You are now logged in";
	  	header('location: /index.php');										//Returns to front page
	}
}
mysqli_close($db);
?>