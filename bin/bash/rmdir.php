<?php

if (isset($params[1])) {		
	$file = getFileSystemNodeFromPath($params[1]);
	if ( $file !== null) {			
		if (!$file->isDirectory())
			$ret["stderr"] .= "Files can not be removed by 'rmdir'.<br>";
		else if (count($file->children) > 2)
			$ret["stderr"] .= "Directory is not empty.<br>";
		else {
			unset($file->children[".."]->children[$file->name]);
			file_put_contents($rootPath . 'accounts/' . $_SESSION["username"] . '/filesys', serialize($root));
		}
	}
	//else'e gerek yok. getFileSystemNodeFromPath hallediyo o isi.
}
else
	$ret["stderr"] .= "File or directory name is expected as parameter.<br>";
?>