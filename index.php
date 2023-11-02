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
$user = logIn();
$out = "";
$createUserErrors = NULL;
//$formatedDateTime =  $date->format('H:i');

if(gvfw("mode")) {
 
  $mode = gvfw('mode');
  if ($mode == "logout") {
  	logOut();
	header("Location: ?mode=login");
	die();
  }
  
	
	if ($mode == "login") {
		loginUser();
	} else if (strtolower($mode) == "create user") {
		$createUserErrors = createUser();
	} else if (strtolower($mode) == "save clip" && $user != false) {
	
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
	$out .= "<div class='loggedin'>You are logged in as <b>" . $user["email"] . "</b> <div class='basicbutton'><a href=\"?mode=logout\">logout</a></div></div>\n"; 
	$out .= "<div>\n";
	$out .= clipForm();
	$out .= "</div>\n";
	$out .= "<div>\n";
	$out .= clips($user["user_id"]);
	$out .= "</div>\n";
} else if ($mode == "startnewuser" || !is_null($createUserErrors)) {
	$out .= "<div class='loggedin'>You are logged out. <div class='basicbutton'><a href=\"?mode=login\">log in</a></div></div>\n"; 
	$out .= newUserForm($createUserErrors);
} else {
  if(gvfa("password", $_POST) != "") {
    $out .= "<div class='genericformerror'>The credentials you entered have failed.</div>";
   }
  $out .= loginForm();

}

echo bodyWrap($out);

 

function logIn() {
  Global $encryptionPassword;
  Global $cookiename;
  if(!isset($_COOKIE[$cookiename])) {
    return false;
  } else {
  
   $cookieValue = $_COOKIE[$cookiename];
   $email = openssl_decrypt($cookieValue, "AES-128-CTR", $encryptionPassword);
   if(strpos($email, "@") > 0){
      return getUser($email);
      
   } else {
      return  false;
   }
  }
}
 
function logOut() {
	Global $cookiename;
	setcookie($cookiename, "");
	return false;
}
 
function loginForm() {
  $out = "";
  $out .= "<form method='post' name='loginform' id='loginform'>\n";
  $out .= "<strong>Login here:</strong>  email: <input name='email' type='text'>\n";
  $out .= "password: <input name='password' type='password'>\n";
  $out .= "<input name='mode' value='login' type='submit'>\n";
  $out .= "<div> or  <div class='basicbutton'><a href=\"?mode=startnewuser\">Create Account</a></div>\n";
  $out .= "</form>\n";
  return $out;
}


 

function newUserForm($error = NULL) {
	$formData = array(
		[
	    'label' => 'email',
		'name' => 'email',
	    'value' => gvfa("email", $_POST), 
		'error' => gvfa('email', $error)
	  ],
		[
	    'title' => 'password',
		'name' => 'password',
		'type' => 'password',
	    'value' => gvfa("password", $_POST), 
		'error' => gvfa('error', $error)
	   ],
		[
	    'label' => 'password (again)',
		'name' => 'password2',
		'type' => 'password',
	    'value' => gvfa("password2", $_POST),
		'error' => gvfa('password2', $error)
	   ]
	);
  return genericForm($formData, "create user");
}


function genericForm($data, $submitLabel) { //$data also includes any errors
	$out = "";
	$out .= "<form method='post' name='genericform' id='genericform'>\n";
	$out .= "<div class='genericform'>\n";
	foreach($data as &$datum) {
		$label = gvfa("label", $datum);
		$value = gvfa("value", $datum); 
		$name = gvfa("name", $datum); 
		$type =gvfa("type", $datum); 
		$error = gvfa("error", $datum); 
		if($label == "") {
			$label = $name;
		}
		if($type == "") {
			$type = "text";
		}
		$out .= "<div class='genericformelementlabel'>" . $label . ": </div><div class='genericformelementinput'><div class='genericformerror'>" . $error . "</div><input name='" . $name . "' value=\"" . addslashes($value) . "\" type='" . $type . "'/></div>\n";
	}
	$out .= "<div class='genericformelementlabel'><input name='mode' value='" .  $submitLabel. "' type='submit'/></div>\n";
	$out .= "</div>\n";
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

function loginUser($source = NULL) {
  Global $conn;
  Global $encryptionPassword;
  Global $cookiename;
  if($source == NULL) {
  	$source = $_REQUEST;
  }
  $email = gvfa("email", $source);
  $passwordIn = gvfa("password", $source);
  $sql = "SELECT `email`, `password` FROM `user` WHERE email = '" . mysqli_real_escape_string($conn, $email) . "' ";
  //echo($sql);
  $result = mysqli_query($conn, $sql);
  $row = $result->fetch_assoc();
  if($row  && $row["email"] && $row["password"]) {
    $email = $row["email"];
	$passwordHashed = $row["password"];
	//for debugging:
	//echo crypt($passwordIn, $encryptionPassword);
	if (password_verify($passwordIn, $passwordHashed)) {
		//echo "DDDADA";
	    setcookie($cookiename, openssl_encrypt($email, "AES-128-CTR", $encryptionPassword), time() + (30 * 365 * 24 * 60 * 60));
	    header('Location: '.$_SERVER['PHP_SELF']);
	    //echo "LOGGED IN!!!" . $email ;
	    die;
	}
  }
  return false;
}


function createUser(){
  Global $conn;
  Global $encryptionPassword;
  $errors = NULL;
  $date = new DateTime("now", new DateTimeZone('America/New_York'));//obviously, you would use your timezone, not necessarily mine
  $formatedDateTime =  $date->format('Y-m-d H:i:s'); 
  $password = gvfa("password", $_POST);
  $password2 = gvfa("password2", $_POST);
  $email = gvfa("email", $_POST);
  if($password != $password2 || $password == "") {
  	$errors["password2"] = "Passwords must be identical and have a value";
  }
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
	$errors["email"] = "Invalid email format";
  }
  if(is_null($errors)) {
  	$encryptedPassword =  crypt($password, $encryptionPassword);
  	$sql = "INSERT INTO user(email, password, created) VALUES ('" . $email . "','" .  mysqli_real_escape_string($conn, $encryptedPassword) . "','" .$formatedDateTime . "')"; 
	//echo $sql;
	$result = mysqli_query($conn, $sql);
    	$id = mysqli_insert_id($conn);
  	loginUser($_POST);
	header("Location: ?");
  } else {
  	return $errors;
  
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
  $out = "";
  $result = mysqli_query($conn, $sql);
  if($result) {
	  $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
	  
	  if($rows) {
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
	  }
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
  $out = gvfa($name, $_REQUEST, $fail);
  return $out;
}

function gvfa($name, $source, $fail = false){ //get value from associative
  if(isset($source[$name])) {
    return $source[$name];
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
