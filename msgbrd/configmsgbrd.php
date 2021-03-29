<?php
	// Define these as constants so that they can't be changed
	DEFINE ('DBUSER', 'root');
	DEFINE ('DBPW', '');
	DEFINE ('DBHOST', 'root');
	DEFINE ('DBNAME', 'msgbrd');

	$dbhServer = "localhost";
	$dbUsername = "root";
	$dbPassword = "";
	$dbName = "msgbrd";

	$dbc = mysqli_connect($dbhServer,$dbUsername,$dbPassword,$dbName);

	if(!$dbc){
	  die("Connection failed ".mysqli_connect_error());
	}



	// A function that strips harmful data.
function escape_data ($data) {

	// Check for mysql_real_escape_string() support.
	// This function escapes characters that could be used for sql injection
	if (function_exists('mysql_real_escape_string')) {
		global $dbc; // Need the connection.
		$data = mysql_real_escape_string (trim($data), $dbc);
		$data = strip_tags($data);
	} else {
		$data = mysql_escape_string (trim($data));
		$data = strip_tags($data);
	}

	// Return the escaped value.
	return $data;

}

function retrieve_messages() {

	global $dbc;

	$query = "select m.subject, m.message_txt, u.username, m.date, m.parent_id, m.user_id, m.mess_block, m.topic_id, m.user_id from message AS m, users AS u where (m.user_id = u.user_id) order by m.mess_block,  m.date;";

	$result = mysqli_query($dbc, $query);

	if (!$result) {
		die("Connection failed ".mysqli_connect_error());
}
$rows = mysqli_num_rows($result);

	if($rows >= 0) {
				$tagSwitch = FALSE;
				while ($messages = mysqli_fetch_assoc($result)) {
					if ($messages['parent_id'] == 0)
					{
						($tagSwitch) ? print '</div>' : print '<div class="commentBox">';
						$tagSwitch = !$tagSwitch;
						echo "<p><h3>{$messages['subject']}</h3></p>";
						echo "<p>{$messages['message_txt']}</p>";
						echo "<br />via: {$messages['username']} <a href='#' class='getComments'>Comments</a><hr />";
						echo "<div class='comments'>";
						echo "<form id='theForm'>";
						echo "<textarea name='comment' class='comment' cols=60 rows=10>Enter Comment...</textarea><br />";

						echo "<input type=hidden name='username' value={$messages['username']}>";

						echo "<input type=hidden name='subject' value={$messages['subject']}>";

						echo "<input type=hidden name='parent_id' value=1>";

						echo "<input type=hidden name='mess_block' value={$messages['mess_block']}>";

						echo "<input type=hidden name='token_id' >";

						echo "<input type=hidden name='topic_id' value={$messages['topic_id']}>";

						echo "<input type=hidden name='user_id' value={$messages['user_id']}>";

						echo "<button type='button' id='oneButton'>Post Comment</button></form></div>";

					} else {
					echo "<div class='comments'>";
					echo "{$messages['message_txt']}<br />";
					echo "via: {$messages['username']}<br /><hr />";
					echo "</div>";
					}
				}
			}

}

?>
