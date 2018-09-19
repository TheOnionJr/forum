<?php
include_once('view.php');
echo '<link rel="stylesheet" type="text/css" href="../css/darkmode.css">'; #Loading the default darkmode view.
echo '<link rel="stylesheet" type="text/css" href="../css/topicview.css">'; #Loading the css for topicview.
?>

<div id="content">
	<table>
		<?php
			$topicID = $_GET['topic'];
			
			$con=mysqli_connect("localhost","guest","","forum");
			// Check connection
			if (mysqli_connect_errno()){
				echo "Failed to connect to MySQL: " . mysqli_connect_error();
			}

			$result = mysqli_query($con,"SELECT * FROM topics WHERE tID LIKE $topicID");

			while($row = mysqli_fetch_array($result))
			{
				echo "<table border='1'>";
				$tID = $row['tID'];
				echo "<tr>";
				echo "<th>" . $row['tName'] . "</th>";
				echo "<th>Threads: " . $row['tNumThreads'] . "</th>";
				echo "<th>Posts: " . $row['tNumPosts'] . "</th>";
				echo "</tr>";
				$threads = mysqli_query($con,"SELECT * FROM threads WHERE thTopicID = $tID ORDER BY thTimestamp DESC");
				while($thread_row =mysqli_fetch_array($threads)) {
					echo "<tr>";
					$thID = $thread_row['thID'];
					echo "<td>" . $thread_row['thName'] . "</td>";
					echo "<td>" . "Posts: " . $thread_row['thNumPosts'] . "</td>";
					echo $thID;
					$lastPost = mysqli_query($con,"SELECT * FROM posts WHERE pThreadID = $thID ORDER BY pTimestamp DESC");
					$post_row =mysqli_fetch_array($lastPost);
					echo "<td>" . "Created:  " . $thread_row['thTimestamp'] . "&emsp;By: " . $thread_row['thAuthor'] . "<br>" 
								. "Last post:" . $post_row['pTimestamp'] .  "&emsp;By: " . $post_row['pAuthor'] . "</br>" . "</td>";
					echo "</tr>";
				}
				echo "</table>";
			}	
			mysqli_close($con);
		?>
	</table>
</div>