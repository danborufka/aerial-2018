<?php
defined("main-call") or die();
?>

<!DOCTYPE html>
<head>
	<title><?=$rb_configuration->title_of_web_application_backend?></title>
	<link href='https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700' rel='stylesheet' type='text/css'>
	<meta name="viewport" content="width=device-width" />
	<meta name="author" content="Ing. Roman Breitschopf, BA">
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<link href="./lib/jqueryui/jquery-ui.css"  rel="stylesheet" type="text/css" />
	<link href="./css/general.css"           rel="stylesheet" type="text/css" />
	<link href="./css/header.css"            rel="stylesheet" type="text/css" />
	<link href="./css/location_detail_view.css" rel="stylesheet" type="text/css" />
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>
	<script src="./lib/jquery.js"></script>
	<script src="./lib/jqueryui/jquery-ui.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {		
			<?php if(isset($_GET["w1"]) && $_GET["w1"] == "small") {
				echo "expand_get_string = '&w1=" . $_GET["w1"] . "';";
			}else{
				echo "expand_get_string = '';";
		} ?>
			$( "#button-go-back").click(function() {
				$("#rb-container, .rb-button, table, label, select, input, a").addClass("loading");
				window.location.href = '<?php echo $rb_path_self?>?view=location_list' + expand_get_string;
			});
		});
	</script>
	</head>
