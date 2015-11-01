<?php
if(!isset($_COOKIE['id']) || !isset($_GET['convid'])) {
	header('Location: index.php');
	die();
}

$user = $_COOKIE['id'];
$convid = htmlspecialchars($_GET['convid']);
$conn = new mysqli('127.0.0.1', 'root', 'password', 'Project');
$stmt = $conn->prepare("SELECT * FROM Messages WHERE convId = ? ORDER BY sent ASC");
$stmt->bind_param('i', $convid);
$stmt->execute();
$results = $stmt->get_result();
$output = array();

while($result = $results->fetch_assoc()) {
	array_push($output, $result);
}

header('Content-type: text/xml');
echo "<?xml version='1.0' ?>";
echo "<MESSAGES>";
foreach ($results as $row) {
	$id = $row['msgId'];
	echo "<MESSAGE id='$id'>";
	if($row['userId'] == $user) 
		echo "<ME>".$row['text']."</ME>";
	else
		echo "<OTHER>".$row['text']."</OTHER>";
	echo "<TIME>".$row['sent']."</TIME>";
	echo "</MESSAGE>";
}
echo "</MESSAGES>"
?>