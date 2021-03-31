<?php
	include('header.html');
?>


<link href="./css/master.css" rel="stylesheet" type="text/css"/>

<style>
<?php
if(isset( $_SESSION['first_name']))
{
	include "./css/mbforum.css";
}
 ?>
</style>
<link rel="stylesheet" href="./css/mbforum.css">



<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>

<script type="text/javascript">

 $("document").ready(function() {

 	$('#oneButton').bind('click',sendInfoToServer);

 	$('div.comments').hide();

 	$('a.getComments').click(function() {

		$(this).parents().siblings('div.comments').toggle();
		// $('div.comments').toggle();

		return false;

		});

	function sendInfoToServer() {

		$('span.commentSent').load('sendInfoToServer.php',
		$('#theForm').serializeArray());
	}

});
</script>

<?php

      if (isset($_POST['submitted'])) { // Check if the form has been submitted.

		// Security check for a valid username
		if (preg_match ('%^[A-Za-z0-9]\S{8,20}$%', stripslashes(trim($_POST['userid'])))) {

			// Scrub username with function in header.php
			$u = $dbc -> real_escape_string($_POST['userid']);

		} else {

			$u = FALSE;

			echo '<p><font color="red" size="+1">Please enter a valid User ID!</font></p>';
		}

		// Security check for a valid password

		if (preg_match ('%^[A-Za-z0-9]\S{8,20}$%', stripslashes(trim($_POST['pass'])))) {

			// Scrub password with function in header.php
			$p = $dbc -> real_escape_string($_POST['pass']);

		} else {

			$p = FALSE;

			echo '<p><font color="red" size="+1">Please enter a valid Password!</font></p>';

		}

		// Query the database. Verify the username, password and captcha

		if ($u && $p ) {

			$query = "SELECT user_id, first_name, last_name, email, username, passwd, active FROM users WHERE username='$u' AND passwd=SHA('$p')";

			$result = mysqli_query ($dbc, $query);

			if (mysql_affected_rows() == 1) { // A match was made

				$row = mysql_fetch_array ($result, MYSQL_NUM);

				mysql_free_result($result);

				// If they haven't activated the account redirect
				if ($row[6] != NULL)
				{
					header("Location: http://localhost/msgbrd/mbforgotpass.php");
					mysqli_close($dbc); // Close the database connection
					exit();
				}

				$_SESSION['first_name'] = $row[1];

				$_SESSION['userid'] = $row[4];

				// Create Second Token for security

				$tokenId = rand(10000, 9999999);

				$query2 = "update users set tokenid = $tokenId where username = '$_SESSION[userid]'";

				$result2 = mysql_query ($query2);

				$_SESSION['token_id'] = $tokenId;

				// Reset session id for security
				session_regenerate_id();

				// Redirect the user
				header("Location: http://localhost/msgbrd/mblogin.php");
				mysqli_close($dbc); // Close the database connection
				exit();
			}

			} else { // No match was made.

			echo '<br><br><p><font color="red" size="+1">Either the Userid or Password are incorrect 2</font></p>';
			mysqli_close($dbc); // Close the database connection
			exit();
			}
	} // End of SUBMIT

?>

<body>


	<div id="wrap">
	<div id="header"><h2>Message Board</h2></div>

     <div id="login">

    <?php
    	echo '<h1>Welcome';

		if (isset($_SESSION['first_name'])) {
			echo ", {$_SESSION['first_name']}! ";
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

				<div class='form_item'>
					<p><b>Username:</b></p> <input type='text' name='userid' size='20' maxlength='20' />
				</div>

				<div class='form_item'>
					<p><b>Password:</b></p> <input type='password' name='pass' size='16' maxlength='30' />
				</div>";



    		echo "<div class='form_item' align='left'><input class='btn' type='submit' name='submit' value='Login' /></div>
				<input type='hidden' name='submitted' value='TRUE' />
				</form>";

			echo '<a href="mbregister.php">Register</a><br />
			<a href="forgot_password.php">Forgot Password</a><br />';

	}
	?>

    </div>

    <div id="main">

    <span class='commentSent'></span>

    <?php

		if (isset($_SESSION['first_name'])) {
			retrieve_messages();
		}
		else{

			if(isset($_GET['errorMsg']))
			{
				echo "<p class='error'>".$_GET['errorMsg']."</p>";
			}

			echo "<p>Please sign in or create an account to use the message board.</p>";

		}



	?>

	</div>
</div>




  </body>

</html>
