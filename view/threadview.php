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
			$model = $_SERVER['DOCUMENT_ROOT']; 	//Find the document root
			$model .= "/model/modelthread.php"; 	//Set absolute path
			include($model);
			$errorpath = $_SERVER['DOCUMENT_ROOT']; //Find the document root
			$errorpath .= "/view/errorsthread.php"; 		//Set absolute path
			include($errorpath); 
		?>
	</table>
</div>

