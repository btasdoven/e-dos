<?php

if (isset($params[1])) {

	$from = getFileSystemNodeFromPath($params[1]);

	$ret["stdout"] .= $from;

}
else
	$ret["stdout"] .= $root;
?>