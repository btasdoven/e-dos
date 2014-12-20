<?php
	header('Content-type: text/html; charset=utf-8');
	if (!isset($_SESSION))
		session_start();
		
	$ipList = explode("\n", file_get_contents('visitors'));

	function getIP() { 
		$ip; 
		if (getenv("HTTP_CLIENT_IP")) 
			$ip = getenv("HTTP_CLIENT_IP"); 
		else if(getenv("HTTP_X_FORWARDED_FOR")) 
			$ip = getenv("HTTP_X_FORWARDED_FOR"); 
		else if(getenv("REMOTE_ADDR")) 
			$ip = getenv("REMOTE_ADDR"); 
		else 
			$ip = "UNKNOWN";
		return $ip; 
	}

	$myIp = getIP();
	
	$found = false;
	for ($i = 0; $i < count($ipList); ++$i) {
		$ip = explode(": ", $ipList[$i]);
		if ($ip[0] == $myIp) {
			$ip[1] = (intval($ip[1]) + 1);
			$ipList[$i] = $ip[0] . ": " . $ip[1] . ": " . date("Y-m-d H:i:s");
			$found = true;
		}
	}
	if (!$found)
		$ipList[] = $myIp . ': 1: ' . date("Y-m-d H:i:s");		

	file_put_contents('visitors', implode("\n", $ipList));	
		
	if (isset($_SESSION["username"])) {
		include("desktop.php");
		exit();
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Batuhan Taşdöven</title>
		<link rel="icon" href="images/favicon.png" type="image/png">
		<link href='http://fonts.googleapis.com/css?family=Ubuntu:400,500' rel='stylesheet' type='text/css'>
		<script src="js/jquery-2.0.0.min.js"></script>

		<script>
			$(document).ready(function() {
				$("#container").css("margin-top", -$("#container").height()/2 + 'px');
				$("#container").css("margin-left", -$("#container").width()/2 + 'px');
				$("#username").focus();
				
				$("#user_list li").click( function() {
					$(this).addClass("selected");
					$("#user_list").css("margin-bottom", "0px");
					$("#user_list li").not(this).hide();
					$("#password").val("");
					if ($("#user_list li.selected span").text() == "guest")
						$("#pw_enter").hide();
					else
						$("#pw_enter").show();
						
					$("#pw_area").show();
					$("#contact").hide();
					$("#password").width( $("#pw_enter").width() - $("#pw_label").width() - 5);
					$("#password").focus();
				});
				
				$("#cancel_btn").click( function() {
					$("#pw_error").hide();
					$("#user_list li.selected").removeClass("selected");
					$("#user_list").css("margin-bottom", "40px");
					$("#user_list li").show();	
					$("#contact").show();
					$("#pw_area").hide();
				});
				
					$('#password').keypress(function (e) {
					    if (e.which == 13) {
						    login();
					    }					  
					});
				
			});

			function login() {
				$("#pw_error").hide();
				$("#login_btn").prop('disabled', true);
				$.post("login.php", { "username" : $("#user_list li.selected span").text(), "password" : $("#password").val()  }, function(data) {
					//data = $.parseJSON(data.substr( data.indexOf("{") ));
					//$('html').html( data.html.substr( data.html.indexOf("<") ) );							
					if (data.indexOf("OK") > 0)
						window.location.replace('desktop.php');
					else
						$("#pw_error").show();
					
					$("#login_btn").prop('disabled', false);
				});
			}
		</script>
		
		<style type="text/css">

			*:focus {
				outline: 0;
			}
			
			* {
				-webkit-font-smoothing: antialiased;
				color : rgb(60, 58, 61);
				font-family: 'Ubuntu', sans-serif;
				text-shadow: 0px 0px 1px rgba(50, 50, 50, 0.2);
				font-size : 14px;
				font-weight : medium;
				cursor : default;
			}
			
			*:not(input){
				-webkit-user-select: none;
				-khtml-user-select: none;
				-moz-user-select: -moz-none;
				-ms-user-select: none;
				user-select: none;
			}
			#bg {
				background-image : url('images/bg.jpg');
				left : 0;
				top : 0;
				width : 100%;
				height : 100%;
				position : fixed;
				
			}
			
			#container {
	     	    position: fixed;
    		    top: 50%;
			    left: 50%;
				width : 280px;
				background-color : rgb(242, 241, 239);
				text-align : center;
				border : 1px solid rgb(255, 248, 249);
				-webkit-box-shadow: 0px 0px 1px 1px rgba(255, 248, 249, 0.4);
				box-shadow: 0px 0px 1px 1px rgba(255, 248, 249, 0.4);
			}
			#user_list {
				border : 1px solid rgb(194,193,191);
				background-color : white;
				list-style-type:none;
				text-align : left;
				padding-left : 0px;
				margin-left : 30px;
				margin-right : 30px;
				margin-bottom : 40px;
			}
			
			#user_list li:hover {
				border: 1px dotted rgb(194,193,191);
				background-color : rgb(242, 121, 71);
				color : white;	
				margin : -1px;
			}
			
			#user_list li.selected {
				border: 1px dotted rgb(194,193,191);
				background-color : rgb(242, 121, 71);
				color : white;	
				margin : -1px;
			}
			
			#user_list li:hover span, #user_list li.selected span{
				color : white;
			}
			
			.user_img {
				border-right : 1px solid rgb(216, 216, 216);
				vertical-align:middle;
				padding : 6px;
				margin-right : 10px;
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
			}
			.button:hover
			{
				background: url(images/button_hover.png) repeat-x;
				height: 25px;
				border: 1px solid #a6a6a6;
				border-radius: 4px;
				padding-left: 15px;
				padding-right: 15px;
				box-shadow: inset 0 0 5px 0 #EEE, inset 0 1px #FFF;
				color: #555;
				text-shadow: 0 1px #DDD;
				font-family: Verdana, Serif;
				padding-bottom: 3px;
				cursor: pointer;
			}
			.button:active
			{
				background: url(images/button_active.png) repeat-x;
				height: 25px;
				border: 1px solid #999;
				border-radius: 4px;
				padding-left: 15px;
				padding-right: 15px;
				box-shadow: inset 0 0 3px 0 #888;
				color: #444;
				text-shadow: 0 1px #DDD;
				font-family: Verdana, Serif;
				padding-bottom: 3px;
				cursor: pointer;
			}
			
			#pw_error {
				color : red;
				display : none;
			}
			
			#contact {
				cursor : pointer;
				float : right;
				margin-right : 5px;
				margin-bottom : 5px;
			}

		</style>
	</head>
	<body style = "margin:0; padding:0;">	
		<img src="images/bg.jpg" id="bg" />
		<div id = "container">			
				<!--img src = 'images/ubuntu-logo.png' style = "margin-top : 25px; margin-bottom : 15px" height = "64px"--><br><br><br><br>
				Login <br>
				<ul id = 'user_list'>
					<?php 
						$userList = unserialize(file_get_contents('users'));
						foreach ($userList as $user)
							if ($user["username"] != "root") {
								echo "<li><img class = 'user_img' src = 'images/user-icon.png' height = '42px'><span>";
								echo $user["username"];
								echo "</span></li>";
							}
					?>
				</ul>
				
				<div id = "pw_area" style = 'display : none; margin : 30px; margin-top : 15px;'>
					<div id = "pw_enter" style = "">
						<div id = "pw_error">Password is incorrect. Try again.</div>
						<span id = 'pw_label'> Password : </span><input id = "password" type = "password" style = 'cursor : auto; width : 48px'><br>
					</div>
					<div style = "float : right; margin: 20px; margin-right : 0px">
						<button id = "cancel_btn" class = "button">Cancel</button><button id = 'login_btn' class = "button" onclick = "login()">Login</button>
					</div>
				</div>
				
				<a id = "contact" target="_blank" href="mailto:btasdoven@gmail.com?subject=Account Request&amp;body=Hi,%0A%0A">Want to create an account?</a>
				
		</div>
	</body>

</html>