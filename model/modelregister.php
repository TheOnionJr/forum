<?php

session_start();

$username = "";
$email = "";
$errors = array();

$db = mysqli_connect("localhost", "guest", "", "forum");

if (mysqli_connect_errno()){
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

if (isset($_POST['reg_user'])) {
	$username = mysqli_real_escape_string($db, $_POST['username']);
	$email = mysqli_real_escape_string($db, $_POST['email']);
	$password_1 = mysqli_real_escape_string($db, $_POST['password_1']);
	$password_2 = mysqli_real_escape_string($db, $_POST['password_2']);

	if (empty($username)) { array_push($errors, "Username is required"); }
	if (empty($email)) { array_push($errors, "Email is required"); }
	if (empty($password_1)) { array_push($errors, "Password is required"); }
	if ($password_1 != $password_2) {
		array_push($errors, "The two password do not match");
	}

	$user_check_query = "SELECT * FROM uuser WHERE uUsername='$username' LIMIT 1";
	$result = mysqli_query($db, $user_check_query);
	$user = mysqli_fetch_assoc($result);

	if ($user) { 
		array_push($errors, "Username already exists");
	}

	if (count($errors) == 0) {
		$query = "INSERT INTO uuser (uEmail, uUsername, uPassword)
				  VALUES('$username', '$email', '$password_1')";
		mysqli_query($db, $query);
		$_SESSION['username'] = $username;
		$_SESSION['success'] = "Your are now logged in";
		header('location: ../index.php');
	}
}

?>