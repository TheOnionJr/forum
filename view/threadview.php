<?php
$path = $_SERVER['DOCUMENT_ROOT']; 	//Find the document root
$path .= "/view/view.php"; 			//Set absolute path
include($path);

echo '<link rel="stylesheet" type="text/css" href="../css/darkmode.css">'; #Loading the default darkmode view.
echo '<link rel="stylesheet" type="text/css" href="../css/threadview.css">'; #Loading the css for threadview.
?>

<div id="content">
	<table>
		<?php
			//input validation
			$threadID = filter_input(INPUT_GET, 'thread', FILTER_VALIDATE_INT);
			
			$con=mysqli_connect("localhost","guest","","forum");
			// Check connection
			if (mysqli_connect_errno()){
				echo "Failed to connect to MySQL: " . mysqli_connect_error();
			}

			$stmt = $con->prepare("SELECT * FROM threads WHERE thID = ?");
			$stmt->bind_param('i', $threadID);
			$stmt->execute();
			$result = $stmt->get_result();
			$stmt->close();
			//If thread id does not exist user error
			if(mysqli_num_rows($result) == 0) {
				echo "This thread does not exist";
			}
			//Else statement not necessary, if $result = 0 -> nothing will be printed
			while($row = mysqli_fetch_array($result))
			{
				echo "<table>";
				$thID = htmlentities($row['thID'], ENT_QUOTES, 'UTF-8');
				echo "<tr>";
				echo "<th>" . htmlentities($row['thName'], ENT_QUOTES, 'UTF-8'). "</th>";
				echo "</tr>";
				$posts = mysqli_query($con,"SELECT * FROM posts WHERE pThreadID = $thID ORDER BY pTimestamp");
				while($post_row =mysqli_fetch_array($posts)) {
					echo "<th>" . htmlentities($post_row['pAuthor'], ENT_QUOTES, 'UTF-8') . " | " . htmlentities($post_row['pTimestamp'], ENT_QUOTES, 'UTF-8') . "</th>";
					echo "<tr>";
					$pID = $post_row['pID'];
					echo "<td>" . "" . htmlentities($post_row['pContent'], ENT_QUOTES, 'UTF-8') . "</td>";
					echo "</tr>";
					echo "<tr><td>" . "Reply | Edit | Delete" . "</td></tr>";
				}
				echo "</table>";
			}	
			mysqli_close($con);
		?>
	</table>
</div>

