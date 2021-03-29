<?php
require_once('configmsgbrd.php');

$u = $dbc -> real_escape_string($_POST["user_id"]);
$tid = $dbc -> real_escape_string($_POST["topic_id"]);
$mt = $dbc -> real_escape_string($_POST["comment"]);
$pid = $dbc -> real_escape_string($_POST["parent_id"]);
$mb = $dbc -> real_escape_string($_POST["mess_block"]);

$query1 = "SELECT user_id FROM users WHERE (user_id='$u') ";

$result2 = mysqli_query($dbc, $query1);

if (!$result2) {
	die("Connection failed ".mysqli_connect_error());
}
$rows = mysqli_num_rows($result2);

if ($rows == 1) {

	$query2 = "INSERT INTO message (user_id, topic_id, message_txt, date, parent_id, mess_block) VALUES ('$u', '$tid', '$mt', NOW(), '$pid', '$mb')";

	$result2 = mysqli_query($dbc, $query2);

	echo "Comment Has Been Submitted";
	exit();
	mysqli_close($dbc); // Close the database connection

} else {
	echo "Comment Has Been Declined";
	exit();
	mysqli_close($dbc); // Close the database connection
}
?>
