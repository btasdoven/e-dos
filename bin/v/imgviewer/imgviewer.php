<?php
if(!isset($_SESSION))
    session_start();
if (!isset($_SESSION["username"]))
	exit();
	
$rootPath = str_repeat("../", substr_count( substr($_SERVER['REQUEST_URI'], strpos($_SERVER['REQUEST_URI'], "e-dos2")) , '/') - 1 );	
$relPath = dirname($_SERVER['REQUEST_URI']);
include_once ($rootPath . 'classes.php');

if (isset($_POST['input'])) {
	$input = $_POST['input'];
	$params = explode(' ', $input);
}

if (isset($params[1])) {
	$fname = $params[1];
}

$f = getFileSystemNodeFromPath($fname);
if ($f !== null) {
	if (!$f->isDirectory()) {
		if (file_exists($rootPath . $f->contentUrl)) {
			;
		}
		else
			$out["error"] .= "The file '" . $params[1] . "' could not be found.<br>";
	}
	else
		$out["error"] .= "'" . $f->name . "' is not a file.<br>";
}
				
?>


<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<link rel="stylesheet" type="text/css" href="css/CmdStyles.css">
		<link rel="stylesheet" type="text/css" href="css/style.css">
		<script src="js/jquery-2.0.0.min.js"></script>
		<script src="js/core.js"></script>
		<script>
			var thou;
			var caller;
			var sessiontoken = <?php echo generateSessionToken(); ?>;
			var username = "<?php if (isset($_SESSION['username'])) echo $_SESSION['username']; ?>";
			
			var orgH;
			var orgW;
			var orgRatio;
			
			function resize() {
				imgW = $("img").width();
				imgH = $("img").height();
				winW = $(window).width();
				winH = $(window).height();
				
				imgRatio = imgW * 1.0 / imgH;
				winRatio = winW * 1.0 / winH;
				
				if (winRatio < imgRatio) {
					if (orgW < winW) {
						$("img").width( orgW );
						$("img").height( orgH );
					}
					else {
						$("img").width( winW );
						$("img").height( winW / orgRatio );
					}
				}
				else {
					if (orgH < winH) {
						$("img").height( orgH );
						$("img").width( orgW );
					}
					else {
						$("img").height( winH );
						$("img").width( orgRatio * winH );
					}
				}
				
				$("img").css("margin-top", (winH- $("img").height()) / 2 );

				$("img").css("margin-left", (winW - $("img").width()) / 2 );
			}
			
			$(document).ready(function() {
				sessiontoken = <?php echo generateSessionToken(); ?>
							
				$(thou.window).find("span").html("<?php echo $f->findPath(); ?>");
				
				$("img").load( function() {
					orgH = this.height;
					orgW = this.width;
					orgRatio = orgW * 1.0 / orgH;
					resize();
				});
				
				$(window).on('mousewheel', function(event) {
					var delta = event.originalEvent.deltaY || event.deltaY || event.wheelDelta;
					
					if (delta < 0) {
						orgH /= 1.5;
						orgW /= 1.5;
					}
					else if (delta > 0) {
						orgH *= 1.5;
						orgW *= 1.5;
					}
					
					resize();
				});
				
				thou.window.resize( resize );
			});
			
			
			function workIsDone() 
			{ 
			}
			
		</script>		
		<style type="text/css">
			html {
				background-color : rgb(239, 234, 225);
				overflow : hidden;
			}
		</style>
	</head>
	<body style = "margin:0; padding:0;">	
		<img src = "<?php echo $f->contentUrl ?>">
	</body>
</html>