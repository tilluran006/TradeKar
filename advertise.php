<?php

if(!isset($_COOKIE['id'])){
	header('Location: index.php');
	die();
}

if(!isset($_COOKIE['advert'])) {
	setcookie('advert', '0',  time() + (3600 * 24), '/');
}

$pid = intval($_COOKIE['advert']);

if($pid > 1000) {
	$pid = 0;
}

$pid++;

setcookie('advert', "$pid",  time() + (3600 * 24), '/');

$servername = "127.0.0.1";
$username = "root";
$userpassword = "password";
$dbname = "Project";
$tablename = "Advertise";

$conn = new mysqli($servername, $username, $userpassword, $dbname);
$results = $conn->query("select productId from Advertise where expire > now()");
$count = $results->num_rows;
$i = 0;
$result = "";
while($i <= $pid % $count) {
$result = $results->fetch_assoc();
$i++;
}

$pid = $result['productId'];

$results = $conn->query("select * from Products where productId = '$pid'");
$results = $results->fetch_assoc();
$image = $results['image'];
$name = $results['name'];
$price = intval($results['price']);
if($results['new'] == '1') {
	$new = 'NEW';
	$price = "Rs. $price";
}else {
	$new = 'At';
	$oprice = intval($results['oPrice']);
	if($oprice > $price) {
		$price = ($oprice - $price) * 100/$oprice;
		$price = round($price);
		$price = "$price% OFF"; 
	}else {
		$price = "Rs. $price";
	}
}

$text = "
	<PRODUCT>
		<ID>$pid</ID>
		<NAME>$name</NAME>
		<IMAGE>$image</IMAGE>
		<DESCRIPTION>$new</DESCRIPTION>
		<OFFER>$price</OFFER>
	</PRODUCT>
";

header('Content-Type: text/xml');
echo "<?xml version='1.0' encoding='UTF-8'?>";
echo $text;
$conn->close();
?>