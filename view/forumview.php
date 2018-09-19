<?php
include_once('view.php');

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
				echo "<th>" . $row['sName'] . "</th>";
				echo "<th>Topics: " . $row['sNumTopic'] . "</th>";
				echo "<th>Posts: " . $row['sNumPosts'] . "</th>";
				echo "</tr>";
				$topics = mysqli_query($con,"SELECT * FROM topics WHERE tSubForumID = $subID");
				while($topic_row =mysqli_fetch_array($topics)) {
					echo "<tr>";
					echo "<td><a href=\"/view/topicview.php?topic=" . $topic_row['tID'] . "\">" . $topic_row['tName'] . "</td>";
					echo "<td>" . $topic_row['tNumThreads'] . "</td>";
					echo "<td></td>";
					echo "</tr>";
				}
				echo "</table>";
			}	
			mysqli_close($con);
		?>
	</table>
</div>



