<?php

//session_start();

$username = "";
$email = "";
$errors = array();

$db = mysqli_connect("localhost", "guest", "", "forum");


if (isset($_POST['reg_user'])) {
	
	/*
	$username = mysqli_real_escape_string($db, $_POST['username']);	
	$email = mysqli_real_escape_string($db, $_POST['email']);
	$password_1 = mysqli_real_escape_string($db, $_POST['password_1']);
	$password_2 = mysqli_real_escape_string($db, $_POST['password_2']);
	*/
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

	if (empty($username)) { array_push($errors, "Username is required"); }
	if (empty($email)) { array_push($errors, "Email is required"); }
	if (empty($password_1)) { array_push($errors, "Password is required"); }
	if ($password_1 != $password_2) {
		array_push($errors, "The two password do not match");
	}


	$user_check_query = "SELECT * FROM uUser WHERE uUsername='$username' OR email='$email' LIMIT 1";
	$result = mysqli_query($db, $user_check_query);
	$user = mysqli_fetch_assoc($result);

	if ($user) { 
		if ($user['username'] === $username) {
			array_push($errors, "Username already exists");
		}
		if ($user['email'] === $email) {
      		array_push($errors, "Email already exists");
    	}	
	}


	if (count($errors) == 0) {
		$password = md5($password_1);
		$query = "INSERT INTO uUser (uUsername, uEmail, uPassword)
				  VALUES('$username', '$email', '$password')";
		mysqli_query($db, $query);
		header('location: ../index.php');
	}
}

?>