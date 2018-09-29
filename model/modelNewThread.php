<?php
$con=mysqli_connect("localhost","guest","","forum");
$path = $_SERVER['DOCUMENT_ROOT']; 					//Find the document root
$path .= "/functions/postFunctions.php"; 			//Set absolute path
include($path);
if (isset($_POST['new_thread'])) {
	$text=htmlentities($_POST['text'], ENT_QUOTES, 'UTF8');
	$username=filter_var($_POST['username'], FILTER_SANITIZE_STRING);
	$title=filter_var($_POST['title'], FILTER_SANITIZE_STRING);
	$tID=$_GET['tID'];
	$sID=$_GET['sID'];
	$thID=NULL;
	$replyTo=NULL;
	if(canPost($username, $thID, $tID, $sID, $replyTo)) {
		$stmt = $db->prepare("INSERT INTO threads (thName, thNumPosts, thTimestamp, thLock, thAuthor, thTopicID, thMoved) VALUES(?, ?, CURRENT_TIMESTAMP, ?, ?, ?, ?)");	//Prepeare statement
		$stmt->bind_param("siisii", $title, 1, 0, $username, $tID, 0);										//Bind parameters
		$stmt->execute();																			//Execute
		$stmt->close();
		$stmt= $con->prepare("INSERT INTO posts (pName, pContent, replyTo, pAuthor, pThreadID) VALUES(?, ?, ?, ?, ?)");
		$stmt->bind_param('ssisi', $pName, $text, $replyTo, $username, $thread);					//Binds params
		$stmt->execute();																			//Executes query
		$stmt->close();																				//Closes statement
	}
	else {
		echo "k";
	}
}


?>