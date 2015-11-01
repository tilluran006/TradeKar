<?php
$filter = "";
$page = "0";
$t = "";

if(!isset($_COOKIE["id"])) {
	header('Location: index.php');
	die();
}

if(isset($_GET['page'])) {
	$page = htmlspecialchars($_GET['page']);
}

if(isset($_GET['q'])) {
	$filter = htmlspecialchars($_GET['q']);
}

if(isset($_GET['type'])) {
	$t = htmlspecialchars($_GET['type']);
}

	$servername = "127.0.0.1";
	$username = "root";
	$userpassword = "password";
	$dbname = "Project";
	$tablename = "Products";

	$conn = new mysqli($servername, $username, $userpassword, $dbname);
	$offset = $page * 10;
	$next = $page + 1;
	$prev = $page - 1;
	$url = "buy.php?";
	if($page == 0)
		$prev = 0;
	if($filter == '') {
		if($t == 'best') {
			$url .="type=best&";
			$stmt = $conn->prepare("select * from Available_view order by rankValue desc, time desc limit 10 offset $offset");
		}else {
			$stmt = $conn->prepare("select * from Available_view order by time desc limit 10 offset $offset");
		}
	}
	else {
		$url .="q=$filter&";
		$filter = "%$filter%";
		if($t == 'best') {
			$url .="type=best&";
			$query = "select * from Available_view 
					where name like ? or type like ? or brandName like ? 
					order by rankValue desc, time desc
					limit 10 offset $offset";
		}else {
			$query = "select * from Available_view 
					where name like ? or type like ? or brandName like ? 
					order by time desc 
					limit 10 offset $offset";
		}
			$stmt = $conn->prepare($query);
			$stmt->bind_param('sss', $filter, $filter, $filter);
	}
	$stmt->execute();
	$results = $stmt->get_result();
	$conn->close();
?>


<!DOCTYPE html>
<html>
<head>
	<title>TradeKar- Buy Product</title>
	<link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="w3.css">
	<link rel="stylesheet" type="text/css" href="buy.css">
	<link rel="stylesheet" type="text/css" href="message.css">
	<script type="text/javascript" src="logOut.js"></script>
	<script type="text/javascript" src="ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script type="text/javascript" src="jquery.messages.js"></script>
</head>
<body>
<div id="header">
	<div class="title" style="background-color:#3f51b5;color:white;">Trade Kar</div>
	<div class="menu">
		<ul>
			<li class="menu-list"><a class="fa fa-home " href="index.php"></a></li>
			<li class="menu-list"><a href="about.php">About</a></li>
			<li class="menu-list"><a href="buy.php">Filters</a>
			<ul>
				<li><a href="buy.php?type=best">Best Offers</a></li>
				<li><a href="buy.php?type=new">New Products</a></li>
				<li><a href="buy.php?q=car">car</a></li>
				<li><a href="buy.php?q=television">television</a></li>
				<li><a href="buy.php?q=mobile">mobile</a></li>
				<li><a href="buy.php?q=refrigerator">refrigerator</a></li>
				<li><a href="buy.php?q=laptop">laptop</a></li>
				<li><a href="buy.php?q=shoes">shoes</a></li>
				<li><a href="buy.php?q=headphones">headphones</a></li>
				<li><a href="buy.php?q=other">other</a></li>
			</ul>
			</li>
			<li class="menu-list"><a href="sell.php">Sell</a></li>
			<li class="menu-list search">
			<form method="get" action="buy.php">
				<input name='q' type="text" onkeyup="showSearch(this.value)" placeholder="Search TradeKar" autocomplete="off"/>
				<input type='hidden' name='type' value="<?php echo $t;?>" />
				<input type='submit' value='Search'/>
			</form>
			<ul></ul>
			</li>
			<li class="menu-list logout"><a href="#" onclick="logout()">LogOut</a></li>
		</ul>
	</div>
</div>
<div class="message-box">
	<div class="message-header">Hey there<div class="cross">x</div></div>
	<div class="message-body">
		<div class='message right'>
			<p>Hello worldsfdsfsfd</p>
			<div class='quote'></div>
			<label>sent</label>
		</div>
	</div>
	<div class="message-input">
			<textarea placeholder="Enter Text"></textarea>
			<button name="msgbtn">Send</button>
	</div>
</div>

