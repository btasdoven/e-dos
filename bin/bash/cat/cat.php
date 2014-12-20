<?php


if (isset($params[1])) {
	if ( isset($currentDir->children[$params[1]]) ) {
		$f = $currentDir->children[$params[1]];
		if (!$f->isDirectory()) {
			if (file_exists($rootPath . $f->contentUrl)) {
				$array = file_get_contents($rootPath . $f->contentUrl);
				if (!mb_check_encoding($array, 'utf-8'))
					$array = utf8_encode($array);
				
				$array = htmlspecialchars($array, ENT_QUOTES);
				
				$search = array("\r\n", "\n", "\r", "\t");
				$replace = array("<br>", "<br>", "<br>", "&nbsp;&nbsp;&nbsp;&nbsp;");
				$ret["stdout"] .= str_replace($search, $replace, $array) . "<br>";
			}
			else
				$ret["stderr"] .= "The file '" . $params[1] . "' could not be found.<br>";
		}
		else
			$ret["stderr"] .= "'" . $params[1] . "' is not a file.<br>";
	} 
	else
		$ret["stderr"] .= "No such file: '" . $params[1] . "'<br>";
}
else
	$ret["stderr"] .= "File is expected as parameter.<br>";
?>