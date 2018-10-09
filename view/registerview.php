<?php 
error_reporting(0);
$path = $_SERVER['DOCUMENT_ROOT'];  //Find the document root
$path .= "/model/modelregister.php";      //Set absolute path
include($path);
?>
<!DOCTYPE html>
<html>
<head>
  <title>ROSCIS Register</title>
  <link rel="stylesheet" type="text/css" href="/css/register.css">
  <!-- Header related security: -->
    <?php
      header('X-XSS-Protection: 1; mode=block'); //Browser XXS protection
      header('X-Frame-Options: DENY'); 
      header('X-Content-Type-Options: nosniff');
    ?>
    <title> ROSCIS Forum</title>
    <style id="antiClickjack">body{display:none !important;}</style>
    <script type="text/javascript">
        if (self === top) {
            var antiClickjack = document.getElementById("antiClickjack");
            antiClickjack.parentNode.removeChild(antiClickjack);
        } else {
            top.location = self.location;
        }
    </script>
    <!-- End header related security -->
</head>
<body>
  <div class="header">
  	<h2>Register</h2>
  </div>

  <form method="post" action="registerview.php">
  	<?php 

    $path = $_SERVER['DOCUMENT_ROOT'];  //Find the document root
    $path .= "/view/errors.php";      //Set absolute path
    include($path);
    
    ?>
  	<div class="input-group"> 
  	  <label>Username</label>
  	  <input type="text" name="username" value="<?php echo $username; ?>">
  	</div>
  	<div class="input-group">
  	  <label>Email</label>
  	  <input type="text" name="email" value="<?php echo $email; ?>">
  	</div>
  	<div class="input-group">
  	  <label>Password</label>
  	  <input type="password" name="password_1">
  	</div>
  	<div class="input-group">
  	  <label>Confirm password</label>
  	  <input type="password" name="password_2">
  	</div>
    <p> Click <b onclick="myFunction()">here</b> for the Password Policy</p>  

<div id="policy" style="Display:none">
 <br>
- The password needs to be between 8 and 128 characters <br><br>
<b>You need to have at least 3 out of the following:</b> <br>
- Password needs at least one capital letter <br>
- Password needs at least one small letter <br>
- Password needs at least one special character <br>
- Password needs at least one digit
</div>
  	<div class="input-group">
  	  <button type="submit" class="btn" name="reg_user">Register</button>
  	</div>
    
  	<p>
  		<a href="/index.php">Homepage</a>
  	</p>
    <input type='hidden' name='csrfToken' value='<?php echo($_SESSION['csrfTOken']) ?>' />
  </form>
  <script>
function myFunction() {
    var x = document.getElementById("policy");
    if (x.style.display === "none") {
        x.style.display = "block";
    } else {
        x.style.display = "none";
        
    }
}
</script>
</body>
</html>

