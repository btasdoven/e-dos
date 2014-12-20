<?php
	foreach ($cmdList as $key => $value)
		if ($value->visible)
			$ret["stdout"] .= "<span class = 'ls_entry'>" . $key . "</span>";
		
	$ret["stdout"] .= "<br>";
?>