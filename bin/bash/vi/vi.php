<?php
if (!isset($_SESSION))
	session_start();

if (isset($params[1])) {
	$f = getFileSystemNodeFromPath($params[1]);
	if ($f !== null) {
		if (!$f->isDirectory()) {
			if (file_exists($rootPath . $f->contentUrl)) {
				$array = file_get_contents($rootPath . $f->contentUrl);
				if (!mb_check_encoding($array, 'utf-8'))
					$array = utf8_encode($array);
				
				$array = htmlspecialchars($array, ENT_QUOTES);
				$ret["stdout"] .= "<script src='bin/bash/vi/vi.js'></script><textarea wrap='hard' spellcheck='false' class = 'vi_container writable'>" . $array . "</textarea>";
				$ret["exec"][] = "vi_initialize();";
				$_SESSION["vi_contentUrl"] = $f->contentUrl;
			}
			else
				$ret["stderr"] .= "The file '" . $params[1] . "' could not be found.<br>";
		}
		else
			$ret["stderr"] .= "'" . $f->name . "' is not a file.<br>";
	}
}
else
	$ret["stderr"] .= "File name is expected as parameter.<br>";
?>