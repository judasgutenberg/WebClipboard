<?php 
//web-based clipboard, for universal exchange between computers and other devices
//gus mueller, January 1 2023
//////////////////////////////////////////////////////////////

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include("config.php");

$conn = mysqli_connect($servername, $username, $password, $database);

$mode = "";
$user = login();
$out = "";
//$formatedDateTime =  $date->format('H:i');

if(gvfw("mode")) {
 
  $mode = gvfw('mode');
 
  if ($mode == "login") {
 
    loginUser();
  } else if ($mode == "Save Clip" && $user != false) {
 
    saveClip($user["user_id"], gvfw("clip", ""));

    
  } else if ($mode == "Save Clip" && $user != false) {
  
  
  } else if ($mode == "download" && $user != false) {
    $path = gvfw("path");
    $friendly = gvfw("friendly");
    download($path, $friendly);
    die();
  }
 
}

 

 
if($user) {
  $out .= "<div class='loggedin'>You are logged in as <b>" . $user["email"] . "</b></div>\n"; 
  $out .= "<div>\n";
  $out .= clipForm();
  $out .= "</div>\n";
  $out .= "<div>\n";
  $out .= clips($user["user_id"]);
  $out .= "</div>\n";
} else {

  $out .= loginForm();

}

echo bodyWrap($out);

 

function logIn() {
  Global $encryptionPassword;
  $cookieName = "webClipBoard";
  if(!isset($_COOKIE[$cookieName])) {
    return false;
  } else {
  
   $cookieValue = $_COOKIE[$cookieName];
   $email = openssl_decrypt($cookieValue, "AES-128-CTR", $encryptionPassword);
   if(strpos($email, "@") > 0){
      return getUser($email);
      
   } else {
      return  false;
   }
  }
}
 
function loginForm() {
  $out = "";
  $out .= "<form method='post' name='loginform' id='loginform'>\n";
  $out .= " email: <input name='email' type='text'>\n";
  $out .= "password: <input name='password' type='password'>\n";
  $out .= "<input name='mode' value='login' type='submit'>\n";
  $out .= "</form>\n";
  return $out;
}

function clipForm() {
  $out = "";
  $out .= "<div>\n";
  $out .= "<form method='post' name='clipForm' id='clipForm'  enctype='multipart/form-data'>\n";
  $out .= "<div class='clipFormButtons'>\n";
  $out .= "<textarea name='clip' style='width:500px; height:100px'>\n";
  $out .= "</textarea>\n";
  $out .= "</div>\n";
  $out .= "<div class='clipFormButtons'>\n";
  $out .= "<input type='file' id='clipfile' name='clipfile'>\n \n";
  $out .= "<input name='mode' value='Save Clip' type='submit'>\n";
  $out .= "</div>\n";
  $out .= "</form>\n";
  $out .= "</div>\n";
  return $out;
}

function getUser($email) {
  Global $conn;
  $sql = "SELECT * FROM `user` WHERE email = '" . mysqli_real_escape_string($conn, $email) . "'";
  //echo($sql);
  $result = mysqli_query($conn, $sql);
  $row = $result->fetch_assoc();
  //echo $row["email"];
  return $row;
}

function loginUser() {
  Global $conn;
  Global $encryptionPassword;
  $cookieName = "webClipBoard";
  $email = gvfw("email");
  $password = gvfw("password");
  $sql = "SELECT `email` FROM `user` WHERE email = '" . mysqli_real_escape_string($conn, $email) . "' AND password = '" . mysqli_real_escape_string($conn, $password) . "'";
  //echo($sql);
  $result = mysqli_query($conn, $sql);
  $row = $result->fetch_assoc();
  if($row  && $row["email"]) {
    $email = $row["email"];
    setcookie($cookieName, openssl_encrypt($email, "AES-128-CTR", $encryptionPassword), time() + (30 * 365 * 24 * 60 * 60));
    header('Location: '.$_SERVER['PHP_SELF']);
    //echo "LOGGED IN!!!" . $email ;
    die;
  }
  return false;
}

