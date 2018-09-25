<?php 
$con=mysqli_connect("localhost","guest","","forum");
$username=filter_var($_SESSION['username'], FILTER_SANITIZE_STRING);
$text=htmlentities($_POST['content'], ENT_QUOTES, 'UTF-8');
$timestamp=CURRENT_TIMESTAMP;
//More to follow... -Sander

function verify(username, subforum, thread, topic) {
	$stmt = $con->prepare("SELECT threads.thID, topics.tID, subforums.sID FROM topics INNER JOIN threads ON threads.thTopicID = topics.tID INNER JOIN subforums ON subforums.sID = topics.tSubForumID WHERE thID = ?"); //Skyt me please
	$stmt->bind_param('i', thread);
	$stmt->execute();
	$idverify = $stmt->get_result();

	$stmt = $con->prepare("SELECT uUsername FROM uuser WHERE uUsername = ?");
	$stmt->bind_param('s', username);
	$stmt->execute();
	$usr = $stmt->get_result();

	if (username != filter_var($_SESSION['username'], FILTER_SANITIZE_STRING)){
		return false;
	}
	
	if (!mysqli_num_rows($usr) or !mysqli_numrows($idverify)){
		return false;
	}
}

?>
