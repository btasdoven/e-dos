<?php
if (!isset($_SESSION))
	session_start();
	
if (isset($params[1])) {
	$dir = getFileSystemNodeFromPath($params[1], false, false);
	if ($dir == null) {
		$dir = getFileSystemNodeFromPath($params[1], true);
		$dir->fileType = FileSystemNode::$DIR;
		file_put_contents($rootPath . 'accounts/' . $_SESSION["username"] . '/filesys', serialize($root));
	}
	else
		$ret["stderr"] .= "Directory name already exists.<br>";
}
else
	$ret["stderr"] .= "Directory name is expected as parameter.<br>";
?>