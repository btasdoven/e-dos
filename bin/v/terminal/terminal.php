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
		<link href='http://fonts.googleapis.com/css?family=Ubuntu+Mono:400&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
		<link rel="stylesheet" type="text/css" href="css/CmdStyles.css">
		<link rel="stylesheet" type="text/css" href="css/style.css">
		<script src="js/jquery-2.0.0.min.js"></script>
		<script src="js/core.js"></script>
		<script src="js/jquery.terminal-0.7.8.min.js"></script>
		<script>
			var thou;
			var caller;
			var sessiontoken = '<?php $sessiontoken = md5(rand()); $_SESSION[$sessiontoken] = "~"; echo $sessiontoken;?>'
			var username = "<?php if (isset($_SESSION['username'])) echo $_SESSION['username']; ?>";
			
			var path = "~";
			var gterm;

			function myRun(input, callback) {	
				run(input, function(data) {
					runCallback(data, callback);
				});			
			}
						
			function runCallback(data, callback) {
				if (data.stderr.length)
					gterm.echo("<span style = 'color : red; text-shadow: 0px 0px 1px rgba(255, 0, 0, 0.5);'>" + data.stderr + "</span>", {raw : true});
				
				else if (data.stdout.length)
					gterm.echo(data.stdout, {raw: true });
					
				if (data.env.path != undefined)
					path = data.env.path;
					
				
				// Switch to another process...
				if (data.exec != undefined) {
					for(i = 0; i < data.exec.length; ++i)
						eval( data.exec[i] );
				}
				else
					workIsDone("", callback);
			}
			
			function workIsDone(data, callback) {
				gterm.set_prompt(username + "@btasdoven:" + path + "$ ");
				
				$(thou.window.iframe).focus();	
				gterm.focus(true);
								
				if (typeof callback !== 'undefined')
					callback();
					
				gterm.resume();
				window.scrollTo(0, document.body.scrollHeight);
				
				
			}

			$(document).ready(function() {
				
				$("*").click( function(event) 
					{ 
					var myClass = event.target.nodeName;
				});
				
				gterm = $('#container').terminal(function(command, term) {
					if (command !== '') {
						gterm.pause();
						myRun(command);
					}
				}, {
					greetings: "Type 'cmd' to see available commands",
					name: 'js_demo',
					height : $('#container').height(),
					tabcompletion : true,	
					prompt: username + "@btasdoven:" + path + "$ ",
					completion : function (term, str, callback){
						callback(['cat', 'cd' , 'clear'
						, 'cmd', 'cp', 'logout', 'ls', 'man', 'mkdir', 
						'mv', 'pwd', 'rm' ,'rmdir', 'touch', 'vi', 'gedit',
						'terminal', 'explorer', 'imgviewer']);
						window.scrollTo(0, document.body.scrollHeight);
					}
					});	
				
				myRun("cd");
				
				$("html,body").click( function() {
					gterm.focus(true);
				});
				
				$(thou.window.iframe).focus();				
			});

		</script>		
		<style type="text/css">
		
			::-webkit-scrollbar {
				display : none;
			}
			
			.clipboard 
			{
				display : none;
			}
			
			.inverted
			{
				background-color : rgb(227, 227, 217);
				color: rgb(48, 9, 36); 
			}
			*:focus {
				outline: 0;
			}
			
			* {
				-webkit-font-smoothing: antialiased;
				font-family: 'Ubuntu Mono', sans-serif;
				font-size : 15px;
				color : rgb(227, 227, 217);
				background-color: rgb(48, 9, 36); 
			}
			
			body, #container {
				cursor : text;
			}
			
			#container
			{
				width : 100%;
				height : 100%;
				overflow-x : hidden;
			}

			.ui-icon-gripsmall-diagonal-se {
				background-position: 16px 16px;
			}

		</style>
	</head>
	<body style = "margin:0; padding:0;">	
		<div id = "container">			
			
		</div>
	</body>
</html>