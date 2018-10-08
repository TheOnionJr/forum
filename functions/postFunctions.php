<?php
//Verifying everything about the post:
function canPost($username, $thread, $topic, $subforum, $replyTo) {
	$con=mysqli_connect("localhost","guest","","forum");
	$stmt = $con->prepare("SELECT threads.thLock, threads.thMoved, threads.thID, topics.tID, subforums.sID FROM topics INNER JOIN threads ON threads.thTopicID = topics.tID INNER JOIN subforums ON subforums.sID = topics.tSubForumID WHERE thID = ?"); 						   		//Fuuuuck me this query
	$stmt->bind_param('i', $thread);																//Binding parameter type
	$stmt->execute();																				//Execute query
	$idverify = $stmt->get_result();																//Store result
	$stmt = $con->prepare("SELECT uUsername FROM uuser WHERE uUsername = ?");						//Select username parameter
	$stmt->bind_param('s', $username);																//Bind parameter type
	$stmt->execute();																				//Execute query
	$usr = $stmt->get_result();																		//Store result
	if (!mysqli_num_rows($usr)) {										//Checks if thread and username even exsist.

		return false;																				//Return can not post
	}
	if ($username != filter_var($_SESSION['username'], FILTER_SANITIZE_STRING)){					//Checks if username parameter is the same as the session username. Prevents spoofing.
		return false;																				//Return can not post
	}
	if ($thread != NULL) {
		if ($idverify['thID'] != $thread) {
			return false;
		}
		if ($idverify['thLock'] == 1 or $idverify['thMoved'] == 1){										//Checks if the thread is locked or moved.
			return false;																				//Return can not post
		}
		if (!mysqli_numrows($idverify)) {
			return false;
		}
		if ($idverify['tID'] != $topic or $idverify['sID'] != $subforum) {								//Checks that the sendt parameters are correct
			return false;																				//Return can not post
		}
	}																								//I cut this into two IF test to not go insane
	if ($replyTo != NULL) {																			//Checks if this is a reply
		$stmt = $con->prepare("SELECT pID, pDeleted FROM posts WHERE pID = ?");						//Querys info about original post
		$stmt->bind_param('i', $replyTo);															//Binding int parameter
		$stmt->execute();																			//Executing statement
		$op=$stmt->get_result();																	//Storing result
		if ($replyTo != $op['pID'] and $op['pDeleted'] != 0) {										//Checks if replying to post that exist and is not deleted
			return false;																			//If not, return false. Can not post
		}
	}
	else{
		return true;
	}
}

function post($postName, $content, $replyTo, $username, $threadID, $con) {
	//if (canPost($username, $content, $topic, $subforum, $replyTo, $con)) {							//Checks that the post can be posted
		$stmt= $con->prepare("INSERT INTO posts (pName, pContent, pReplyTo, pAuthor, pThreadID) VALUES(?, ?, ?, ?, ?)");
		$stmt->bind_param('ssisi', $postName, $content, $replyTo, $username, $threadID);					//Binds params
		$stmt->execute();																			//Executes query
		$stmt->close();																				//Closes statement
		return true;																				//Returns true if completed correctly
	//}
	//else {
	//	return false;																				//Returns false if not completed
	//}
}
?>