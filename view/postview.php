<?php
$path = $_SERVER['DOCUMENT_ROOT']; 	//Find the document root
$path .= "/view/view.php"; 			//Set absolute path
include($path);

$topic=htmlentities($_GET['tID'], ENT_QUOTES, 'UTF-8');
$subforum=htmlentities($_GET['sID'], ENT_QUOTES, 'UTF-8');

$path = $_SERVER['DOCUMENT_ROOT'];  //Find the document root
$path .= "/model/modelNewThread.php";      //Set absolute path
include($path);

$tID = filter_input(INPUT_GET, 'tID', FILTER_VALIDATE_INT);
$sID = filter_input(INPUT_GET, 'sID', FILTER_VALIDATE_INT);
?>

<?php 
echo "<form method=\"post\" action=\"/view/postview.php?tID=" . $tID . "&sID=" . $sID . "\">";
$csrf = $_SESSION['csrfTOken'];
echo "<input type='hidden' name='csrfToken' value='" . $csrf . "' /> </form>";
?>
<?php //echo "<a href=\"/view/postview.php?tID=" . $tID . "&sID=" . $sID . "\" id=\"newThread\">New Thread</a>" ?>

<?php //echo <form method=\"post\" action=\"/model/modelNewThread.php?tID=" . $topic . "&sID=" . $subforum . ">"
?>

		<form method="post">
			<div class="input-group"> 
		  	  <label>Title</label>
		  	  <input type="text" name="title" value="">
		  	</div>
		Text
		<textarea id="CBox" name="text" type="text" > </textarea>											  
			<button type="submit" class="btn" name="new_thread">Post</button>
			<input type='hidden' name='csrfToken' value='<?php echo($_SESSION['csrfTOken']) ?>' />
		</form>

    <?php
$errorpath = $_SERVER['DOCUMENT_ROOT']; //Find the document root
$errorpath .= "/view/errorsthread.php"; 		//Set absolute path
include($errorpath); 
    ?>
   