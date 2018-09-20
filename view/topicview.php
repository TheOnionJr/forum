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

			$stmt = $con->prepare("SELECT * FROM topics WHERE tID = ?");
			$stmt->bind_param('i', $topicID);
			$stmt->execute();
			$result = $stmt->get_result();
			$stmt->close();

			while($row = mysqli_fetch_array($result))
			{
				echo "<table border='1'>";
				$tID = htmlentities($row['tID'], ENT_QUOTES, 'UTF-8');
				echo "<tr>";
				echo "<th>" . htmlentities($row['tName'], ENT_QUOTES, 'UTF-8'). "</th>";
				echo "<th>Threads: " . htmlentities($row['tNumThreads'], ENT_QUOTES, 'UTF-8') . "</th>";
				echo "<th>Posts: " . htmlentities($row['tNumPosts'], ENT_QUOTES, 'UTF-8') . "</th>";
				echo "</tr>";
				$threads = mysqli_query($con,"SELECT * FROM threads WHERE thTopicID = $tID ORDER BY thTimestamp DESC");
				while($thread_row =mysqli_fetch_array($threads)) {
					echo "<tr>";
					$thID = $thread_row['thID'];
					echo "<td>" . htmlentities($thread_row['thName'], ENT_QUOTES, 'UTF-8') . "</td>";
					echo "<td>" . "Posts: " . htmlentities($thread_row['thNumPosts'], ENT_QUOTES, 'UTF-8') . "</td>";
					$lastPost = mysqli_query($con,"SELECT * FROM posts WHERE pThreadID = $thID ORDER BY pTimestamp DESC");
					$post_row =mysqli_fetch_array($lastPost);
					echo "<td>" . "Created:  " . htmlentities($thread_row['thTimestamp'], ENT_QUOTES, 'UTF-8') . "&emsp;By: " . htmlentities($thread_row['thAuthor'], ENT_QUOTES, 'UTF-8') . "<br>" 
								. "Last post:" . htmlentities($post_row['pTimestamp'], ENT_QUOTES, 'UTF-8') .  "&emsp;By: " . htmlentities($post_row['pAuthor'], ENT_QUOTES, 'UTF-8') . "</br>" . "</td>";
					echo "</tr>";
				}
				echo "</table>";
			}	
			mysqli_close($con);
		?>
	</table>
</div>

