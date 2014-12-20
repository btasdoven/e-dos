<?php
if(!isset($_SESSION))
    session_start();
if (!isset($_SESSION["username"]))
	exit();

$rootPath = str_repeat("../", substr_count( substr($_SERVER['REQUEST_URI'], strpos($_SERVER['REQUEST_URI'], "e-dos2")) , '/') - 1 );	
$relPath = dirname($_SERVER['REQUEST_URI']);
include_once ($rootPath . 'classes.php');

$input = $_POST['input'];

$params = explode(' ', $input);

$cmdList = getCommandList($rootPath);

$authLevelList = getAuthLevelList();
		
// run command
if ( isset($cmdList[$params[0]]) ) {
	if ( isset($authLevelList[$params[0]]) && intval($_SESSION["authlevel"]) >= intval($authLevelList[$params[0]]) ) {
		if ($cmdList[$params[0]]->windowed)
			$ret["exec"][] = "parent.openWindow('" . $cmdList[$params[0]]->getPath() . "', '" . $params[0] . "','" . $cmdList[$params[0]]->path . "/icon.png', 1, window, '" . implode(" ", $params) . "');";
		else
			include($rootPath . $cmdList[$params[0]]->getPath());
	}
	else
		$ret["stderr"] .= "Do not have permission to run '" . $params[0] . "'!<br>";
}
else
	$ret["stderr"] .= "Command '" . $params[0] . "' could not be found.<br>";

echo json_encode($ret);//, JSON_UNESCAPED_UNICODE);


?>