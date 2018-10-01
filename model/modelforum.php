<?php
	$con=mysqli_connect("localhost","guest","","forum");										//Database connection
			// Check connection
	if (mysqli_connect_errno()){																//Error handeling
		echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}
	$result = mysqli_query($con,"SELECT * FROM subforums"); 									//Query for subforum data. No user input, no prepared statement requierd
	
	while($row = mysqli_fetch_array($result))													//Table generation
	{
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
			$threads = mysqli_fetch_array($numThreads);												//gets amount of threads for the subforum	
			
			$numPosts = mysqli_query($con,"SELECT COUNT(*) FROM posts INNER JOIN threads ON posts.pThreadID = threads.thID WHERE threads.thTopicID = $topicID");
			$posts = mysqli_fetch_array($numPosts);	
			
			echo "<tr>";																		//Table data start
			echo "<td><a href=\"/view/topicview.php?tID=" . htmlentities($topic_row['tID'], ENT_QUOTES, 'UTF-8') . "&sID=" . htmlentities($topic_row['tSubForumID'], ENT_QUOTES, 'UTF-8') . "\">" . htmlentities($topic_row['tName'], ENT_QUOTES, 'UTF-8') . "</td>";	//topic link with GET parameter
			echo "<td>Threads: " . htmlentities($threads['COUNT(*)'], ENT_QUOTES, 'UTF-8') . "</td>";//Number of threads in topic
			echo "<td>Posts: " . htmlentities($posts['COUNT(*)'], ENT_QUOTES, 'UTF-8') . "</td>";	//Number of posts in topic
			echo "</tr>";																		//End table data
		}
		echo "</table>";																		//End table
	}	
	mysqli_close($con);																			//Closing database connection
?>