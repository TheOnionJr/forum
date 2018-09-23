<?php
$path = $_SERVER['DOCUMENT_ROOT']; 			//Find the document root
$path .= "/view/view.php"; 					//Set absolute path
include($path);
echo '<link rel="stylesheet" type="text/css" href="../css/forumview.css">'; #Loading the css for forumview
?>
<div id="content">
	<?php 
		$path = $_SERVER['DOCUMENT_ROOT'];  //Find the document root
		$path .= "/model/modelforum.php";   //Set absolute path
		include($path);						//Includes php code from modelforum.php
	?>
</div>



