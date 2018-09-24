<?php 
$con=mysqli_connect("localhost","user","","forum");
$username=filter_var($_SESSION['username'], FILTER_SANITIZE_STRING);
$text=htmlentities($_POST['content'], ENT_QUOTES, 'UTF-8');
//$timestamp= Figure out how timestamps work...
//More to follow... -Sander


 ?>