<div id="main">
<div id="left">
	<div class="left-holder">
		<a href="#" target="_blank">
		<div  class='img-wrapper'><img src=""></div>
		<div class="ad-desc">
			<div class="ad-name"></div>
			<div class="ad-core"></div>
			<div class="ad-price"></div>
		</div>
	</a>
	</div>
</div>

<div id="right">
<div class="conversation">
	<div class='name'>Rohan</div>
	<div class='msg'>Me: Hey Thereee</div>
</div>
</div>


<div id="center">
	<?php
	function print_product($src) {
	$pid = $src["productId"];
	$image = $src["image"];
	$desc = nl2br($src["name"]."\n".$src["description"]);
	$price = $src["price"];
	$name = $src["name"];
	$text = "<div class='w3-card-4 w3-margin-top' style='width:100%;'>
	<a href='product.php?pid=$pid'><img src='$image' alt='Car' style='width:100%;'/></a>
	<div class='w3-container w3-indigo'>
  	<p>$desc</p>
	</div>
	<div class='w3-container w3-red w3-large w3-padding-medium w3-margin-medium'>
  	<p>Price: Rs.$price</p>
	</div>
	</div>";
	echo $text;
	}

	foreach($results as $src) 
		print_product($src);
?>

<div class="nav">
<div class="nav_left"><a href='<?php echo $url."page=$prev" ?>'>Prev</a></div>
<div class="nav_right"><a href='<?php echo $url."page=$next" ?>'>Next</a></div>
</div>
</div>

</div>
<script>

	var offset = 0;
	"use strict";

	function loadXML(url, obj) {
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange =  function () {
			if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				var x = xmlhttp.responseXML.getElementsByTagName('PRODUCT')[0];
				var id = x.getElementsByTagName('ID')[0].childNodes[0].nodeValue;
				$('.left-holder > a').attr('href', 'product.php?pid=' + id);
				$('.left-holder .ad-name').text(x.getElementsByTagName('NAME')[0].childNodes[0].nodeValue);
				var imgsrc = x.getElementsByTagName('IMAGE')[0].childNodes[0].nodeValue;
				$('.left-holder img').attr('src', imgsrc);
				$('.left-holder .ad-core').text(x.getElementsByTagName('DESCRIPTION')[0].childNodes[0].nodeValue);
				$('.left-holder .ad-price').text(x.getElementsByTagName('OFFER')[0].childNodes[0].nodeValue);
				obj.slideDown(2000, function() {
				obj.children().css("overflow", "auto");
			});
			}
		};
		xmlhttp.open('GET', url , true);
		xmlhttp.send();
	}

	function changeAdvert() {
		var advert = $(".left-holder");
		var active = advert.children();
		active.children().css("overflow", "hidden");
		active.parent().slideUp(2000, function() {
			var url = 'advertise.php';
			loadXML(url, $(this));
		});	
	}

	function showSearch(query) {
		var xmlhttp = new XMLHttpRequest();
		var url = 'searchresult.php?q=' + query.replace(/ /, '+');
		var output = $('.menu .search ul');
		xmlhttp.onreadystatechange = function() {
			if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				var result = xmlhttp.responseText;
				result = result.split(',');
				output.hide();
				output.empty();
				if(query !== '') {
					for(var r = 0; r < result.length - 1; r++) {
						var link = 'buy.php?q=' + result[r].replace(/ /, '+');
						var lindex = result[r].toLowerCase().search(query.toLowerCase());
						var str = result[r].substr(lindex, query.length);
							result[r] = result[r].replace(str, '<b>' + str + '</b>');
						text= "<li><a href='" + link + "'>" + result[r] + "</a></li>";
						output.append(text);
					}
					output.append("<li><b>No more Results</b></li>");
				}
				output.show(); 
			}
		}
		xmlhttp.open('GET', url, true);
		xmlhttp.send();

	}
	changeAdvert();
	var inter = window.setInterval(changeAdvert, 7000);

	$("#left").mouseover(function() {
		window.clearInterval(inter);
	}).mouseleave(function() {
		inter = window.setInterval(changeAdvert, 7000);
	});

	$('input[type="text"]').focus(function() {
		$(this).parent().siblings('ul').slideDown('slow');
	});

	$('input[type="text"]').blur(function() {
		$(this).parent().siblings('ul').slideUp('slow');
	});
</script>
</body>
</html>