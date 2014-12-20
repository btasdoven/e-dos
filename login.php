<?php
if(!isset($_SESSION))
    session_start();
$rootPath = str_repeat("../", substr_count( substr($_SERVER['REQUEST_URI'], strpos($_SERVER['REQUEST_URI'], "e-dos2")) , '/') - 1 );	
$relPath = dirname($_SERVER['REQUEST_URI']);
include_once ($rootPath . 'classes.php');

$uname = $_POST['username'];
$pass = $_POST['password'];

$userList = unserialize(file_get_contents('users'));print_r($userList);
foreach ($userList as $user) {
	if ($user["username"] === $uname && $user['password'] === md5($pass)) {
		
		$_SESSION["userid"] = $user["userid"];
		$_SESSION["username"] = $uname;
		$_SESSION["authlevel"] = $user["authlevel"];
		$_SESSION["currentDir"] = "~";
		
		/*
		$ret = array();
		$ret["username"] = $uname;

		ob_start();
		include ('desktop.php');
		$terminal = ob_get_clean();
		
		$ret["html"] = $terminal;
		
		echo json_encode($ret);*/
		echo "OK";
		break;
	}	
}
?>