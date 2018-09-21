<?php

session_start();                                                            //Starts a session

$username = "";
$email = "";
$errors = array();  //Created to store error messages in an array

$db = mysqli_connect("localhost", "guest", "", "forum");


if (isset($_POST['login_user'])) {
  $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);       //Strip tags
  $password_1 = filter_var($_POST['password'], FILTER_SANITIZE_STRING);     //Strip tags

  //$username = mysqli_real_escape_string($db, $_POST['username']);
  //$password_1 = mysqli_real_escape_string($db, $_POST['password']);

  if (empty($username)) {                                                   //Check if a username was entered
    array_push($errors, "Username is required");
  }
  if (empty($password_1)) {                                                 //Check if a password was entered
    array_push($errors, "Password is required");
  }

  if (count($errors) == 0) {                                                //If no errors encountered (e.g., a user entered a username and password)
    $stmt = $db->prepare("SELECT uPassword FROM uUser WHERE uUsername=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();


    //$getpass = "SELECT uPassword FROM uUser WHERE uUsername='$username'";   //Queries the database for the hashed password
    //$querypass = mysqli_query($db, $getpass);
    
    $i = 0;   
    while ($dbPASS = mysqli_fetch_array($result)) {                      //Stores the hashed password as a string (it normally returns as an array)
      $string .= $dbPASS[$i];
    }
    $stmt->close();
    if (password_verify($password_1, $string)) {                            //Verifies that the entered password is the hashed password (both parameteres needs to be a string)
      $_SESSION['username'] = $username;                                    //Sets the session username to be the logged in username
      $_SESSION['success'] = "You are now logged in";
      header('location: index.php');
    } 
    else {
      array_push($errors , "Wrong username/password combination");            
    }
  }
}

mysqli_close($db);
?>