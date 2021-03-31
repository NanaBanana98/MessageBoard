<?php
include ('header.html');
?>
<link rel="stylesheet" href="./css/register.css">

<body>

<div id="header"><h1>Message Board</h1></div>

<div id="login">
	<p>
    <?php

	// Welcome the user (by name if they are logged in).

	echo '<h4>Welcome';

	if (isset($_SESSION['first_name'])) {

	    echo ", {$_SESSION['first_name']}!";
	}

	echo '</h4>';

	// Display links based upon the login status

	if (isset($_SESSION['user_id']) AND (substr($_SERVER['PHP_SELF'], -10) != 'logout.php')) {

	echo '<a href="logout.php">Logout</a><br />

	<a href="change_password.php">Change Password</a><br />';

	} else { //  Not logged in.

	echo '	<a href="mbregister.php">Register</a><br />

	<a href="mbforum.php">Login to your account</a><br />

	<a href="forgot_password.php">Forgot Password</a><br />';

	}

    ?>
    </p>


</div>

<div id="lypsum">
      <?php

      if (isset($_POST['submitted'])) { // Handle the form.

       // Check for a valid first name

	if (preg_match ('%^[-_a-zA-Z ]{2,20}$%', stripslashes(trim($_POST['firstname'])))) {

	 $fn = $dbc -> real_escape_string($_POST['firstname']);


	} else {

		$ui = FALSE;

		echo '<p><font color="red" size="+1">Please enter a valid first name!</font></p>';

	}

      // Check for a valid last name

	if (preg_match ('%^[-_a-zA-Z ]{2,30}$%', stripslashes(trim($_POST['lastname'])))) {

	 $ln = $dbc -> real_escape_string($_POST['lastname']);


	} else {

		$ui = FALSE;

		echo '<p><font color="red" size="+1">Please enter a valid last name!</font></p>';

	}

	// Check for an email address.

	if (preg_match ('%^[A-Za-z0-9._\%-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$%', stripslashes(trim($_POST['email'])))) {

		$e = $dbc -> real_escape_string($_POST['email']);

	} else {

		$e = FALSE;

		echo '<p><font color="red" size="+1">Please enter a valid email address!</font></p>';

	}

	// Check for a valid username

	if (preg_match ('%\A(?=[-_a-zA-Z0-9]*?[A-Z])(?=[-_a-zA-Z0-9]*?[a-z])(?=[-_a-zA-Z0-9]*?[0-9])\S{8,}\z%', stripslashes(trim($_POST['userid'])))) {

	 $ui = $dbc -> real_escape_string($_POST['userid']);


	} else {

		$ui = FALSE;

		echo '<p><font color="red" size="+1">Please enter a valid userid!</font></p>';

	}

	// Check for a password and match against the confirmed password.

	if (preg_match ('%\A(?=[-_a-zA-Z0-9]*?[A-Z])(?=[-_a-zA-Z0-9]*?[a-z])(?=[-_a-zA-Z0-9]*?[0-9])\S{8,}\z%', stripslashes(trim($_POST['password1'])))) {

		if (($_POST['password1'] == $_POST['password2']) && ($_POST['password1'] != $_POST['userid'])) {

			$p = $dbc -> real_escape_string($_POST['password1']);

		} elseif ($_POST['password1'] == $_POST['userid']) {
			$p = FALSE;

			echo '<p><font color="red" size="+1">Your password cannot be the same as the userid!</font></p>';
		} else {
			$p = FALSE;

			echo '<p><font color="red" size="+1">Your password did not match the confirmed password!</font></p>';

		}

	} else {

		$p = FALSE;

		echo '<p><font color="red" size="+1">Please enter a valid password!</font></p>';

	}

	// PHP Code for the CAPTCHA System
	if ($fn && $ln && $e && $p && $ui ) { // If everything's OK.

		// Make sure the userid is available.

		$query = "SELECT username FROM users WHERE username='$ui'";

		$result = mysqli_query($dbc, $query);
		$rows = mysqli_num_rows($result);

		if ($rows == 0) { // Available.

			// Create the activation code.
			// Create a random number with rand.
			// Use it as a seed for uniqid, which when set to true generates a random number 23 digits in length
			// Use it to seed md5 that creates a random string 32 characters in length

			$a = md5(uniqid(rand(), true));	//token

			// Add the user. By entering values in a different order from the form sql injection can be limited

			$query = "INSERT INTO users (first_name, last_name, email, passwd, active, username) VALUES (?,?,?,?,?,?)";

			// By using mysql_query I can make sure only one query is submitted blocking sql injection
			// Never use the php multi_query function
			$stmt = mysqli_stmt_init($dbc);



			//check if conncetion is est
			if(!mysqli_stmt_prepare($stmt,$query))
			{
				header("Location: ../index.php?error=sqlerror");
				exit();
			}
			$hashedPwd = password_hash($p, PASSWORD_DEFAULT);
			mysqli_stmt_bind_param($stmt,"ssssss",$fn, $ln, $e, $hashedPwd, $a, $ui);
			mysqli_stmt_execute($stmt);

			// Check that the effected rows was equal to 1 in the last query. Should log if greater than
			if (mysqli_stmt_affected_rows($stmt) == 1) { // If it ran OK.

				// Send the email.

				$body = "Thank you for registering. To activate your account, please click on this link:<br />";

				// mysql_insert_id() retrieves the value of the last auto_incremented id
				// Attach the random activation code in the link sent to the email
				//$body .= "http://localhost/msgbrd/mbactivate.php?x=" . mysql_insert_id() . "&y=$a";

				//mail($_POST['email'], 'Registration Confirmation', $body, 'From: derekbanas@verizon.net');


				// Finish the page.

				echo '<br /><br /><h3>Thank you for registering! Click on the link to login.</h3>';

				exit();

			} else { // If it did not run OK.

				echo '<p><font color="red" size="+1">You could not be registered due to a system error. We apologize for any inconvenience.</font></p>';

			}

		} else { // The email address is not available.

			echo '<p><font color="red" size="+1">That email address has already been registered. If you have forgotten your password, use the link to have your password sent to you.</font></p>';

		}


	} else { // If one of the data tests failed.

		echo '<p><font color="red" size="+1">Please try again.</font></p>';

	}


} // End of the main Submit conditional.

