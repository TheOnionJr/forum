<?php 
$path = $_SERVER['DOCUMENT_ROOT'];  //Find the document root
$path .= "/model/modelregister.php";      //Set absolute path
include($path);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Registration system</title>
  <link rel="stylesheet" type="text/css" href="/css/register.css">
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
- At least one capital letter <br>
- At least one small letter <br>
- At least one special character <br>
- At least one digit
</div>
  	<div class="input-group">
  	  <button type="submit" class="btn" name="reg_user">Register</button>
  	</div>
    
  	<p>
  		<a href="/index.php">Homepage</a>
  	</p>
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

