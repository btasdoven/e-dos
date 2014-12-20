<?php
if (!isset($_SESSION))
	session_start();
if (!isset($_SESSION['username'])) {
	include('index.php');
	exit();
}

$rootPath = str_repeat("../", substr_count( substr($_SERVER['REQUEST_URI'], strpos($_SERVER['REQUEST_URI'], "e-dos2")) , '/') - 1 );	
$relPath = dirname($_SERVER['REQUEST_URI']);
include_once ($rootPath . 'classes.php');
?>

<!DOCTYPE html>
<html>
	<head>
		<title>Batuhan Taşdöven</title>
		<meta charset="utf-8">
		<link rel="icon" href="images/favicon.png" type="image/png">
		<link rel="stylesheet" type="text/css" href="css/style.css">
		<link rel="stylesheet" href="css/jquery-ui.min.css" />
		<script src="js/jquery-2.0.0.min.js"></script>
	    <script src="js/jquery-ui.min.js"></script>
		<script src="js/core.js"></script>
		<script>
			var username = "<?php if (isset($_SESSION['username'])) echo $_SESSION['username']; ?>";	
			
			function openWindow(url, title, icon, isExecutable, caller, params, left, top, width, height) {
				wind = new Window(url, title, icon, isExecutable, caller, params, left, top, width, height);
				
			}
			
			$(document).ready(function() {

			});
		</script>		
		<script>
		  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

		  ga('create', 'UA-48111628-1', 'btasdoven.me');
		  ga('send', 'pageview');

		</script>
		<style type="text/css">
			*:focus {
				
				outline : 0;
				border : 3px solid black;
			}
			
			
			* {
				-webkit-font-smoothing: antialiased;
				font-family: 'Ubuntu', sans-serif;
				font-size : 13px;
				
			}
			
			body, #container {
				color : rgb(227, 227, 617);
				background-color: rgb(48, 9, 36); 
				-webkit-user-select: none;
				-khtml-user-select: none;
				-moz-user-select: -moz-none;
				-ms-user-select: none;
				user-select: none;
			}
			#container {
				padding : 0;
				margin : 0;
				width : 100%;
				height : 100%;
				left : 0;
				top : 0;
				background-image : url('accounts/<?php echo $_SESSION['username']; ?>/desktop_bg.jpg');
				position : fixed;
				background-size: 100% 100%;
			}
			
			.folder {
				border-radius : 5px;
				margin : 5px;
				padding : 4px;
				min-width : 50px;
				text-align : center;
				background-size : 50px 50px;
				background-position : 50% 0;
				background-repeat : no-repeat;
				text-shadow: 0px 0px 5px rgba(0, 0, 0, 1),1px 1px 5px rgba(0, 0, 0, 1), 2px 2px 5px rgba(0, 0, 0, 1);
				color : rgba(220, 220, 220, 1);
				padding-top : 55px;
				list-style-type : none;
				display: inline-block;				
			}
			.folder:hover {
				background-color : rgba(255, 255, 255, 0.4);
				box-shadow: 0 0 2px rgba(200, 100, 100, 0.7);
				cursor : pointer;
			}
		</style>
	</head>
	<body style = "margin:0; padding:0;">			
		<div class="window">
			<div class = "window_cover"></div>
			<div class = "window_header">
				<img data-filename = '' src = '<?php echo $rootPath;?>images/filetypes/file.png' height = '20px' style = 'vertical-align: middle; margin-top: -1px;'><span class = "window_name" style = ""></span>
				<div id = "close_btn" class = "window_close_btn">×</div>
				<div id = "maximize_btn" class = "window_close_btn">□</div>
			</div>
			<div class = 'window_iframe_cont'>
				<iframe id = 'iframe' class = 'window_iframe' width = "100%" height = "100%" frameborder = "0"></iframe>
			</div>
		</div>
		
		<div id = "container">			
			<?php
				$cmdList = getCommandList();
				foreach ($cmdList as $cmd) {
					if ($cmd->windowed) {
						echo "<span class = 'folder' style = \"background-image : url('" . $cmd->path . "icon.png');\" ";
						echo "onclick = \"new Window('" . $cmd->getPath() . "', " .
													"'" . $cmd->name . "', " . 
													"'" . $cmd->path . "icon.png', " .
													"1);\">" . $cmd->name . "</span>";
					}
				}
			?>
			<span data-filename = "cv.pdf" class = 'folder' onclick = "new Window('files/cv2.pdf', '~/cv.pdf', 'images/filetypes/pdf.png', 0, undefined, undefined, 200, 20, 800, 600);">cv.pdf</span>
			<!--span data-filename = "ders_prog.png" class = 'folder' onclick = "new Window('bin/v/imgviewer/imgviewer.php', 'imgviewer ~/ders_prog.png', 'images/filetypes/png.png', 1, undefined, 'imgviewer ~/ders_prog.png', 300, 100, 764, 387);">ders_prog_old.png</span>			
			<span data-filename = "ccc.exc" class = 'folder' onclick = "new Window('bin/v/excel/excel.php', 'excel ~/ders_prog.exc', 'images/filetypes/exc.png', 1, undefined, 'excel ~/ders_prog.exc', 293, 27, 644, 486);">ders_prog.exc</span-->			
			
		</div>
		<!--div class = "window_header" style = "position : fixed; margin : 0; height : 25px; bottom :0px; width : 100%;">
		</div-->	
	</body>
</html>