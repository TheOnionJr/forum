<?php

session_start();                                                            //Starts a session

$username = "";
$email = "";
$errors = array();  //Created to store error messages in an array
$successful = "no";

$db = mysqli_connect("localhost", "guest", "", "forum");


if (isset($_POST['login_user'])) {
  $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);       //Strip tags
  $password_1 = filter_var($_POST['password'], FILTER_SANITIZE_STRING);     //Strip tags

  if (empty($username)) {                                                   //Check if a username was entered
    array_push($errors, "Username is required");
  }
  if (empty($password_1)) {                                                 //Check if a password was entered
    array_push($errors, "Password is required");
  }

  $stmt = $db->prepare("SELECT * FROM uUser WHERE uUsername=?");            //See if the user exists
  $stmt->bind_param("s", $username);
  $stmt->execute();
  $result = $stmt->get_result();

  if(mysqli_num_rows($result) == 0) {                                       //If the user does not exists
    array_push($errors, "Username does not exist");
  }
  $stmt->close();

  if (count($errors) == 0) {                                                //If no errors encountered (e.g., a user entered a username and password)
    $stmt = $db->prepare("SELECT uPassword FROM uUser WHERE uUsername=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    $i = 0;
    $string = NULL;
    while ($dbPASS = mysqli_fetch_array($result)) {                      //Stores the hashed password as a string (it normally returns as an array)
      $string .= $dbPASS[$i];
    }

    $stmt->close();

    if (password_verify($password_1, $string)) {                            //Verifies that the entered password is the hashed password (both parameteres needs to be a string)
      $_SESSION['username'] = $username;                                    //Sets the session username to be the logged in username
      $successful = "yes";
    } 
    else {
      array_push($errors , "Wrong password for $username");            
    }
  }

  if(mysqli_num_rows($result) != 0) {                                                                     //If username exists
    $ip = $_SERVER['REMOTE_ADDR'];                            // This should work aslong as a reverse proxy isn't used https://stackoverflow.com/questions/4773969/is-it-safe-to-trust-serverremote-addr

    $stmt = $db->prepare("INSERT INTO loginAttempts (loginUserName, loginSuccessful, loginIP) VALUES(?,?,?)");        //Inserts into login attempts
    $stmt->bind_param("sss", $username, $successful, $ip);
    $stmt->execute();
    $stmt->close();
  }
  
}

mysqli_close($db);
?>