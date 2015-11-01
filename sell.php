
<!DOCTYPE html>
<html>
<head>
	<title>TradeKar- Sell Product</title>
	<script src="ajax.googleapis.com/ajax/libs/angularjs/1.3.14/angular.min.js"></script>
	<link rel="stylesheet" type="text/css" href="error.css">
	<link rel="stylesheet" type="text/css" href="w3.css">
	<script type="text/javascript" src="ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<style type="text/css">
	.w3-label {
		color:#3f51b5;
	}
	</style>
	<script type="text/javascript">

	$(document).ready(function() {
		$('.dropdown').hide();
		$('[name="advert"]').click(function() {
			var dropdown = $('.dropdown');
			$('.dropdown').slideToggle();
		});
	});

	var app = angular.module('descApp', []);
	app.controller('descCtrl', function($scope) {
		$scope.desc = "";
		$scope.left = function() {
			if($scope.desc === undefined)
				return 0;
			return $scope.desc.length;
		}
		$scope.adhours = 1;
		$scope.getPrice = function() {
			return $scope.adhours * 500;
		}
	});
	</script>
</head>
<body>
<header class="w3-container w3-indigo">
<h1 class="w3-xxxlarge">Sell Your Product</h1>
</header>

<?php

if(!isset($_COOKIE["id"])) {
	header('Location: index.php');
	die();
}

date_default_timezone_set("Asia/Calcutta");

if($_SERVER['REQUEST_METHOD'] == "POST") {
	date_default_timezone_set("Asia/Calcutta");
	$prod_name = htmlspecialchars($_POST['p_name']);
	$prod_type = htmlspecialchars($_POST['p_type']);
	$prod_desc = htmlspecialchars($_POST['p_desc']);
	$prod_price = htmlspecialchars($_POST['p_price']);
	$b_name = htmlspecialchars($_POST['b_name']);
	$manf_year = htmlspecialchars($_POST['manf_year']);
	$orig_price = htmlspecialchars($_POST['orig_price']);
	$new_p = htmlspecialchars($_POST['new']);
	$advert = htmlspecialchars($_POST['advert']);
	$adinput = htmlspecialchars($_POST['adinput']);
	$expire = $adinput * 500;
	$d = strtotime("+$adinput hours", time());
	$target_dir = 'products/';
	$target_file = $target_dir. randomname(). basename($_FILES["p_image"]["name"]);
	if(date($manf_year) > date('Y')) {
		echo "<span class='error'>Invalid Manuf Year</span>";
	} 
	else if($_FILES["p_image"]["size"] > 5000000) {
		echo "<span class='error'>File must be less than 5MB</span>";
	} else {
		if (move_uploaded_file($_FILES["p_image"]["tmp_name"], $target_file)) {
			$tablename = 'Products';
			$conn = new mysqli('127.0.0.1', 'root', 'password', 'Project');
			$stmt = $conn->prepare("insert into ".$tablename." values (?, ?, ?, ?, ?, ?, ?, '1', ?, ?, ?, ?)");
			$prod_id = getId();
			$stmt->bind_param("sssssisssis", $prod_id, $prod_name, $prod_type, $prod_desc, 
					$target_file, intval($prod_price),date("Y-m-d H:i:s"), $b_name,
					 $manf_year, $orig_price, $new_p);
			do {
				$prod_id = getId();
				$stmt->execute();
			}while($stmt->error);

			$tablename = 'Sells';
			$stmt = $conn->prepare("insert into ".$tablename."(session_Id, product_Id) values (?, ?)");
			$stmt->bind_param("ss", $_COOKIE["sessionId"], $prod_id);
			$stmt->execute();

			if($advert == '1') {
				$tablename = 'Advertise';
				$stmt = $conn->prepare("insert into ".$tablename." values ('', ?, ?)");
				$stmt->bind_param("ss", $prod_id, date('Y-m-d H:i:s', $d));
				$stmt->execute();
			}
			$conn->close();
			echo "<span name='php_notif' style='color: green'>Successfully uploaded</span>";
		}
		else 
		{
			echo "<span class='error'>error in uploading</span>";
		}
	}

}

function getId() {
	$char_set = "ui30_-oplsazxcvbKJHGFqwertyDSAZXCnmQWERTVBNM7896YUIOPL5412kjhgfd";
	$id = "";
	for($i = 0; $i < 23; $i++) {

		$index = mt_rand(0, strlen($char_set) - 1);
		$id .= $char_set[$index];
	}
	return $id;
}

