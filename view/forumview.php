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

			echo "<table border='1'>";

			while($row = mysqli_fetch_array($result))
			{
				$subID = $row['sID'];
				echo "<tr>";
				echo "<th>" . $row['sName'] . "</th>";
				echo "<th>" . $row['sNumTopic'] . "</th>";
				echo "<th>" . $row['sNumPosts'] . "</th>";
				echo "</tr>";
				$topics = mysqli_query($con,"SELECT * FROM topics WHERE tSubForumID = $subID");
				while($topic_row =mysqli_fetch_array($topics)) {
					echo "<tr>";
					echo "<td>" . $topic_row['tName'] . "</td>";
					echo "<td>" . $topic_row['tNumThreads'] . "</td>";
					echo "<td></td>"
					echo "</tr>";
				}
			}	
			echo "</table>";

			mysqli_close($con);
		?>
	</table>
</div>



