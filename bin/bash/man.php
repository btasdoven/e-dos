<?php


if (isset($params[1])) {
	if (isset($cmdList[$params[1]])) {
		if (file_exists($rootPath . $cmdList[$params[1]]->getManPage() )) {
			$array = file_get_contents($rootPath . $cmdList[$params[1]]->getManPage());
			if (!mb_check_encoding($array, 'utf-8'))
				$array = utf8_encode($array);
			
			$array = htmlspecialchars($array, ENT_QUOTES);
			
			$search = array("\r\n", "\n", "\r", "\t");
			$replace = array("<br>", "<br>", "<br>", "&nbsp;&nbsp;&nbsp;&nbsp;");
			$ret["stdout"] .= str_replace($search, $replace, $array) . "<br>";
		}
		else
				$ret["stderr"] .= "The file '" . $params[1] . ".man' could not be found.<br>";
	}
	else
		$ret["stderr"] .= "No such command: '" . $params[1] . "'<br>";
}
else
	$ret["stderr"] .= "Command name is expected as parameter.<br>";
?>