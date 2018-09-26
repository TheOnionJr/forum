<?php 
$con=mysqli_connect("localhost","guest","","forum");						//Establish connection
if (mysqli_connect_errno()){												//If connection fails
	echo "Failed to connect to MySQL: " . mysqli_connect_error();			//Post error message
}
$username=htmlentities($_SESSION['username'], ENT_QUOTES, 'UTF-8');			//Get username from session
$thread=htmlentities($_GET['thID'], ENT_QUOTES, 'UTF-8');					//Get thread ID
$topic=htmlentities($_GET['tID'], ENT_QUOTES, 'UTF-8');						//Get topic ID
$subforum=htmlentities($_GET['sID'], ENT_QUOTES, 'UTF-8');					//Get subforum ID
$text=htmlentities($_POST['content'], ENT_QUOTES, 'UTF-8');					//Get content from postview
$replyTo=htmlentities($_POST['replyTo'], ENT_QUOTES, 'UTF-8');				//Get id of post that is being replied to, NULL if not a reply
//More to follow... -Sander

//Verifying everything about the post:
function canPost(username, thread, topic, subforum, replyTo, connection) {
	$con=connection;
	$stmt = $con->prepare("SELECT threads.thLock, threads.thMoved, threads.thID, topics.tID, subforums.sID FROM topics INNER JOIN threads ON threads.thTopicID = topics.tID INNER JOIN subforums ON subforums.sID = topics.tSubForumID WHERE thID = ?"); //Fuuuuck me this query
	$stmt->bind_param('i', thread);
	$stmt->execute();
	$idverify = $stmt->get_result();
	$stmt = $con->prepare("SELECT uUsername FROM uuser WHERE uUsername = ?");
	$stmt->bind_param('s', username);
	$stmt->execute();
	$usr = $stmt->get_result();
	if (!mysqli_num_rows($usr) or !mysqli_numrows($idverify)){										//Checks if thread and username even exsist.
		return false;
	}
	if (username != filter_var($_SESSION['username'], FILTER_SANITIZE_STRING)){						//Checks if username parameter is the same as the session username. Prevents spoofing.
		return false;
	}
	if ($idverify['thID'] != thread or $idverify['tID'] != topic or $idverify['sID'] != subforum) {	//Checks that the sendt parameters are correct
		return false;																				//I cut this into two IF test to not go insane
	}
	if ($idverify['thLock'] == 1 or $idverify['thMoved'] == 1){										//Checks if the thread is locked or moved.
		return false;
	}
	if (replyTo != NULL) {																			//Checks if this is a reply
		$stmt = $con->prepare("SELECT pID, pDeleted FROM posts WHERE pID = ?");						//Querys info about original post
		$stmt->bind_param('i', replyTo);															//Binding int parameter
		$stmt->execute();																			//Executing statement
		$op=$stmt->get_result();																	//Storing result
		if (replyTo != $op['pID'] and $op['pDeleted'] != 0) {										//Checks if replying to post that exist and is not deleted
			return false;																			//If not, return false
		}
	}
}

?>
