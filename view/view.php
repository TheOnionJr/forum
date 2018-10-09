<?php 
	error_reporting(0);
	$path = $_SERVER['DOCUMENT_ROOT']; 	//Find the document root
	$path .= "/model/modellogin.php"; 	//Set absolute path
	include($path);

	if (isset($_GET['logout'])) {		//If user logs out
		session_destroy();				//Destroys session
		unset($_SESSION['username']);	//Unsets name
		header("location: index.php");	//Returns to main page
	}
?>
<html>
	<head>
		<!-- Header related security: -->
		<?php
			header('X-XSS-Protection: 1; mode=block'); //Browser XXS protection
			header('X-Frame-Options: DENY'); 
			header('X-Content-Type-Options: nosniff');
		?>
		<title> ROSCIS Forum</title>
		<style id="antiClickjack">body{display:none !important;}</style>
		<script type="text/javascript">
  			if (self === top) {
       			var antiClickjack = document.getElementById("antiClickjack");
       			antiClickjack.parentNode.removeChild(antiClickjack);
   			} else {
       			top.location = self.location;
   			}
		</script>
		<!-- End header related security -->
		<link type="text/css" id="dark-mode" rel="stylesheet" href="/css/darkmode.css">
	</head>
	<body>
		<?php
		$randomtoken = md5(uniqid(rand(), true));
		$_SESSION['csrfTOken']=$randomtoken
		?>
		<div id="header">
	    	<a href="/index.php"><img src="/img/white_logo_transparent.png" align="left" ></a>
	    	<div id="login">
		    	<?php if (!isset($_SESSION['username'])) : ?>
		    		<form method="post" action="/index.php">
			    		<?php
			    			$errorpath = $_SERVER['DOCUMENT_ROOT']; //Find the document root
			    			$errorpath .= "/view/errors.php"; 		//Set absolute path
			    			include($errorpath); 
			   			?>
			       		Username: <input type="text" name="username"><br>
			       		Password: <input type="password" name="password"><br>
			       		<a href="/view/registerview.php" id="register">Register</a> 
			        	<button type="submit" class="login_button" name="login_user">Login</button>
			        	<input type='hidden' name='csrfToken' value='<?php echo($_SESSION['csrfTOken']) ?>' /> 
					</form>
				<?php endif ?> 
				<?php if (isset($_SESSION['username'])) : ?>
	   		 		<p id="p"> Logged in as <strong><?php echo $_SESSION['username']; ?></strong></p>
	    			<p id="p"> <a href="/index.php?logout='1'" style="color: red;">Logout</a></p>
	    		<?php endif ?>
			</div> 	
		</div>	
		<div id="navigationBar">
			<?php
				$url = $_SERVER['REQUEST_URI'];
				$urlParts = parse_url($url);
				$inTopic = in_array("/view/topicview.php", $urlParts);
				$inThread = in_array("/view/threadview.php", $urlParts);

				$con=mysqli_connect("localhost","guest","","forum");	//Find the server

				if ($inTopic || $inThread)
				{
					$topicID = filter_input(INPUT_GET, 'topic', FILTER_VALIDATE_INT);
					$threadID = filter_input(INPUT_GET, 'thread', FILTER_VALIDATE_INT);

					echo "<table><th>";
					echo '<a href="\">Home</a>';
					if (!mysqli_connect_errno()){
						//Topic
						$stmt = $con->prepare("SELECT * FROM topics WHERE tID = ?");
						$stmt->bind_param('i', $topicID);
						$stmt->execute();
						$result = $stmt->get_result();
						$stmt->close();
						if(mysqli_num_rows($result) != 0)
						{
							$topicName = mysqli_fetch_array(mysqli_query($con,"SELECT tName FROM topics WHERE tID = $topicID"));
							echo " | ";
							echo '<a href=/view/topicview.php?tID=' . htmlentities($topicID, ENT_QUOTES, 'UTF-8'). "&sID=" . htmlentities($threadID) . '>' . htmlentities($topicName['tName'], ENT_QUOTES, 'UTF-8') . '</a>';

							if ($inThread)
							{
								//Thread
								$stmt = $con->prepare("SELECT * FROM threads WHERE thID = ?");
								$stmt->bind_param('i', $threadID);
								$stmt->execute();
								$result = $stmt->get_result();
								$stmt->close();
								if(mysqli_num_rows($result) != 0)
								{
									$threadName = mysqli_fetch_array(mysqli_query($con,"SELECT thName FROM threads WHERE thTopicID = $topicID AND thID = $threadID"));
									echo " | ";
									echo '<a href=/view/threadview.php?topic=' . htmlentities($topicID, ENT_QUOTES, 'UTF-8') . "&thread=" . htmlentities($threadID) . '>' . htmlentities($threadName['thName'], ENT_QUOTES, 'UTF-8') . '</a>';
								}
							}
						}
					}
					echo "</th></table>";
				}
			?>
		</div>	
	</body>
</html>