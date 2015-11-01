<?php
if(!isset($_COOKIE['id'])) {
	header('Location: login.php');
	die();
}
$u_id = $_COOKIE['id'];
$s_id = $_COOKIE['sessionId'];

$servername = "127.0.0.1";
$username = "root";
$userpassword = "password";
$dbname = "Project";
$tablename = "Products";

$conn = new mysqli($servername, $username, $userpassword, $dbname);
$stmt = $conn->prepare("select * from Users where id=?");
$stmt->bind_param('s', $u_id);
$stmt->execute();
$results = $stmt->get_result();
$u_details = $results->fetch_assoc();
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
	<title>TradeKar</title>
	<link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="w3.css">
	<link rel="stylesheet" type="text/css" href="buy.css">
	<link rel="stylesheet" type="text/css" href="about.css">
	<script type="text/javascript" src="ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script>
	$(document).ready(function(e) {
		$('.tabs > .tab-links a').click(function() {
			var currentLink = $(this).attr('href');
			$(currentLink).siblings().slideUp(1000);
			$(currentLink).slideDown(1000);
			$(this).parent('li').addClass('active').siblings().removeClass('active');
		});
	});	
	</script>

</head>
<body>
<div id="header">
	<div class="title" style="background-color:#3f51b5;color:white">Trade Kar</div>
	<div class="menu">
		<ul>
			<li class="menu-list"><a class="fa fa-home" href="index.php"></a></li>
			<li class="menu-list"><a href="buy.php">Buy</a></li>
			<li class="menu-list"><a href="sell.php">Sell</a></li>
		</ul>
	</div>
</div>

<div class="about_main">
	<h1 class="name"><?php echo $u_details['name']; ?></h1>
	<div class="details"><label>Email:</label> <div><?php echo $u_details['email'];?></div></div>
	<div class="details"><label>Address:</label> <div><?php echo nl2br($u_details['address']);?></div></div>
	<div class="details"><label>Pincode:</label> <div><?php echo $u_details['pin'];?></div></div>
	<div class="details"><label>State:</label> <div><?php echo $u_details['state'];?></div></div>
	<div class="details"><label>Phone Num:</label> <div><?php echo $u_details['phone'];?></div></div>
	<div><a href="editDetail.php">Edit</a></div>
</div>

<div class="tabs">
	<ul class="tab-links">
		<li class="active"><a href="#tab1">Purchased</a></li>
		<li><a href="#tab2">Sold</a></li>
	</ul>
	<div class="tab-content">
		<div class="tab active" id="tab1">
		<?php 
		function print_row($src) {
		 	$name = $src['name'];
		 	$img = $src['image'];
		 	$company = $src['brandName'];
		 	$price = $src['price'];
		 	if($src['available'] === '0') {
		 		$class = 'sold';
		 		$msg = 'Sold Out';
		 	}
		 	else {
		 		$class = '';
		 		$msg = 'In Stock';
		 	}
		 	$text = "
		 	<div class='prod_tabs'>
		 	<img src='$img'/>
		 	<div class='p_name'>$name</div>
		 	<div class='p_brand'>$company</div>
		 	<div class='p_avai $class'>$msg</div>
		 	<div class='p_price'>Rs: $price</div>
		 	</div>
		 	";
		 	echo $text;
		 }

		 $conn = new mysqli($servername, $username, $userpassword, $dbname);
		 $stmt = $conn->prepare('call getPurchased(?)');
		 $stmt->bind_param('s', $u_id);
		 $stmt->execute();
		 $results = $stmt->get_result();
		 while($row = $results->fetch_assoc())
		 	print_row($row);
		 $conn->close();
		 ?>
		</div>
		<div class="tab" id="tab2">
		<?php
		 $conn = new mysqli($servername, $username, $userpassword, $dbname);
		 $stmt = $conn->prepare('call getProducts(?)');
		 $stmt->bind_param('s', $u_id);
		 $stmt->execute();
		 $results = $stmt->get_result();
		 while($row = $results->fetch_assoc())
		 	print_row($row);
		 $conn->close();
		?>
		</div>
	</div>
</div>
</body>
</html>