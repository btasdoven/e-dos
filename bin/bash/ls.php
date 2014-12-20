<?php

if (!isset($params[1])) {
	// '.' and '..' are also children!!!
	if (count($currentDir->children) > 2) {
		foreach($currentDir->children as $key => $value) 
			if ($key != ".." && $key != ".") {
				$ret["stdout"] .= "<li class = 'ls_entry";
				if ($value->isDirectory())
					$ret["stdout"] .= " ls_folder";
				else if ($value->isSystemFile())
					$ret["stdout"] .= " ls_sysfile";
				$ret["stdout"] .= "'>" . $value->name . "</li>";
			}
		$ret["stdout"] .= "<br>";
	}
}
else if ($params[1] == "-l") {
	if (count($currentDir->children) > 2) {
		$ret["stdout"] .= "<table class = 'ls_table'>";
		foreach($currentDir->children as $key => $value) {
			if ($key != ".." && $key != ".") {
				$ret["stdout"] .= "<tr>";
				$ret["stdout"] .= "<td width = '30%'>" . $value->name . "</td><td>";
				
				if ($value->isDirectory())
					$ret["stdout"] .= "d";
				else if ($value->isSystemFile())
					$ret["stdout"] .= "s";
				else if ($value->isExecutable())
					$ret["stdout"] .= "x";
				else
					$ret["stdout"] .= "-";
				
				$u = $value->getUserPermission();
				$ret["stdout"] .= ($u & 4 ? 'r' : '-') . ($u & 2 ? 'w' : '-') . ($u & 1 ? 'x' : '-');
				$u = $value->getGroupPermission();
				$ret["stdout"] .= ($u & 4 ? 'r' : '-') . ($u & 2 ? 'w' : '-') . ($u & 1 ? 'x' : '-');
				$u = $value->getOthersPermission();
				$ret["stdout"] .= ($u & 4 ? 'r' : '-') . ($u & 2 ? 'w' : '-') . ($u & 1 ? 'x' : '-');
				
				$ret["stdout"] .= "</td></tr>";
			}
		}
		$ret["stdout"] .= "</table>";
	}
}
else if ($params[1] == "-nostyle"){
	// '.' and '..' are also children!!!
	$files = array();
	if (count($currentDir->children) > 2) {
		foreach($currentDir->children as $key => $value) 
			if ($key != ".." && $key != ".") {
				$files[] = array("name" => $key, "fileType" => $value->fileType, "cmd" => $value->command);
			}
	}
	
	$ret["stdout"] .= json_encode($files);
}
?>