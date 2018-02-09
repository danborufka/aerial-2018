<!DOCTYPE html>
<head>
	<title>Login - Aerial Silk Booking</title>
	<link href='https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700' rel='stylesheet' type='text/css'>
	<meta name="viewport" content="width=device-width" />
	<meta name="author" content="Ing. Roman Breitschopf, BA">
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" href="./css/general.css" type="text/css" />
	<link rel="stylesheet" href="./css/header.css" type="text/css" />
	<link rel="stylesheet" href="./css/login_view.css" type="text/css" />
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>
	</head>
<body>
	<div id="super-container" class="clearfix">
		<?php require "header.php"; ?>
		<div id="container-main-menu">
			<div id="login-box">
				<div id="login-title">
					<h2>Login</h2>		
				</div>
				<div id="login-container">
				<?php echo (isset($_SESSION["login"]) && $_SESSION["login"]=="failed") ? '<p class="error-message">Benutzername und Passwort stimmen nicht überein. Bitte versuche es erneut.</p>' : ""; ?>			
					<form method="POST" action="process.php">
						<input id="input-username" class="login-field" type="text" name="username"
							value ="<?php echo isset($_SESSION["temp_username"]) ? $_SESSION["temp_username"] : ""; ?>" placeholder="Benutzername oder E-Mail">
						<input id="input-password" class="login-field" type="password" name="password"
							placeholder="Passwort">
						<div id="login-bottom-line">					
		<?php $_SESSION["login"]="processing" ?>
							<input type="hidden" name="login" value="processing">
							<input id="login-button" value="Login" type="submit">
							<div id="password-forget">Passwort vergessen?</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	<script type="text/javascript" src="./lib/jquery.js"></script>
	<script type="text/javascript">
		$( "#password-forget" ).click(function() {
			alert("Bitte kontaktiere die Organisatorin oder einen Administrator für das Zurücksetzen des Passworts.");
		});
		
		$("<link/>", {
		   rel: "stylesheet",
		   type: "text/css",
		   href: "./css/main_menu_view.css"
		}).appendTo("head");
		
	</script>
	<script src="./lib/jqueryui/jquery-ui.js"></script>
</body>
</html>