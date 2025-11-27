<?php
	//if the form has been submitted
	if (isset($_POST['submitted'])){
		if ( !isset($_POST['username'], $_POST['password']) ) {
		// Could not get the data that should have been sent.
		 exit('Please fill both the username and password fields!');
	    }
		// connect DB
		session_start();
		require_once ("config.php");
		try {
			$stat = $db->prepare('SELECT uid, password FROM user WHERE username = ?');
			$stat->execute(array($_POST['username']));
		    
			// fetch the result row and check 
			if ($stat->rowCount()>0){  // matching username
				$row=$stat->fetch(); 

				if (password_verify($_POST['password'], $row['password'])){ //matching password
					$_SESSION["uid"]=$row['uid'];
					//??recording the user session variable and go to loggedin page?? 
					$_SESSION["username"]=$_POST['username'];
					header('Location:Home.html');
					exit();
				
				} else {
				 echo "<p style='color:red'>Error logging in, password does not match </p>";
 			    }
		    } else {
			 //else display an error
			  echo "<p style='color:red'>Error logging in, Username not found </p>";
		    }
		}
		catch(PDOException $ex) {
			echo("Failed to connect to the database.<br>");
			echo($ex->getMessage());
			exit;
		}

  }
?>

<html>
<head>
	<title>Login</title>

</head>

	<h2>Login</h2>
	<script src="login.js"></script>


<body>
<!-- a HTML form that allows the user to enter their username and password for log in.-->
<form action="Login.html" method="post">

	<label>User Name</label>
	<input type="text" name="username" id="username" size="15" maxlength="25" />
    <label>Password:</label>
	<input type="password" name="password" id = "password" size="15" maxlength="25" />

	<input type="submit" value="Login" />
	<input type="reset" value="clear"/>
    <input type="hidden" name="submitted" value="TRUE" />
	<p>
		Not a registered user? <a href="register.php">Register</a>
	</p>

</form>
</body>
</html>