function saveClip($userId, $clip){
  Global $conn;
  
  $tempFile = $_FILES["clipfile"]["tmp_name"];
  $extension = "";
  $filename = "";
  if($tempFile) {
    $extension = pathinfo($_FILES["clipfile"]["name"], PATHINFO_EXTENSION);
    $filename = $_FILES["clipfile"]["name"];
  }
  $date = new DateTime("now", new DateTimeZone('America/New_York'));//obviously, you would use your timezone, not necessarily mine
  $formatedDateTime =  $date->format('Y-m-d H:i:s'); 
  $sql = "INSERT INTO clipboard_item(user_id, clip, file_name, created) VALUES (" . $userId . ",'" .  mysqli_real_escape_string($conn, $clip) . "','" . mysqli_real_escape_string($conn, $filename) . "','" .$formatedDateTime . "')"; 
  if($filename != "" || $clip != "") {
    $result = mysqli_query($conn, $sql);
    $id = mysqli_insert_id($conn);
    $targetDir = "uploads/";
    if($filename != "") {
      copy($tempFile, "./downloads/" . $id .  "." . $extension);
    }
  }
}

function clips($userId) {
  Global $conn;
  Global $encryptionPassword;
 
  $sql = "SELECT * FROM `clipboard_item` WHERE user_id = " . $userId . " ORDER BY created DESC LIMIT 0,100";
  //echo($sql);
  $result = mysqli_query($conn, $sql);
  $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
  $out = "";
  for($rowCount = 0; $rowCount< count($rows); $rowCount++) {
    $row = $rows[$rowCount]; 
    $out .= "<div class='postRow'>\n<div class='postDate'>" . $row["created"] . "</div>\n";
    $clip = $row["clip"];
    if($clip != "") {
      $out .= "<div class='clipTools'>" . clipTools($row["clipboard_item_id"]) . "</div>\n";
    }
    $out .= "<div  class='postClip'>\n";
    $out .= "<span id='clip" . $row["clipboard_item_id"] . "'>";
    
    $endClip = "";
    if(beginsWith($clip, "http")) {
      $out .= "<a id='href" . $row["clipboard_item_id"] . "' href='" . $clip . "'>";
      $endClip = "</a>";
    }
    $out .= $clip;
    $out .= $endClip;
    $out .= "</span>";
    if($row["file_name"] != "") {
      $extension = pathinfo($row["file_name"], PATHINFO_EXTENSION);
      $out .= "<div class='downloadLink'><a href='index.php?friendly=" . urlencode($row["file_name"]) . "&mode=download&path=" . urlencode("./downloads/" . $row["clipboard_item_id"] .  "." . $extension) . "'>" . $row["file_name"] . "</a>";
      $out .= "</div>";
    }
    
    
    
    $out .= "</div>";
    $out .= "</div>\n";
  }
  return $out;
}

function bodyWrap($content) {
  $out = "";
  $out .= "<html>\n";
  $out .= "<head>\n";
  $out .= "<script src='site.js'></script>\n";
  $out .= "<link rel='stylesheet' href='site.css'>\n";
  $out .= "<title>Web Clipboard</title>\n";
  $out .= "</head>\n";
  $out .= "<body>\n";
  $out .= $content;
  $out .= "</body>\n";
  $out .= "</html>\n";
  return $out;
}

function clipTools($clipId) {
  $out = "";
  $out .= "<a href='javascript:copy(" . $clipId . ")'> <img src='copy.png' height='10' border='0'/></a>\n";  
  return $out;
}

function gvfw($name, $fail = false){ //get value from wherever
  if(isset($_REQUEST[$name])) {
    return $_REQUEST[$name];
  }
  return $fail;
}

function beginsWith($strIn, $what) {
//Does $strIn begin with $what?
	if (substr($strIn,0, strlen($what))==$what){
		return true;
	}
	return false;
}

function endsWith($strIn, $what) {
//Does $strIn end with $what?
	if (substr($strIn, strlen($strIn)- strlen($what) , strlen($what))==$what) {
		return true;
	}
	return false;
}

function download($path, $friendlyName){
    $file = file_get_contents($path);
    header("Cache-Control: no-cache private");
    header("Content-Description: File Transfer");
    header('Content-disposition: attachment; filename='.$friendlyName);
    header("Content-Type: application/whatevs");
    header("Content-Transfer-Encoding: binary");
    header('Content-Length: '. strlen($file));
    echo $file;
    exit;
}