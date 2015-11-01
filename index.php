<?php
if(!isset($_COOKIE['id'])) {
	header('Location: login.php');
	die();
}
header('Location: buy.php')
?>