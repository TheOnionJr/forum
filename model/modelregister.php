<?php

session_start();

$username = "";
$email = "";
$errors = array();  //Created to store error messages in an array

$db = mysqli_connect("localhost", "guest", "", "forum"); //Connect to database


if (isset($_POST['reg_user'])) {
	
	
	$username = mysqli_real_escape_string($db, $_POST['username']);	
	$email = mysqli_real_escape_string($db, $_POST['email']);
	$password_1 = mysqli_real_escape_string($db, $_POST['password_1']);
	$password_2 = mysqli_real_escape_string($db, $_POST['password_2']);



// Filter_sanitize - http://php.net/manual/en/filter.filters.sanitize.php
// Might swap out FILTER_SANITIZE_EMAIL on username and password	
	/*
	 $username = filter_var($_POST['username'], FILTER_SANITIZE_EMAIL);
	if (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
		array_push($errors, "Not a valid username!");
	}
	
	$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		array_push($errors, "Not a valid email!");
	}
	
	$password_1 = filter_var($_POST['password_1'], FILTER_SANITIZE_EMAIL);
	if (!filter_var($password_1, FILTER_VALIDATE_EMAIL)) {
		array_push($errors, "Not a valid password!");
	}
	
	$password_2 = filter_var($_POST['password_2'], FILTER_SANITIZE_EMAIL);
	if (!filter_var($password_2, FILTER_VALIDATE_EMAIL)) {
		array_push($errors, "Not a valid password!");
	}
	*/

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

	//Check if the username OR email alredy exist
	if ($user) { 
		if ($user['username'] === $username) {
			array_push($errors, "Username already exists");
		}
		if ($user['email'] === $email) {
      		array_push($errors, "Email already exists");
    	}	
	}


	if (count($errors) == 0) {
		$options = [
    		'cost' => 15,
    		'salt' => random_bytes(64),
    	];
    	$salt = $options['salt'];
		$password = password_hash($password_1, PASSWORD_BCRYPT, $options);
		$query = "INSERT INTO uUser (uUsername, uEmail, uPassword, uSalt)
				  VALUES('$username', '$email', '$password', '$salt')";
		mysqli_query($db, $query);
		$_SESSION['username'] = $username;
	  	$_SESSION['success'] = "You are now logged in";
	  	header('location: ../index.php');
	}
}

mysqli_close($db);

?>