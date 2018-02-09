<?php
defined("main-call") or die();
?>

<!DOCTYPE html>
<head>
	<title>Aerial Silk Booking</title>
	<link href='https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700' rel='stylesheet' type='text/css'>
	<meta name="viewport" content="width=device-width" />
	<meta name="author" content="Ing. Roman Breitschopf, BA">
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<link href="./lib/jqueryui/jquery-ui.css"  rel="stylesheet" type="text/css" />
	<link href="./css/general.css"           rel="stylesheet" type="text/css" />
	<link href="./css/header.css"            rel="stylesheet" type="text/css" />
	<link href="./css/subcategory_detail_view.css" rel="stylesheet" type="text/css" />
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>
	<script src="./lib/jquery.js"></script>
	<script src="./lib/jqueryui/jquery-ui.js"></script>
	<script src="./lib/jqueryui/jquery-ui.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$( "#button-go-back").click(function() {
				$("#rb-container, .rb-button, table, label, select, input, a").addClass("loading");
			window.location.href = '<?php echo $rb_path_self?>?view=subcategory_list';
			});
		});
	</script>
	</head>
<body>
	<div id="rb-container">
		<?php
			require $rb_configuration->relative_path_of_header_php;
			
			switch ($_SESSION["view"]) {
				case "subcategory_new":
					$title = "Neue Unterkategorie anlegen";
					break;
				case "subcategory_detail":
					$title = "Unterkategorie- Details";
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
				<div id="button-bar">
					
				<?php if(isset($_GET["w1"]) && $_GET["w1"] == "small") { ?>
					<div id="button-close" class="rb-button">
						Schließen
					</div>
				<?php } ?>
					<div id="button-go-back" class="rb-button">
						Zurück
					</div>	
					
				</div>
	
			<?php /* ******  BUTTON BAR END ****** */ ?>
			
			
				<?php /* ******  INIT VALUES BEGIN ****** */
				
		
					if(!isset($_SESSION["sca_name"])) {
						$_SESSION["sca_name"] = "";
					}
					if(!isset($_SESSION["sca_sort_no"])) {
						$_SESSION["sca_sort_no"] = "50";
					}
					if(!isset($_SESSION["sca_category"])) {
						$_SESSION["sca_category"] = "-1";
					}
					if(!isset($_SESSION["sca_status"]) ) {
						$_SESSION["sca_status"] = "1";
					}
								
			       /* ******  INIT VALUES END ****** */
								  
								  
					if ($_SESSION["view"] == "subcategory_detail") {
						if(isset($_SESSION["sca_no_reload_data"]) && $_SESSION["sca_no_reload_data"] == "true")	{
							unset($_SESSION["sca_no_reload_data"]);
						}else{							
							$db_functions->subcategories->db_load_subcategory_values_from_id($_SESSION["subcat_id"]);  // SET VALUES FROM ID *****
						}
					}
					
				
			       /* ******  DATA BEGIN ****** */?>
				
				<form id="form" method="POST" action="process.php" class="input-container clear-fix">
					<div class="clearfix">
						<div id="input-column1">
							<div class="input-row">
								<label for="sca_name">Name*:</label>
								<input name="sca_name" type="text" id="sca_name" value="<?php echo $_SESSION["sca_name"] ?>">
							</div>
							<div class="input-row">
								
								<label for="sca_category">Kategorie*:</label>
								<select name='sca_category' id='sca_category'>
									<?php $db_functions->select_options->db_get_category_select_options($_SESSION["sca_category"], false); ?>
								</select>
							</div>
							<div class="input-row">
								<label for="sca_is_kid_course">Abmelde-Link in Bestätigungs-E-Mail</label>
								<? if(isset($_SESSION["sca_is_kid_course"]) && $_SESSION["sca_is_kid_course"] = 1) {
									$sca_is_kid_course = "checked";
								}else {
									$sca_is_kid_course = "";
								} ?>
								<input name="sca_is_kid_course" type="checkbox" id="sca_is_kid_course" value="1" <?=$sca_is_kid_course?>>
							</div>
							<div class="input-row">
								<label for="sca_auto_subscribe">Abmelde-Link in Bestätigungs-E-Mail</label>
								<? if(isset($_SESSION["sca_auto_subscribe"]) && $_SESSION["sca_auto_subscribe"] = 1) {
									$sca_auto_subscribe = "checked";
								}else {
									$sca_auto_subscribe = "";
								} ?>
								<input name="sca_auto_subscribe" type="checkbox" id="sca_auto_subscribe" value="1" <?=$sca_auto_subscribe?>>
								<span>Abmelde-Link in Bestätigungs-E-Mail</span>
							</div>
							<div class="input-row">
								<label for="sca_sort_no">Sortierung*:</label>
								<input name="sca_sort_no" type="text" id="sca_sort_no" value="<?php echo $_SESSION["sca_sort_no"] ?>">
							</div>

							<div class="input-row">
								
								<label for="sca_status">Status*:</label>
								<select name='sca_status' id='sca_status' class='select-with-symbols'>
									<option value='1'<?php echo ($_SESSION["sca_status"] == 1)? " selected":"" ?>>✔   aktiv</option>
									<option value='0'<?php echo ($_SESSION["sca_status"] == 0)? " selected":"" ?>>✖   deaktiviert</option>
								</select>
							</div>
						</div>
					</div>	
					<div id="button-bar2">
					<?php if(isset($_GET["w1"]) && $_GET["w1"] == "small") { ?>
						<input name="w1" type="hidden" value="small">
					<?php }?>
					<input name="view" type="hidden" value="<?php echo $_SESSION["view"] ?>">
					<input name="sca_no_reload_data" type="hidden" value = "true">
			   <?php     /* ******  DATA END ****** */
			   
			   
			   
			    	     /* ******  BOTTOM BUTTON BAR BEGIN ****** */
						
					if ($_SESSION["view"] == 'subcategory_detail')
					{ ?>	
						<input name="subcat_id" type="hidden" value="<?php echo $_SESSION["subcat_id"] ?>">
						<button form="form" type="submit" name="action"
								value="sca_update_subcategory" class="rb-button">Speichern</button>
						<button form="form" type="submit" name="action"
								value="sca_update_subcategory_back" class="rb-button">Speichern und zurück</button>
						<button form="form" type="submit" name="action"
								value="sca_update_subcategory_new" class="rb-button">Speichern und neu</button>
					<?php
					}else{?>	
						<button form="form" type="submit" name="action"
								value="sca_insert_subcategory" class="rb-button">Speichern</button>
						<button form="form" type="submit" name="action"
								value="sca_insert_subcategory_back" class="rb-button">Speichern und Zurück</button>
						<button form="form" type="submit" name="action"
								value="sca_insert_subcategory_new" class="rb-button">Speichern und neu</button>
					<?php
					} ?>
					</div>
			    	    <?php  /* ******  BOTTOM BUTTON BAR END ****** */    ?>
				</form>
				
				<div id="ca-save-message">
					<?php     /* ******  CONFIRMATION MESSAGE BEGIN  ****** */
					
						if(isset($_SESSION["sca_success_msg"]) && $_SESSION["sca_success_msg"]) {
							
							echo $_SESSION["sca_success_msg"];
							$_SESSION["sca_success_msg"] = false;
							
						}
						if(isset($_SESSION["sca_error_msg"]) && $_SESSION["sca_error_msg"] ) {
							echo $_SESSION["sca_error_msg"];
							unset($_SESSION["sca_error_msg"]);							
						}
						
							 /* ******  CONFIRMATION MESSAGE END ****** */   ?>
				</div>
				
			</div>
		</div>
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
			
		}       /* ******  CLICK ACTION BEGIN  ****** */   ?>
		 

		
		
		
		$( "#button-close").click(function() {
			window.close();			
		});
		
		
		
		<?php     /* ******  CLICK ACTION END  ****** */   ?>
		
	});

	</script>
</html>