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
		<link type="text/css" id="dark-mode" rel="stylesheet" href="css/darkmode.css">
	</head>
	<body>
		<?php
			include_once("controller/controller.php");
			$controller = new controller();
			$controller->invoke();
		?>
	</body>
</html>
