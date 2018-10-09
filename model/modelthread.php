<?php
	$path = $_SERVER['DOCUMENT_ROOT']; 					//  Find the document root.
	$path .= "/functions/postFunctions.php"; 			//  Set absolute path for functions.
	include($path);
	

	//input validation
	$threadID = filter_input(INPUT_GET, 'thread', FILTER_VALIDATE_INT);
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

	$rowNum = 0;
	
	$maxPage = ceil((mysqli_fetch_array(mysqli_query($con, "SELECT COUNT(*) FROM posts WHERE pThreadID = $threadID"))['COUNT(*)'])/25);
	
	if($page > $maxPage || $page < 1)
		$page = 1;
	
	echo "<p>";

	if($page > 5)
		echo "<a href=\"/view/threadview.php?thread=" . htmlentities($threadID) . "&page=" . htmlentities($page-5, ENT_QUOTES, 'UTF-8') . "\">" . " << " . "</a>";	
	if($page > 1)
		echo "<a href=\"/view/threadview.php?thread=" . htmlentities($threadID) . "&page=" . htmlentities($page-1, ENT_QUOTES, 'UTF-8') . "\">" . " < " . "</a>";
	
	if($page > 3)
		echo "<a href=\"/view/threadview.php?thread=" . htmlentities($threadID) . "&page=1\"> 1 </a>";
	if($page > 4)
		echo " ... ";
	
	if($page > 2)
		echo "<a href=\"/view/threadview.php?thread=" . htmlentities($threadID) . "&page=" . htmlentities($page-2, ENT_QUOTES, 'UTF-8') . "\">" . htmlentities($page-2, ENT_QUOTES, 'UTF-8') . " " . "</a>";
	if($page > 1)
		echo "<a href=\"/view/threadview.php?thread=" . htmlentities($threadID) . "&page=" . htmlentities($page-1, ENT_QUOTES, 'UTF-8') . "\">" . htmlentities($page-1, ENT_QUOTES, 'UTF-8') . "</a>";
	
	echo " $page ";
	
	if($page < $maxPage)
		echo "<a href=\"/view/threadview.php?thread=" . htmlentities($threadID) . "&page=" . htmlentities($page+1, ENT_QUOTES, 'UTF-8') . "\">" . htmlentities($page+1, ENT_QUOTES, 'UTF-8') . " " . "</a>";
	if($page < $maxPage-1)
		echo "<a href=\"/view/threadview.php?thread=" . htmlentities($threadID) . "&page=" . htmlentities($page+2, ENT_QUOTES, 'UTF-8') . "\">" . htmlentities($page+2, ENT_QUOTES, 'UTF-8') . "</a>";
	
	if($page < $maxPage-3)
		echo " ... ";
	if($page < $maxPage-2)
		echo "<a href=\"/view/threadview.php?thread=" . htmlentities($threadID) . "&page=" . htmlentities($maxPage, ENT_QUOTES, 'UTF-8') . "\">" . " $maxPage " . "</a>";
	
	if($page < $maxPage)
		echo "<a href=\"/view/threadview.php?thread=" . htmlentities($threadID) . "&page=" . htmlentities($page+1, ENT_QUOTES, 'UTF-8') . "\">" . " > " . "</a>";
	if($page < $maxPage-4)
		echo "<a href=\"/view/threadview.php?thread=" . htmlentities($threadID) . "&page=" . htmlentities($page+5, ENT_QUOTES, 'UTF-8') . "\">" . " >> " . "</a>";
	
	echo "</p>";

	$txID = 0;
	
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
		} else {
			echo "This thread has been deleted by an admin.";
	}

}
	$path2 = $_SERVER['DOCUMENT_ROOT'];					//  Find document root.
	$path2 .= "/view/errors.php";						//  Setting absolute path for errors.
	include($path2);

	echo "<p>";

	if($page > 5)
		echo "<a href=\"/view/threadview.php?thread=" . htmlentities($threadID) . "&page=" . htmlentities($page-5, ENT_QUOTES, 'UTF-8') . "\">" . " << " . "</a>";	
	if($page > 1)
		echo "<a href=\"/view/threadview.php?thread=" . htmlentities($threadID) . "&page=" . htmlentities($page-1, ENT_QUOTES, 'UTF-8') . "\">" . " < " . "</a>";
	
	if($page > 3)
		echo "<a href=\"/view/threadview.php?thread=" . htmlentities($threadID) . "&page=1\"> 1 </a>";
	if($page > 4)
		echo " ... ";
	
	if($page > 2)
		echo "<a href=\"/view/threadview.php?thread=" . htmlentities($threadID) . "&page=" . htmlentities($page-2, ENT_QUOTES, 'UTF-8') . "\">" . htmlentities($page-2, ENT_QUOTES, 'UTF-8') . " " . "</a>";
	if($page > 1)
		echo "<a href=\"/view/threadview.php?thread=" . htmlentities($threadID) . "&page=" . htmlentities($page-1, ENT_QUOTES, 'UTF-8') . "\">" . htmlentities($page-1, ENT_QUOTES, 'UTF-8') . "</a>";
	
	echo " $page ";
	
	if($page < $maxPage)
		echo "<a href=\"/view/threadview.php?thread=" . htmlentities($threadID) . "&page=" . htmlentities($page+1, ENT_QUOTES, 'UTF-8') . "\">" . htmlentities($page+1, ENT_QUOTES, 'UTF-8') . " " . "</a>";
	if($page < $maxPage-1)
		echo "<a href=\"/view/threadview.php?thread=" . htmlentities($threadID) . "&page=" . htmlentities($page+2, ENT_QUOTES, 'UTF-8') . "\">" . htmlentities($page+2, ENT_QUOTES, 'UTF-8') . "</a>";
	
	if($page < $maxPage-3)
		echo " ... ";
	if($page < $maxPage-2)
		echo "<a href=\"/view/threadview.php?thread=" . htmlentities($threadID) . "&page=" . htmlentities($maxPage, ENT_QUOTES, 'UTF-8') . "\">" . " $maxPage " . "</a>";
	
	if($page < $maxPage)
		echo "<a href=\"/view/threadview.php?thread=" . htmlentities($threadID) . "&page=" . htmlentities($page+1, ENT_QUOTES, 'UTF-8') . "\">" . " > " . "</a>";
	if($page < $maxPage-4)
		echo "<a href=\"/view/threadview.php?thread=" . htmlentities($threadID) . "&page=" . htmlentities($page+5, ENT_QUOTES, 'UTF-8') . "\">" . " >> " . "</a>";
	
	echo "</p>"; 
	mysqli_close($con);

	function fetchPosts($replyTo, $indent = 0)
	{
		global $con;
		global $thID;
		global $rowNum;
		global $i;
		global $privileges;
		global $txID;
		global $subforumname;
		global $page;
		global $maxPage;
		global $errorsthread;

        if ($replyTo)
            $posts = mysqli_query($con,"SELECT * FROM posts WHERE pThreadID = {$thID} AND pReplyTo = {$replyTo} ORDER BY pTimestamp");
        else
            $posts = mysqli_query($con,"SELECT * FROM posts WHERE pThreadID = {$thID} AND pReplyTo IS NULL ORDER BY pTimestamp");

        if($page > 0 && $page <= $maxPage)
			$posts->data_seek(($page-1)*25);
		else
			$page = 1;

		while(($post_row = mysqli_fetch_array($posts)) && $rowNum <  25 * $page) 
		{
			$rowNum++;
			$pID = $post_row['pID'];
			$i++;		//Integer to keep track of reply-boxes.
			$txID = $i;	
			$delID = "delete" . $i;
			$contID = "content" . $i;
			if ($rowNum > 25 * ($page-1))
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

				//	Reply, Edit, Delete functions
				if (!$post_row['pDeleted'])
				{
					echo "<tr><p style=\"text-indent: {$indpx}\"><td name='".$contID."'>";
					echo "<div class='some' style=\"text-indent: {$indpx}\">";
					echo "<b onclick='textbox($txID)'><button >Reply</button></b>";	//	Calls function for post on click.
					//$author = $_GET['pAuthor'];
					$author = $post_row['pAuthor'];

					if (isset($_SESSION['username'])) { 						// If user is logged in
						if ($_SESSION['username'] === $author || $privileges) {				// If user = to the author
							$csrf = $_SESSION['csrfTOken'];
							echo " <form method='post' name='deleteform'>
								<button type='submit' name='" . $delID . "'>Delete</button>";
							echo "<input type='hidden' name='csrfToken' value='" . $csrf . "' /> 
								</form>";	//Creates the form and button
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
						 </div>';								//  Adds default:hidden textboxes and button after replies.
					echo "</td></p></tr>";

					
					if (isset($_POST[$txID])) {
						if (isset($_SESSION['username'])) {
							$rplyContent = htmlentities($_POST['postContent'], ENT_SUBSTITUTE, 'UTF-8');
							$rplyContent = ltrim($rplyContent);											//Removes whitespace from left side of text
							if (!empty($rplyContent)){
								$postNm = $post_row["pName"];
								$rplyTo = $pID;
								$rplyUsrnm = htmlentities($_SESSION['username'], ENT_QUOTES, 'UTF-8');
								$rplyThID = $thID;
							
								if (post($postNm, $rplyContent, $rplyTo, $rplyUsrnm, $rplyThID, $con)) {
									//echo '<meta http-equiv="refresh" content="0">';
								} else {
									//echo "<p> $con->error </p>";
									array_push($errorsthread, "Could not post reply.");
								}
							} else {
								array_push($errorsthread, "Textbox cannot be empty");
							}
						}
						else {
							array_push($errorsthread, "Please login in order to reply to a post");
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

?>