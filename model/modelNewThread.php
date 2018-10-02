<?php

$path = $_SERVER['DOCUMENT_ROOT']; 					//Find the document root
$path .= "/functions/postFunctions.php"; 			//Set absolute path
include($path);

$errors = array();

if (isset($_POST['new_thread'])) {
	//session_start();
	if (isset($_SESSION['username'])) { 
		$con=mysqli_connect("localhost","guest","","forum");
		$tID= $_GET['tID'];									//Get topic ID
		$sID= $_GET['sID'];									//Get subforum ID

		$text=htmlentities($_POST['text'], ENT_QUOTES, 'UTF-8');
		$username= filter_var($_SESSION['username'], FILTER_SANITIZE_STRING);
		$title=filter_var($_POST['title'], FILTER_SANITIZE_STRING);

		  if (empty($text)) {                                   //Check if some post content was entered
		  														//This might be not needed as a thread doesn't neccesarily need content to begin with
		    array_push($errors, "Some content is required, please fill out the text box");
		  }
		  if (empty($title)) {                                                 	//Check if a title was entered
		    array_push($errors, "A title is required");
		  }

	  	$stmt = $con->prepare("SELECT thName, thTopicID FROM threads WHERE thName = ?");		//Prepare statement 
		$stmt->bind_param('s', $title);															//Bind parameters
		$result = $stmt->execute();																//executes
		$result = $stmt->get_result();															//Get result
		$user = mysqli_fetch_assoc($result);													//Fetch result


		//Check if title exists
		if (mysqli_num_rows($result)!=0) {													//If the query returned any rows
			//if (!$user['thTopicID'] === $tID) {			This is still in development
			//The title should only be UNIQUE for the topic
				if ($user['thName'] === $title) {
					array_push($errors, "A thread with that title already exists. Please choose a different title");
				}
			//}
			
		}
		$stmt->close();

		$thID=NULL;
		$replyTo=NULL;
		if(count($errors) == 0 ) {										//If there were no errors
			if(canPost($username, $thID, $tID, $sID, $replyTo)) {
				$stmt = $con->prepare("INSERT INTO threads (thName, thNumPosts, thAuthor, thTopicID) VALUES(?, ?, ?, ?)");	//Prepeare statement

				$first = 1;			//This has to an if statement if the check for text input is not needed

				$stmt->bind_param("sisi", $title, $first, $username, $tID);													//Bind parameters
				$stmt->execute();																							//Execute
				$stmt->close();

				$stmt = $con->prepare("SELECT thID FROM threads WHERE thName = ?");							//Get thID from the newly inserted thread
				$stmt->bind_param('s', $title);																//Bind parameters
				$stmt->execute();																			//Execute
				$result = $stmt->get_result();																//Get result

				$i = 0;
				$thread = NULL;
				while ($dbPASS = mysqli_fetch_array($result)) {				//only works if the thread names are UNIQUE
						$thread .= $dbPASS[$i];								//Stores the returned threadID
																			//This while loop might be needed to altered if the check for unique thread in specific topic works
				}

				$stmt= $con->prepare("INSERT INTO posts (pName, pContent, pAuthor, pThreadID) VALUES(?, ?, ?, ?)");		//Prepares to insert into posts
				$stmt->bind_param('sssi', $title, $text, $username, $thread);											//Binds params
				$stmt->execute();																						//Executes query
				$stmt->close();
				header("Location: /view/topicview.php?tID=".$tID."&sID=".$sID);											//Returnes to the topic
			}
			else {
				array_push($errors, "Error");
			}
		}
	} else { 
		array_push($errors, "You need to login in order to post");
	}
	
}


?>