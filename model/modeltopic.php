<?php
	$path = $_SERVER['DOCUMENT_ROOT']; 					//  Find the document root.
	$path .= "/functions/postFunctions.php"; 			//  Set absolute path for functions.
	include($path);
	//input validation
	$topicID = filter_input(INPUT_GET, 'tID', FILTER_VALIDATE_INT);	//ID of the topic
	$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);	//current page
	$con=mysqli_connect("localhost","guest","","forum");
	// Check connection
	if (mysqli_connect_errno()){
		echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}
	$stmt = $con->prepare("SELECT * FROM topics WHERE tID = ?");
	$stmt->bind_param('i', $topicID);
	$stmt->execute();
	$result = $stmt->get_result();
	$stmt->close();
	//If topic id does not exist user error
	if(mysqli_num_rows($result) == 0) {
		echo "This topic does not exist";
		http_response_code(404);
	}
	//Else statement not necessary, if $result = 0 -> nothing will be printed

	//	Find out if the user should have privileges in this topic
	$privileges = false; 	//	If the current user should have deletion / locking privileges
	$subforumname = htmlentities(mysqli_fetch_array(mysqli_query($con, "SELECT * FROM subforums JOIN topics ON subforums.sID = topics.tSubForumID WHERE tID = {$topicID}"))['sName'], ENT_QUOTES, 'UTF-8');
	if (isset($_SESSION['username'])) 			// If user is logged in
		if (mysqli_fetch_array(mysqli_query($con, "SELECT COUNT(*) FROM uuser JOIN urole ON uuser.uID = urole.urID WHERE uuser.uUsername = \"{$_SESSION['username']}\" AND ( urType = \"admin\" OR urType = \"mod{$subforumname}\")"))[0])
			$privileges = true;

	$rowNum = 0;	//counter for rows	
	
	$maxPage = ceil((mysqli_fetch_array(mysqli_query($con, "SELECT COUNT(*) FROM threads WHERE thTopicID = $topicID"))['COUNT(*)'])/25);	//max amount of pages needed
	
	if($page > $maxPage || $page < 1)	//If current page is outside legal range
		$page = 1;						//set page to default
	
	paging();		//sets up paging
	
	while($row = mysqli_fetch_array($result))
	{
		echo "<table>";
		$tID = htmlentities($row['tID'], ENT_QUOTES, 'UTF-8');
		
		$numThreads = mysqli_query($con, "SELECT COUNT(*) FROM threads WHERE threads.thTopicID = $tID");	
		$threads = mysqli_fetch_array($numThreads);												//gets amount of threads for the topic
		
		$numPosts = mysqli_query($con,"SELECT COUNT(*) FROM posts INNER JOIN threads ON posts.pThreadID = threads.thID WHERE threads.thTopicID = $tID");
		$posts = mysqli_fetch_array($numPosts);													//gets amount of posts for the topic	
		
		echo "<tr>";
		echo "<th>" . htmlentities($row['tName'], ENT_QUOTES, 'UTF-8'). "</th>";
		echo "<th>Threads: " . htmlentities($threads['COUNT(*)'], ENT_QUOTES, 'UTF-8') . "</th>";
		echo "<th>Posts: " . htmlentities($posts['COUNT(*)'], ENT_QUOTES, 'UTF-8') . "</th>";
		if ($privileges)
			echo "<th></th>";
		echo "</tr>";
		$threads = mysqli_query($con,"SELECT * FROM threads WHERE thTopicID = $tID ORDER BY thTimestamp DESC");
		
		if($page > 0 && $page <= $maxPage)		//if current page is in legal range
			$threads->data_seek(($page-1)*25);	//finds correct thread to start
		else
			$page = 1;	//sets page to default

		$topicdelID = 0;
		while(($thread_row =mysqli_fetch_array($threads)) && $rowNum < 25) {

				$topicdelID++;
				$rowNum++;		//increments row counter
				$thID = $thread_row['thID'];
				
				$numPosts = mysqli_query($con,"SELECT COUNT(*) FROM posts WHERE posts.pThreadID = $thID");
				$posts = mysqli_fetch_array($numPosts);				//gets amount of posts for the thread
				
				echo "<tr>";
				if($thread_row['thLock'] != NULL)
					echo '<td><font color="red">DELETED </font></td>';
				else 
					echo "<td><a href=\"/view/threadview.php?topic=". htmlentities($topicID, ENT_QUOTES, 'UTF-8') . "&thread=" . htmlentities($thID) . "\">" . htmlentities($thread_row['thName'], ENT_QUOTES, 'UTF-8') . "</td>"; //Links to correct threadview. God this line is aids...
				echo "<td>" . "Posts: " . htmlentities($posts['COUNT(*)'], ENT_QUOTES, 'UTF-8') . "</td>";
				$lastPost = mysqli_query($con,"SELECT * FROM posts WHERE pThreadID = $thID ORDER BY pTimestamp DESC");
				$post_row =mysqli_fetch_array($lastPost);
				echo "<td>" . "Created:  " . htmlentities($thread_row['thTimestamp'], ENT_QUOTES, 'UTF-8') . "&emsp;By: " . htmlentities($thread_row['thAuthor'], ENT_QUOTES, 'UTF-8') . "<br>" . "Last post:" . htmlentities($post_row['pTimestamp'], ENT_QUOTES, 'UTF-8') .  "&emsp;By: " . htmlentities($post_row['pAuthor'], ENT_QUOTES, 'UTF-8') . "</br>" . "</td>";
				
				if ($privileges) { 

					$csrf = $_SESSION['csrfTOken'];
					echo "<td>";
					echo "<form method='post' name='deleteform'>
							<button type='submit' name='".$topicdelID."'>Delete</button> 
							<input type='hidden' name='csrfToken' value='" . $csrf . "' />
						</form>";

					if (isset($_POST[$topicdelID])) {
						if(deltopic($con, $thID)) {
							echo '<meta http-equiv="refresh" content="0">';
						}
					}
				}
				echo "</tr>";
		}
		echo "</table>";
	}	
	
	paging();		//sets up paging
	
	mysqli_close($con);
	
	function paging()			//Sets up the paging
	{							
		global $page;			//Current page
		global $maxPage;		//Maximum pages needed
		global $topicID;		//topic id
		echo "<p>";             //Start of paging 
		

		if($page > 5)			//If more than 4 pages before current page
			echo "<a href=\"/view/topicview.php?tID=" . htmlentities($topicID) . "&page=" . htmlentities($page-5, ENT_QUOTES, 'UTF-8') . "\">" . " << " . "</a>";	//Enables skipping back 5 pages
		if($page > 1)           //If current page is not the first page                                                                                                                                    
			echo "<a href=\"/view/topicview.php?tID=" . htmlentities($topicID) . "&page=" . htmlentities($page-1, ENT_QUOTES, 'UTF-8') . "\">" . " < " . "</a>";    //Enables going back one page	
		
		if($page > 3)		//If more than 2 pages before current page
			echo "<a href=\"/view/topicview.php?tID=" . htmlentities($topicID) . "&page=1\"> 1 </a>";	//Enable skipping to first page
		if($page > 4)		//If more than 3 pages before current page	
			echo " ... ";   //Shows that there is more pages between 
		
		if($page > 2)		//If there is more than one page before current page
			echo "<a href=\"/view/topicview.php?tID=" . htmlentities($topicID) . "&page=" . htmlentities($page-2, ENT_QUOTES, 'UTF-8') . "\">" . htmlentities($page-2, ENT_QUOTES, 'UTF-8') . " " . "</a>";	//Enables going back two pages
		if($page > 1)		//If there is pages before current page	                                                                                                                                            
			echo "<a href=\"/view/topicview.php?tID=" . htmlentities($topicID) . "&page=" . htmlentities($page-1, ENT_QUOTES, 'UTF-8') . "\">" . htmlentities($page-1, ENT_QUOTES, 'UTF-8') . "</a>";       //Enables going back one page
		
		echo " $page ";		//prints current page	
		
		if($page < $maxPage)	//If not last page
			echo "<a href=\"/view/topicview.php?tID=" . htmlentities($topicID) . "&page=" . htmlentities($page+1, ENT_QUOTES, 'UTF-8') . "\">" . htmlentities($page+1, ENT_QUOTES, 'UTF-8') . " " . "</a>";	//Enables going forwards one page
		if($page < $maxPage-1)	//If more than one page after current                                                                                                                                            
			echo "<a href=\"/view/topicview.php?tID=" . htmlentities($topicID) . "&page=" . htmlentities($page+2, ENT_QUOTES, 'UTF-8') . "\">" . htmlentities($page+2, ENT_QUOTES, 'UTF-8') . "</a>";       //Enable going forwards two pages
		
		if($page < $maxPage-3)	//If more than 3 pages after current page
			echo " ... ";       //Shows that there is more pages between
		if($page < $maxPage-2)  //If more than 2 pages after current page
			echo "<a href=\"/view/topicview.php?tID=" . htmlentities($topicID) . "&page=" . htmlentities($maxPage, ENT_QUOTES, 'UTF-8') . "\">" . " $maxPage " . "</a>";	//Enable skipping to last page	
		
		if($page < $maxPage)	//If current page is not the last page
			echo "<a href=\"/view/topicview.php?tID=" . htmlentities($topicID) . "&page=" . htmlentities($page+1, ENT_QUOTES, 'UTF-8') . "\">" . " > " . "</a>";	//Enable going forward one page
		if($page < $maxPage-4)	//If more than 4 pages after current page                                                                                                  
			echo "<a href=\"/view/topicview.php?tID=" . htmlentities($topicID) . "&page=" . htmlentities($page+5, ENT_QUOTES, 'UTF-8') . "\">" . " >> " . "</a>";   //Enables skipping forward 5 pages
		
		echo "</p>";	//paging end	
	}
?>