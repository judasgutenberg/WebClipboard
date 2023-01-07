<?php 
//web-based clipboard, for universal exchange between computers and other devices
//gus mueller, January 1 2023
//////////////////////////////////////////////////////////////

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

include("config.php");

$conn = mysqli_connect($servername, $username, $password, $database);

$mode = "";

$date = new DateTime("now", new DateTimeZone('America/New_York'));//obviously, you would use your timezone, not necessarily mine
$formatedDateTime =  $date->format('Y-m-d H:i:s');
//$formatedDateTime =  $date->format('H:i');

if($_REQUEST) {
	$mode = $_REQUEST["mode"];
	$locationId = $_REQUEST["locationId"];
	
	
	
}

function isLoggedIn() {
  Global $conn;
  $sql = "SELECT 

}
 