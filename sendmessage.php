<?php

if(!isset($_COOKIE['id'])) {
	header('Location: index.php');
	die();
}

$convid = htmlspecialchars($_POST['convid']);
$msg = htmlspecialchars($_POST['msg']);
$conn = new mysqli('127.0.0.1', 'root', 'password', 'Project');
$stmt = $conn->prepare('CALL sendConvMsg(?, ?, ?);');
$stmt->bind_param('sis', $_COOKIE['id'], $convid, $msg);
$stmt->execute();
$results = $stmt->get_result();
$results = $results->fetch_assoc();
$conn->close();
echo $results['msgId'].",".$results['sent'];
?>