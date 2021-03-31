<?php
	include('header.html');
?>

<style>
#recaptcha_image img {
      width: 185px;
      height: 28.5px;
      border: 1px solid gainsboro;
    }
#recaptcha_widget {
    	height:400;
    }
</style>



<?php

      if (isset($_POST['submitted'])) { // Check if the form has been submitted.
				//get user info
				$username = $_POST['userid'];
				$password = $_POST['pass'];

				  //check if empty feilds
				  if( empty($username) || empty($password)){
						$error = 'Please fill in all fields.';
				    header("Location: ./mbforum.php?error=emptyfeilds&errorMsg=".$error);
				    exit();
				  }
				  //check that info matches that in database
				  else{
				    //allow login with username or email
				    $sql = "SELECT * FROM users WHERE username=?;";

				    $stmt = mysqli_stmt_init($dbc);



				    //check if conncetion is est
				    if(!mysqli_stmt_prepare($stmt,$sql))
				    {
							$error = 'We cannot authenticate user. System error.';
				      header("Location: ../index.php?error=sqlerror&errorMsg=".$error);
				      exit();
				    }
				    //if connect sucessfully
				    else {
				      //send info to database
							mysqli_stmt_bind_param($stmt,"s",$username);
				      mysqli_stmt_execute($stmt);
				      $result = mysqli_stmt_get_result($stmt);
				      if ($row = mysqli_fetch_assoc($result)) {
				        //check pasword matches
				        $passCheck = password_verify($row['passwd']);
				        //if wrong user
				        if($passCheck == false)
				        {
									$error = 'Your password is incorrect. Click Forgot Password to reset it.';
									header("Location: ./mbforum.php?error=incorrectpassword&errorMsg=".$error);
				          exit();
				        }
				        //evreything is correct! login user
				        elseif($passCheck == true){
				          //Create a session
				          session_start();
				          $_SESSION['userid'] = $row['user_id'];
				          $_SESSION['username'] = $row['userName'];
									$_SESSION['first_name'] = $row["first_name"];
									$_SESSION['last_name'] = $row["last_name"];

				          header("Location: ./mbforum.php");
				          exit();
				        }
				        else{
									$error = 'Your password is incorrect. Click Forgot Password to reset it.';
									header("Location: ./mbforum.php?error=incorrectpassword&errorMsg=".$error);
				          exit();
				        }
				      }
				      //otherwise error
				      else{
								$error = 'User does not exist. Please register.';
				        header("Location: ./mbforum.php?error=userdne&errorMsg=".$error);

				      }
				    }
				  }
	} // End of SUBMIT

?>

<body>
	<div id="header"><h2>Message Board</h2></div>

     <div id="login">

    <?php
    	echo '<h1>Welcome';

		if (isset($_SESSION['first_name'])) {
			echo ", {$_SESSION['first_name']}!";
		}
		echo '</h1>';

		// Display links based upon the login status
		// If user is on the logout page disable the login

		if (isset($_SESSION['userid']) AND (substr($_SERVER['PHP_SELF'], -10) != 'logout.php')) {

			echo '<a href="logout.php">Logout</a><br />
			<a href="change_password.php">Change Password</a><br />';

		} else { //  Not logged in.

			echo "

				<form action='mblogin.php' method='post'>
				<p><b>Userid:</b> <input type='text' name='userid' size='20' maxlength='20' /></p>
				<p><b>Password:</b> <input type='password' name='pass' size='16' maxlength='30' /></p>";



    		echo '	<div align="center"><input type="submit" name="submit" value="Login" /></div>

					<input type="hidden" name="submitted" value="TRUE" />';

			echo '<a href="register.php">Register</a><br />
			<a href="forgot_password.php">Forgot Password</a><br />';

	}
	?>

    </div>

  </body>

</html>
