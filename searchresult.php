<?php

if(!isset($_COOKIE['id']) || !isset($_GET['q'])) {
	header('Location: index.php');
	die();
}

$servername = "127.0.0.1";
$username = "root";
$userpassword = "password";
$dbname = "Project";
$tablename = "Advertise";


$query = htmlspecialchars($_GET['q']);
$output = array();
$query = "%$query%";

$conn = new mysqli($servername, $username, $userpassword, $dbname);

$stmt = $conn->prepare("select name from Available_view where name like ?");
$stmt->bind_param('s', $query);
$stmt->execute();
$results = $stmt->get_result();
$count = $results->num_rows;

while($res = $results->fetch_array()) {
	if(!in_array($res[0], $output)) {
		array_push($output, $res[0]);
	}	
}

$stmt = $conn->prepare("select type from Available_view where type like ?");
$stmt->bind_param('s', $query);
$stmt->execute();
$results = $stmt->get_result();
$count = $results->num_rows;

while($res = $results->fetch_array()) {
	if(!in_array($res[0], $output)) {
		array_push($output, $res[0]);
	}	
}

$stmt = $conn->prepare("select brandName from Available_view where brandName like ?");
$stmt->bind_param('s', $query);
$stmt->execute();
$results = $stmt->get_result();
$count = $results->num_rows;

while($res = $results->fetch_array()) {
	if(!in_array($res[0], $output)) {
		array_push($output, $res[0]);
	}	
}

array_push($output, 'No more results');

$conn->close();
echo implode(',', $output);
?>