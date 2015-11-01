<?php
if(!isset($_COOKIE['id'])) {
	header('Location: index.php');
	die();
}
$servername = '127.0.0.1';
$user = 'root';
$pswd = 'password';
$db = 'Project';
$error = "";

function testinput($data){
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
	$password = testinput($_POST['password']);
	$conn = new mysqli($servername, $user, $pswd, $db);
	$stmt = 'select password from Users where id = '.$_COOKIE['id'];
	$result = $conn->query($stmt);
	$result = $result->fetch_assoc();
	$conn->close();
	if($password === $result['password']) {
		$address = $_POST['address'];
		$state = $_POST['state'];
		$pin = $_POST['pin'];
		$phone = $_POST['phone'];
		$newpassword = $_POST['newpassword'];
		if($newpassword != '') {
			$password = $newpassword;
		}
		$phone = $_POST['phone'];
		$conn = new mysqli($servername, $user, $pswd, $db);
		$stmt = $conn->prepare('update Users set address = ?, state = ?,
			pin = ?, phone = ?, password = ? where id = ?');
		$stmt->bind_param('ssssss', $address, $state, $pin, $phone, $password, $_COOKIE['id']);
		$stmt->execute();
		$error = 'Saved Changes';
		$conn->close();
	}else {
		$error = "Enter Valid Password";
	}
}

$conn = new mysqli($servername, $user, $pswd, $db);
$stmt = 'select * from Users where id = '.$_COOKIE['id'];
$result = $conn->query($stmt);
$result = $result->fetch_assoc();
$conn->close();

$u_pswd = $result['password'];
?>

<!DOCTYPE html>
<html>
<head>
	<title>TradeKar Edit Details</title>
	<link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="w3.css">
	<link rel="stylesheet" type="text/css" href="buy.css">
</head>
<body>
<div id="header"  style="position: relative;">
	<div class="title" style="background-color:#3f51b5;color:white">Trade Kar</div>
	<div class="menu">
		<ul>
			<li class="menu-list"><a class="fa fa-home" href="index.php"></a></li>
			<li class="menu-list"><a href="about.php">About</a></li>
			<li class="menu-list"><a href="buy.php">Buy</a></li>
			<li class="menu-list"><a href="sell.php">Sell</a></li>
		</ul>
	</div>
</div>
<div class='main-details'>
<span id="error" style='color: red; font-size: 20px;'><?php echo $error;?></span>
<form class="w3-container w3-large" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>"  method="post" onsubmit="return validate(this);">
<div class="w3-group" class="w3-group">
<textarea class="w3-textarea" cols="30" rows="5" name="address" required><?php echo $result['address'];?></textarea><br/>
<label class="w3-label">Address</label>
</div>
<div class="w3-group">
<label class="w3-label">State </label><br/>
<select class="w3-select" name="state">
<option value = "andra Pradesh" selected>Andra Pradesh</option>
<option value = "assam">Assam</option>
<option value = "bihar">Bihar</option>
<option value = "delhi">Delhi</option>
<option value = "gujarat">Gujarat</option>
<option value = "jammu and kashmir">Jammu and Kashmir</option>
<option value = "jharkhand">Jharkhand</option>
<option value = "karnataka" selected="selected">Karnataka</option>
<option value = "kerala">Kerala</option>
<option value = "madhya pradesh">Madhya Pradesh</option>
<option value = "maharashtra">Maharashtra</option>
<option value = "punjab">Punjab</option>
<option value = "tamil nadu">Tamil Nadu</option>
<option value = "uttar pradesh">Uttar Pradesh</option>
<option value = "west bengal">West Bengal</option>
</select><br/>
</div>
<script type="text/javascript">
	var address = "<?php echo $result['state']?>";
	document.forms[0]['state'].value = address;
</script>
<div class="w3-group">
<input class="w3-input" type="text" name="pin" value="<?php echo $result['pin']?>" required/><br/>
<label class="w3-label">Pincode</label>
</div>
<div class="w3-group">
<input class="w3-input" type="text" name="phone" value="<?php echo $result['phone']; ?>" required/><br/>
<label class="w3-label">Phone </label>
</div>
<p style="color: red">Enter Password to apply the new changes</p>
<div class="w3-group">
<input class="w3-input" type="password" name="password" required/><br/>
<label class="w3-label">Password</label>
</div>
<p style="color: blue">Enter new password to change the password</p>
<div class="w3-group">
<input class="w3-input" type="password" name="newpassword"/><br/>
<label class="w3-label">New Password</label>
</div>
<div class="w3-group">
<input class="w3-input" type="password" name="renewpassword"/><br/>
<label class="w3-label">Reenter New Password</label>
</div>
<input class="w3-btn" type="submit" value="Save"/><br/>
</form>
</div>
<script type="text/javascript">
	var newpswd = document.forms[0]['newpassword'];
	var renewpswd = document.forms[0]['renewpassword'];
	var flag = 0;
	var error = document.getElementById('error');
	newpswd.addEventListener('focus', function() {
		flag = 1;
	});
	renewpswd.addEventListener('focus', function() {
		flag = 1;
	});
	function validate(form) {
		if(flag) {
			if(newpswd.value == renewpswd.value)
				return true;
			else {
				error.innerHTML = 'Passwords should match';
				return false;
			}
		}
		return true;
	}
</script>
</body>
</html>