<body>
	<div id="rb-container">
		<?php
			require $rb_configuration->relative_path_of_header_php;
			
			switch ($_SESSION["view"]) {
				case "location_new":
					$title = "Neuen Standort anlegen";
					break;
				case "location_detail":
					$title = "Standort - Detail";
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
				<div id="button-go-back" class="rb-button">
					Zurück
				</div>		
					
	
			<?php /* ******  BUTTON BAR END ****** */ ?>
			
			
				<?php /* ******  INIT location VALUES BEGIN ****** */
				
		
					if(!isset($_SESSION["l_location_name"])) {
						$_SESSION["l_location_name"] = "";
					}		
					if(!isset($_SESSION["l_short_name"])) {
						$_SESSION["l_short_name"] = "";
					}
					if(!isset($_SESSION["l_sort_no"])) {
						$_SESSION["l_sort_no"] = "50";
					}
					if(!isset($_SESSION["l_street"])) {
						$_SESSION["l_street"] = "";
					}
					if(!isset($_SESSION["l_street_number"]) ) {
						$_SESSION["l_street_number"] = "";
					}
					if(!isset($_SESSION["l_plz"]) ) {
						$_SESSION["l_plz"] = "";
					}
					if(!isset($_SESSION["l_ort"]) ) {
						$_SESSION["l_ort"] = "";
					}
					if(!isset($_SESSION["l_status"]) ) {
						$_SESSION["l_status"] = "1";
					}	
			       /* ******  INIT VALUES END ****** */
								  
								  
					if ($_SESSION["view"] == "location_detail") {
						if(isset($_SESSION["l_no_reload_data"]) && $_SESSION["l_no_reload_data"] == "true")	{
							unset($_SESSION["l_no_reload_data"]);
						}else{				
							$db_functions->locations->db_load_location_values_from_id($_SESSION["location_id"]);  // SET VALUES FROM ID *****
						}
					}
					
				
			       /* ******  location DATA BEGIN ****** */   ?>
					<form id="l-detail-form" method="POST" action="process.php" class="input-container clear-fix">
						<div class="clearfix">
							<div id="input-column1">
								<div class="input-row">
									<label for="l_short_name">Kurzname*:</label>
									<input name="l_short_name" type="text" id="l_short_name" value="<?php echo $_SESSION["l_short_name"] ?>">
								</div>
								<div class="input-row">
									<label for="l_location_name">Name (lang)*:</label>
									<input name="l_location_name" type="text" id="l_location_name" value="<?php echo $_SESSION["l_location_name"] ?>">
								</div>
								<div class="input-row">
									<label for="l_sort_no">Sortierung*:</label>
									<input name="l_sort_no" type="text" id="l_sort_no" value="<?php echo $_SESSION["l_sort_no"] ?>">
								</div>
								<div class="input-row">
									<label for="l_street">Straße*:</label>
									<input name="l_street" type="text" id="l_street" value="<?php echo $_SESSION["l_street"] ?>">
								</div>
								<div class="input-row">
									<label for="l_street_number">Hausnummer*:</label>
									<input name="l_street_number" type="text" id="l_street_number" value="<?php echo $_SESSION["l_street_number"] ?>">
								</div>
								<div class="input-row">
									<label for="l_plz">Postleitzahl*:</label>
									<input name="l_plz" type="text" id="l_plz" value="<?php echo $_SESSION["l_plz"] ?>">
								</div>
								<div class="input-row">
									<label for="l_ort">Ort*:</label>
									<input name="l_ort" type="text" id="l_ort" value="<?php echo $_SESSION["l_ort"] ?>">
								</div>
								<div class="input-row">
									<label for="l_status">Status*:</label>
									<select name='l_status' id='l_status' class='select-with-symbols'>
										<option value='1'<?php echo ($_SESSION["l_status"] == '1')? " selected":"" ?>>✔ aktiviert</option>
										<option value='0'<?php echo ($_SESSION["l_status"] == '0')? " selected":"" ?>>✖  deaktiviert</option>
									</select>
								</div>
							</div>
		
							<?php if(isset($_GET["w1"]) && $_GET["w1"] == "small") { ?>
								<input name="w1" type="hidden" value="small">
							<?php }?>
							
							<input name="view" type="hidden" value="<?php echo $_SESSION["view"] ?>">
							<input name="l_no_reload_data" type="hidden" value = "true">
						
						</div>	
			  			 <?php     /* ******  DATA END ****** */
			   
			   	
			    
			    	           /* ******  BOTTOM BUTTON BAR BEGIN ****** */ ?>
						
	
						<div id="button-bar2">
							<?
							if ($_SESSION["view"] == 'location_detail')
							{ ?>	
							<input name="location_id" type="hidden" value="<?php echo $_SESSION["location_id"] ?>">
							<button form="l-detail-form" type="submit" name="action"
									value="l_update_location" class="rb-button">Speichern</button>
							<button form="l-detail-form" type="submit" name="action"
									value="l_update_location_back" class="rb-button">Speichern und Zurück</button>
							<button form="l-detail-form" type="submit" name="action"
									value="l_update_location_new" class="rb-button">Speichern und Neu</button>
							<?php
							}else{?>	
							<button form="l-detail-form" type="submit" name="action"
									value="l_insert_location" class="rb-button">Speichern</button>
							<button form="l-detail-form" type="submit" name="action"
									value="l_insert_location_back" class="rb-button">Speichern und Zurück</button>
							<button form="l-detail-form" type="submit" name="action"
									value="l_insert_location_new" class="rb-button">Speichern und Neu</button>
							<?php
							} ?>
						</div>
						
				    	    <?php  /* ******  BOTTOM BUTTON BAR END ****** */    ?>
					</form>
			
				
				<div id="u-save-message">
					<?php     /* ******  CONFIRMATION MESSAGE BEGIN  ****** */
					
						if(isset($_SESSION["l_success_msg"]) && $_SESSION["l_success_msg"]) {
							
							echo $_SESSION["l_success_msg"];
							$_SESSION["l_success_msg"] = false;
							
						}
						if(isset($_SESSION["l_error_msg"]) && $_SESSION["l_error_msg"] ) {
							echo $_SESSION["l_error_msg"];
							unset($_SESSION["l_error_msg"]);							
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
		 

		
		$( "#button-close").click(function() {
			window.close();			
		});
		
		
		
		<?php     /* ******  CLICK ACTION END  ****** */   ?>
		
	});

	</script>
</html>