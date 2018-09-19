<div id="wrapper">
    <div id="header">
        <a href="index.php"><img src="img/white_logo_transparent.png" align="left" ></a>
        <div id="login">
            Username: <input type="text" name="username"><br>
            Password: <input type="password" name="password"><br>
            <a href="view/registerview.php" id="register">Register</a>		
        </div>
    </div>
    <table>
    	<?php
			$con=mysqli_connect("localhost","guest","","forum");
			// Check connection
			if (mysqli_connect_errno()){
				echo "Failed to connect to MySQL: " . mysqli_connect_error();
			}

			$result = mysqli_query($con,"SELECT * FROM subforums");

			echo "<table border='1'>
			<tr>
				<th>Subforum</th>
				<th>Topics</th>
				<th>Posts</th>
			</tr>";

			while($row = mysqli_fetch_array($result))
			{
				echo "<tr>";
				echo "<td>" . $row['sName'] . "</td>";
				echo "<td>" . $row['sNumTopics'] . "</td>";
				echo "<td>" . $row['sNumPosts'] . "</td>";
				echo "</tr>";
			}	
			echo "</table>";

			mysqli_close($con);
		?>
    </table>
</div>	