function randomname() {
	$temp_id = mt_rand(1, 1000000);
	return strval($temp_id);
}
?>
<span class='error' name='image_err'></span>
<form name="sellForm" ng-app="descApp" ng-controller="descCtrl" class="w3-container" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" enctype="multipart/form-data" onsubmit="return validate()">
	<div class="w3-group">
	<input class="w3-input" name="p_name" type="text" required/><br/>
	<label class="w3-label">Product Name</label>
	</div>
	<div class="w3-group">
	<label class="w3-label">Product Type</label><br/>
	<select class="w3-select" name="p_type">
		<option value='car' selected>car</option>
		<option value='television'>television</option>
		<option value='mobile'>mobile</option>
		<option value='refrigerator'>refrigerator</option>
		<option value='laptop'>laptop</option>
		<option value='shoes'>shoes</option>
		<option value='headphones'>headphones</option>
		<option value='other'>other</option>
	</select>
	</div>
	<div class="w3-group">
	<label class="w3-label">Image</label><br/>
	<input class="w3-input" name='p_image' type='file' required/>
	</div>
	<div class="w3-group">
	<textarea ng-model="desc" class="w3-textarea" style="height: 120px;" name="p_desc" maxlength="150" required></textarea><br/>
	<label class="w3-label">Description<span ng-show="sellForm.p_desc.$dirty">({{left()}} out of 150)</span></label>
	</div>
	<div class="w3-group">
	<input class="w3-input" name='b_name' type='text' required/><br/>
	<label class="w3-label">Brand Name</label>
	</div>
	<div class="w3-group">
	<input class="w3-input" name='manf_year' type='number' min="1980" required/><br/>
	<label class="w3-label">Year Of Manufacture</label>
	</div>
	<div class="w3-group">
	<label class="w3-label">Product Is:</label><br/>
	<label class="w3-checkbox" >
	<input type="radio" name="new" value="0" checked="true" />
	<div class="w3-checkmark"></div>  Used
	</label><br/>
	<label class="w3-checkbox" >
	<input type="radio" name="new" value="1"/>
	<div class="w3-checkmark"></div>  New
	</label>
	</div>
	<div class="w3-group">
	<input class="w3-input" name='orig_price' type='number' min='0' required/><br/>
	<label class="w3-label">Original Price(Rs)</label>
	</div>
	<div class="w3-group">
	<input class="w3-input" name='p_price' type='number' min='0' required/><br/>
	<label class="w3-label">Selling Price(Rs)</label>
	</div>
	<div class="w3-group">
	<label class="w3-label">Put Advertisement:</label><br/>
	<label class="w3-checkbox" >
	<input type="radio" name="advert" value="1" />
	<div class="w3-checkmark"></div>  Yes
	</label><br/>
	<label class="w3-checkbox" >
	<input class="w3-checkbox" type="radio" name="advert" value="0" checked="true" />
	<div class="w3-checkmark"></div>  No
	</label>
	</div>
	<div class="w3-group dropdown" >
	<input class="w3-input" ng-model="adhours" type="number" name="adinput" min="1" value="1" /><label class="w3-text-red" name="adprice">Price: Rs.{{getPrice()}}</label><br/>
	<label class="w3-label">Number Of Hours</label>
	</div>
	<button class="w3-btn w3-teal" type='submit'>Sell</button><br/>
	<script type="text/javascript">
	"use strict";
	function validate() {
		var image;
		var error = false;
		image = document.forms[0]['p_image'].value;
		var extensions = ['jpeg', 'jpg', 'rif', 'tif', 'png'];
		var is_image = false;
		for( var i in extensions) {
			var ext = extensions[i];
			var ext_len = ext.length;
			if(image.substr(ext_len * -1, ext_len) == ext) {
				is_image = true;
				break;
			}
		}
		if(!is_image) {
			error = true;
			document.getElementsByName('image_err')[0].innerHTML = 'Enter valid image format';
		} else {
			document.getElementsByName('image_err')[0].innerHTML = '';
		}
		if(error) {
			var ele = document.getElementsByName('php_notif');
			if(ele.length != 0) {
				ele[0].innerHTML = "";
			}
			return false;
		}
		else
			return true;
	}
	</script>
</form>
<footer class="w3-container w3-indigo w3-xlarge" style="margin: 10px 0px 0px 0px">
<a href="buy.php">Go Back</a>
</footer>
</body>
</html>