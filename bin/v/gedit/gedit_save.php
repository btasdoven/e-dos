<?php
if(!isset($_SESSION))
    session_start();
if (!isset($_SESSION["username"]))
	exit();
	
	
if (isset($_POST['filename']) && isset($_SESSION[$_POST['filename']])) {	
	
	$rootPath = str_repeat("../", substr_count( substr($_SERVER['REQUEST_URI'], strpos($_SERVER['REQUEST_URI'], "e-dos2")) , '/') - 1 );	
	$relPath = dirname($_SERVER['REQUEST_URI']);
	include_once ($rootPath . 'classes.php');
	
	if (file_exists($rootPath . $_SESSION[$_POST['filename']])) {
		$data = $_POST['data'];		
		file_put_contents($rootPath . $_SESSION[$_POST['filename']], $data);
		echo "File is saved successfully.";
	}
	else
		echo "Unexpected error is occured!";
			
}
else
	echo "File could not be found.";

?>