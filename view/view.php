<?php
#include_once("view/forumview.php");

	abstract Class View {
		public function create() {
			echo <<<HTML
<!DOCTYPE html>
<html>
<head>
<title>
HTML;
	echo "Title";
	echo <<<HTML
</title>
</head>

<body>
<h1>
HTML;
	echo "ECHOOO";
	echo <<<<HTML
</h1>
</body>
</html>
HTML;
		}
	}

?>
