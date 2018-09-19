<?php
include_once('view.php');

echo '<link rel="stylesheet" type="text/css" href="../css/darkmode.css">'; #Manual workaround for css.
?>

<div id="content">
	<table>
		<?php
			$con=mysqli_connect("localhost","guest","","forum");
			// Check connection
			if (mysqli_connect_errno()){
				echo "Failed to connect to MySQL: " . mysqli_connect_error();
			}

			$result = mysqli_query($con,"SELECT * FROM topics");

			while($row = mysqli_fetch_array($result))
			{
				echo "<table border='1'>";
				$tID = $row['tID'];
				echo "<tr>";
				echo "<th>" . $row['tName'] . "</th>";
				echo "<th>Threads: " . $row['tNumThreads'] . "</th>";
				echo "<th>Posts: " . $row['tNumPosts'] . "</th>";
				echo "</tr>";
				$threads = mysqli_query($con,"SELECT * FROM threads WHERE thTopicID = $tID");
				while($thread_row =mysqli_fetch_array($threads)) {
					echo "<tr>";
					echo "<td>" . $thread_row['thName'] . "</td>";
					echo "<td>" . $thread_row['thNumPosts'] . "</td>";
					echo "<td></td>";
					echo "</tr>";
				}
				echo "</table>";
			}	
			mysqli_close($con);
		?>
	</table>
</div>