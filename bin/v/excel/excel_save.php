﻿<?php
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
		$w = 0;
		$h = count($data);
		$array = "";
		foreach ( $data as $row ) {
			$w = count($row) > $w ? count($row) : $w;
			$array .= implode("\1", $row) . "\2";
		}
		$array = strval($w) . " " . strval($h) . " " . substr($array, 0, -1);
		file_put_contents($rootPath . $_SESSION[$_POST['filename']], $array);
		echo "File is saved successfully.";
	}
	else
		echo "Unexpected error is occured!";
			
}
else
	echo "File could not be found.";

?>