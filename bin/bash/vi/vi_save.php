<?php
if(!isset($_SESSION))
    session_start();
if (!isset($_SESSION["username"]))
	exit();

$rootPath = str_repeat("../", substr_count( substr($_SERVER['REQUEST_URI'], strpos($_SERVER['REQUEST_URI'], "e-dos2")) , '/') - 1 );	
$relPath = dirname($_SERVER['REQUEST_URI']);
include_once ($rootPath . 'classes.php');
	
if (isset($_SESSION['vi_contentUrl'])) {	
	$file = $_POST['input'];		
	file_put_contents($rootPath . $_SESSION['vi_contentUrl'], $file);
	echo "OK";
}
else
	echo "ERROR";
?>