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
	<link href="./css/course_detail_view.css" rel="stylesheet" type="text/css" />
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>
	<script src="./lib/jquery.js"></script>
	<script src="./lib/jqueryui/jquery-ui.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$( "#button-go-back").click(function() {
				$("#rb-container, .rb-button, table, label, select, input, a").addClass("loading");
				window.location.href = '<?php echo $rb_path_self?>?view=course_list';
			});
		});
	</script>
	</head>
<body>
	<div id="rb-container">
		<?php
			require $rb_configuration->relative_path_of_header_php;
			
			$course_detail_readonly = false;
			
			switch ($_SESSION["view"]) {
				case "course_new":
					$c_title = "Neuen Kurs anlegen";
					$course_number = "neu";
					break;
				case "course_copy":
					$c_title = "Neuen Kurs anlegen (Kopie)";
					$_SESSION["c_no_reload_data"] = false;
					$course_number = "neu";
					break;
				case "course_detail":
					$c_title = "Kursdetails";
					$course_number = $_SESSION["course_id"];
					break;
				default:
					$c_title = "View nicht gefunden - Fehler";
					$course_number = "";
					break;
			}
		?>
			<div id="container">
				<div id="title">
					<h2><?php echo $c_title?></h2>
				</div>
	
			<?php /* ******  BUTTON BAR BEGIN ****** */ ?>
				<div id="button-bar">
					<div id="button-go-back" class="rb-button3">
						Zurück
					</div>
					<? if(!($_SESSION["user_is_organizer"] == 1 || $_SESSION["user_is_admin"] == 1)) {
							echo "<br><br>Keine Berechtigung für diese Ansicht.";
							die;
					 } ?>
					
					<?php
					if ($_SESSION["view"] == 'course_new') {?>
							<div id="button-clear" class="rb-button3">
								Werte zurücksetzen
							</div>
					<?php
					
					}
					if ($_SESSION["view"] == 'course_detail') {?>
						<div id="button-registrations" class="rb-button3">
							Anmeldungen
						</div>
						<div id="button-attendance" class="rb-button3">
							Anwesenheitsliste
						</div>
						<div id="button-safety-check" class="rb-button3">
							Sicherheitsüberprüfung
						</div>
						<div id="button-course-notes" class="rb-button3">
							Kursnotizen
						</div>
						<div id="button-registration-link" class="rb-button3">
							Anmelde- Link
						</div>
						<? if($_SESSION["user_is_organizer"] == 1 || $_SESSION["user_is_admin"] == 1) { ?>
							<div id="button-copy-course" class="rb-button3">
								Kopie erstellen
							</div>
						<? } ?>
							
					<? } ?>			
					
				</div>
	
			<?php /* ******  BUTTON BAR END ****** */ ?>
			
			
				<?php /* ******  INIT COURSE VALUES BEGIN ****** */
				
		
					if(!isset($_SESSION["c_name"]) ||
							  $_SESSION["c_name"] == "default") {
							  $_SESSION["c_name"] = "";
					}
					if(!isset($_SESSION["c_trainer"]) ||
							  $_SESSION["c_trainer"] == "default") {
							  $_SESSION["c_trainer"] = "";
					}
					if(!isset($_SESSION["c_trainer2"]) ||
							  $_SESSION["c_trainer2"] == "default") {
							  $_SESSION["c_trainer2"] = "none";
					}
					if(!isset($_SESSION["c_location"]) ||
							  $_SESSION["c_location"] == "default") {
							  $_SESSION["c_location"] = 1;
					}
					if(!isset($_SESSION["c_count"]) ||
							  $_SESSION["c_count"] == "default") {
							  $_SESSION["c_count"] = 8;
					}
					if(!isset($_SESSION["c_category"]) ||
							  $_SESSION["c_category"] == "default") {
							  $_SESSION["c_category"] = "";
					}
					if(!isset($_SESSION["c_subcategory"]) ||
							  $_SESSION["c_subcategory"] == "default") {
							  $_SESSION["c_subcategory"] = "";
					}
					if(!isset($_SESSION["c_status"]) ||
							  $_SESSION["c_status"] == "default") {
							  $_SESSION["c_status"] = 1;
					}
					if(!isset($_SESSION["c_price"]) ||
							  $_SESSION["c_price"] == "default") {
							  $_SESSION["c_price"] = 100;
					}
					if(!isset($_SESSION["c_note1"]) ||
							  $_SESSION["c_note1"] == "default") {
							  $_SESSION["c_note1"] = "";
					}
					if(!isset($_SESSION["c_note2"]) ||
							  $_SESSION["c_note2"] == "default") {
							  $_SESSION["c_note2"] = "";
					}
					if(!isset($_SESSION["c_duration"]) ||
							  $_SESSION["c_duration"] == "default") {
							  $_SESSION["c_duration"] = 75;
					}
					if(!isset($_SESSION["c_time"]) ||
							  $_SESSION["c_time"] == "default") {
							  $_SESSION["c_time"] = "";
					}
					if(!isset($_SESSION["c_date1"]) ||
							  $_SESSION["c_date1"] == "default") {
							  $_SESSION["c_date1"] = "";
					}
					if(!isset($_SESSION["c_date2"]) ||
							  $_SESSION["c_date2"] == "default") {
							  $_SESSION["c_date2"] = "";
					}
					if(!isset($_SESSION["c_date3"]) ||
							  $_SESSION["c_date3"] == "default") {
							  $_SESSION["c_date3"] = "";
					}
					if(!isset($_SESSION["c_date4"]) ||
							  $_SESSION["c_date4"] == "default") {
							  $_SESSION["c_date4"] = "";
					}
					if(!isset($_SESSION["c_date5"]) ||
							  $_SESSION["c_date5"] == "default") {
							  $_SESSION["c_date5"] = "";
					}
					if(!isset($_SESSION["c_date6"]) ||
							  $_SESSION["c_date6"] == "default") {
							  $_SESSION["c_date6"] = "";
					}
					if(!isset($_SESSION["c_date7"]) ||
							  $_SESSION["c_date7"] == "default") {
							  $_SESSION["c_date7"] = "";
					}
					if(!isset($_SESSION["c_date8"]) ||
							  $_SESSION["c_date8"] == "default") {
							  $_SESSION["c_date8"] = "";
					}
					if(!isset($_SESSION["c_date9"]) ||
							  $_SESSION["c_date9"] == "default") {
							  $_SESSION["c_date9"] = "";
					}
					if(!isset($_SESSION["c_date10"]) ||
							  $_SESSION["c_date10"] == "default") {
							  $_SESSION["c_date10"] = "";
					}
					if(!isset($_SESSION["c_todo"]) ||
							  $_SESSION["c_todo"] == "default") {
							  $_SESSION["c_todo"] = "";
					}
					if(!isset($_SESSION["c_registration_code"]) ||
							  $_SESSION["c_registration_code"] == "default") {
							  $_SESSION["c_registration_code"] = "1";
					}
					if(!isset($_SESSION["c_publishing"]) ||
							  $_SESSION["c_publishing"] == "default") {
							  $_SESSION["c_publishing"] = "1";
					}
					if(!isset($_SESSION["c_conf_text"]) ||
							  $_SESSION["c_conf_text"] == "default") {
							  $_SESSION["c_conf_text"] = "";
					}
					if(!isset($_SESSION["c_not_on"]) ||
							  $_SESSION["c_not_on"] == "default") {
							  $_SESSION["c_not_on"] = "";
					}
					if(!isset($_SESSION["c_precondition"]) ||
							  $_SESSION["c_precondition"] == "default") {
							  $_SESSION["c_precondition"] = "";
					}
					if(!isset($_SESSION["c_textblock_mode"]) ||
							  $_SESSION["c_textblock_mode"] == "default") {
							  $_SESSION["c_textblock_mode"] = 0;
					}
					if(!isset($_SESSION["c_textblock"]) ||
							  $_SESSION["c_textblock"] == "default") {
							  $_SESSION["c_textblock"] = "";
					}
					
			       /* ******  INIT COURSE VALUES END ****** */
								  
								  
					if ($_SESSION["view"] == 'course_detail' || $_SESSION["view"] == 'course_copy') {
						if(isset($_SESSION["c_no_reload_data"]) && $_SESSION["c_no_reload_data"] == "true")	{
							unset($_SESSION["c_no_reload_data"]);
						}else{							
							$db_functions->courses->db_load_course_values_from_id($_SESSION["course_id"]);  // SET COURSE VALUES FROM ID *****
						}
					}
				
			       /* ******  COURSE DATA BEGIN ****** */?>
				
				<form id="c-detail-form" method="POST" action="process.php" class="input-container clear-fix">
					<div class="clearfix">
						<div id="input-column1">
							<div class="input-row">
								<label for='c-course-nr'>Kurs- Nr.:</label>
								<div style="display: inline-block; margin-bottom: 15px">
									<?=$course_number?>
								</div>
							</div>
							<div class="input-row">
								<label for='c-category'>Kategorie*:</label>
								<select name='c_category' id='c-category'>";
									<?php $db_functions->select_options->db_get_category_select_options($_SESSION["c_category"], false, true); ?>
								</select>
							</div>
							<div class="input-row">
								<label for='c-subcategory'>Unterkat.:*</label>
								<select name='c_subcategory' id='c-subcategory'>";
									<?php $db_functions->select_options->db_get_subcategory_select_options($_SESSION["c_subcategory"], false, true); ?>
								</select>
							</div>
							<div class="input-row">
								<label for="c-name">Kursname*:</label>
								<input class="input-short-width" name="c_name" type="text" id="c-name" value="<?php echo $_SESSION["c_name"] ?>">
								<div id="button-auto-fill-name" class="rb-mini-button"> ◀ </div>
							</div>
							<div class="input-row">						
								
								<label for='c-trainer'>Trainer*:</label>
								<select name='c_trainer' id='c-trainer'>
									<?php $db_functions->select_options->db_get_trainer_select_options($_SESSION["c_trainer"], false, true, true, true, -1); ?>
								</select>
								
							</div>
							<div class="input-row">						
								
								<label for='c-trainer2'>Trainer 2:</label>
								<select name='c_trainer2' id='c-trainer2'>
									<?php $db_functions->select_options->db_get_trainer_select_options($_SESSION["c_trainer2"], false, true, true, true, "none"); ?>
								</select>
							
							</div>
							<div class="input-row">
							<label for='c-location'>Standort*:</label>
								<select name='c_location' id='c-location'>
									<?php $db_functions->select_options->db_get_location_select_options($_SESSION["c_location"], false, true); ?>
								</select>
							</div>
							<div class="input-row">
								<label for="c-price">Preis*:</label>
								<input style="display:inline-block" name="c_price" type="text" id="c-price" value="<?php echo $_SESSION["c_price"] ?>">
							</div>
							<div class="input-row">
								<label for="c-count">Teilnehmer*:</label>
								<input name="c_count" type="number" id="c-count" value="<?php echo $_SESSION["c_count"] ?>">
							</div>
							<div class="input-row">
								<label for="c-precondition">Voraus-<br>setzungen:</label>
								<textarea style="min-height: 54px;" id="c-precondition" name="c_precondition" rows="2" placeholder="leer lassen wenn keine Voraussetzung erforderlich"><?php echo $_SESSION["c_precondition"];?></textarea>		
							</div>
							<div class="input-row">
								<label for="c-status">Status*:</label>
								
								<select name='c_status' id='c-status' class="select-with-symbols">
								
									<?php if($_SESSION["c_status"] == -1) {
									echo "
										  <option style='display:none' value= -1 disabled selected></option>";
									} ?>
									<option value='1'<?php echo ($_SESSION["c_status"] == 1)? " selected":"" ?>>✎   in Bearbeitung</option>
									<option value='2'<?php echo ($_SESSION["c_status"] == 2)? " selected":"" ?>>✔   aktiv</option>
									<option value='3'<?php echo ($_SESSION["c_status"] == 3)? " selected":"" ?>>✔✔  bezahlt (Bezahlung vollständig)</option>
									<option value='4'<?php echo ($_SESSION["c_status"] == 4)? " selected":"" ?>>✔✔✔ erledigt (bezahlt, Trainer entlohnt)</option>
									<option value='0'<?php echo ($_SESSION["c_status"] == 0)? " selected":"" ?>>✖   deaktiviert</option>
								</select>
							</div>
							<div class="input-row">
								<label for="c-publishing">Veröffentl.:</label>
								
								<select name='c_publishing' id='c-publishing' class="select-with-symbols">
									<option value='1'<?php echo ($_SESSION["c_publishing"] == 1)? " selected":"" ?>>bereit zur Veröffentlichung</option>
									<option value='2'<?php echo ($_SESSION["c_publishing"] == 2)? " selected":"" ?>>veröffentlicht solange Status aktiv</option>
									<option value='3'<?php echo ($_SESSION["c_publishing"] == 3)? " selected":"" ?>>nicht veröffentlicht</option>
								</select>
							</div>
						</div>
		
						<div id="input-column2">
							
							<div class="input-row">
								<label for="c-duration">Dauer*:</label>
								<input name="c_duration" type="number" id="c-duration" value="<?php echo $_SESSION["c_duration"] ?>">
							</div>
							<div class="input-row">
								<label for="c-time">Uhrzeit*:</label>
								<input name="c_time" type="text" id="c-time" value="<?php echo $_SESSION["c_time"] ?>">
							</div>
							<div class="input-row">
								<label for="c-date1" id="c-label-datepicker">Termin 1*:</label>
								<input name="c_date1" type="text" id="c-date1" value="<?php echo $_SESSION["c_date1"] ?>">
							</div>
							<div class="input-row">
								<label for="c-date2" id="c-label-datepicker">Termin 2:</label>
								<input class="input-short-width" name="c_date2" type="text" id="c-date2" value="<?php echo $_SESSION["c_date2"]?>">
								<div id="button-auto-fill-date2" class="rb-mini-button"> ◀ </div>
							</div>
							<div class="input-row">
								<label for="c-date3" id="c-label-datepicker">Termin 3:</label>
								<input class="input-short-width" name="c_date3" type="text" id="c-date3" value="<?php echo $_SESSION["c_date3"] ?>">
								<div id="button-auto-fill-date3" class="rb-mini-button"> ◀ </div>
							</div>
							<div class="input-row">
								<label for="c-date4" id="c-label-datepicker">Termin 4:</label>
								<input class="input-short-width" name="c_date4" type="text" id="c-date4" value="<?php echo $_SESSION["c_date4"] ?>">
								<div id="button-auto-fill-date4" class="rb-mini-button"> ◀ </div>
							</div>
							<div class="input-row">
								<label for="c-date5" id="c-label-datepicker">Termin 5:</label>
								<input class="input-short-width" name="c_date5" type="text" id="c-date5" value="<?php echo $_SESSION["c_date5"] ?>">
								<div id="button-auto-fill-date5" class="rb-mini-button"> ◀ </div>
							</div>
							<div class="input-row">
								<label for="c-date6" id="c-label-datepicker">Termin 6:</label>
								<input class="input-short-width" name="c_date6" type="text" id="c-date6" value="<?php echo $_SESSION["c_date6"] ?>">
								<div id="button-auto-fill-date6" class="rb-mini-button"> ◀ </div>
							</div>
							<div class="input-row">
								<label for="c-date7" id="c-label-datepicker">Termin 7:</label>
								<input class="input-short-width" name="c_date7" type="text" id="c-date7" value="<?php echo $_SESSION["c_date7"] ?>">
								<div id="button-auto-fill-date7" class="rb-mini-button"> ◀ </div>	
							</div>
							<div class="input-row">
								<label for="c-date8" id="c-label-datepicker">Termin 8:</label>
								<input class="input-short-width" name="c_date8" type="text" id="c-date8" value="<?php echo $_SESSION["c_date8"] ?>">
								<div id="button-auto-fill-date8" class="rb-mini-button"> ◀ </div>
							</div>
							<div class="input-row">
								<label for="c-date9" id="c-label-datepicker">Termin 9:</label>
								<input class="input-short-width" name="c_date9" type="text" id="c-date9" value="<?php echo $_SESSION["c_date9"] ?>">
								<div id="button-auto-fill-date9" class="rb-mini-button"> ◀ </div>
							</div>
							<div class="input-row">
								<label for="c-date10" id="c-label-datepicker">Termin 10:</label>
								<input class="input-short-width" name="c_date10" type="text" id="c-date10" value="<?php echo $_SESSION["c_date10"] ?>">
								<div id="button-auto-fill-date10" class="rb-mini-button"> ◀ </div>
							</div>
							<div class="input-row">
								<label for="c-not-on">entfällt am:</label>
								<input name="c_not_on" type="text" id="c-not-on" placeholder="leer lassen wenn nichts ausfällt" value="<?php echo $_SESSION["c_not_on"] ?>">
							</div>
						</div>
						<div id="input-column3">
							
							<div class="input-row">
								<label for="c-todo">Aufgabe:</label>
								
								<select name='c_todo' id='c-todo' >
								
									<option value='0'<?php echo ($_SESSION["c_todo"] == 0)? " selected":"" ?>>keine offene Aufgabe</option>
									<option value='1'<?php echo ($_SESSION["c_todo"] == 1)? " selected":"" ?>>offene Aufgabe</option>
								</select>
								
							</div>
							
							<div class="input-row">
								<label for="c-note1">Kursnotizen:</label>
								
								<textarea id="c-note1" name="c_note1" rows="11"><?php echo $_SESSION["c_note1"];?></textarea>
								
							</div>
							<? if($_SESSION["user_is_organizer"] == 1 || $_SESSION["user_is_admin"] == 1) { ?>
								<div class="input-row">
									<label for="c-note2">Kursnotizen (verborgen):</label>
									<textarea id="c-note2" name="c_note2" rows="11"><?php echo $_SESSION["c_note2"];?></textarea>
								</div>
							<? } ?>
							<?/*<div class="input-row">
								<label for="c-source">Source Code:</label>
								
								<textarea id="c-source" name="c-source" rows="4" readonly><?php
								
								if($_SESSION["view"] == 'course_detail')								
								echo htmlspecialchars("<a href='http://" . $_SERVER['SERVER_NAME'] . "/anmeldung/?kurs=" . $_SESSION["course_id"] . "&code=" . $_SESSION["c_registration_code"] . "'
target='_blank' class='rb-button'>Anmelden</a>");?></textarea>
								
							</div>*/?>
						</div>
						<div id="input-column4">
							<div class="input-row">
								<label for="c-conf-text">Bestätigung:</label>
								
								<textarea id="c-conf-text" name="c_conf_text" rows="8" placeholder="leer lassen für die Verwendung des Standard- Bestätigungstexts."><?php echo $_SESSION["c_conf_text"];?></textarea>
								
							</div>
							<div class="input-row">
								<label for="c-textblock-mode">Textblock:</label>
								<select name='c_textblock_mode' id='c-textblock-mode' >
									<option value='0'<?php echo ($_SESSION["c_textblock_mode"] == 0)? " selected":"" ?>>automatisiert</option>
									<option value='1'<?php echo ($_SESSION["c_textblock_mode"] == 1)? " selected":"" ?>>Manueller Textblock</option>
								</select>
							</div>
							<div class="input-row">
								<label for="c-textblock">Manueller<br>Textblock:</label>
								<textarea id="c-textblock" name="c_textblock" rows="14" placeholder="leer lassen wenn nicht in Verwendung"><?php echo $_SESSION["c_textblock"];?></textarea>
								
							</div>
						</div>
						
						<input name="view" type="hidden" value="<?php echo $_SESSION["view"] ?>">
						<input name="c_no_reload_data" type="hidden" value = "true">
	
					</div>	
					<div id="button-bar2">
						
					
					
			   <?php     /* ******  COURSE DATA END ****** */
			   
			   
			   
			    	           /* ******  BOTTOM BUTTON BAR BEGIN ****** */
						
						if ($course_detail_readonly == false) {
							if ($_SESSION["view"] == 'course_detail')
							{ ?><div>	
									<input name="course_id" type="hidden" value="<?=$_SESSION["course_id"] ?>">
									<button form="c-detail-form" type="submit" name="action" value="c_update_course" class="rb-button3" style='margin-left: 0px;'>Speichern</button>
									<button form="c-detail-form" type="submit" name="action" value="c_update_back" class="rb-button3">Speichern und Zurück</button>
									<button form="c-detail-form" type="submit" name="action" value="c_update_new" class="rb-button3">Speichern und Neu</button>
								</div>
							<?
							}else{?>
								<div>		
									<button form="c-detail-form" type="submit" name="action" value="c_insert_course" class="rb-button3" style='margin-left: 0px;'>Speichern</button>
									<button form="c-detail-form" type="submit" name="action" value="c_insert_course_back" class="rb-button3">Speichern und Zurück</button>
									<button form="c-detail-form" type="submit" name="action" value="c_insert_course_new" class="rb-button3">Speichern und Neu</button>
								</div>
							<?
							}
						}else { ?>
						
								<div>		
									<div id="button-unlock" class="rb-button3"'>Sperre aufheben</div>
								</div>
						<? } ?>
					</div>
					
					
			    	    <?php  /* ******  BOTTOM BUTTON BAR END ****** */    ?>
				</form>
				
				<div id="c-save-message">
					<?php     /* ******  CONFIRMATION MESSAGE BEGIN  ****** */
					
						if(isset($_SESSION["c_success_msg"]) && $_SESSION["c_success_msg"]) {
							
							echo $_SESSION["c_success_msg"];
							$_SESSION["c_success_msg"] = false;
							
						}
						if(isset($_SESSION["c_error_msg"]) && $_SESSION["c_error_msg"] ) {
							echo $_SESSION["c_error_msg"];
							unset($_SESSION["c_error_msg"]);							
						}
						
							 /* ******  CONFIRMATION MESSAGE END ****** */   ?>
				</div>
				
			</div>
		</div>
	</body>
	<script type="text/javascript">
	$(document).ready(function() {
		
		
		
		function rb_init_datepicker(p_element) {				
			p_element.datepicker({
				<?php $rb_configuration->get_datepicker_options(); ?>
			});
			
		};
		
		function rb_set_loading_effect() {
				$("#rb-container, .rb-button, table, label, select, input, a").addClass("loading");
		};
		
		$("form").submit(function(){   // loading effect
				rb_set_loading_effect();
		});	
		
		 $( "#saved_done" ).fadeOut( 5000, function() {});
		 
		 
		 
		<?php     /* ******  CLICK ACTION BEGIN  ****** */   ?>
		 
		
		 $("#button-clear" ).click(function() {

			rb_set_loading_effect();
			window.location.href = '<?php echo $rb_path_self?>?view=<?php if(isset($_SESSION["view"])) echo $_SESSION["view"]?>&action=reset_course_values';

		});
		
		 $("#button-registrations" ).click(function() {

			rb_set_loading_effect();
			window.location.href = '<?php echo $rb_path_self?>?view=course_registrations&r_course=' +
																'<?php if(isset($_SESSION["course_id"])) echo $_SESSION["course_id"] ?>&reg=0';
		});
		 $("#button-attendance" ).click(function() {

			rb_set_loading_effect();
			window.location.href = '<?php echo $rb_path_self?>?view=attendance&course=' +
																'<?php if(isset($_SESSION["course_id"])) echo $_SESSION["course_id"] ?>';
		});
		 $("#button-safety-check" ).click(function() {
			rb_set_loading_effect();
			window.location.href = '<?php echo $rb_path_self?>?view=safety_check&course=' +
																'<?php if(isset($_SESSION["course_id"])) echo $_SESSION["course_id"] ?>';
		});
		 $("#button-course-notes" ).click(function() {
			rb_set_loading_effect();
			window.location.href = '<?php echo $rb_path_self?>?view=course_notes&course=' +
																'<?php if(isset($_SESSION["course_id"])) echo $_SESSION["course_id"] ?>';
		});
		$( "#button-copy-course" ).click(function() {
			rb_set_loading_effect();
			window.location.href = '<?php echo $rb_path_self?>?view=course_copy&course=' +
																'<?php if(isset($_SESSION["course_id"])) echo $_SESSION["course_id"] ?>';
		});
		
		
		$( "#button-auto-fill-name" ).click(function() {
			if($("select[name='c_subcategory'").val() == -1 ) {
				$("#c-name").val($("select[name='c_category'] option:selected").text());
			}else {
				$("#c-name").val($("select[name='c_subcategory'] option:selected").text());
			}
		});
			
		
		function change_subcut_options() {
			$(".rb-options-subcat-all:not(.rb-options-subcat-" + $('#c-category').val() + ")").hide();
			$(".rb-options-subcat-all:not(.rb-options-subcat-" + $('#c-category').val() + ")").attr('selected', false)
			$(".rb-options-subcat-" + $('#c-category').val()).show();
		};
		
		change_subcut_options();
		$("select[name='c_category']").on('change', function () {
			change_subcut_options();
		});

		function get_next_week_date(pre_date) {
			var rb_date = new Date(pre_date.substring(6,10) + '-' + pre_date.substring(3,5) + '-' + pre_date.substring(0,2));
			rb_date = new Date (rb_date.getTime() + 7*24*60*60*1000);
			var m = (rb_date.getMonth()+1).toString();
			var d = rb_date.getDate().toString();
			if(m.length == 1) {m = '0' + m;}
			if(d.length == 1) {d = '0' + d;}
			return(d + '.' + m + '.' + rb_date.getFullYear());
		}
		
		<? if ($course_detail_readonly) { ?>
			
			
		$("input:not([type='submit']), select, textarea").prop('readonly', true);
		
		$("input:not([type='submit']), select, textarea").css('background','grey');	
			
		<? }else { ?>
			
			
				rb_init_datepicker($( "#c-date1, #c-date2, #c-date3, #c-date4, #c-date5, #c-date6, #c-date7, #c-date8, #c-date9, #c-date10" ));
			
				$( "#button-auto-fill-name" ).click(function() {
					if($("select[name='c_subcategory'").val() == -1 ) {
						$("#c-name").val($("select[name='c_category'] option:selected").text());
					}else {
						$("#c-name").val($("select[name='c_subcategory'] option:selected").text());
					}
				});
				
				$("#button-auto-fill-date2").click(function() {
					if($("#c-date1").val().length == 0 || $("#c-date1").val() == "") {
						alert("Bitte zunächst Datum in Termin 1 eintragen.");
					}else {
						rb_new_date = get_next_week_date($("#c-date1").val());
						$("#c-date2").val(rb_new_date);
					}
				});
				$("#button-auto-fill-date3").click(function() {
					if($("#c-date2").val().length == 0 || $("#c-date2").val() == "") {
						$("#c-date3").val("");
					}else {
						rb_new_date = get_next_week_date($("#c-date2").val());
						$("#c-date3").val(rb_new_date);
					}
				});
				$("#button-auto-fill-date4").click(function() {
					if($("#c-date3").val().length == 0 || $("#c-date3").val() == "") {
						$("#c-date4").val("");
					}else {
						rb_new_date = get_next_week_date($("#c-date3").val());
						$("#c-date4").val(rb_new_date);
					}
				});	
				$("#button-auto-fill-date5").click(function() {
					if($("#c-date4").val().length == 0 || $("#c-date4").val() == "") {
						$("#c-date5").val("");
					}else {
						rb_new_date = get_next_week_date($("#c-date4").val());
						$("#c-date5").val(rb_new_date);
					}
				});
				$("#button-auto-fill-date6").click(function() {
					if($("#c-date5").val().length == 0 || $("#c-date5").val() == "") {
						$("#c-date6").val("");
					}else {
						rb_new_date = get_next_week_date($("#c-date5").val());
						$("#c-date6").val(rb_new_date);
					}
				});		
				$("#button-auto-fill-date7").click(function() {
					if($("#c-date6").val().length == 0 || $("#c-date6").val() == "") {
						$("#c-date7").val("");
					}else {
						rb_new_date = get_next_week_date($("#c-date6").val());
						$("#c-date7").val(rb_new_date);
					}
				});		
				$("#button-auto-fill-date8").click(function() {
					if($("#c-date7").val().length == 0 || $("#c-date7").val() == "") {
						$("#c-date8").val("");
					}else {
						rb_new_date = get_next_week_date($("#c-date7").val());
						$("#c-date8").val(rb_new_date);
					}
				});		
				$("#button-auto-fill-date9").click(function() {
					if($("#c-date8").val().length == 0 || $("#c-date8").val() == "") {
						$("#c-date9").val("");
					}else {
						rb_new_date = get_next_week_date($("#c-date8").val());
						$("#c-date9").val(rb_new_date);
					}
				});		
				$("#button-auto-fill-date10").click(function() {
					if($("#c-date9").val().length == 0 || $("#c-date9").val() == "") {
						$("#c-date10").val("");
					}else {
						rb_new_date = get_next_week_date($("#c-date9").val());
						$("#c-date10").val(rb_new_date);
					}
				});
			
			
		<? } ?>
		
		
		
	<? if(isset($_SESSION['course_id']) && isset($_SESSION['c_registration_code'])) { ?>
		$( "#button-registration-link" ).click(function() {
        	window.open('<?=str_replace("booking/", "", $rb_path_self)?>anmeldung/?kurs=<?=$_SESSION['course_id']?>&code=<?=$_SESSION['c_registration_code']?>','_blank');
		});		
	<?}?>

		<?php     /* ******  CLICK ACTION END  ****** */   ?>
		
	});

	</script>
</html>