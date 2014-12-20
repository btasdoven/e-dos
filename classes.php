<?php

	
class FileSystemNode {

	public $fileType;					//xsduuugggooo
	public $children;					//|||
	public $name;						//|||=> directory
	public $contentUrl;					//||=>	system file
										//|=> executable
	public $command;					// like "gedit file.txt"
	
	public static $objId = 0;
	public $id;
	
	public static $DIR = 512;
	public static $SYS = 1024;
	public static $EXE = 2048;
	public static $USR = 6;
	public static $GRP = 3;
	public static $OTH = 0;
	
	public static function addChild_safe(&$parent, $child) {
		$child->fileType = $child->fileType & ~FileSystemNode::$SYS;	// To make it not system file.
		if ($parent->name == "~")
			$parent->addChild($child);
		else{
			$parent->children[".."]->children[$parent->name]->addChild($child);
			$parent = $parent->children[".."]->children[$parent->name];
		}
	}
	
	public function getUserPermission() {
		return ($this->fileType >> FileSystemNode::$USR) & 7;
	}
	public function getGroupPermission() {
		return ($this->fileType >> FileSystemNode::$GRP) & 7;
	}
	public function getOthersPermission() {
		return ($this->fileType >> FileSystemNode::$OTH) & 7;
	}
	
	public function isDirectory() {
		return $this->fileType & FileSystemNode::$DIR;
	}
	
	public function isSystemFile() {
		return $this->fileType & FileSystemNode::$SYS;
	}
	public function isExecutable() {
		return $this->fileType & FileSystemNode::$EXE;
	}
	
	public function addChild($child) {
		$child->children[".."] = &$this;
		$this->children[$child->name] = &$child;
	}
	
	function __clone()
    {
		foreach ($this->children as $k => $v) {
			if ($k != "." && $k != "..")
				$this->children[$k] = clone $v;
		}
    }
	
	public function __construct($_name, $_fileType, $_contentUrl = "", $cmd = "") {
		$this->id = FileSystemNode::$objId++;
		$this->name = $_name;
		$this->fileType = $_fileType;
		$this->children["."] = $this;
		$this->contentUrl = $_contentUrl;
		$this->command = $cmd;
	}
	
	
	public function __toString() {
		$str = "<label>" . $this->name . "</label>";
		$str .= "<ul>";
		foreach($this->children as $key => &$value)
			if ($key != "." && $key != "..") {
				if ($value->isDirectory())
					$str .= "<li class = 'dir'>" . $value . "</li>";
				else
					$str .= "<li>" . $value . "</li>";
			}
				
		$str .= "</ul>";		
		return $str;
    }
	
	public function findPath() {
		if ($this->children[".."] === $this)
			return "~";
		
		return $this->children[".."]->findPath() . "/" . $this->name;
	}
	
	
}

class Command {
	public $fname;
	public $name;
	public $path;
	public $windowed;
	public $visible;
	/* window */
	public $left;
	public $top;
	public $width;
	public $height;
	
	public $auth;
	
	public function __construct($_name, $scrName, $_path, $_windowed = false, $_visible = true, $l = 140, $t = 140, $w = 600, $h = 450, $auth = 100) {		
		$this->name = $scrName;
		$this->fname = $_name;
		$this->path = $_path;
		$this->windowed = $_windowed;
		$this->visible = $_visible;
		$this->left = $l;
		$this->top = $t;
		$this->width = $w;
		$this->height = $h;
		$this->auth = $auth;
	}
	
	public function getPath() {
		return $this->path . $this->fname . ".php";
	}
	
	public function getManPage() {
		return $this->path . $this->fname . ".man";
	}
}

/*******************************************************************************/

function getCommandList($showVisible = false) {
	$cmds = json_decode(file_get_contents('commands'));

	$cmdList = array();
	foreach ($cmds as $key => $value) {
		if ($showVisible || $value->visible)
			$cmdList[$key] = new Command($key, $value->name, $value->path, $value->windowed, $value->visible, $value->left, $value->top, $value->width, $value->height, $value->auth);
	}
	
	return $cmdList;
	
	/*
	$cmdList = array();
	if ($handle = opendir($rootPath . "bin/bash/")) {
		while (false !== ($entry = readdir($handle))) {
			$dotPos = strrpos($entry, '.');
			if ($dotPos > 0) {
				$name = substr($entry, 0, $dotPos);
				if ($name !== '.' && $name !== ''){
					$cmdList[$name] = new Command($name, "bin/bash/");
				}
			}
			else {
				$name = $entry;
				if ($name !== '.' && $name !== '') {
					$cmdList[$name] = new Command($name, "bin/bash/" . $name . "/");
				}
			}		
		}
		closedir($handle);
	}
	if ($handle = opendir($rootPath . "bin/v/")) {
		while (false !== ($entry = readdir($handle))) {
			$dotPos = strrpos($entry, '.');
			if ($dotPos > 0) {
				$name = substr($entry, 0, $dotPos);
				if ($name !== '.' && $name !== ''){
					$cmdList[$name] = new Command($name, "bin/v/");
				}
			}
			else {
				$name = $entry;
				if ($name !== '.' && $name !== '') {
					$cmdList[$name] = new Command($name, "bin/v/" . $name . "/", true);
				}
			}		
		}
		closedir($handle);
	}
	
	return $cmdList;
	*/
}

