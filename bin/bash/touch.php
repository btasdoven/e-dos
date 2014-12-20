<?php
if (!isset($_SESSION))
	session_start();
	
if (isset($params[1])) {
	$f = getFileSystemNodeFromPath($params[1]);
	if ($f == null) {
		$f = getFileSystemNodeFromPath($params[1], true);
		$f->contentUrl = "files/" . $params[1] . "$" . time();
		file_put_contents($rootPath . 'accounts/' . $_SESSION["username"] . '/filesys', serialize($root));
		fclose(fopen($f->contentUrl, "w"));
	}
	else
		$ret["stderr"] .= "File is not created. '" . $params[1] . "' already exists.<br>";
}
else
	$ret["stderr"] .= "File name is expected as parameter.<br>";
?>