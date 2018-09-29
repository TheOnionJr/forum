<?php
$path = $_SERVER['DOCUMENT_ROOT']; 	//Find the document root
$path .= "/view/view.php"; 			//Set absolute path
include($path);
$topic=htmlentities($_GET['tID'], ENT_QUOTES, 'UTF-8');
$subforum=htmlentities($_GET['sID'], ENT_QUOTES, 'UTF-8');
$path = $_SERVER['DOCUMENT_ROOT'];  //Find the document root
$path .= "/model/modelNewThread.php";      //Set absolute path
include($path);
?>

<?php echo "<form method=\"post\" action=\"modelNewThread.php?tID=" . $topic . "&sID=" . $subforum . ">"?>
	<div class="input-group"> 
  	  <label>Title</label>
  	  <input type="text" name="title" value="">
  	</div>
  	<div class="input-group"> 
  	  <label>Text</label>
  	  <input type="text" name="text" value="">
  	</div>
  	<div class="input-group">
  		<button type="submit" class="btn" name="new_thread">Post</button>
  	</div>
</form>
