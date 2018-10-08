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
echo "<form method=\"post\" action=\"/view/postview.php?tID=" . $tID . "&sID=" . $sID . "\">" 
echo "<input type='hidden' name='csrfToken' value='<?php echo($_SESSION['csrfTOken']) ?>' /> </form>"
?>
<?php //echo "<a href=\"/view/postview.php?tID=" . $tID . "&sID=" . $sID . "\" id=\"newThread\">New Thread</a>" ?>

<?php //echo <form method=\"post\" action=\"/model/modelNewThread.php?tID=" . $topic . "&sID=" . $subforum . ">"
?>

	<div class="input-group"> 
  	  <label>Title</label>
  	  <input type="text" name="title" value="">
  	</div>

	<script>											//  Function for displaying textbox.
						function textbox(ID) {
							var x = document.getElementById(ID);
							if (x.style.display === "none") {
								x.style.display = "block";
							} else {
								x.style.display = "none";
							}
						}
					</script>


	<div id=0 style="Display:none">		
						<form method="post">
						Text
						<textarea id="CBox" name="text" type="text" > </textarea>											  
							<button type="submit" class="btn" name="new_thread">Post</button>
						</form>
						<style> 
						form[name=threadform] {
						    display:block;
						    margin:0px;
						    padding:0px;
						}
						</style>
					 </div>

	<?php
	echo "<script> textbox(0); </script>";
	?>
					

    <?php
    $errorpath = $_SERVER['DOCUMENT_ROOT']; //Find the document root
    $errorpath .= "/view/errors.php";     //Set absolute path
    include($errorpath);
    ?>
    <input type='hidden' name='csrfToken' value='<?php echo($_SESSION['csrfTOken']) ?>' />
</form>
