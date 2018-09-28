<?php 
$con=mysqli_connect("localhost","guest","","forum");						//Establish connection, needs to be changed to 'User'
if (mysqli_connect_errno()){												//If connection fails
	echo "Failed to connect to MySQL: " . mysqli_connect_error();			//Post error message
}
$username=htmlentities($_SESSION['username'], ENT_QUOTES, 'UTF-8');			//Get username from session
$thread=htmlentities($_GET['thID'], ENT_QUOTES, 'UTF-8');					//Get thread ID
$topic=htmlentities($_GET['tID'], ENT_QUOTES, 'UTF-8');						//Get topic ID
$subforum=htmlentities($_GET['sID'], ENT_QUOTES, 'UTF-8');					//Get subforum ID
$text=htmlentities($_POST['content'], ENT_QUOTES, 'UTF-8');					//Get content from postview
$replyTo=htmlentities($_POST['replyTo'], ENT_QUOTES, 'UTF-8');				//Get id of post that is being replied to, NULL if not a reply
$pName=htmlentities($_POST['pName'], ENT_QUOTES, 'UTF-8');
?>
