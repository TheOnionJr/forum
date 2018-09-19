<?php
include('view.php');

?>

<div id="content">
	<table>
		<?php
			$con=mysqli_connect("localhost","guest","","forum");
			// Check connection
			if (mysqli_connect_errno()){
				echo "Failed to connect to MySQL: " . mysqli_connect_error();
			}

			$result = mysqli_query($con,"SELECT * FROM subforums");

			while($row = mysqli_fetch_array($result))
			{
				echo "<table border='1'>";
				$subID = $row['sID'];
				echo "<tr>";
				echo "<th>" . htmlentities($row['sName'], ENT_QUOTES, 'UTF-8') . "</th>";
				echo "<th>Topics: " . htmlentities($row['sNumTopic'], ENT_QUOTES, 'UTF-8') . "</th>";
				echo "<th>Posts: " . htmlentities($row['sNumPosts'], ENT_QUOTES, 'UTF-8') . "</th>";
				echo "</tr>";
				$topics = mysqli_query($con,"SELECT * FROM topics WHERE tSubForumID = $subID");
				while($topic_row =mysqli_fetch_array($topics)) {
					echo "<tr>";
					echo "<td><a href=\"/view/topicview.php?topic=" . htmlentities($topic_row['tID'], ENT_QUOTES, 'UTF-8') . "\">" . htmlentities($topic_row['tName'], ENT_QUOTES, 'UTF-8') . "</td>";
					echo "<td>" . htmlentities($topic_row['tNumThreads'], ENT_QUOTES, 'UTF-8') . "</td>";
					echo "<td></td>";
					echo "</tr>";
				}
				echo "</table>";
			}	
			mysqli_close($con);
		?>
	</table>
</div>



