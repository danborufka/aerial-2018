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
					if(!isset($_SESSION["sca_pictures"])) {
						$_SESSION["sca_pictures"] = 0;
					}
					if(!isset($_SESSION["sca_filename_picture_1"])) {
						$_SESSION["sca_filename_picture_1"] = "";
					}
					if(!isset($_SESSION["sca_filename_picture_2"])) {
						$_SESSION["sca_filename_picture_2"] = "";
					}
					if(!isset($_SESSION["sca_filename_picture_3"])) {
						$_SESSION["sca_filename_picture_3"] = "";
					}
					if(!isset($_SESSION["sca_description"])) {
						$_SESSION["sca_description"] = "";
					}
					if(!isset($_SESSION["sca_status"]) ) {
						$_SESSION["sca_status"] = "1";
					}
					if(!isset($_SESSION["sca_is_kid_course"]) ) {
						$_SESSION["sca_is_kid_course"] = "0";
					}
					if(!isset($_SESSION["sca_auto_unsubscribe"]) ) {
						$_SESSION["sca_auto_unsubscribe"] = "0";
					}
					if(!isset($_SESSION["sca_conf_text"]) ) {
						$_SESSION["sca_conf_text"] = "";
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
							<br />
							<div class="input-row">
								<label for="sca_pictures">Bilderanzahl:</label>
								<select name="sca_pictures" id="sca_pictures">
									<option value="0" <?php echo ($_SESSION["sca_pictures"] == 0)? " selected":"" ?>>Keine Bilder auf der Homepage anzeigen</option>
									<option value="1" <?php echo ($_SESSION["sca_pictures"] == 1)? " selected":"" ?>>Ein Bild auf der Homepage anzeigen</option>
									<option value="2" <?php echo ($_SESSION["sca_pictures"] == 2)? " selected":"" ?>>Zwei Bilder auf der Homepage anzeigen</option>
									<option value="3" <?php echo ($_SESSION["sca_pictures"] == 3)? " selected":"" ?>>Drei Bilder auf der Homepage anzeigen</option>
								</select>
							</div>
							<div id="filename_row1" class="input-row button-test-included">
								<label for="sca_filename_picture_1">Dateiname 1*:</label>
								<input name="sca_filename_picture_1" type="text" id="sca_filename_picture_1" placeholder="Dateiname von Bild 1" value="<?php echo $_SESSION["sca_filename_picture_1"] ?>">
								<div id="button-test-filename1" class="rb-test-button">Bild testen</div>
							</div>
							<div id="filename_row2" class="input-row button-test-included">
								<label for="sca_filename_picture_2">Dateiname 2*:</label>
								<input name="sca_filename_picture_2" type="text" id="sca_filename_picture_2" placeholder="Dateiname von Bild 2" value="<?php echo $_SESSION["sca_filename_picture_2"] ?>">
								<div id="button-test-filename2" class="rb-test-button">Bild testen</div>
							</div>
							<div id="filename_row3" class="input-row button-test-included">
								<label for="sca_filename_picture_3">Dateiname 3*:</label>
								<input name="sca_filename_picture_3" type="text" id="sca_filename_picture_3" placeholder="Dateiname von Bild 3" value="<?php echo $_SESSION["sca_filename_picture_3"] ?>">
								<div id="button-test-filename3" class="rb-test-button">Bild testen</div>
							</div>
							<div class="input-row">
								<label for="sca_description">Text auf der Homepage:</label>
								<textarea id="sca_description" name="sca_description" rows="8" placeholder="Text für die Beschreibung der Kurse auf der Homepage"><?=$_SESSION["sca_description"]?></textarea>
							</div>
							<br />
							<br />
							<div class="input-row">
								<label for="sca_is_kid_course">Kinderkurs:</label>
								<select name="sca_is_kid_course" id="sca_is_kid_course">
									<option value="0" <?php echo ($_SESSION["sca_is_kid_course"] == 0)? " selected":"" ?>>nein</option>
									<option value="1" <?php echo ($_SESSION["sca_is_kid_course"] == 1)? " selected":"" ?>>ja, Kinderkurs</option>
								</select>
							</div>
							<div class="input-row">
								<label for="sca_auto_unsubscribe">Abmeldung:</label>
								<select name="sca_auto_unsubscribe" id="sca_auto_unsubscribe">
									<option value="0" <?php echo ($_SESSION["sca_auto_unsubscribe"] == 0)? " selected":"" ?>>keine automatisierte Abmeldung</option>
									<option value="1" <?php echo ($_SESSION["sca_auto_unsubscribe"] == 1)? " selected":"" ?>>automatisierte Abmeldung aktiviert</option>
								</select>
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
							<div class="input-row">
								<label for="sca_conf_text">Bestätigung:</label>
								<textarea id="sca_conf_text" name="sca_conf_text" rows="8" placeholder="Bestätigung per E-Mail: Leer lassen für die Verwendung des Standard- Bestätigungstexts"><?=$_SESSION["sca_conf_text"]?></textarea>
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
		
		$("#button-test-filename1").click(function() {
			var file_url = 'http://aerialsilks.at/content/images/workshops/' + $("#sca_filename_picture_1").val().replace(' ', '%20');
			window.open(file_url,'_blank')
		});
		$("#button-test-filename2").click(function() {
			var file_url = 'http://aerialsilks.at/content/images/workshops/' + $("#sca_filename_picture_2").val().replace(' ', '%20');
			window.open(file_url,'_blank')
		});
		$("#button-test-filename3").click(function() {
			var file_url = 'http://aerialsilks.at/content/images/workshops/' + $("#sca_filename_picture_3").val().replace(' ', '%20');
			window.open(file_url,'_blank')
		});
		
		<?php     /* ******  CLICK ACTION END  ****** */   ?>
		
		function update_picture_input_visibility() {
			var picture_count = $("#sca_pictures").val();
			
			if(picture_count == 0) {
				$("#filename_row1").hide();
				$("#filename_row2").hide();
				$("#filename_row3").hide();
			}
			if(picture_count == 1) {
				$("#filename_row1").show();
				$("#filename_row2").hide();
				$("#filename_row3").hide();
			}
			if(picture_count == 2) {
				$("#filename_row1").show();
				$("#filename_row2").show();
				$("#filename_row3").hide();
			}
			if(picture_count == 3) {
				$("#filename_row1").show();
				$("#filename_row2").show();
				$("#filename_row3").show();
			}
		}
		
		update_picture_input_visibility();
		
		$("#sca_pictures").on("change", function() {
			update_picture_input_visibility();
		});
		
		
	});

	</script>
</html>