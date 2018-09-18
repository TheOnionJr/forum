<?php
include_once("view/forumview.php");

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
</html>
		}
	}

?>
