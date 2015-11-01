<?php
$email = $password = "";
$email_err = $password_err = $title_err = "";
$error = False;

date_default_timezone_set("Asia/Calcutta");

if(isset($_COOKIE["id"]))
{
	header('Location: index.php');
	exit;
}

if($_SERVER["REQUEST_METHOD"] == "POST"){

	$servername = "127.0.0.1";
	$username = "root";
	$userpassword = "password";
	$dbname = "Project";
	$tablename = "Users";

	$email = testinput($_POST["email"]);
	$conn = new mysqli($servername, $username, $userpassword, $dbname);
	if($conn->error)
		die("Error in database");
	$stmt = $conn->prepare("select * from ".$tablename." where email = ?");
	$stmt->bind_param('s', $email);
	$result = $stmt->execute();
	$result = $stmt->get_result();
	if($result->num_rows == 0)
	{
		$error = True;
		$title_err = "Email Id doesnt exist";
	}
	$conn->close();

	$password = testinput($_POST["password"]);
	if(!$error){
		$conn = new mysqli($servername, $username, $userpassword, $dbname);
		if($conn->error)
			die("Error in database");
		$stmt = $conn->prepare("select * from ".$tablename." where email = ? and password = ?");
		$stmt->bind_param('ss', $email, $password);
		$result = $stmt->execute();
		$result = $stmt->get_result();
		if($result->num_rows == 0){
			$error = True;
			$title_err = "Incorrect email or password";
		}
		else
		{
			$row = $result->fetch_assoc();
			$id = $row["id"];
			setcookie("id", $id, time() + (3600 * 24 * 30), '/');
			execute_Sql($id);
			header('Location: index.php');
			exit;
		}
		$conn->close();
	}
}
function execute_Sql($user_id) {
	$servername = '127.0.0.1';
	$username = 'root';
	$password = "dancer";
	$dbname = "Project";
	$tablename = "Logs";

	$conn = new mysqli($servername, $username, $password, $dbname) or die();
	do {
		$stmt = $conn->prepare("insert into ".$tablename. "(sessionId, userId, logIn) values(?, ?, ?)");
		$sessionId = mt_rand(1, 100000000);
		$stmt->bind_param('sss', $sessionId, $user_id, date('Y-m-d H:i:s'));
		$stmt->execute();
		setcookie('sessionId', $sessionId, time() + (3600 * 24 * 30), '/');
	}while($stmt->error);
	$conn->close();
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
	<title>
	</title>
		<link rel="stylesheet" type="text/css" href="login.css">
		<script type="text/javascript" src="ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
		<script type="text/javascript" src="back_efffect.js"></script>
	
</head>
<body>
	<div class="background">
	<img src="Pictures/car.jpg" width="1400" height="819" alt="pic1" /> <!--pics are can be added here-->
	<img src="Pictures/books2.jpg" width="1401" height="800" alt="pic2" /> 
	<img src="Pictures/apple1.jpg"  width="1400" height="850"alt="pic3" />
  	<img src="Pictures/coins.jpg"  width="1400" height="850"alt="pic3" />
	</div>
	<div class="info">
	<h1> Welcome to TradeKar.</h1>
	<p>A place where you get to buy or sell what you wish to.</p>
	</div>
	<form class="form_login"  action="login.php"  method = "POST">
  	<fieldset class="account-info">
    <label>
      <input type="text" name="email" placeholder="Enter Email">
	    <?php 
		    if(isset($_POST['login']) && isset($error['email']))
			  {
				  echo "<div style=\"color:red;font-size:8px;text-align:right;font-family:Verdana;\" >".htmlentities($error['user_name'])."</div>";
			  }
			?>
    </label>
    <label>
      <input id="password" type="password" name="password" placeholder="Password">	    
    </label>
  </fieldset>
  <fieldset class="account-action">
    <input class="btn1" type="submit" name="login" value="Log in">
	<?php 
		if(isset($_POST['login']) && isset($error['password']))
	  	{
		  echo "<div style=\"color:red;font-size:8px;text-align:right;font-family:Verdana;\" >".htmlentities($error['user_password'])."</div>";
	  	}
	?>
  </fieldset>
  <label style="padding-left: 5px;">Dont have an account? <a href="signup.php">SignUp</a></label>
</form >
</body>
</html>