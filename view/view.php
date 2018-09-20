<?php 
include('model/modellogin.php');


if (isset($_GET['logout'])) {
	session_destroy();
	unset($_SESSION['username']);
	header("location: index.php");
}

?>
<html>
	<head>
		<!-- Global site tag (gtag.js) - Google Analytics -->
		<script async src="https://www.googletagmanager.com/gtag/js?id=UA-125913159-1"></script>
		<script>
			window.dataLayer = window.dataLayer || [];
			function gtag(){dataLayer.push(arguments);}
			gtag('js', new Date());
				gtag('config', 'UA-125913159-1');
		</script>
		<link type="text/css" id="dark-mode" rel="stylesheet" href="/css/darkmode.css">
	</head>
	<body>
	<div id="header">
	    <a href="/index.php"><img src="/img/white_logo_transparent.png" align="left" ></a>
	    <form method="post" action="index.php">
		    <?php include('view/errors.php') ?>
		    <div id="login">
		        Username: <input type="text" name="username"><br>
		        Password: <input type="password" name="password"><br>
		       
		        <button type="submit" class="login_button" name="login_user">Login</button>
		    </div>
		</form>
	    <a href="/view/registerview.php" id="register">Register</a>	
	    <?php if (isset($_SESSION['username'])) : ?>
	    	<p> Logged in as <strong><?php echo $_SESSION['username']; ?></strong></p>
	    	<p> <a href="index.php?logout='1'" style="color: red;">Logout</a></p>
	    <?php endif ?>	
	</div>	
	</body>
</html>