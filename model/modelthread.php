<?php
	$path = $_SERVER['DOCUMENT_ROOT']; 					//  Find the document root.
	$path .= "/functions/postFunctions.php"; 			//  Set absolute path for functions.
	include($path);
	

	//input validation
	$threadID = filter_input(INPUT_GET, 'thread', FILTER_VALIDATE_INT);
	$topicID = filter_input(INPUT_GET, 'topic', FILTER_VALIDATE_INT);
	$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
	$errorsthread = array();
	
	$con=mysqli_connect("localhost","guest","","forum");
	// Check connection
	if (mysqli_connect_errno()){
		echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}
	$stmt = $con->prepare("SELECT * FROM threads WHERE thID = ?");
	$stmt->bind_param('i', $threadID);
	$stmt->execute();
	$result = $stmt->get_result();
	$stmt->close();
	//If thread id does not exist user error
	if(mysqli_num_rows($result) == 0) {
		echo "This thread does not exist";
	}
	//Else statement not necessary, if $result = 0 -> nothing will be printed
 
	//	Find out if the user should have privileges in this topic
	$privileges = false; 	//	If the current user should have deletion / locking privileges
	$subforumname = htmlentities(mysqli_fetch_array(mysqli_query($con, "SELECT * FROM subforums JOIN topics ON subforums.sID = topics.tSubForumID JOIN threads ON threads.thTopicID = topics.tID WHERE thID = {$threadID}"))['sName'], ENT_QUOTES, 'UTF-8');
	if (isset($_SESSION['username'])) 			// If user is logged in
		if (mysqli_fetch_array(mysqli_query($con, "SELECT COUNT(*) FROM uuser JOIN urole ON uuser.uID = urole.urID WHERE uuser.uUsername = \"{$_SESSION['username']}\" AND ( urType = \"admin\" OR urType = \"mod{$subforumname}\")"))[0])
			$privileges = true;

		
	
	$maxPage = ceil((mysqli_fetch_array(mysqli_query($con, "SELECT COUNT(*) FROM posts WHERE pThreadID = $threadID AND pReplyTo IS NULL"))['COUNT(*)'])/25);	//max amount of pages needed 
	
	if($page > $maxPage || $page < 1)	//if current page is outside legal range
		$page = 1;		//sets current page to default	
	
	paging();	//upper page navigation

	$txID = 0;

	//--------------------------------------------------------------
	//	Load Posts
	//--------------------------------------------------------------
	
	while($row = mysqli_fetch_array($result))
	{
		if($row['thLock'] == NULL) { 

			//	Thread name
			echo "<table>";
			$thID = htmlentities($row['thID'], ENT_QUOTES, 'UTF-8');
			echo "<tr><th>" . htmlentities($row['thName'], ENT_QUOTES, 'UTF-8') . "</th></tr>";

			fetchPosts(NULL);

			//	Load and populate posts except replies
			$posts = mysqli_query($con,"SELECT * FROM posts WHERE pThreadID = $thID AND pReplyTo IS NULL ORDER BY pTimestamp");
			
			$i = 0;		// Int def.
			$j = 0;

			//	New Post Button
			echo "</table><table><tr><td>";
			echo "<b onclick='textbox(" . ($txID+1) . ")'><button>New Post</button></b>";	//	Calls function for post
			$csrf = $_SESSION['csrfTOken'];
			echo '<div id="' . ($txID+1) . '" style="Display:none">		
							<form method="post">
							<textarea id="CBox" name="postContent" type="text" > </textarea>											  
								<button type="submit" name="' . ($txID+1) . '">Submit</button>
							<input type=\'hidden\' name=\'csrfToken\' value=\'' . $csrf . '\' />
							</form>
							<style> 
							form[name=replyform] {
							    display:block;
							    margin:0px;
							    padding:0px;
							}
							</style>
						 </div>';								//  Adds default:hidden textboxes and button after replies.

					
					if (isset($_POST[$txID+1])) {
						if (isset($_SESSION['username'])) {
							 $content = htmlentities($_POST['postContent'], ENT_SUBSTITUTE, 'UTF-8');
							 $content = ltrim($content);											//Removes whitespace from left side of text
							if (!empty($content)){
								$postNm = $row["thName"];
								$rplyTo = null;
								$usrnm = htmlentities($_SESSION['username'], ENT_QUOTES, 'UTF-8');
								$thID = $threadID;
							
								if (post($postNm, $content, $rplyTo, $usrnm, $thID, $con)) {
									echo '<meta http-equiv="refresh" content="0">';
								} else {
									//echo "<p> $con->error </p>";
									array_push($errorsthread, "Could not post.");
									echo "<p>Committa kys</p>";
								}
							}
							else {
								array_push($errorsthread, "Textbox cannot be empty");
							}
						}
						else {
							array_push($errorsthread, "You need to login in order to post");
						}
					}

					?>
						<script>											//  Function for displaying textbox.
							function textbox(ID) {
								var x = document.getElementById(ID);
								if (x.style.display === "none") {
									x.style.display = "block";
								} else {
									x.style.display = "none";
								}
							}
						</script>
					<?php

			echo "</td></tr></table>";
		} else {	//	If the thread is locked, print error
			echo "<font color=red>";
			echo "This thread has been deleted by an admin.";
			echo "</font>";
		}

	}
	$path2 = $_SERVER['DOCUMENT_ROOT'];					//  Find document root.
	$path2 .= "/view/errors.php";						//  Setting absolute path for errors.
	include($path2);

	paging();	//lower page navigation
	
	mysqli_close($con);

	//--------------------------------------------------------------
	//	Functions
	//--------------------------------------------------------------

	//	Fetches posts from the server
	function fetchPosts($replyTo, $indent = 0)
	{
		global $con;
		global $thID;
		//global $rowNum;
		global $i;
		global $privileges;
		global $txID;
		global $subforumname;
		global $page;
		global $maxPage;
		global $errorsthread;
		
		$rowNum = 0;

        if ($replyTo)
            $posts = mysqli_query($con,"SELECT * FROM posts WHERE pThreadID = {$thID} AND pReplyTo = {$replyTo} ORDER BY pTimestamp");
        else
            $posts = mysqli_query($con,"SELECT * FROM posts WHERE pThreadID = {$thID} AND pReplyTo IS NULL ORDER BY pTimestamp");

        if($page > 0 && $page <= $maxPage)
			$posts->data_seek(($page-1)*25);
		else
			$page = 1;

		//	Load posts for the current page
		while(($post_row = mysqli_fetch_array($posts)) && $rowNum <  25 /** $page*/) 
		{
			$rowNum++;
			$pID = $post_row['pID'];
			$i++;		//Integer to keep track of reply-boxes.
			$txID = $i;	
			$delID = "delete" . $i;
			$contID = "content" . $i;
			//if ($rowNum > 25 * ($page-1))
			{
	            $rolecolour = 0;     //    What colour the author's name should be
	            if (mysqli_fetch_array(mysqli_query($con, "SELECT COUNT(*) FROM uuser JOIN urole ON uuser.uID = urole.urID WHERE uuser.uUsername = \"{$post_row['pAuthor']}\" AND urType = \"admin\""))[0])
	                $rolecolour = 1;
	            else if (mysqli_fetch_array(mysqli_query($con, "SELECT COUNT(*) FROM uuser JOIN urole ON uuser.uID = urole.urID WHERE uuser.uUsername = \"{$post_row['pAuthor']}\" AND urType = \"mod{$subforumname}\""))[0])
	                $rolecolour = 2;
	            else if (mysqli_fetch_array(mysqli_query($con, "SELECT COUNT(*) FROM uuser JOIN urole ON uuser.uID = urole.urID WHERE uuser.uUsername = \"{$post_row['pAuthor']}\""))[0])
	                $rolecolour = 3;


	            $indpx = $indent * 40 . "px";
	            echo "<th><p style=\"text-indent: {$indpx}\">";
	            // Username and Timestamp
	            if ($rolecolour == 1)    //    admin or mod
	                echo '<font color="gold">';        //    gold for admins
	            else if ($rolecolour == 2) 
	                echo '<font color="purple">';    //    purple for local moderators
	            else if ($rolecolour == 3) 
	                echo '<font color="blue">';        //    blue for other moderators

	            echo htmlentities($post_row['pAuthor'], ENT_QUOTES, 'UTF-8');
	            if ($rolecolour != 0)    //    admin or mod
	                echo '</font>';

				echo " | " . htmlentities($post_row['pTimestamp'], ENT_QUOTES, 'UTF-8') . "</p></th>";



				//	Post content
				echo "<tr>";
				
				if ($post_row['pDeleted'])	//	Deleted post
					echo "<td><p style=\"text-indent: {$indpx}\">" . '<font color="red">This post was deleted by ' . htmlentities($post_row['pDeletedBy'], ENT_QUOTES, 'UTF-8') . ".</font></p></td>";
				else						//	Post content
					echo "<td name='".$contID."'><p style=\"text-indent: {$indpx}\">" . htmlentities($post_row['pContent'], ENT_QUOTES, 'UTF-8') . "</font></p></td>";
				
				echo "</tr>";



				//	Reply, Delete functions
				if (!$post_row['pDeleted'])
				{
					//	Reply function
					echo "<tr><p style=\"text-indent: {$indpx}\"><td name='".$contID."'>";
					echo "<div class='some' style=\"text-indent: {$indpx}\">";
					echo "<b onclick='textbox($txID)'><button >Reply</button></b>";		//	Calls function for post on click.
					//$author = $_GET['pAuthor'];
					$author = $post_row['pAuthor'];


					//	Delete function
					if (isset($_SESSION['username'])) { 									// If user is logged in
						if ($_SESSION['username'] === $author || $privileges) {					// If user = to the author
							$csrf = $_SESSION['csrfTOken'];
							echo " <form method='post' name='deleteform'>
								<button type='submit' name='" . $delID . "'>Delete</button>";
							echo "<input type='hidden' name='csrfToken' value='" . $csrf . "' /> 
								</form>";															//Creates the form and button
							echo "<style type='text/css'>					
									form[name=deleteform] {
								    display:inline;
								    margin:0px;
								    padding:0px;
									}
									</style>";														//Added some css to keep the delete button inline

							if (isset($_POST[$delID])) {
								$name = $post_row['pName'];

								//IF YOU GET "Call to a member function bind_param() on boolean" THEN PLEASE UPDATE THE REQUESTS FOR USER (look DROP *)

								$stmt = $con->prepare("UPDATE posts SET pDeleted = 1, pDeletedBy = ? WHERE pName = ? AND pThreadID = ? AND pID = ?");
								$stmt->bind_param('ssii', $_SESSION['username'], $name, $thID, $pID);
								//echo $con->error;
								$stmt->execute();
								$stmt->close();
								echo '<meta http-equiv="refresh" content="0">';	//Refreshes the page
							}
						}
					}
					echo "</div>";


					echo '<div id="' . $txID . '" style="Display:none">		
							<form method="post">
							<textarea id="CBox" name="postContent" type="text" > </textarea>											  
								<button type="submit" name="' . $txID . '">Submit</button>
							<input type="hidden" name="csrfToken" value="<?php echo($_SESSION[\'csrfTOken\']) ?>" />
							</form>
							<style> 
							form[name=replyform] {
							    display:block;
							    margin:0px;
							    padding:0px;
							}
							</style>
						 </div>';																		//  Adds default:hidden textboxes and button after replies.
					echo "</td></p></tr>";

					
					if (isset($_POST[$txID])) {															// Gets called when reply-button is pressed.
						if (isset($_SESSION['username'])) {													// Checks if user is logged in.
							$rplyContent = htmlentities($_POST['postContent'], ENT_SUBSTITUTE, 'UTF-8');		// Retrieves and sanitizes content from reply-textbox.
							$rplyContent = ltrim($rplyContent);													// Removes whitespace from left side of text
							if (!empty($rplyContent)){																// Checks if the reply actually contains content.
								$postNm = $post_row["pName"];														// Gets post name.
								$rplyTo = $pID;																		// Gets post-ID.
								$rplyUsrnm = htmlentities($_SESSION['username'], ENT_QUOTES, 'UTF-8');				// Gets current users username.
								$rplyThID = $thID;																	// Gets thread-ID.
							
								if (post($postNm, $rplyContent, $rplyTo, $rplyUsrnm, $rplyThID, $con)) {		// If post is succsessful.
									echo '<meta http-equiv="refresh" content="0">';									// Refresh page. (To display updates)
								} else {																		// Post unsuccessful.
									array_push($errorsthread, "Could not post reply.");								// Display error.
								}
							} else {																			// Reply contains no content.
								array_push($errorsthread, "Textbox cannot be empty");								// Display error.
							}
						}
						else {																					// User is not logged in.
							array_push($errorsthread, "Please login in order to reply to a post");					//Display error.
						}
					}
					?>
						<script>				//  Function for displaying textbox.
							function textbox(ID) {
								var x = document.getElementById(ID);
								if (x.style.display === "none") {
									x.style.display = "block";
								} else {
									x.style.display = "none";
								}
							}
						</script>
					<?php
				}
			}
			fetchPosts($pID, $indent + 1);
		}
	}
	
	function paging()		//Sets up the paging
	{                       
		global $page;       //Current page
		global $maxPage;    //Maximum pages needed
		echo "<p>";         //Start of paging 

		if($page > 5)		//If more than 4 pages before current page
			echo "<a href=\"/view/threadview.php?topic=" . htmlentities($topicID) . "&thread=" . htmlentities($threadID) . "&page=" . htmlentities($page-5, ENT_QUOTES, 'UTF-8') . "\">" . " << " . "</a>";		//Enables skipping back 5 pages
		if($page > 1)		//If current page is not the first page	                                                                                                                                                                            
			echo "<a href=\"/view/threadview.php?topic=" . htmlentities($topicID) . "&thread=" . htmlentities($threadID) . "&page=" . htmlentities($page-1, ENT_QUOTES, 'UTF-8') . "\">" . " < " . "</a>";      //Enables going back one page	

		if($page > 3)		//If more than 2 pages before current page	
			echo "<a href=\"/view/threadview.php?topic=" . htmlentities($topicID) . "&thread=" . htmlentities($threadID) . "&page=1\"> 1 </a>";	//Enable skipping to first page
		if($page > 4)		//If more than 3 pages before current page
			echo " ... ";   //Shows that there is more pages between 

		if($page > 2)		//If there is more than one page before current page
			echo "<a href=\"/view/threadview.php?topic=" . htmlentities($topicID) . "&thread=" . htmlentities($threadID) . "&page=" . htmlentities($page-2, ENT_QUOTES, 'UTF-8') . "\">" . htmlentities($page-2, ENT_QUOTES, 'UTF-8') . " " . "</a>";	//Enables going back two pages
		if($page > 1)		//If there is pages before current page                                                                                                                                                                                         
			echo "<a href=\"/view/threadview.php?topic=" . htmlentities($topicID) . "&thread=" . htmlentities($threadID) . "&page=" . htmlentities($page-1, ENT_QUOTES, 'UTF-8') . "\">" . htmlentities($page-1, ENT_QUOTES, 'UTF-8') . "</a>";         //Enables going back one page

		echo " $page ";		//prints current page

		if($page < $maxPage)	//If not last page
			echo "<a href=\"/view/threadview.php?topic=" . htmlentities($topicID) . "&thread=" . htmlentities($threadID) . "&page=" . htmlentities($page+1, ENT_QUOTES, 'UTF-8') . "\">" . htmlentities($page+1, ENT_QUOTES, 'UTF-8') . " " . "</a>";	//Enables going forwards one page
		if($page < $maxPage-1)	//If more than one page after current                                                                                                                                                                                        
			echo "<a href=\"/view/threadview.php?topic=" . htmlentities($topicID) . "&thread=" . htmlentities($threadID) . "&page=" . htmlentities($page+2, ENT_QUOTES, 'UTF-8') . "\">" . htmlentities($page+2, ENT_QUOTES, 'UTF-8') . "</a>";         //Enable going forwards two pages

		if($page < $maxPage-3)	//If more than 3 pages after current page
			echo " ... ";       //Shows that there is more pages between
		if($page < $maxPage-2)  //If more than 2 pages after current page
			echo "<a href=\"/view/threadview.php?topic=" . htmlentities($topicID) . "&thread=" . htmlentities($threadID) . "&page=" . htmlentities($maxPage, ENT_QUOTES, 'UTF-8') . "\">" . " $maxPage " . "</a>";	//Enable skipping to last page	

		if($page < $maxPage)	//If current page is not the last page
			echo "<a href=\"/view/threadview.php?topic=" . htmlentities($topicID) . "&thread=" . htmlentities($threadID) . "&page=" . htmlentities($page+1, ENT_QUOTES, 'UTF-8') . "\">" . " > " . "</a>";	//Enable going forward one page
		if($page < $maxPage-4)	//If more than 4 pages after current page                                                                                                                                          
			echo "<a href=\"/view/threadview.php?topic=" . htmlentities($topicID) . "&thread=" . htmlentities($threadID) . "&page=" . htmlentities($page+5, ENT_QUOTES, 'UTF-8') . "\">" . " >> " . "</a>"; //Enables skipping forward 5 pages

		echo "</p>";	//paging end
	}
?>