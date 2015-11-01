<?php
date_default_timezone_set("Asia/Calcutta");

$servername = "127.0.0.1";
$username = "root";
$userpassword = "password";
$dbname = "Project";
$tablename = "Users";

$name = $email = $password = $gender = $address = "";
$state = $pin = $phone = $repassword = "";
$error = False;
$title_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){

	$name = testinput($_POST["name"]);	
	if($_POST["password"] != $_POST["repassword"]){
		$error = True;
		$title_err = "Password doesnt match";
	}else{
		$password = testinput($_POST["password"]);
	}
	$email = testinput($_POST["email"]);
	if(!filter_var($email, FILTER_VALIDATE_EMAIL))
	{
		$error = True;
		$title_err = "Enter a valid email";
	}else{
		$conn = new mysqli($servername, $username, $userpassword, $dbname);
		if($conn->connect_error)
			die ("Connection failed ".$conn->error);
		$stmt = $conn->prepare("select * from ".$tablename." where email = ?");
		$stmt->bind_param('s', $email);
		$stmt->execute();
		$result = $stmt->get_result();
		if($result->num_rows > 0){
			$error = True;
			$email = "";
			$title_err = "The given email id exists";
		}
		$conn->close();
	}
	$address = testinput($_POST["address"]);
	$pin = testinput($_POST["pin"]);
	if(!is_numeric($pin) || strlen($pin) != 6)
	{
		$error = True;
		$title_err = "Enter valid pincode";
	}
		$phone = testinput($_POST["phone"]);
		if (ord($phone) != ord('+')){
			$error = True;
			$title_err = "Phone number must begin with +";
		}else if(!is_numeric(substr($phone, 1, strlen($phone))))
		{
			$error = True;
			$title_err = "Enter valid phone number";
		}
	$gender = testinput($_POST["gender"]);
	$state = testinput($_POST["state"]);
	if(!$error){

		$conn = new mysqli($servername, $username, $userpassword, $dbname);
		if($conn->connect_error)
			die ("Connection failed ".$conn->error);
		do{
			$id = newid();
			$stmt = $conn->prepare("insert into ".$tablename."(id, name, email, password, gender, address, state, pin, phone, time) 
				values(?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ");
			$stmt->bind_param('ssssssssss',$id, $name, $email, $password, $gender, $address, $state, $pin, $phone, date("Y-m-d H:i:s"));
			$stmt->execute();
			header("Location: login.php");
			exit;
		}while($stmt->error);
		$conn->close();
	}
}

function newid(){
	$key = mt_rand(1, 1000000);
	return strval($key);
}

function testinput($data){
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
}


?>

<!DOCTYPE html>
<html>
<head>
	<title>Trade kar: SignUp</title>
	<link rel="stylesheet" type="text/css" href="error.css">
	<link rel="stylesheet" type="text/css" href="w3.css">
	<style type="text/css">
	.w3-label {
		color: #3f51b5;
	}

	.w3-input {
		width: 300px;
	}
	</style>
</head>
<body>
<header class="w3-container w3-indigo">
<h1 class="w3-xxlarge">Welcome to <span style="font-size: 150%;">Trade kar</span> Signup page</h1>
</header>
<form class="w3-container w3-large" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>"  method="post">
<span class="error"><?php if($error) echo $title_err;?></span>
<div class="w3-group">
<input class="w3-input" type="text" name="name" value="<?php if($error) echo $name; else echo "";?>" required/><br/>
<label class="w3-label">Name </label>
</div>
<div class="w3-group">
<input class="w3-input" type="text" name="email" value="<?php if($error) echo $email; else echo ""; ?>" required/><br/>
<label class="w3-label">Email Id</label>
</div>
<div class="w3-group">
<input class="w3-input" type="password" name="password" required/><br/>
<label class="w3-label">Password</label>
</div>
<div class="w3-group">
<input class="w3-input" type="password" name="repassword" required/><br/>
<label class="w3-label">Re-enter Password</label>
</div>
<div class="w3-group">
<label class="w3-label">Gender </label><br/>
<label class="w3-checkbox" >
<input type="radio" name="gender" value="m" <?php if($gender == "" || $gender == "m" ) echo "checked";?>/>
<div class="w3-checkmark"></div>  Male
</label><br/>
<label class="w3-checkbox" >
<input class="w3-checkbox" type="radio" name="gender" value="f" <?if(isset($gender) && $gender == "f") echo "checked"?> />
<div class="w3-checkmark"></div>  Female
</label><br/>
</div>
<div class="w3-group" class="w3-group">
<textarea class="w3-textarea" cols="30" rows="5" name="address" required><?php if($error) echo $address; else echo "";?></textarea><br/>
<label class="w3-label">Address</label>
</div>
<div class="w3-group">
<label class="w3-label">State </label><br/>
<select class="w3-select" name="state">
<option value = "andra Pradesh">Andra Pradesh</option>
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
<div class="w3-group">
<input class="w3-input" type="text" name="pin" value="<?php if($error) echo $pin; else echo "";?>" required/><br/>
<label class="w3-label">Pincode</label>
</div>
<div class="w3-group">
<input class="w3-input" type="text" name="phone" value="<?php if($error) echo $phone; else echo "";?>" required/><br/>
<label class="w3-label">Phone </label>
</div>
<input class="w3-btn" type="submit" value="Submit"/><br/>
</form>
<footer class="w3-container w3-indigo w3-xlarge" style="margin: 5px 0px;">
Already have a account?  <a href="index.php">Log In</a>
</footer>
</body>
</html>