?>

<h2>Register</h2>

<form action="mbregister.php" method="post">

	<fieldset>

	<div class="form_item">
	<p><b>First Name:</b> </p> <input type="text" name="firstname" size="20" maxlength="20" value="<?php if (isset($_POST['firstname'])) echo $_POST['firstname']; ?>" /> </div>

	<div class="form_item">
	<p><b>Last Name:</b> </p> <input type="text" name="lastname" size="30" maxlength="30" value="<?php if (isset($_POST['lastname'])) echo $_POST['lastname']; ?>" /> </div>

	<div class="form_item">
	<p><b>Email Address:</b> </p> <input type="text" name="email" size="40" maxlength="40" value="<?php if (isset($_POST['email'])) echo $_POST['email']; ?>" /> </div>

	<div class="form_item">
	<p><b>Username:</b> </p> <input type="text" name="userid" size="20" maxlength="20" /> <small>Must contain a letter of both cases, a number and a minimum length of 8 characters.</small></div>

	<div class="form_item">
	<p><b>Password:</b> </p> <input type="password" name="password1" size="20" maxlength="20" /> <small>Must contain a letter of both cases, a number and a minimum length of 8 characters.</small></div>

	<div class="form_item">
	<p><b>Confirm Password:</b> </p> <input type="password" name="password2" size="20" maxlength="20" /></div>



	</fieldset>

	<div class="form_item"align="center"><input  class="btn" type="submit" name="submit" value="Register" /></div>

	<input type="hidden" name="submitted" value="TRUE" />

</form>

    </div>

    <div id="footer"><h2>This is the Footer</h2></div>

  </body>

</html>
