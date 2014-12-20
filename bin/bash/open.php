<?php

if (isset($params[1])) {
	$f = getFileSystemNodeFromPath($params[1]);
	
	if ( $f !== null ) {
		if (!$f->isDirectory()) {
			if (isset($params[2]) && $params[2] == "&")
				$ret["exec"][] = "parent.openWindow('" . $f->contentUrl . "', '" . $f->name . "', undefined, 0, undefined, '". implode(" ", $params) . "', 100, 100, 800, 500);";
			else
				$ret["exec"][] = "parent.openWindow('" . $f->contentUrl . "', '" . $f->name . "', undefined, 0, window, '". implode(" ", $params) . "', 100, 100, 800, 500);";
		}
		else
			$ret["stderr"] .= "'" . $f->name . "' can not be opened.<br>";
	}
}
else
	$ret["stderr"] .= "File is expected as parameter.<br>";
?>