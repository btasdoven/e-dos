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
			var sessiontoken; 
			var username = "<?php if (isset($_SESSION['username'])) echo $_SESSION['username']; ?>";
			
			var filename;
			
			function openFile(name) {
				$.post("<?php echo $relPath; ?>/gedit_open.php", { filename : name, token : sessiontoken }, function(data) {
					data = $.parseJSON(data.substr( data.indexOf("{") ));
					
					if (data.error.length > 0) {
						//error
					}
					else {
						$(thou.window).find("span").html(data.filepath);
						
						linecount = (data.data.match(/\n/g) != null) ? data.data.match(/\n/g).length + 1 : 1;
						
						$("#text").val(data.data);
						$("#header span:eq(2)").removeClass("disable");		

						filename = data.filename;
					}
				});
			}
			
			$(document).ready(function() {

				sessiontoken = <?php echo generateSessionToken(); ?>
				
				$("#text").focus();
				
				$("html").click( function() {
					$("#text").focus();
				});
				
				$(document).on('click', '#save:not(.disable)', function() {
				   $.post("<?php echo $relPath; ?>/gedit_save.php", { filename : filename, data : $("#text").val() }, function(data) {
						alert(data);
					});
				});

				$(document).on('click', '#open:not(.disable)', function() {
					run("openfiledialog", function(data) {
						for (var i = 0; i < data.exec.length; ++i)
							eval(data.exec[i]);
					});
				});
				
				$(document).on('click', '#new:not(.disable)', function() {
					
				});
				
				<?php 
					if (isset($fname))
						echo "openFile('" . $fname . "');";
				?>
							
			});

			function workIsDone(name, callback) {
				if (typeof name != 'undefined')
					openFile(name);				
				if (typeof callback != 'undefined')
					callback();
			}
		</script>		
		<style type="text/css">
			*:focus {
				outline: 0;
			}
			
			#text {
				-webkit-font-smoothing: antialiased;
				font-family: 'Ubuntu', sans-serif;
				font-size : 14px;
				border : 0;
				padding : 0px;
				position : absolute;
				top : 35px;
				bottom : 20px;
				margin : 0;
				left : 2px;
				right : 2px;
				outline: none;
				resize: none;
				width: calc(100% - 4px);
			}
			
			#header {
				padding-left : 5px;
				position : fixed; 
				height : 35px;
				top : 0px; 
				left : 0px;
				width : 100%;
				background-color : rgb(239, 235, 231);
				-webkit-touch-callout: none;
				-webkit-user-select: none;
				-khtml-user-select: none;
				-moz-user-select: -moz-none;
				-ms-user-select: none;
				user-select: none;
			}
			
			.folder {
				height: 32px;
				width: 32px;
				padding: 1px;
				text-align: center;
				background-image: url('bin/v/gedit/menu.png');
				background-repeat: no-repeat;
				text-shadow: 0px 0px 5px rgba(0, 0, 0, 1);
				color: rgba(240, 240, 240, 0.9);
				list-style-type: none;
				display: inline-block;				
			}
			.folder:not(.disable):hover {
				background-color : rgba(255, 255, 255, 0.4);
				box-shadow: 0 0 1px rgba(150, 100, 100, 0.7);
				cursor : pointer;
			}
			
			.disable {
				-webkit-filter : grayscale(100%);
				opacity : 0.5;
			}
			
			#container {
				padding : 5px;
			}
			
			#footer {
				padding-left : 5px;
				position : fixed; 
				height : 20px;
				bottom : 0px; 
				left : 0px;
				width : 100%;
				background-color : rgb(239, 235, 231);
				-webkit-touch-callout: none;
				-webkit-user-select: none;
				-khtml-user-select: none;
				-moz-user-select: -moz-none;
				-ms-user-select: none;
				user-select: none;
			}
		</style>
	</head>
	<body style = "margin:0; padding:0;">
		<div id = "container">			
			<div id = "header">
				<span id = "new"  class = 'folder disable' style = "background-position : 0px 0px"></span>
				<span id = "open" class = 'folder'		   style = "background-position : -32px 0px"></span>
				<span id = "save" class = 'folder disable' style = "background-position : -64px 0px"></span>
			</div>
			<textarea id = 'text'></textarea>
			<div id = "footer">
			</div>
		</div>
	</body>
</html>