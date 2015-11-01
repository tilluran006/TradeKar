<?php
$purchased = '';
$product_id = "";
if(!isset($_COOKIE['id'])) {
	header('Location: index.php');
	die();
}
if(isset($_GET['pid'])) {
$product_id = htmlspecialchars($_GET['pid']);
$tableName = "Products";

$conn = new mysqli('127.0.0.1', 'root', 'password', 'Project');
$stmt = $conn->prepare("select * from $tableName where productId = ?");
$stmt->bind_param('s', $product_id);
$stmt->execute();
$p_results = $stmt->get_result();
$p_results = $p_results->fetch_assoc();
if($p_results['available'] == "0") {
	$purchased = '';
}
else {
	$purchased = 'false';
}
$conn->close();
}
else {
	echo "<h1>Invalid URL</h1>";
	die();
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
	$pid = htmlspecialchars($_GET['pid']);
	$conn = new mysqli('127.0.0.1', 'root', 'password', 'Project');
	$stmt = $conn->prepare("call get_Seller(?)");
	$stmt->bind_param('s', $pid);
	$stmt->execute();
	$u_results = $stmt->get_result();
	$u_results = $u_results->fetch_assoc();

	$stmt = "select * from Users where id= ".$_COOKIE['id'];
	$stmt = $conn->query($stmt);
	$b_results = $stmt->fetch_assoc();

	$text = "Hey I want to buy your Product!!
	Product Details:
	PID: ".$p_results['productId']."
	Product Name: ".$p_results['name']."
	Selling Price: Rs.".$p_results['price']."

	My Details:
	Name: ".$b_results['name']."
	Email: ".$b_results['email']."
	Address: ".$b_results['address']."
	State: ".$b_results['state']."
	Pin: ".$b_results['pin']."
	Phone: ".$b_results['phone'];
	
	
	$stmt = "CALL sendNewMsg(".$b_results['id'].", ".$u_results['id'].", '$text')";
	$conn->query($stmt);

	$text = "These are my details:
	Name: ".$u_results['name']."
	Email: ".$u_results['email']."
	Address: ".$u_results['address']."
	State: ".$u_results['state']."
	Pin: ".$u_results['pin']."
	Phone: ".$u_results['phone'];
	
	$conn->close();

	$conn = new mysqli('127.0.0.1', 'root', 'password', 'Project');
	$stmt = "CALL sendNewMsg(".$u_results['id'].", ".$b_results['id'].", '$text')";
	$conn->query($stmt);
	$conn->close();
	$conn = new mysqli('127.0.0.1', 'root', 'password', 'Project');
	if($purchased == 'false') {
		$stmt = "insert into Buys values('', ".$_COOKIE['sessionId'].",'$product_id', now())";
		$conn->query($stmt);
	}
	$purchased = 'true';
	$conn->close();
}

?>
<!DOCTYPE html>
<html>
<head>
	<title>Product Info</title>
	<link rel="stylesheet" type="text/css" href="w3.css"/>
	<link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="buy.css">
	<link rel="stylesheet" type="text/css" href="product.css">
	<script type="text/javascript" src="ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script type="text/javascript">
	$(document).ready(function() {
		var left = $(".p_main").css("marginLeft");
		$(".buyBtn").css("marginLeft", left);
		var t = $(".buyBtn").attr('disabled', <?php if($purchased != 'false')
															echo 'true'; 
														else 
															echo 'false';?>);
	});
	</script>
	<style type="text/css">
	.buyBtn {
		display: block;
		margin-top: 10px;
		width: 200px;
		height: 50px;
	}

	.buyBtn:hover {
		color: red;
		background-color: white;
	}

	</style>
</head>
<body>
<div id="header">
	<div class="title" style="background-color:#3f51b5;color:white">Trade Kar</div>
	<div class="menu">
		<ul>
			<li class="menu-list"><a class="fa fa-home" href="index.php"></a></li>
			<li class="menu-list"><a href="about.php">About</a></li>
			<li class="menu-list"><a href="sell.php">Sell</a></li>
		</ul>
	</div>
</div>
<div class="p_body">

<p style="width: 100%; color: red; text-align: center; font-size: 1.5em">
<?php if($purchased == "") 
		echo "Item is not available";
	else if($purchased == "true")
		echo "Congratulations!!<br/> Message with details of Seller has been sent to you";
?>
</p>
<?php
function print_Product($src) {
	$image = $src['image'];
	$name = $src['name'];
	$sprice = $src['price'];
	$desc = nl2br($src['description']);
	$b_name = $src['brandName'];
	$man_year = $src['manufYear'];
	$oPrice = $src['oPrice'];
	$new = $src['new'];
	if($new === '1')
		$new = 'new';
	else
		$new = 'old';
	$text = "
	<div class='p_main'>
	<div class='p_left'>
		<div class='img_wrapper'>
		<img src='$image'/>
		</div>
	</div>
	<div class='p_right'>
	<div class='p_name'>$name</div>
	<div class='p_desc'>
	Description: $desc<br/>
	Brand name: $b_name<br/>
	Manufacture Year: $man_year<br/>
	Original Price: $oPrice<br/>
	Product is $new<br/>
	</div>
	<div class='p_price'>Rs.$sprice</div>
	</div>
	</div>
	";
	echo $text;
}
print_Product($p_results);
?>
<form action="<?php $_SERVER['PHP_SELF']?>" method="post" onsubmit="return confirm('Do you really want to Buy?')">
<button class='w3-btn w3-teal w3-xlarge buyBtn' type="submit">BUY</button>
</form>
</body>
</html>