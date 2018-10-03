<?php
/*
	$path = $_SERVER['DOCUMENT_ROOT']; 					//Find the document root
	$path .= "/functions/postFunctions.php"; 			//Set absolute path
	include($path);
	*/
	//input validation
	$threadID = filter_input(INPUT_GET, 'thread', FILTER_VALIDATE_INT);
	
	
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

	
	while($row = mysqli_fetch_array($result))
	{
		//	Thread name
		echo "<table>";
		$thID = htmlentities($row['thID'], ENT_QUOTES, 'UTF-8');
		echo "<tr><th>" . htmlentities($row['thName'], ENT_QUOTES, 'UTF-8') . "</th></tr>";

		//	Load and populate posts
		$posts = mysqli_query($con,"SELECT * FROM posts WHERE pThreadID = $thID ORDER BY pTimestamp");
		$i = 0;		// Int def.
		while($post_row = mysqli_fetch_array($posts)) {
			echo "<th>";
			$i++;		//Integer to keep track of reply-boxes.
			$txID = $i;	
			// Username and Timestamp
			if (false)	//	admin or mod
				echo '<font color="gold">';	//	gold for moderators, darkorange for admins
			echo htmlentities($post_row['pAuthor'], ENT_QUOTES, 'UTF-8');
			if (false)	//	admin or mod
				echo '</font>';
			echo " | " . htmlentities($post_row['pTimestamp'], ENT_QUOTES, 'UTF-8') . "</th>";

			//	Post content
			echo "<tr>";
			$pID = $post_row['pID'];
			if ($post_row['pDeleted'])	//	Deleted post
				echo "<td>" . '<font color="red">This post was deleted by ' . htmlentities($post_row['pDeletedBy'], ENT_QUOTES, 'UTF-8') . ".</font></td>";
			else						//	Post content
				echo "<td>" . htmlentities($post_row['pContent'], ENT_QUOTES, 'UTF-8') . "</td>";
			echo "</tr>";

			//	Reply, Edit, Delete functions
			if (!$post_row['pDeleted'])
			{
				echo "<tr><td>";
				echo "<b onclick='textbox($txID)'>Reply</b>";	//	Calls function for post on click.
				//$author = $_GET['pAuthor'];
				$author = $post_row['pAuthor'];

				if (isset($_SESSION['username'])) { 						// If user is logged in
					if ($_SESSION['username'] === $author) {				// If user = to the author
						echo " | " . "Edit";								//	Replace these with functions
						echo " | <form method='post' name='deleteform'>
							<button type='submit' name='deletepost'>Delete</button> </form>";	//Creates the form and button
						echo "<style type='text/css'>					
								form[name=deleteform] {
							    display:inline;
							    margin:0px;
							    padding:0px;
								}
								</style>";														//Added some css to keep the delete button inline
						if (isset($_POST['deletepost'])) {
							$name = $row['thName'];

							//IF YOU GET "Call to a member function bind_param() on boolean" THEN PLEASE UPDATE THE REQUESTS FOR USER (look DROP *)

							$stmt = $con->prepare("UPDATE posts SET pDeleted = 1, pDeletedBy = ? WHERE pName = ? AND pThreadID = ?");
							$stmt->bind_param('ssi', $_SESSION['username'], $name, $thID);
							//echo $con->error;
							$stmt->execute();
							$stmt->close();
							header("Refresh: 0");	//Refreshes the page
						}
					}
				}
				
				echo '<div id="' . $txID . '" style="Display:none">		
						<textarea id="CBox" form="textarea" type="text" > </textarea>
					 </div>';								//  Adds default:hidden textboxes after replies.
				echo "</td></tr>";
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
		echo "</table>";
	}	
	mysqli_close($con);
?>