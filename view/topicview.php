<?php
$path = $_SERVER['DOCUMENT_ROOT']; 	//Find the document root
$path .= "/view/view.php"; 			//Set absolute path
include($path);

echo '<link rel="stylesheet" type="text/css" href="../css/darkmode.css">'; #Loading the default darkmode view.
echo '<link rel="stylesheet" type="text/css" href="../css/topicview.css">'; #Loading the css for topicview.
$tID = filter_input(INPUT_GET, 'tID', FILTER_VALIDATE_INT);
$sID = filter_input(INPUT_GET, 'sID', FILTER_VALIDATE_INT);
?>

<div id="content">
	<?php echo "<a href=\"/view/postview.php?tID=" . $tID . "&sID=" . $sID . "\" id=\"newThread\"><button>New Thread</button></a>" ?>
	<table>
		<?php
			$model = $_SERVER['DOCUMENT_ROOT']; 	//Find the document root
			$model .= "/model/modeltopic.php"; 			//Set absolute path
			include($model);
		?>
	</table>
</div>

