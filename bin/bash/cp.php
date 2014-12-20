<?php

if (isset($params[1]) && isset($params[2])) {
		
	$from = getFileSystemNodeFromPath($params[1]);
	if ($from != null) {	// System files can be copied.
		if ($from->isDirectory())
			$to = getFileSystemNodeFromPath($params[2]);
		else		
			$to = getFileSystemNodeFromPath($params[2], true);	
		if ($from !== null && $to !== null) {
			if ($to->isDirectory()){
				FileSystemNode::addChild_safe($to, clone $from);			
			}
			else if (!$from->isDirectory()) {
				//if $to is directory then $from should also be directory
				$to->contentUrl = $from->contentUrl;		
				
			}
			else
				$ret["stderr"] .= "'" . $params[2] . "' should be a folder.<br>";
				
			file_put_contents($rootPath . 'accounts/' . $_SESSION["username"] . '/filesys', serialize($root));
		}
		//else'e gerek yok. getFileSystemNodeFromPath hallediyo o isi.
	}
	//else'e gerek yok. getFileSystemNodeFromPath hallediyo o isi.
}
else
	$ret["stderr"] .= "File or directory is expected as parameter.<br>";
?>