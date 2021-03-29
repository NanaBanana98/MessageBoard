<?php
	// Initialize a session.

	session_start();

    require_once("configmsgbrd.php");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"

   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">

  <head>

    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />

    <title>You Logged In</title>

		<style>

		body {
			background-color: #FFF;
		}

		#header {
			background-color: #006;
			color: #FFF;
			padding: 20px;
		}

		#footer {
			background-color: #006;
			color: #FFF;
			padding: 20px;
		}

		#lypsum {
			padding: 15px;
			background-color: #FAF0E6;
			margin-right: 230px;
			margin-top: 5px;
		}

		#login {
			padding: 10px;
			padding-bottom: 15px;
			border: None;
			background-color: CornSilk;
			width: 200px;
			text-align: left;
			float: right;
			margin-left: 10px;
			margin-right: 5px;
			margin-top: 5px;
		}

		</style>
  </head>
  <body>

     <div id="main">

     <?php
      // if (isset($_POST['submitted'])) {

    ?>

    <?php

    	echo '<h3>Welcome';

		if (isset($_SESSION['username'])) {
			$sql = "SELECT tokenid from users where username = '$_SESSION[username]'";
			$result = mysql_query($sql) or trigger_error("You're not logged in");

			if (mysql_affected_rows() == 1) { // A match was made.

				$row = mysql_fetch_array ($result, MYSQL_NUM);
				mysql_free_result($result);
				mysqli_close(); // Close the database connection

				if($_SESSION['token_id'] == $row[0])
				{
					echo ", {$_SESSION['username']}!";
					$loggedin = 1;
				}
				else {
					echo ", You're not logged in";
					$loggedin = 0;
				}
			}
		}
		echo '</h3>';

		// Display links based upon the login status

		if (isset($_SESSION['username']) AND (substr($_SERVER['PHP_SELF'] AND $loggedin, -10) != 'logout.php')) {

			echo '<a href="./logout.php">Logout</a><br />
			<a href="./change_password.php">Change Password</a><br />';

		} else { //  Not logged in.

			echo '	<a href="./mbregister.php">Register</a><br />
			<a href="../msgbrd/mblogin.php">Login to your account</a><br />
			<a href="../forgot_password.php">Forgot Password</a><br />';

		}

		$listq = "SELECT m.subject, u.username, m.date, m.mess_id FROM message AS m INNER JOIN users AS u ON m.user_id = u.user_id GROUP BY(m.mess_id) ORDER BY m.date DESC";
		$result = mysqli_query($dbc, $listq);
		$rows = mysqli_num_rows($result);

		if($rows >= 1) {
			echo '<table>';
			while ($row = mysqli_fetch_assoc($result)) {

				echo '<tr><td align="left">' . $row['subject'] . '</td>';
				echo '<td align="left">' . $row['username'] . '</td>';
				echo '<td align="left">' . $row['date'] . '</td>';
				echo '<td align="left">' . $row['mess_id'] . '</td>';
			}
			echo '</tr></table><br />';
		} else {
			echo ' Why don\'t you write a message? <br />';
		}




	?>

	<?php

		if (isset($_SESSION['username'])) {

			echo '<form action="index.php" method="post">';

			echo '<p><b>Subject:</b> <input type="text" name="subject" size="40" maxlength="40"  /> </p>';

			echo '<p><b>Message:</b></p> <p><textarea cols=40 rows=10 name="message">Enter message here</textarea></p>';

			echo '<div align="center"><input type="submit" name="submit" value="Submit" /></div>';

			echo '<input type="hidden" name="submitted" value="TRUE" />';

			echo '</form>';

		} else {

			echo '<br />You must login to see the forum';
		}
	?>





  </body>
  </html>
