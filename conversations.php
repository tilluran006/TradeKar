<?php
if(!isset($_COOKIE['id'])) {
	header('Location: index.php');
	die();
}
$conn = new mysqli('127.0.0.1', 'root', 'password', 'Project');
$details = array();
$user = $_COOKIE['id'];
/* This sets the primary fields*/
$stmt = "SELECT convId, suser userid, last_sent FROM Conversations WHERE fuser = $user ORDER BY last_sent DESC";
$results = $conn->query($stmt);
while($result = $results->fetch_assoc()) {
	array_push($details, $result);
}

$stmt = "SELECT convId, fuser userid, last_sent FROM Conversations WHERE suser = $user ORDER BY last_sent DESC";
$results = $conn->query($stmt);
while($result = $results->fetch_assoc()) {
	array_push($details, $result);
}

/////This gets the user names
for($i = 0; $i < count($details); $i++) {
	$row = $details[$i];
	$id = $row['userid'];
	$stmt = "SELECT name FROM Users WHERE id = $id";
	$result = $conn->query($stmt);
	$result = $result->fetch_assoc();
	$details[$i]['name'] = $result['name'];
}

///// This gets the top chat
for($i = 0; $i < count($details); $i++) {
	$row = $details[$i];
	$id = $row['convId'];
	$stmt = "SELECT userId, text FROM Messages WHERE convId = $id ORDER BY sent DESC";
	$result = $conn->query($stmt);
	$result = $result->fetch_assoc();
	if($result['userId'] == $user) {
		$details[$i]['text'] = 'Me: '.$result['text'];
	}else {
		$details[$i]['text'] = $row['name'].": ".$result['text'];
	}
}
$conn->close();

header("Content-type: text/xml");
echo "<?xml version='1.0'?>";
echo "<CONVERSATIONS>";
foreach ($details as $conver) {
	$id = $conver['convId'];
	echo "<CONVERSATION id='$id'>";
	echo "<SENDER>".$conver['userid']."</SENDER>";
	echo "<NAME>".$conver['name']."</NAME>";
	echo "<TEXT>".$conver['text']."</TEXT>";
	echo "<ACTIVE>".$conver['last_sent']."</ACTIVE>";
	echo "</CONVERSATION>";
}
echo "</CONVERSATIONS>";
?>