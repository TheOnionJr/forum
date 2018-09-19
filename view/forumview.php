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

			echo "<table border='1'>
			<tr>
				<th>Subforum</th>
				<th>Topics</th>
				<th>Posts</th>
			</tr>";

			while($row = mysqli_fetch_array($result))
			{
				$subID = $row['sID'];
				echo "<tr>";
				echo "<td>" . $row['sName'] . "</td>";
				echo "<td>" . $row['sNumTopic'] . "</td>";
				echo "<td>" . $row['sNumPosts'] . "</td>";
				echo "</tr>";
				$topics = mysqli_query($con,"SELECT * FROM topics WHERE tSubForumID = $subID");
				while($topic_row =mysqli_fetch_array($topics)) {
					echo "<tr>";
					echo "<td>" . $topic_row['tName'] . "</td>";
					echo "<td>" . $topic_row['tNumThreads'] . "</td>";
					echo "</tr>";
				}
			}	
			echo "</table>";

			mysqli_close($con);
		?>
	</table>
</div>



