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
		//	Thread name
		echo "<table>";
		$thID = htmlentities($row['thID'], ENT_QUOTES, 'UTF-8');
		echo "<tr><th>" . htmlentities($row['thName'], ENT_QUOTES, 'UTF-8') . "</th></tr>";

		//	Load and populate posts
		$posts = mysqli_query($con,"SELECT * FROM posts WHERE pThreadID = $thID ORDER BY pTimestamp");
		
		if($page > 0 && $page <= $maxPage)
			$posts->data_seek(($page-1)*25);
		else
			$page = 1;
		
		$i = 0;		// Int def.
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
					if ($_SESSION['username'] === $author) {				// If user = to the author
						echo " | " . "Edit";								//	Replace this with functions
						echo " | <form method='post' name='deleteform'>
							<button type='submit' name='".$delID."'>Delete</button> </form>";	//Creates the form and button
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
				if ($post_row['pReplyTo'] != NULL)  {
					echo "<style type='text/css'>
							td[name=".$contID."] {
								text-indent: 40px;
							} </style>";
				}

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
		}	
	
	echo "</table>";

	echo "<table><tr><td>";
		echo "<b onclick='textbox(" . ($txID+1) . ")'>New Post</b>";	//	Calls function for post

		echo '<div id="' . ($txID+1) . '" style="Display:none">		
						<form method="post">
						<textarea id="CBox" name="postContent" type="text" > </textarea>											  
							<button type="submit" name="' . ($txID+1) . '">Submit</button>
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