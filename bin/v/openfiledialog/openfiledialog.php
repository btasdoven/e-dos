<?php
if(!isset($_SESSION))
    session_start();
if (!isset($_SESSION["username"]))
	exit();
	
$rootPath = str_repeat("../", substr_count( substr($_SERVER['REQUEST_URI'], strpos($_SERVER['REQUEST_URI'], "e-dos2")) , '/') - 1 );	
$relPath = dirname($_SERVER['REQUEST_URI']);
include_once ($rootPath . 'classes.php');
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
			
			var filepath;
			var selected;
			
			function openFolder(folder) {
				run("cd " + folder, function(data) {
					filepath = data.env.path + "/";
					$("#location").val(filepath);
					
					listEntries();
				});
			}
			
			function listEntries() {
				run("ls -nostyle", function(data) {
					var files = $.parseJSON(data.stdout);
					var out = "";
					for (var i = 0; i < files.length; ++i) {				
						if (files[i].fileType & 512)
							out += "<span data-filename = '" + files[i].name + "' class = 'entry folder'>" + files[i].name + "</span>";
						else
							out += "<span data-filename = '" + files[i].name + "' class = 'entry'>" + files[i].name + "</span>";			
					}
					
					$("#container").html(out);
																		
				});
			}
			
			$(document).ready(function() {
				sessiontoken = <?php echo generateSessionToken(); ?>
				
				openFolder(".");
				
				$(thou.window).find(".window_close_btn").click( function() {
				});
				
				$("#cancel").click( function() {
					filepath = "";
					$("#filename").val('');
					$("#open").trigger('click');
				});
				
				$("#open").click( function() {			
					if (typeof caller != 'undefined' && filepath.length > 0)
						caller.workIsDone(filepath + $("#filename").val());
						
					$(thou.window).find(".window_close_btn").trigger('click');
				});
				
				$(document).on('click', '.entry', function() {
					if ($(this).hasClass('folder')) {
						openFolder($(this).text());
					}
					else {
						if (typeof selected != 'undefined')
							selected.removeClass('selected');
						$(this).addClass("selected");
						selected = $(this);	
						$('#filename').val($(this).text());
					}
				});
				
				$(document).on('dblclick', '.entry', function() {
					if (!$(this).hasClass('folder')) {
						if (typeof selected != 'undefined')
							selected.removeClass('selected');
						$(this).addClass("selected");
						selected = $(this);	
						$('#filename').val($(this).text());
						$("#open").trigger('click');
					}
				});
	
			});
			
			
		</script>		
		<style type="text/css">
			*:not(input) {
				-webkit-font-smoothing: antialiased;
				font-family: 'Ubuntu', sans-serif;
				font-size : 13px;
				-webkit-touch-callout: none;
				-webkit-user-select: none;
				-khtml-user-select: none;
				-moz-user-select: -moz-none;
				-ms-user-select: none;
				user-select: none;
			}
			
			.entry {
				border-radius : 5px;
				margin : 5px;
				padding : 4px;
				min-width : 50px;
				text-align : center;
				background-size : 50px 50px;
				background-position : 50% 0;
				background-repeat : no-repeat;
				padding-top : 55px;
				list-style-type : none;
				display: inline-block;				
			}
			.entry:not(.selected):hover {
				background-color : rgba(255, 255, 255, 0.4);
				box-shadow: 0 0 2px rgba(200, 100, 100, 0.7);
				cursor : pointer;
			}
			
			.up {
				background-image : url('<?php echo $relPath;?>/up.png');
			}
			.folder {
				background-image : url('<?php echo $relPath;?>/folder.png');
			}
			.file {
				background-image : url('<?php echo $relPath;?>/file.png');
			}
			.selected {
				background-color : rgba(250, 208, 132, 1);
			}
			.menuitem {
				padding : 3px;
				padding-bottom : 4px;
				position : fixed;
				width : 16px;
				height : 16px;
				margin : 1px;
				background-size : 16px 16px;
				background-position : 3px 2px;
				background-repeat : no-repeat;
			}
			.menuitem:hover {
				background-color : rgba(255, 255, 255, 0.4);
				box-shadow: 0 0 2px rgba(200, 100, 100, 0.7);
				cursor : pointer;
			}
			#header {
				height : 25px;
				width : 100%;
				background-color : rgb(239, 235, 231);
			}
			#footer {
				position : fixed;
				bottom : 0px;
				padding-top : 5px;
				height : 70px;
				width : 100%;
				background-color : rgb(239, 235, 231);
			}
			.button
			{
				background: url(images/button.png) repeat-x;
				height: 25px;
				border: 1px solid #a6a6a6;
				border-radius: 4px;
				padding-left: 15px;
				padding-right: 15px;
				box-shadow: inset 0 0 5px 0 #EFEFEF, inset 0 1px #EEE;
				color: #555;
				text-shadow: 0 1px #DDD;
				font-family: Verdana, Serif;
				padding-bottom: 3px;
				cursor: pointer;
				width : 70px; 
			}
			.button:hover
			{
				background: url(images/button_hover.png) repeat-x;
				box-shadow: inset 0 0 5px 0 #EEE, inset 0 1px #FFF;
			}
			.button:active
			{
				background: url(images/button_active.png) repeat-x;
				border: 1px solid #999;
				box-shadow: inset 0 0 3px 0 #888;
				color: #444;			
			}
		</style>
	</head>
	<body style = "margin:0; padding:0;">	
		<div id = "header">
			<span style = 'padding-left : 5px; padding-right : 5px;'>Location:</span><input id = 'location' type = "text" style = "width : 250px; margin-right : 10px" value = "~/">
			<span class = 'menuitem up' onclick = "openFolder('..');"></span>
		</div>
		<div id = "container">			

		</div>
		<div id = "footer">
			<span style = 'padding-left : 5px; padding-right : 5px;'>File Name:</span><input id = 'filename' type = "text" style = "width : 200px; margin-right : 10px" value = "~/">
			<div style = "float : right; padding : 5px 14px 5px 5px">
				<button class = "button" id = 'open' style = 'margin-bottom : 5px;'>Open</button><br>
				<button class = "button" id = 'cancel'>Cancel</button>
			</div>
		</div>
	</body>
</html>