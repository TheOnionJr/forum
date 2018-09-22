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
		<?php
			header("X-XSS-Protection: 1; mode=block"); //Browser XXS protection
		?>
		<link type="text/css" id="dark-mode" rel="stylesheet" href="/css/darkmode.css">
	</head>
	<body>
	<div id="header">
	    <a href="/index.php"><img src="/img/white_logo_transparent.png" align="left" ></a>
	    <form method="post" action="index.php">
		    <?php
		    	$errorpath = $_SERVER['DOCUMENT_ROOT']; //Find the document root
		    	$errorpath .= "/view/errors.php"; 		//Set absolute path
		    	include($errorpath) 
		    ?>
		    <?php if (!isset($_SESSION['username'])) : ?> 
		    	<div id="login">
		       		Username: <input type="text" name="username"><br>
		       		Password: <input type="password" name="password"><br>
		       		<a href="/view/registerview.php" id="register">Register</a> 
		        	<button type="submit" class="login_button" name="login_user">Login</button>
		    	</div>
		    <?php endif ?>
		</form>
	    <?php if (isset($_SESSION['username'])) : ?>
	    	<p> Logged in as <strong><?php echo $_SESSION['username']; ?></strong></p>
	    	<p> <a href="index.php?logout='1'" style="color: red;">Logout</a></p>
	    <?php endif ?>	
	</div>	
	</body>
</html>