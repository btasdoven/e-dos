<?php

if (isset($params[1])) {
		
	$file = getFileSystemNodeFromPath($params[1]);
	if ( $file !== null) {
		if (!$file->isSystemFile()) {			
			if (!$file->isDirectory()){
				unset($file->children[".."]->children[$file->name]);
				file_put_contents($rootPath . 'accounts/' . $_SESSION["username"] . '/filesys', serialize($root));
			}
			else
				$ret["stderr"] .= "Directories can not be removed by 'rm'.<br>";				
		}
		else
			$ret["stderr"] .= "System files can not be removed.<br>";
	}
	//else'e gerek yok. getFileSystemNodeFromPath hallediyo o isi.
}
else
	$ret["stderr"] .= "File or directory name is expected as parameter.<br>";
?>