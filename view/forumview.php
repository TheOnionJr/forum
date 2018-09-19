<?php
include_once('view.php');

?>

<div id="content">
	<table>
		<?php
			$con=mysqli_connect("localhost","user","","forum");
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
				echo "<tr>";
				echo "<td>" . $row['sName'] . "</td>";
				echo "<td>" . $row['sNumTopic'] . "</td>";
				echo "<td>" . $row['sNumPosts'] . "</td>";
				echo "</tr>";
			}	
			echo "</table>";

			mysqli_close($con);
		?>
	</table>
</div>