function getFileSystemNodeFromPath($path, $createFile = false, $showErrors = true) {
	global $ret;
	global $currentDir;
	global $root;
	
	if ($path[0] == '/' || $path[0] == '~') {
		//absolute path
		$slashPos = strpos($path, '/');
		$p = preg_split('/[\/]/', substr($path, $slashPos+1), -1, PREG_SPLIT_NO_EMPTY);
		$file = &$root;	
	}
	else {
		//relative path
		$p = preg_split('/[\/]/', $path, -1, PREG_SPLIT_NO_EMPTY);
		$file = &$currentDir;
	}
	
	$found = true;
	for ($i = 0; $i < count($p); ++$i) {
		if ( isset($file->children[$p[$i]]))
			$file = &$file->children[$p[$i]];
		else if ($createFile && $i == count($p) - 1) {
			FileSystemNode::addChild_safe($file, new FileSystemNode($p[$i], (6 << FileSystemNode::$USR) | (4 << FileSystemNode::$GRP) | (4 << FileSystemNode::$OTH), "files/".$p[$i], "gedit " . $path));
			$file = &$file->children[$p[$i]];
			break;
		}
		else {
			if ($showErrors)
				$ret["stderr"] .= "'" . $path . "' could not be found.<br>";
			$found = false;
			break;
		}
	}
	
	if ($found)
		return $file;
	
	return null;
}

function refreshFileSystem($rootPath) {

	$root = new FileSystemNode("~", FileSystemNode::$DIR | FileSystemNode::$SYS);
	$root->children[".."] = $root;
	$root->addChild( new FileSystemNode("Projects"	, (7 << FileSystemNode::$USR) | (5 << FileSystemNode::$GRP) | (5 << FileSystemNode::$OTH) | FileSystemNode::$DIR) );
	$root->addChild( new FileSystemNode("Contact"	, (7 << FileSystemNode::$USR) | (5 << FileSystemNode::$GRP) | (5 << FileSystemNode::$OTH) | FileSystemNode::$DIR) );
	$root->addChild( new FileSystemNode("cv.pdf"	, (4 << FileSystemNode::$USR) | (4 << FileSystemNode::$GRP) | (4 << FileSystemNode::$OTH) | FileSystemNode::$SYS, "files/cv2.pdf", "open ~/cv.pdf") );
	$root->addChild( new FileSystemNode("arkaplan.jpg"	, (4 << FileSystemNode::$USR) | (4 << FileSystemNode::$GRP) | (4 << FileSystemNode::$OTH) | FileSystemNode::$SYS, "files/la.jpg", "imgviewer ~/arkaplan.jpg") );
	$root->addChild( new FileSystemNode("ders_prog.png"	, (4 << FileSystemNode::$USR) | (4 << FileSystemNode::$GRP) | (4 << FileSystemNode::$OTH) | FileSystemNode::$SYS, "files/program.png", "imgviewer ~/ders_prog.png") );

	$root->children["Projects"]->addChild( new FileSystemNode("MTakvim",(7 << FileSystemNode::$USR) | (5 << FileSystemNode::$GRP) | (5 << FileSystemNode::$OTH) | FileSystemNode::$DIR) );
	$root->children["Projects"]->children["MTakvim"]->addChild( new FileSystemNode("download_link.txt",(4 << FileSystemNode::$USR) | (4 << FileSystemNode::$GRP) | (4 << FileSystemNode::$OTH) | FileSystemNode::$SYS, "files/download_link.txt", "gedit ~/Projects/MTakvim/download_link.txt") );
	$root->children["Projects"]->addChild( new FileSystemNode("QuickOrder", (7 << FileSystemNode::$USR) | (5 << FileSystemNode::$GRP) | (5 << FileSystemNode::$OTH) | FileSystemNode::$DIR) );
	$root->children["Projects"]->children["QuickOrder"]->addChild( new FileSystemNode("about.txt", (4 << FileSystemNode::$USR) | (4 << FileSystemNode::$GRP) | (4 << FileSystemNode::$OTH) | FileSystemNode::$SYS, "files/about_qo.txt", "gedit ~/Projects/QuickOrder/about.txt") );
	$root->children["Contact"]->addChild( new FileSystemNode("contact.txt", FileSystemNode::$SYS, "files/contact.txt", "gedit ~/Contact/contact.txt") );
	
	$currentDir = &$root;

	file_put_contents($rootPath . 'accounts/' . $_SESSION["username"] . '/filesys', serialize($root));
	//file_put_contents($rootPath . 'accounts/' . $_SESSION["username"] . '/currentDir', $currentDir->findPath());
	
	/*
	$userList = array();
	$userList[] = array( "userid" => 0, "username" => "batu", "password" => md5("admin"), "authlevel" => "2");
	$userList[] = array( "userid" => 1, "username" => "cagla", "password" => md5("batu08batu"), "authlevel" => "1");
	$userList[] = array( "userid" => 1, "username" => "baris", "password" => md5("brsznraiuue"), "authlevel" => "1");
	$userList[] = array( "userid" => 2, "username" => "guest", "password" => md5(""), "authlevel" => "0");
	
	file_put_contents($rootPath . 'users', serialize($userList));
	*/
}

function generateSessionToken() {
	$sessiontoken = md5(rand()); 
	$_SESSION[$sessiontoken] = "~"; 
	echo "(typeof caller != 'undefined') ? caller.sessiontoken : '" . $sessiontoken . "';"; 
}

	//file_put_contents('commands', json_encode(getCommandList("./")));
	
if (isset($_SESSION['username'])) {
	$root = unserialize(file_get_contents($rootPath . 'accounts/' . $_SESSION["username"] . '/filesys'));
	
	if (isset($_POST['token']))
		$currentDir = getFileSystemNodeFromPath($_SESSION[$_POST['token']]);
	else
		$currentDir = $root;
		
	$env = array("path" => $currentDir->findPath());
	$ret = array( "stdout" => "", "stderr" => "", "debug" => "", "env" => $env);
}

?>