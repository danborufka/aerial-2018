<?php
defined("main-call") or die();
?>

<!DOCTYPE html>
<head>
	<title><?=$rb_configuration->title_of_web_application_backend?></title>
	<link href='http://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700' rel='stylesheet' type='text/css'>
	<meta name="viewport" content="width=device-width" />
	<meta name="author" content="Ing. Roman Breitschopf, BA">
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<link href="./lib/jqueryui/jquery-ui.css"  rel="stylesheet" type="text/css" />
	<link href="./css/general.css"           rel="stylesheet" type="text/css" />
	<link href="./css/header.css"            rel="stylesheet" type="text/css" />
	<link href="./css/user_detail_view.css" rel="stylesheet" type="text/css" />
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>
	<script src="./lib/jquery.js"></script>
	<script src="./lib/jqueryui/jquery-ui.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$( "#button-go-back").click(function() {
				$("#rb-container, .rb-button, table, label, select, input, a").addClass("loading");
			window.location.href = '<?php echo $rb_path_self?>?view=category_list'; // todo
			});
		});
	</script>
	</head>
<body>
	<div id="rb-container">
		<?php
			require $rb_configuration->relative_path_of_header_php;
			
			switch ($_SESSION["view"]) {
				case "user_new":
					$title = "Neuen Trainer oder User anlegen";
					break;
				case "user_detail":
					$title = "Trainer und User - Detail";
					break;
				case "user_change_password":
					if(isset($_SESSION["user_is_admin"]) && $_SESSION["user_is_admin"] == 1) {
						$title = "Passwort ändern";
					}else {
						$_SESSION["view"] = "user_detail";
						$title = "Trainer und User - Detail";						
					}
					break;
				default:
					$title = "View nicht gefunden - Fehler";
					break;
			}
		?>
		<div id="container">
			<div id="title">
				<h2><?php echo $title?></h2>
			</div>
	
			<?php /* ******  BUTTON BAR BEGIN ****** */ ?>
					
			<?php if(isset($_GET["w1"]) && $_GET["w1"] == "small") { ?>
			<div id="button-close" class="rb-button">
				Schließen
			</div>
			<?php } ?>
				<div id="button-user-list" class="rb-button">
					Zurück
				</div>
				<?php
				if ($_SESSION["view"] == 'user_detail') {?>
					<div id="button-change-password" class="rb-button">
						Passwort ändern
					</div>
				<?php } ?>			
					
			
	
			<?php /* ******  BUTTON BAR END ****** */ ?>
			
			
				<?php /* ******  INIT USER VALUES BEGIN ****** */
				
		
					if(!isset($_SESSION["u_login_name"])) {
						$_SESSION["u_login_name"] = "";
					}		
					if(!isset($_SESSION["u_prename"])) {
						$_SESSION["u_prename"] = "";
					}
					if(!isset($_SESSION["u_surname"])) {
						$_SESSION["u_surname"] = "";
					}
					if(!isset($_SESSION["u_email"])) {
						$_SESSION["u_email"] = "";
					}
					if(!isset($_SESSION["u_trainer"]) ) {
						$_SESSION["u_trainer"] = "1";
					}
					if(!isset($_SESSION["u_organizer"]) ) {
						$_SESSION["u_organizer"] = "0";
					}
					if(!isset($_SESSION["u_admin"]) ) {
						$_SESSION["u_admin"] = "0";
					}
					if(!isset($_SESSION["u_status"]) ) {
						$_SESSION["u_status"] = "1";
					}		
					if(!isset($_SESSION["u_password"])) {
						$_SESSION["u_password"] = "";
					}		
			       /* ******  INIT USER VALUES END ****** */
								  
								  
					if ($_SESSION["view"] == "user_detail" || $_SESSION["view"] == "user_change_password") {
						if(isset($_SESSION["u_no_reload_data"]) && $_SESSION["u_no_reload_data"] == "true")	{
							unset($_SESSION["u_no_reload_data"]);
						}else{							
							$db_functions->users->db_load_user_values_from_id($_SESSION["user_id"]);  // SET USER VALUES FROM ID *****
						}
					}
					
				
			       /* ******  USER DATA BEGIN ****** */
				if ($_SESSION["view"] != "user_change_password") { ?>
					<form id="u-detail-form" method="POST" action="process.php" class="input-container clear-fix">
						<div class="clearfix">
							<div id="input-column1">
								<div class="input-row">
									<label for="u_login_name">Login Name*:</label>
									<input name="u_login_name" type="text" id="u_login_name" value="<?php echo $_SESSION["u_login_name"] ?>">
								</div>
								<div class="input-row">
									<label for="u_prename">Vorname*:</label>
									<input name="u_prename" type="text" id="u_prename" value="<?php echo $_SESSION["u_prename"] ?>">
								</div>
								<div class="input-row">
									<label for="u_surname">Nachname*:</label>
									<input name="u_surname" type="text" id="u_surname" value="<?php echo $_SESSION["u_surname"] ?>">
								</div>
								<div class="input-row">
									<label for="u_email">E-Mail*:</label>
									<input name="u_email" type="text" id="u_email" value="<?php echo $_SESSION["u_email"] ?>">
								</div>
	
								<div class="input-row">
									<label for="u_trainer">Trainer*:</label>
									<select name='u_trainer' id='u_trainer' class='select-with-symbols'>
										<option value='1'<?php echo ($_SESSION["u_trainer"] == '1')? " selected":"" ?>>✔ </option>
										<option value='0'<?php echo ($_SESSION["u_trainer"] == '0')? " selected":"" ?>>✖ </option>
									</select>
								</div>
								<div class="input-row">
									<label for="u_organizer">Organizer*:</label>
									<select name='u_organizer' id='u_organizer' class='select-with-symbols'>
										<option value='1'<?php echo ($_SESSION["u_organizer"] == '1')? " selected":"" ?>>✔ </option>
										<option value='0'<?php echo ($_SESSION["u_organizer"] == '0')? " selected":"" ?>>✖ </option>
									</select>
								</div>
								<div class="input-row">
									<label for="u_admin">Administrator*:</label>
									<select name='u_admin' id='u_admin' class='select-with-symbols'>
										<option value='1'<?php echo ($_SESSION["u_admin"] == '1')? " selected":"" ?>>✔ </option>
										<option value='0'<?php echo ($_SESSION["u_admin"] == '0')? " selected":"" ?>>✖ </option>
									</select>
								</div>
								<div class="input-row">
									<label for="u_status">Status*:</label>
									<select name='u_status' id='u_status' class='select-with-symbols'>
										<option value='1'<?php echo ($_SESSION["u_status"] == '1')? " selected":"" ?>>✔ aktiviert</option>
										<option value='0'<?php echo ($_SESSION["u_status"] == '0')? " selected":"" ?>>✖  deaktiviert</option>
									</select>
								</div>
								<? if($_SESSION["view"] == "user_new") { ?>
								<div class="input-row">
									<label for="u_password">Passwort*:</label>
									<input name="u_password" type="text" id="u_password" autocomplete="off" value="<?php echo $_SESSION["u_password"] ?>">
								</div>
								<? } ?>
							</div>
		
							<?php if(isset($_GET["w1"]) && $_GET["w1"] == "small") { ?>
								<input name="w1" type="hidden" value="small">
							<?php }?>
							
							<input name="view" type="hidden" value="<?php echo $_SESSION["view"] ?>">
							<input name="u_no_reload_data" type="hidden" value = "true">
							<input name="u_admin_pw" type="password" id="u_admin_pw" autocomplete="off" value="" style="display: none;">
							
						
						</div>	
			  			 <?php     /* ******  USER DATA END ****** */
			   
			   	
			    
			    	           /* ******  BOTTOM BUTTON BAR BEGIN ****** */ ?>
						
	
						<div id="button-bar2">
							<?
							if ($_SESSION["view"] == 'user_detail')
							{ ?>	
							<input name="user_id" type="hidden" value="<?php echo $_SESSION["user_id"] ?>">
							<button form="u-detail-form" type="submit" name="action"
									value="u_update_user" class="rb-button">Speichern</button>
							<button form="u-detail-form" type="submit" name="action"
									value="u_update_user_back" class="rb-button">Speichern und Zurück</button>
							<button form="u-detail-form" type="submit" name="action"
									value="u_update_user_new" class="rb-button">Speichern und Neu</button>
							<?php
							}else{?>	
							<button form="u-detail-form" type="submit" name="action"
									value="u_insert_user" class="rb-button">Speichern</button>
							<button form="u-detail-form" type="submit" name="action"
									value="u_insert_user_back" class="rb-button">Speichern und Zurück</button>
							<button form="u-detail-form" type="submit" name="action"
									value="u_insert_user_new" class="rb-button">Speichern und Neu</button>
							<?php
							} ?>
						</div>
						
				    	    <?php  /* ******  BOTTOM BUTTON BAR END ****** */    ?>
					</form>
				<? } else {   // view = user_change_password
				
				
			      				 /* ******  USER DATA BEGIN FROM CHANGE PASSWORD****** */  ?>
				
					
					<form id="u-password-form" method="POST" action="process.php" class="input-container clear-fix">
						<div class="clearfix">
							<div id="input-column1">
								<div class="input-row">
									<label for="u_admin_name">Admin-Loginname*:</label>
									<input class="rb-readonly" name="u_admin_name" type="text" id="u_admin_name" readonly value="<?php echo $_SESSION["user_name"] ?>  ">
								</div>
								<div class="input-row">
									<label for="u_admin_pw">Admin-Passwort*:</label>
									<input name="u_admin_pw" type="password" id="u_admin_pw" autocomplete="off" value="">
								</div>
								<br/>							
								<br/>
								<div class="input-row">
									<label for="u_login_name">Loginname*:</label>
									<input class="rb-readonly" name="u_login_name" type="text" id="u_login_name" readonly value="<?php echo $_SESSION["u_login_name"] ?>">
								</div>
								<div class="input-row">
									<label for="u_password">Neues Passwort*:</label>
									<input name="u_password" type="text" id="u_password" autocomplete="off" value="<?php echo $_SESSION["u_password"] ?>">
								</div>
							</div>
		
							<?php if(isset($_GET["w1"]) && $_GET["w1"] == "small") { ?>
								<input name="w1" type="hidden" value="small">
							<?php }?>
							
							<input name="view" type="hidden" value="<?php echo $_SESSION["view"] ?>">
							<input name="u_no_reload_data" type="hidden" value = "true">
						
						</div>	
			  			 <?php     /* ******  USER DATA END FROM CHANGE PASSWORD****** */
			   
			   	
			    
			    	           /* ******  BOTTOM BUTTON BAR BEGIN FROM CHANGE PASSWORD****** */ ?>
						
	
						<div id="button-bar2">
							<input name="user_id" type="hidden" value="<?php echo $_SESSION["user_id"] ?>">
							<button form="u-password-form" type="submit" name="action"
									value="u_change_password" class="rb-button">Passwort speichern</button>
							<button form="u-password-form" type="submit" name="action"
									value="u_skip_pw_change" class="rb-button">Abbrechen</button>
							
						</div>
						
				    	    <?php  /* ******  BOTTOM BUTTON BAR END FROM CHANGE PASSWORD ****** */    ?>
					</form>
				<? } ?>
				
				<div id="u-save-message">
					<?php     /* ******  CONFIRMATION MESSAGE BEGIN  ****** */
					
						if(isset($_SESSION["u_success_msg"]) && $_SESSION["u_success_msg"]) {
							
							echo $_SESSION["u_success_msg"];
							$_SESSION["u_success_msg"] = false;
							
						}
						if(isset($_SESSION["u_error_msg"]) && $_SESSION["u_error_msg"] ) {
							echo $_SESSION["u_error_msg"];
							unset($_SESSION["u_error_msg"]);							
						}
						
							 /* ******  CONFIRMATION MESSAGE END ****** */   ?>
				</div>
			
			</div>
		</div>
	<
	</body>
	<script type="text/javascript">
	$(document).ready(function() {
		
		

		function rb_set_loading_effect() {
				$("#rb-container, .rb-button, table, label, select, input, a").addClass("loading");
		};
		
		$("form").submit(function(){   // loading effect
				rb_set_loading_effect();
		});	
		
		 $( "#saved_done" ).fadeOut( 5000, function() {});
		 
		<?php if(isset($_GET["w1"]) && $_GET["w1"] == "small") {
			echo "expand_get_string = '&w1=" . $_GET["w1"] . "';";
		}else{
			echo "expand_get_string = '';";
			
		} ?>
		 
		 
		<?php     /* ******  CLICK ACTION BEGIN  ****** */   ?>
		 
		 $("#button-user-list" ).click(function() {

			rb_set_loading_effect();
			<? if(isset($_SESSION["view"]) && $_SESSION["view"] == "user_change_password") { ?>
				
				window.location.href = '<?php echo $rb_path_self?>?view=user_detail&action=u_skip_pw_change';
			<? } else { ?>	
				
			window.location.href = '<?php echo $rb_path_self?>?view=user_list' + expand_get_string;
			<? } ?>
		});
		
		
		
		$( "#button-change-password").click(function() {
			window.location.href = '<?php echo $rb_path_self?>?view=user_change_password' + expand_get_string;		
		});
		
		$( "#button-close").click(function() {
			window.close();			
		});
		
		
		
		<?php     /* ******  CLICK ACTION END  ****** */   ?>
		
	});

	</script>
</html>