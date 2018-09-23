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
		echo "<tr>";																			//Start table headers
		echo "<th>" . htmlentities($row['sName'], ENT_QUOTES, 'UTF-8') . "</th>";  				//Subforum name with sanetazion
		echo "<th>Threads: " . htmlentities($row['sNumTopic'], ENT_QUOTES, 'UTF-8') . "</th>";  //Threadcount with sanetazion
		echo "<th>Posts: " . htmlentities($row['sNumPosts'], ENT_QUOTES, 'UTF-8') . "</th>";	//Postcount with sanetazion
		echo "</tr>";	//End table headers
		$topics = mysqli_query($con,"SELECT * FROM topics WHERE tSubForumID = $subID");			//Query for topic data. No user input, no prepared statement requierd
		while($topic_row =mysqli_fetch_array($topics)) {										//Table data generation
			echo "<tr>";																		//Table data start
			echo "<td><a href=\"/view/topicview.php?topic=" . htmlentities($topic_row['tID'], ENT_QUOTES, 'UTF-8') . "\">" . htmlentities($topic_row['tName'], ENT_QUOTES, 'UTF-8') . "</td>";	//topic link with GET parameter
			echo "<td>" . htmlentities($topic_row['tNumThreads'], ENT_QUOTES, 'UTF-8') . "</td>";//Number of threads in topic
			echo "<td>" . htmlentities($topic_row['tNumPosts'], ENT_QUOTES, 'UTF-8') . "</td>";	//Number of posts in topic
			echo "</tr>";																		//End table data
		}
		echo "</table>";																		//End table
	}	
	mysqli_close($con);																			//Closing database connection
?>