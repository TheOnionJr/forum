<?php 

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
		<div id="header">
	    	<a href="/index.php"><img src="/img/white_logo_transparent.png" align="left" ></a>
	    	<div id="login">
		    	<?php if (!isset($_SESSION['username'])) : ?>
		    		<form method="post" action="index.php">
			    		<?php
			    			$errorpath = $_SERVER['DOCUMENT_ROOT']; //Find the document root
			    			$errorpath .= "/view/errors.php"; 		//Set absolute path
			    			include($errorpath) 
			   			?>
			       		Username: <input type="text" name="username"><br>
			       		Password: <input type="password" name="password"><br>
			       		<a href="/view/registerview.php" id="register">Register</a> 
			        	<button type="submit" class="login_button" name="login_user">Login</button> 
					</form>
				<?php endif ?> 
				<?php if (isset($_SESSION['username'])) : ?>
	   		 		<p> Logged in as <strong><?php echo $_SESSION['username']; ?></strong></p>
	    			<p> <a href="index.php?logout='1'" style="color: red;">Logout</a></p>
	    		<?php endif ?>
			</div> 	
		</div>	
	</body>
</html>