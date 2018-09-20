<?php

session_start();

$username = "";
$email = "";
$errors = array();  //Created to store error messages in an array

$db = mysqli_connect("localhost", "guest", "", "forum");

if (isset($_POST['login_user'])) {
  $username = mysqli_real_escape_string($db, $_POST['username']);
  $password_1 = mysqli_real_escape_string($db, $_POST['password']);

  if (empty($username)) {
    array_push($errors, "Username is required");
  }
  if (empty($password_1)) {
    array_push($errors, "Password is required");
  }

  if (count($errors) == 0) {
   
    $getpass = "SELECT uPassword FROM uUser WHERE uUsername='$username'";
    $querypass = mysqli_query($db, $getpass);



    $i = 0;
    while ($dbPASS = mysqli_fetch_array($querypass)) {
      $string .= $dbPASS[$i];
    }
    /*
    $options = [
      'cost' => 15,
      'salt' => random_bytes(64),
    ];
    */
    
    /*
    // $salt = $salted['salt'];
    //$password = password_hash($password_1, PASSWORD_BCRYPT, $salted);

    /*
    if (password_verify('$password_1, $password')) {
    }
    */
    /*
    $password = password_verify($password_1, $salted)
    $query = "SELECT * FROM uUser WHERE uUsername='$username' AND uPassword='$password_1'";
    $results = mysqli_query($db, $query);
    if (mysqli_num_rows($results) == 1) {

      */
      if (password_verify($password_1, $string)) {
        $_SESSION['username'] = $username;
        $_SESSION['success'] = "You are now logged in";
        header('location: index.php');
      }
    else {
      array_push($errors , "Wrong username/password combination");
    }
  }
}


?>