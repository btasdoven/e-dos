<?php
if(!isset($_SESSION))
    session_start();
if (!isset($_SESSION["username"]))
	exit();
	
$out = array("data" => "", "filename" => "", "filepath" => "", "error" => "");
if (isset($_POST['filename'])) {	
	$rootPath = str_repeat("../", substr_count( substr($_SERVER['REQUEST_URI'], strpos($_SERVER['REQUEST_URI'], "e-dos2")) , '/') - 1 );	
	$relPath = dirname($_SERVER['REQUEST_URI']);
	include_once ($rootPath . 'classes.php');
		
	$f = getFileSystemNodeFromPath($_POST['filename']);
	if ($f !== null) {
		if (!$f->isDirectory()) {
			if (file_exists($rootPath . $f->contentUrl)) {
				$array = file_get_contents($rootPath . $f->contentUrl);
				if (!mb_check_encoding($array, 'utf-8'))
					$array = utf8_encode($array);
				
				//$array = htmlspecialchars($array, ENT_QUOTES);
				
				$out["data"] = $array;
				$out["filename"] = md5($f->contentUrl);
				$_SESSION[$out["filename"]] = $f->contentUrl;
				$out["filepath"] = $f->findPath();
			}
			else
				$out["error"] .= "The file '" . $params[1] . "' could not be found.<br>";
		}
		else
			$out["error"] .= "'" . $f->name . "' is not a file.<br>";
	}
}
else
	$out["error"] .= "File could not be found.<br>";
	
$out["error"] .= $ret["stderr"];
echo json_encode($out);

?>