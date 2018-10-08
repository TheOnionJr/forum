<?php
	$path = $_SERVER['DOCUMENT_ROOT']; 					//  Find the document root.
	$path .= "/functions/postFunctions.php"; 			//  Set absolute path for functions.
	include($path);
	
	$path2 = $_SERVER['DOCUMENT_ROOT'];					//  Find document root.
	$path2 .= "/view/errors.php";						//  Setting absolute path for errors.
	include($path2);
	//input validation
	$threadID = filter_input(INPUT_GET, 'thread', FILTER_VALIDATE_INT);
	$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
	$errors = array();
	
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
	
	$maxPage = ceil((mysqli_fetch_array(mysqli_query($con, "SELECT COUNT(*) FROM posts WHERE pThreadID = $threadID AND pReplyTo IS NULL"))['COUNT(*)'])/25);
	
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
		//	Thread name
		echo "<table>";
		$thID = htmlentities($row['thID'], ENT_QUOTES, 'UTF-8');
		echo "<tr><th>" . htmlentities($row['thName'], ENT_QUOTES, 'UTF-8') . "</th></tr>";

		//	Load and populate posts except replies
		$posts = mysqli_query($con,"SELECT * FROM posts WHERE pThreadID = $thID AND pReplyTo IS NULL ORDER BY pTimestamp");
		
		if($page > 0 && $page <= $maxPage)
			$posts->data_seek(($page-1)*25);
		else
			$page = 1;
		
		$i = 0;		// Int def.
		$j = 0;
		while(($post_row = mysqli_fetch_array($posts)) && $rowNum < 25) {
			$rowNum++;
			$pID = $post_row['pID'];
			$i++;		//Integer to keep track of reply-boxes.
			$txID = $i;	
			$delID = "delete" . $i;
			$contID = "content" . $i;
			echo "<th>";

			// Username and Timestamp
			if (false)	//	admin or mod
				echo '<font color="gold">';	//	gold for moderators, darkorange for admins
			echo htmlentities($post_row['pAuthor'], ENT_QUOTES, 'UTF-8');
			if (false)	//	admin or mod
				echo '</font>';
			echo " | " . htmlentities($post_row['pTimestamp'], ENT_QUOTES, 'UTF-8') . "</th>";

			//	Post content
			echo "<tr>";
			
			if ($post_row['pDeleted'])	//	Deleted post
				echo "<td>" . '<font color="red">This post was deleted by ' . htmlentities($post_row['pDeletedBy'], ENT_QUOTES, 'UTF-8') . ".</font></td>";
			else						//	Post content
				echo "<td name='".$contID."'>" . htmlentities($post_row['pContent'], ENT_QUOTES, 'UTF-8') . "</font></td>";
			
			echo "</tr>";

			//	Reply, Edit, Delete functions
			if (!$post_row['pDeleted'])
			{
				echo "<tr><td name='".$contID."'>";
				echo "<b onclick='textbox($txID)'>Reply</b>";	//	Calls function for post on click.
				//$author = $_GET['pAuthor'];
				$author = $post_row['pAuthor'];

				if (isset($_SESSION['username'])) { 						// If user is logged in
					if ($_SESSION['username'] === $author || $privileges) {				// If user = to the author
						echo " | <form method='post' name='deleteform'>
							<button type='submit' name='".$delID."'>Delete</button><input type='hidden' name='csrfToken' value='<?php echo($_SESSION['csrfTOken']) ?>' /> </form>";	//Creates the form and button
						echo "<style type='text/css'>					
								form[name=deleteform] {
							    display:inline;
							    margin:0px;
							    padding:0px;
								}
								</style>";														//Added some css to keep the delete button inline
						if (isset($_POST[$delID])) {
							$name = $row['thName'];

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
				
				echo '<div id="' . $txID . '" style="Display:none">		
						<form method="post">
						<textarea id="CBox" name="postContent" type="text" > </textarea>											  
							<button type="submit" name="' . $txID . '">Submit</button>
						<input type=\'hidden\' name=\'csrfToken\' value=\'<?php echo($_SESSION[\'csrfTOken\']) ?>\' />
						</form>
						<style> 
						form[name=replyform] {
						    display:block;
						    margin:0px;
						    padding:0px;
						}
						</style>
					 </div>';								//  Adds default:hidden textboxes and button after replies.
				echo "</td></tr>";

				if (isset($_SESSION['username'])) {
					if (isset($_POST[$txID])) {
						 $rplyContent = filter_var($_POST['postContent'], FILTER_SANITIZE_STRING);
						if (!empty($rplyContent)){
							$postNm = $row["thName"];
							$rplyTo = $pID;
							$rplyUsrnm = filter_var($_SESSION['username'], FILTER_SANITIZE_STRING);
							$rplyThID = $threadID;
						
							if (post($postNm, $rplyContent, $rplyTo, $rplyUsrnm, $rplyThID, $con)) {
								echo '<meta http-equiv="refresh" content="0">';
							} else {
								//echo "<p> $con->error </p>";
								array_push($errors, "Could not post reply.");
							}
						}
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

			}

			//Code below is almost a duplicate of code above, should probably move some stuff into functions

			//Tried solving the duplicate code differently by replacing the first select statement for post on line 83 so that it displays the replies after the post

			//If we want to still try on doing that we can use the select statements below as a base for figuring it out
			//SELECT * FROM posts WHERE posts.pThreadID = 55 AND posts.pReplyTo = (SELECT A.pReplyTo FROM posts AS A, posts AS B WHERE A.pThreadID = 55 AND A.pReplyTo = B.pID LIMIT 1)					This is not gonna work but might be useful info... 

			//Closest i got 
			//SELECT * FROM posts WHERE pThreadID = 55 ORDER BY (pID = ANY (SELECT A.pReplyTo FROM posts AS A, posts AS B WHERE A.pThreadID = 55 AND A.pReplyTo = B.pID)) ASC, pTimestamp 				This might be on to something..
			//SELECT * FROM posts WHERE pThreadID = 55 ORDER BY CASE WHEN pID = ANY (SELECT A.pReplyTo FROM posts AS A, posts AS B WHERE A.pThreadID = 55 AND A.pReplyTo = B.pID) THEN pID ELSE pTimestamp END 			Returns the same as above, just done differently


			$replies = mysqli_query($con,"SELECT * FROM posts WHERE pThreadID = $threadID AND pReplyTo = $pID ORDER BY pTimestamp");
											//Selects all replies for a post
			$indent = 40;								//indent px
			while($reply_row = mysqli_fetch_array($replies)) {
				$j++;
				$replytxID = 1000 + $j;						//This is not safe, and should try to find another way of using it
				$replydelID = "replydelete" . $j;
				$replycontID = "replycontent" . $j;
				$replypID = $reply_row['pID'];				//gets pID for the reply post
				

				//DELETED POSTS ARE CURRENTLY NOT INDENTED 

				echo "<th>";

				// Username and Timestamp
				if (false)	//	admin or mod
					echo '<font color="gold">';	//	gold for moderators, darkorange for admins
				echo htmlentities($reply_row['pAuthor'], ENT_QUOTES, 'UTF-8');
				if (false)	//	admin or mod
					echo '</font>';
				echo " | " . htmlentities($reply_row['pTimestamp'], ENT_QUOTES, 'UTF-8') . "</th>";

				//	Post content
				echo "<tr>";
				
				if ($reply_row['pDeleted'])	//	Deleted post
					echo "<td>" . '<font color="red">This post was deleted by ' . htmlentities($reply_row['pDeletedBy'], ENT_QUOTES, 'UTF-8') . ".</font></td>";
				else						//	Post content
					echo "<td name='".$replycontID."'>" . htmlentities($reply_row['pContent'], ENT_QUOTES, 'UTF-8') . "</font></td>";
				
				echo "</tr>";

				//	Reply, Edit, Delete functions
				if (!$reply_row['pDeleted'])
				{

					echo "<tr><td name='".$replycontID."'>";
					echo "<b onclick='textbox($replytxID)'>Reply</b>";	//	Calls function for post on click.
					//$author = $_GET['pAuthor'];
					$author = $reply_row['pAuthor'];

					if (isset($_SESSION['username'])) { 						// If user is logged in
						if ($_SESSION['username'] === $author || $privileges) {				// If user = to the author
							echo " | <form method='post' name='deleteform'>
								<button type='submit' name='".$replydelID."'>Delete</button> 
								<input type='hidden' name='csrfToken' value='<?php echo($_SESSION['csrfTOken']) ?>' /></form>";	//Creates the form and button
							echo "<style type='text/css'>					
									form[name=deleteform] {
								    display:inline;
								    margin:0px;
								    padding:0px;
									}
									</style>";														//Added some css to keep the delete button inline
							if (isset($_POST[$replydelID])) {
								$name = $row['thName'];

								//IF YOU GET "Call to a member function bind_param() on boolean" THEN PLEASE UPDATE THE REQUESTS FOR USER (look DROP *)

								$stmt = $con->prepare("UPDATE posts SET pDeleted = 1, pDeletedBy = ? WHERE pName = ? AND pThreadID = ? AND pID = ?");
								$stmt->bind_param('ssii', $_SESSION['username'], $name, $thID, $replypID);
								//echo $con->error;
								$stmt->execute();
								$stmt->close();
								echo '<meta http-equiv="refresh" content="0">';	//Refreshes the page
							}
						}
					}
					
					echo '<div id="' . $replytxID . '" style="Display:none">		
							<form method="post">
							<textarea id="CBox" name="postContent" type="text" > </textarea>											  
								<button type="submit" name="' . $replytxID . '">Submit</button>
							<input type=\'hidden\' name=\'csrfToken\' value=\'<?php echo($_SESSION[\'csrfTOken\']) ?>\' />
							</form>
							<style> 
							form[name=replyform] {
							    display:block;
							    margin:0px;
							    padding:0px;
							}
							</style>
						 </div>';								//  Adds default:hidden textboxes and button after replies.

					echo "</td></tr>";

					if (isset($_SESSION['username'])) {			
						if (isset($_POST[$replytxID])) {		//This is the only different
							 $rplyContent = filter_var($_POST['postContent'], FILTER_SANITIZE_STRING);
							if (!empty($rplyContent)){
								$postNm = $row["thName"];
								$rplyTo = $pID;					//Change this to replypID if we want a reply to be to a reply
								$rplyUsrnm = filter_var($_SESSION['username'], FILTER_SANITIZE_STRING);
								$rplyThID = $threadID;
							
								if (post($postNm, $rplyContent, $rplyTo, $rplyUsrnm, $rplyThID, $con)) {
									echo '<meta http-equiv="refresh" content="0">';
								} else {
									//echo "<p> $con->error </p>";
									array_push($errors, "Could not post reply.");
								}
							}
						}
					}					
					echo "<style type='text/css'>			
							td[name=".$replycontID."] {
								text-indent: ".$indent."px;
							} 
							</style>";						//Displays indent for replies
					//$indent += 40;
				}
			}
		}	
	
	echo "</table>";

	echo "<table><tr><td>";
		echo "<b onclick='textbox(" . ($txID+1) . ")'>New Post</b>";	//	Calls function for post

		echo '<div id="' . ($txID+1) . '" style="Display:none">		
						<form method="post">
						<textarea id="CBox" name="postContent" type="text" > </textarea>											  
							<button type="submit" name="' . ($txID+1) . '">Submit</button>
						<input type=\'hidden\' name=\'csrfToken\' value=\'<?php echo($_SESSION[\'csrfTOken\']) ?>\' />
						</form>
						<style> 
						form[name=replyform] {
						    display:block;
						    margin:0px;
						    padding:0px;
						}
						</style>
					 </div>';								//  Adds default:hidden textboxes and button after replies.

				if (isset($_SESSION['username'])) {
					if (isset($_POST[$txID+1])) {
						 $content = filter_var($_POST['postContent'], FILTER_SANITIZE_STRING);
						if (!empty($content)){
							$postNm = $row["thName"];
							$rplyTo = null;
							$usrnm = filter_var($_SESSION['username'], FILTER_SANITIZE_STRING);
							$thID = $threadID;
						
							if (post($postNm, $content, $rplyTo, $usrnm, $thID, $con)) {
								echo '<meta http-equiv="refresh" content="0">';
							} else {
								//echo "<p> $con->error </p>";
								array_push($errors, "Could not post.");
								echo "<p>Committa kys</p>";
							}
						}
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
	}

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
?>
