<?php
	$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);								//Current page
	$con=mysqli_connect("localhost","guest","","forum");										//Database connection
			// Check connection
	if (mysqli_connect_errno()){																//Error handeling
		echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}
	$result = mysqli_query($con,"SELECT * FROM subforums"); 									//Query for subforum data. No user input, no prepared statement requierd
	$rowNum = 0;	//A counter for limiting amount of subforums
	
	$maxPage = ceil((mysqli_fetch_array(mysqli_query($con, "SELECT COUNT(*) FROM subforums"))['COUNT(*)'])/25);	//Max amount of pages needed
	
	if($page > 0 && $page <= $maxPage)		//Checks that current page is not the first page and within the max amount of pages
		$result->data_seek(($page-1)*25);	//Finds the correct subforum to start at
	else
		$page = 1;		//sets current page to be 1 if the page was outside legal page numbers
	
	paging();			//Sets up paging
	
	while(($row = mysqli_fetch_array($result)) && $rowNum < 25)									//Table generation
	{
		$rowNum++;																				//Increments counter						
		echo "<table>";																			//Table start
		$subID = $row['sID'];
		
		$numThreads = mysqli_query($con, "SELECT COUNT(*) FROM threads INNER JOIN topics ON threads.thTopicID = topics.tID WHERE topics.tSubForumID = $subID");	
		$threads = mysqli_fetch_array($numThreads);												//gets amount of threads for the subforum	
		
		$numPosts = mysqli_query($con,"SELECT COUNT(*) FROM posts INNER JOIN (threads INNER JOIN topics ON threads.thTopicID = topics.tID) ON posts.pThreadID = threads.thID WHERE topics.tSubForumID = $subID");
		$posts = mysqli_fetch_array($numPosts);													//gets amount of posts for the subforum
		
		echo "<tr>";																			//Start table headers
		echo "<th>" . htmlentities($row['sName'], ENT_QUOTES, 'UTF-8') . "</th>";  				//Subforum name with sanetazion
		echo "<th>Threads: " . htmlentities($threads['COUNT(*)'], ENT_QUOTES, 'UTF-8') . "</th>";  //Threadcount with sanetazion
		echo "<th>Posts: " . htmlentities($posts['COUNT(*)'], ENT_QUOTES, 'UTF-8') . "</th>";	//Postcount with sanetazion
		echo "</tr>";	//End table headers
		$topics = mysqli_query($con,"SELECT * FROM topics WHERE tSubForumID = $subID");			//Query for topic data. No user input, no prepared statement requierd
		while($topic_row =mysqli_fetch_array($topics)) {										//Table data generation
			$topicID = $topic_row['tID'];
			
			$numThreads = mysqli_query($con, "SELECT COUNT(*) FROM threads WHERE threads.thTopicID = $topicID");	
			$threads = mysqli_fetch_array($numThreads);											//gets amount of threads for the topic	
			
			$numPosts = mysqli_query($con,"SELECT COUNT(*) FROM posts INNER JOIN threads ON posts.pThreadID = threads.thID WHERE threads.thTopicID = $topicID");
			$posts = mysqli_fetch_array($numPosts);												//gets amount of posts for the topic
			
			echo "<tr>";																		//Table data start
			echo "<td><a href=\"/view/topicview.php?tID=" . htmlentities($topic_row['tID'], ENT_QUOTES, 'UTF-8') . "&sID=" . htmlentities($topic_row['tSubForumID'], ENT_QUOTES, 'UTF-8') . "\">" . htmlentities($topic_row['tName'], ENT_QUOTES, 'UTF-8') . "</td>";	//topic link with GET parameter
			echo "<td>Threads: " . htmlentities($threads['COUNT(*)'], ENT_QUOTES, 'UTF-8') . "</td>";//Number of threads in topic
			echo "<td>Posts: " . htmlentities($posts['COUNT(*)'], ENT_QUOTES, 'UTF-8') . "</td>";	//Number of posts in topic
			echo "</tr>";																		//End table data
		}
		echo "</table>";																		//End table
	}	
	
	paging();																					//Sets up paging
	
	mysqli_close($con);																			//Closing database connection
	
	function paging()							//Sets up the paging
	{
		$page = $GLOBALS['page'];				//Current page
		$maxPage = $GLOBALS['maxPage'];			//Maximum pages needed
		echo "<p>";								//Start of paging 

		if($page > 5)		//If more than 4 pages before current page
			echo "<a href=\"/?page=" . htmlentities($page-5, ENT_QUOTES, 'UTF-8') . "\">" . " << " . "</a>";	//Enables skipping back 5 pages
		if($page > 1)		//If current page is not the first page
			echo "<a href=\"/?page=" . htmlentities($page-1, ENT_QUOTES, 'UTF-8') . "\">" . " < " . "</a>";		//Enables going back one page	
		
		if($page > 3)		//If more than 2 pages before current page
			echo "<a href=\"/?page=1\"> 1 </a>";	//Enable skipping to first page
		if($page > 4)		//If more than 3 pages before current page
			echo " ... ";	//Shows that there is more pages between 
		
		if($page > 2)		//If there is more than one page before current page
			echo "<a href=\"/?page=" . htmlentities($page-2, ENT_QUOTES, 'UTF-8') . "\">" . htmlentities($page-2, ENT_QUOTES, 'UTF-8') . " " . "</a>";	//Enables going back two pages
		if($page > 1)		//If there is pages before current page
			echo "<a href=\"/?page=" . htmlentities($page-1, ENT_QUOTES, 'UTF-8') . "\">" . htmlentities($page-1, ENT_QUOTES, 'UTF-8') . "</a>";		//Enables going back one page
		
		echo " $page ";		//prints current page
		
		if($page < $maxPage)	//If not last page
			echo "<a href=\"/?page=" . htmlentities($page+1, ENT_QUOTES, 'UTF-8') . "\">" . htmlentities($page+1, ENT_QUOTES, 'UTF-8') . " " . "</a>";	//Enables going forwards one page
		if($page < $maxPage-1)	//If more than one page after current
			echo "<a href=\"/?page=" . htmlentities($page+2, ENT_QUOTES, 'UTF-8') . "\">" . htmlentities($page+2, ENT_QUOTES, 'UTF-8') . "</a>";		//Enable going forwards two pages
		
		if($page < $maxPage-3)	//If more than 3 pages after current page
			echo " ... ";		//Shows that there is more pages between
		if($page < $maxPage-2)	//If more than 2 pages after current page
			echo "<a href=\"/?page=" . htmlentities($maxPage, ENT_QUOTES, 'UTF-8') . "\">" . " $maxPage " . "</a>";		//Enable skipping to last page	
		
		if($page < $maxPage)	//If current page is not the last page
			echo "<a href=\"/?page=" . htmlentities($page+1, ENT_QUOTES, 'UTF-8') . "\">" . " > " . "</a>";		//Enable going forward one page
		if($page < $maxPage-4)	//If more than 4 pages after current page
			echo "<a href=\"/?page=" . htmlentities($page+5, ENT_QUOTES, 'UTF-8') . "\">" . " >> " . "</a>";	//Enables skipping forward 5 pages
		
		echo "</p>";	//paging end
	}
?>