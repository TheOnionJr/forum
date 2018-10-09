<?php

$path = $_SERVER['DOCUMENT_ROOT']; 					//Find the document root
$path .= "/functions/postFunctions.php"; 			//Set absolute path
include($path);



$errorsthread = array();

if (isset($_POST['new_thread'])) {
	if (isset($_SESSION['username'])) {
		$content = true;
		$con=mysqli_connect("localhost","guest","","forum");
		$tID= $_GET['tID'];									//Get topic ID
		$sID= $_GET['sID'];									//Get subforum ID

		$text=htmlentities($_POST['text'], ENT_QUOTES, 'UTF-8');
		$username= filter_var($_SESSION['username'], FILTER_SANITIZE_STRING);
		$title=filter_var($_POST['title'], FILTER_SANITIZE_STRING);

		if (empty($text)) {                                 //Check if some post content was entered
			array_push($errorsthread, "Some content is required, please fill out the text box");
		}
		if (empty($title)) {                                                 	//Check if a title was entered
			array_push($errorsthread, "A title is required");
		}

	  	$stmt = $con->prepare("SELECT thName FROM threads WHERE thName = ? AND thTopicID = ?");		//Prepare statement 
		$stmt->bind_param('si', $title, $tID);															//Bind parameters
		$result = $stmt->execute();																//executes
		$result = $stmt->get_result();															//Get result
		$user = mysqli_fetch_assoc($result);													//Fetch result


		//Check if title exists
		if (mysqli_num_rows($result)!=0) {													//If the query returned any rows
				if ($user['thName'] === $title) {
					array_push($errorsthread, "A thread with that title already exists in this Topic. Please choose a different title");
				}
		}
		$stmt->close();

		$thID=NULL;
		$replyTo=NULL;
		if(count($errorsthread) == 0 ) {										//If there were no errors
			if(canPost($username, $thID, $tID, $sID, $replyTo)) {

				//IF YOU GET "Call to a member function bind_param() on boolean" THEN PLEASE UPDATE THE REQUESTS FOR USER (look DROP *)

				$stmt = $con->prepare("INSERT INTO threads (thName, thNumPosts, thAuthor, thTopicID) VALUES(?, ?, ?, ?)");	//Prepeare statement
				if ($content) {																								//If content for post exists
					$first = 1;	
				}
				else {
					$first = NULL;
				}

				$stmt->bind_param("sisi", $title, $first, $username, $tID);													//Bind parameters
				$stmt->execute();																							//Execute
				$stmt->close();
				
				if ($content) {
					$stmt = $con->prepare("SELECT thID FROM threads WHERE thName = ? AND thTopicID = ?");		//Get thID from the newly inserted thread
					$stmt->bind_param('si', $title, $tID);														//Bind parameters
					$stmt->execute();																			//Execute
					$result = $stmt->get_result();																//Get result

					$i = 0;
					$thread = NULL;
					while ($dbPASS = mysqli_fetch_array($result)) {				//only works if the thread names are UNIQUE
							$thread .= $dbPASS[$i];								//Stores the returned threadID
					}

					$stmt= $con->prepare("INSERT INTO posts (pName, pContent, pAuthor, pThreadID) VALUES(?, ?, ?, ?)");		//Prepares to insert into posts
					$stmt->bind_param('sssi', $title, $text, $username, $thread);											//Binds params
					$stmt->execute();																						//Executes query
					$stmt->close();
				}
				header("Location: /view/topicview.php?tID=".$tID."&sID=".$sID);											//Returnes to the topic
			}
			else {
				array_push($errorsthread, "Error please contact the administrators");
			}
		}
	} else { 
		array_push($errorsthread, "You need to login in order to post");
	}
}

?>