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
		<script>
			var thou;
			var caller;
			var sessiontoken = '<?php $sessiontoken = md5(rand()); $_SESSION[$sessiontoken] = "~"; echo $sessiontoken;?>'
			var username = "<?php if (isset($_SESSION['username'])) echo $_SESSION['username']; ?>";
			
			var path = "~/";
			var cmdHistory = new Array();
			var currentCmd = 0;
			var firstCmd = 1;

			function myRun(input, callback) {	
				run(input, function(data) {
					runCallback(data, callback);
				});			
			}
						
			function runCallback(data, callback) {
				if (data.stderr.length)
					$('#past').append("<span style = 'color : red; text-shadow: 0px 0px 1px rgba(255, 0, 0, 0.5);'>" + data.stderr + "</span>");
				else if (data.stdout.length)
					$('#past').append(data.stdout);
				
				if (typeof data.debug !== 'undefined')
					$("#debug_window").html(data.debug);
					
				if (data.env.path != undefined)
					path = data.env.path;
					
				
				// Switch to another process...
				if (data.exec != undefined) {
					for(i = 0; i < data.exec.length; ++i) {
						$("#inp_container").hide();
						eval( data.exec[i] );
					}
				}
				else
					workIsDone("", callback);
			}
			
			function workIsDone(data, callback) {
				$("#path").html(username + "@btasdoven:" + path + "$&nbsp;");
				$("#inp_container").show();
				$("#inp").focus();
				$("html, body").click(function() {
					$('#inp').focus();
				});
				
				if (firstCmd <= 0) {
					$(document).scrollTop($(document).height());
				}
				else
					firstCmd--;
				
				if (typeof callback !== 'undefined')
					callback();

				$("#path").change();
			}

			$(document).ready(function() {
				
				
				$('#inp').focus();
				$("html, body").click(function() {
					$('#inp').focus();
				});
				
				$("#path").change( function() {
					$("#inp").width( $(window).width() - $("#path").width() - 25 );
				});
				
				myRun("cd");
				
				$('#inp').keydown(function(e){
					if (e.keyCode == 38) { 
						if (typeof cmdHistory[currentCmd-1] != 'undefined') {
							$("#inp").val(cmdHistory[--currentCmd]);
						}
						e.preventDefault();
					}
					else if (e.keyCode == 40) {
						if (typeof cmdHistory[currentCmd+1] != 'undefined')
							$("#inp").val(cmdHistory[++currentCmd]);
						else if ( currentCmd == cmdHistory.length-1 ) {
							currentCmd = cmdHistory.length;
							$("#inp").val("");
						}
						e.preventDefault();
					}
					/* auto_complete
					else if (e.keyCode == 9) {
						auto_complete($("#inp").val());
						e.preventDefault();
					}
					*/
				});

				$('#inp').keypress(function (e) {
					
				  if (e.which == 13) {
					doIt($("#inp").val());
				  }
				  
				  
				});
			});
			
			function doIt(input) {
				if (input.length == 0)
					return;
					
				cmdHistory[cmdHistory.length] = input;	
				currentCmd = cmdHistory.length;
				
				$("#past").append(username + "@btasdoven:" + path + "$&nbsp;" + input + "<br/>");
				$("#inp").val("");
				$("#path").html("");
				
				myRun(input);
				
			}
		</script>		
		<style type="text/css">
			*:focus {
				outline: 0;
			}
			
			* {
				-webkit-font-smoothing: antialiased;
				font-family: 'Ubuntu Mono', sans-serif;
				font-size : 15px;
				color : rgb(227, 227, 617);
				background-color: rgb(48, 9, 36); 
			}
			
			body, #inp, #container {
				cursor : text;
			}
			
			#past {
				word-wrap: break-word;
			}
			
			#inp {
				border : 0;
				margin : 0;
				padding : 0;
				position : absolute;
			}

			.ui-icon-gripsmall-diagonal-se {
				background-position: 16px 16px;
			}

		</style>
	</head>
	<body style = "margin:0; padding:0;">	
		<div id = "container">			
			<div id = 'past' style = 'width : 100%;'>
				Type 'cmd' to see available commands. <br>
			</div>
			<div id = 'inp_container' style = "width : 100%;">
				<span id = 'path'></span>
				<input id = "inp" type = "text" spellcheck="false">
			</div>
		</div>
	</body>
</html>