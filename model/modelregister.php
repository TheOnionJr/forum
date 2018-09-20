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

	//Check if the inputs are empty 
	if (empty($username)) { array_push($errors, "Username is required"); }
	if (empty($email)) { array_push($errors, "Email is required"); }
	if (empty($password_1)) { array_push($errors, "Password is required"); }
	if ($password_1 != $password_2) {
		array_push($errors, "The two passwords do not match");
	}

	//Run the query
	$user_check_query = "SELECT * FROM uUser WHERE uUsername='$username' OR uEmail='$email' LIMIT 1";
	$result = mysqli_query($db, $user_check_query);
	$user = mysqli_fetch_assoc($result);

	//Check if the username OR email already exist
	if ($user) { 
		if ($user['uUsername'] === $username) {
			array_push($errors, "Username already exists");
		}
		if ($user['uEmail'] === $email) {
      		array_push($errors, "Email already exists");
    	}	
	}

	if (count($errors) == 0) {													//If there were no errors
		$options = [											
    		'cost' => 15,
    		'salt' => random_bytes(64),
    	];
    	$salt = $options['salt'];
		$password = password_hash($password_1, PASSWORD_BCRYPT, $options);		//Creates a hashed password with salt
		$query = "INSERT INTO uUser (uUsername, uEmail, uPassword, uSalt)	
				  VALUES('$username', '$email', '$password', '$salt')";			//Dont think it is necessary to add the salt to the database as it is not used
		mysqli_query($db, $query);
		$_SESSION['username'] = $username;										//Logs the new registered user inn
	  	$_SESSION['success'] = "You are now logged in";
	  	header('location: /index.php');										//Returns to front page
	}
}

mysqli_close($db);

?>