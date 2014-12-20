<?php
if (!isset($_SESSION))
	session_start();

if (!isset($params[1])) {
	$currentDir = &$root;
	$ret["env"]["path"] = $root->findPath();
	$_SESSION[$_POST['token']] = $ret["env"]["path"]; //file_put_contents(rootPath'accounts/' . $_SESSION["username"] . '/currentDir', $ret["env"]["path"]);
	
}
else {
	$f = getFileSystemNodeFromPath($params[1]);
	if ($f !== null) {
		if ($f->isDirectory()) {
			$currentDir = &$f;
			$ret["env"]["path"] = $currentDir->findPath();
			$_SESSION[$_POST['token']] = $ret["env"]["path"]; //file_put_contents(rootPath'accounts/' . $_SESSION["username"] . '/currentDir', $ret["env"]["path"]);
		}
		else
			$ret["stderr"] .= "'" . $f->name . "' is not a folder.<br>";
	}
}
?>