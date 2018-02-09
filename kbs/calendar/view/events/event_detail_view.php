<?php
defined("main-call") or die();
?>

<!DOCTYPE html>
<head>
	<title>Kalendar</title>
	<link href='https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700' rel='stylesheet' type='text/css'>
	<meta name="viewport" content="width=device-width" />
	<meta name="author" content="Ing. Roman Breitschopf, BA">
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<link href="../booking/lib/jqueryui/jquery-ui.css"  rel="stylesheet" type="text/css" />
	<link href="./css/general.css"           rel="stylesheet" type="text/css" />
	<link href="./css/header.css"            rel="stylesheet" type="text/css" />
	<link href="./css/event_detail_view.css" rel="stylesheet" type="text/css" />
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>
	<script src="../booking/lib/jquery.js"></script>
	<script src="../booking/lib/jqueryui/jquery-ui.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$( "#button-go-back").click(function() {
				$("#rb-container, .rb-button, table, label, select, input, a").addClass("loading");
				window.location.href = '<?php echo $rb_path_self?>?cal_view=month_view';
			});
		});
	</script>
	</head>
<body>
	<div id="rb-container">
		<?php

			require $rb_configuration->relative_path_of_header_php;
			
			$event_detail_readonly = false;
			
			switch ($_SESSION["cal_view"]) {
				case "event_new":
					$e_title = "Neuen Termin anlegen";
					$event_number = "neu";
					break;
				case "event_detail":
					$e_title = "Termindetails";
					$event_number = $_SESSION["event_id"];
					break;
				default:
					$e_title = "View nicht gefunden - Fehler";
					$event_number = "";
					break;
			}
		?>
			<div id="container">
				<div id="title">
					<h2><?php echo $e_title?></h2>
				</div>

	
			<?php /* ******  BUTTON BAR END ****** */ ?>
			
			
				<?php /* ******  INIT EVENT VALUES BEGIN ****** */
				
		
					if(!isset($_SESSION["e_name"]) ||
							  $_SESSION["e_name"] == "default") {
							  $_SESSION["e_name"] = "";
					}	
					if(!isset($_SESSION["e_type"]) ||
							  $_SESSION["e_type"] == "default") {
							  $_SESSION["e_type"] = "2";
					}
					if(!isset($_SESSION["e_trainer1"]) ||
							  $_SESSION["e_trainer1"] == "default") {
							  $_SESSION["e_trainer1"] = -1;  // blank
					}
					if(!isset($_SESSION["e_trainer2"]) ||
							  $_SESSION["e_trainer2"] == "default") {
							  $_SESSION["e_trainer2"] = -1;  // blank
					}
					if(!isset($_SESSION["e_owner"]) ||
							  $_SESSION["e_owner"] == "default") {
							  $_SESSION["e_owner"] = $_SESSION["user_id"];
					}
					if(!isset($_SESSION["e_location"]) ||
							  $_SESSION["e_location"] == "default") {
							  $_SESSION["e_location"] = 1;
					}
					if(!isset($_SESSION["e_status"]) ||
							  $_SESSION["e_status"] == "default") {
							  $_SESSION["e_status"] = 1;
					}
					if(!isset($_SESSION["e_overlap"]) ||
							  $_SESSION["e_overlap"] == "default") {
							  $_SESSION["e_overlap"] = 1;
					}
					if(!isset($_SESSION["e_show_unit"]) ||
							  $_SESSION["e_show_unit"] == "default") {
							  $_SESSION["e_show_unit"] = 1;
					}
					if(!isset($_SESSION["e_description"]) ||
							  $_SESSION["e_description"] == "default") {
							  $_SESSION["e_description"] = "";
					}
					if(!isset($_SESSION["e_from1"]) ||
							  $_SESSION["e_from1"] == "default") {
							  $_SESSION["e_from1"] = "";
					}
					if(!isset($_SESSION["e_to1"]) ||
							  $_SESSION["e_to1"] == "default") {
							  $_SESSION["e_to1"] = "";
					}
					if(!isset($_SESSION["e_from2"]) ||
							  $_SESSION["e_from2"] == "default") {
							  $_SESSION["e_from2"] = "";
					}
					if(!isset($_SESSION["e_to2"]) ||
							  $_SESSION["e_to2"] == "default") {
							  $_SESSION["e_to2"] = "";
					}
					if(!isset($_SESSION["e_from3"]) ||
							  $_SESSION["e_from3"] == "default") {
							  $_SESSION["e_from3"] = "";
					}
					if(!isset($_SESSION["e_to3"]) ||
							  $_SESSION["e_to3"] == "default") {
							  $_SESSION["e_to3"] = "";
					}
					if(!isset($_SESSION["e_from4"]) ||
							  $_SESSION["e_from4"] == "default") {
							  $_SESSION["e_from4"] = "";
					}
					if(!isset($_SESSION["e_to4"]) ||
							  $_SESSION["e_to4"] == "default") {
							  $_SESSION["e_to4"] = "";
					}
					if(!isset($_SESSION["e_from5"]) ||
							  $_SESSION["e_from5"] == "default") {
							  $_SESSION["e_from5"] = "";
					}
					if(!isset($_SESSION["e_to5"]) ||
							  $_SESSION["e_to5"] == "default") {
							  $_SESSION["e_to5"] = "";
					}
					if(!isset($_SESSION["e_from6"]) ||
							  $_SESSION["e_from6"] == "default") {
							  $_SESSION["e_from6"] = "";
					}
					if(!isset($_SESSION["e_to6"]) ||
							  $_SESSION["e_to6"] == "default") {
							  $_SESSION["e_to6"] = "";
					}
					if(!isset($_SESSION["e_from7"]) ||
							  $_SESSION["e_from7"] == "default") {
							  $_SESSION["e_from7"] = "";
					}
					if(!isset($_SESSION["e_to7"]) ||
							  $_SESSION["e_to7"] == "default") {
							  $_SESSION["e_to7"] = "";
					}
					if(!isset($_SESSION["e_from8"]) ||
							  $_SESSION["e_from8"] == "default") {
							  $_SESSION["e_from8"] = "";
					}
					if(!isset($_SESSION["e_to8"]) ||
							  $_SESSION["e_to8"] == "default") {
							  $_SESSION["e_to8"] = "";
					}
					if(!isset($_SESSION["e_from9"]) ||
							  $_SESSION["e_from9"] == "default") {
							  $_SESSION["e_from9"] = "";
					}
					if(!isset($_SESSION["e_to9"]) ||
							  $_SESSION["e_to9"] == "default") {
							  $_SESSION["e_to9"] = "";
					}
					if(!isset($_SESSION["e_from10"]) ||
							  $_SESSION["e_from10"] == "default") {
							  $_SESSION["e_from10"] = "";
					}
					if(!isset($_SESSION["e_to10"]) ||
							  $_SESSION["e_to10"] == "default") {
							  $_SESSION["e_to10"] = "";
					}
					if(!isset($_SESSION["e_date1"]) ||
							  $_SESSION["e_date1"] == "default") {
							  $_SESSION["e_date1"] = "";
					}
					if(!isset($_SESSION["e_date2"]) ||
							  $_SESSION["e_date2"] == "default") {
							  $_SESSION["e_date2"] = "";
					}
					if(!isset($_SESSION["e_date3"]) ||
							  $_SESSION["e_date3"] == "default") {
							  $_SESSION["e_date3"] = "";
					}
					if(!isset($_SESSION["e_date4"]) ||
							  $_SESSION["e_date4"] == "default") {
							  $_SESSION["e_date4"] = "";
					}
					if(!isset($_SESSION["e_date5"]) ||
							  $_SESSION["e_date5"] == "default") {
							  $_SESSION["e_date5"] = "";
					}
					if(!isset($_SESSION["e_date6"]) ||
							  $_SESSION["e_date6"] == "default") {
							  $_SESSION["e_date6"] = "";
					}
					if(!isset($_SESSION["e_date7"]) ||
							  $_SESSION["e_date7"] == "default") {
							  $_SESSION["e_date7"] = "";
					}
					if(!isset($_SESSION["e_date8"]) ||
							  $_SESSION["e_date8"] == "default") {
							  $_SESSION["e_date8"] = "";
					}
					if(!isset($_SESSION["e_date9"]) ||
							  $_SESSION["e_date9"] == "default") {
							  $_SESSION["e_date9"] = "";
					}
					if(!isset($_SESSION["e_date10"]) ||
							  $_SESSION["e_date10"] == "default") {
							  $_SESSION["e_date10"] = "";
					}
					if(!isset($_SESSION["e_date11"]) ||
							  $_SESSION["e_date11"] == "default") {
							  $_SESSION["e_date11"] = "";
					}
					if(!isset($_SESSION["e_date12"]) ||
							  $_SESSION["e_date12"] == "default") {
							  $_SESSION["e_date12"] = "";
					}
					if(!isset($_SESSION["e_date13"]) ||
							  $_SESSION["e_date13"] == "default") {
							  $_SESSION["e_date13"] = "";
					}
					if(!isset($_SESSION["e_date14"]) ||
							  $_SESSION["e_date14"] == "default") {
							  $_SESSION["e_date14"] = "";
					}
					if(!isset($_SESSION["e_date15"]) ||
							  $_SESSION["e_date15"] == "default") {
							  $_SESSION["e_date15"] = "";
					}
					if(!isset($_SESSION["e_date16"]) ||
							  $_SESSION["e_date16"] == "default") {
							  $_SESSION["e_date16"] = "";
					}
					if(!isset($_SESSION["e_date17"]) ||
							  $_SESSION["e_date17"] == "default") {
							  $_SESSION["e_date17"] = "";
					}
					if(!isset($_SESSION["e_date18"]) ||
							  $_SESSION["e_date18"] == "default") {
							  $_SESSION["e_date18"] = "";
					}
					if(!isset($_SESSION["e_date19"]) ||
							  $_SESSION["e_date19"] == "default") {
							  $_SESSION["e_date19"] = "";
					}
					if(!isset($_SESSION["e_date20"]) ||
							  $_SESSION["e_date20"] == "default") {
							  $_SESSION["e_date20"] = "";
					}
							  
							  
					
					if(!isset($_SESSION["e_to10"]) ||
							  $_SESSION["e_to10"] == "default") {
							  $_SESSION["e_to10"] = "";
					}
					if(!isset($_SESSION["e_to11"]) ||
							  $_SESSION["e_to11"] == "default") {
							  $_SESSION["e_to11"] = "";
					}
					if(!isset($_SESSION["e_to12"]) ||
							  $_SESSION["e_to12"] == "default") {
							  $_SESSION["e_to12"] = "";
					}
					if(!isset($_SESSION["e_to13"]) ||
							  $_SESSION["e_to13"] == "default") {
							  $_SESSION["e_to13"] = "";
					}
					if(!isset($_SESSION["e_to14"]) ||
							  $_SESSION["e_to14"] == "default") {
							  $_SESSION["e_to14"] = "";
					}
					if(!isset($_SESSION["e_to15"]) ||
							  $_SESSION["e_to15"] == "default") {
							  $_SESSION["e_to15"] = "";
					}
					if(!isset($_SESSION["e_to16"]) ||
							  $_SESSION["e_to16"] == "default") {
							  $_SESSION["e_to16"] = "";
					}
					if(!isset($_SESSION["e_to17"]) ||
							  $_SESSION["e_to17"] == "default") {
							  $_SESSION["e_to17"] = "";
					}
					if(!isset($_SESSION["e_to18"]) ||
							  $_SESSION["e_to18"] == "default") {
							  $_SESSION["e_to18"] = "";
					}
					if(!isset($_SESSION["e_to19"]) ||
							  $_SESSION["e_to19"] == "default") {
							  $_SESSION["e_to19"] = "";
					}
					if(!isset($_SESSION["e_to20"]) ||
							  $_SESSION["e_to20"] == "default") {
							  $_SESSION["e_to20"] = "";
					}
					if(!isset($_SESSION["e_from10"]) ||
							  $_SESSION["e_from10"] == "default") {
							  $_SESSION["e_from10"] = "";
					}
					if(!isset($_SESSION["e_from11"]) ||
							  $_SESSION["e_from11"] == "default") {
							  $_SESSION["e_from11"] = "";
					}
					if(!isset($_SESSION["e_from12"]) ||
							  $_SESSION["e_from12"] == "default") {
							  $_SESSION["e_from12"] = "";
					}
					if(!isset($_SESSION["e_from13"]) ||
							  $_SESSION["e_from13"] == "default") {
							  $_SESSION["e_from13"] = "";
					}
					if(!isset($_SESSION["e_from14"]) ||
							  $_SESSION["e_from14"] == "default") {
							  $_SESSION["e_from14"] = "";
					}
					if(!isset($_SESSION["e_from15"]) ||
							  $_SESSION["e_from15"] == "default") {
							  $_SESSION["e_from15"] = "";
					}
					if(!isset($_SESSION["e_from16"]) ||
							  $_SESSION["e_from16"] == "default") {
							  $_SESSION["e_from16"] = "";
					}
					if(!isset($_SESSION["e_from17"]) ||
							  $_SESSION["e_from17"] == "default") {
							  $_SESSION["e_from17"] = "";
					}
					if(!isset($_SESSION["e_from18"]) ||
							  $_SESSION["e_from18"] == "default") {
							  $_SESSION["e_from18"] = "";
					}
					if(!isset($_SESSION["e_from19"]) ||
							  $_SESSION["e_from19"] == "default") {
							  $_SESSION["e_from19"] = "";
					}
					if(!isset($_SESSION["e_from20"]) ||
							  $_SESSION["e_from20"] == "default") {
							  $_SESSION["e_from20"] = "";
					}
							  
					if(!isset($_SESSION["e_not_on"]) ||
							  $_SESSION["e_not_on"] == "default") {
							  $_SESSION["e_not_on"] = "";
					}
					
			       /* ******  INIT EVENT VALUES END ****** */
								  
								  
					if ($_SESSION["cal_view"] == 'event_detail' || $_SESSION["cal_view"] == 'event_copy') {
						if(isset($_SESSION["e_no_reload_data"]) && $_SESSION["e_no_reload_data"] == "true")	{
							unset($_SESSION["e_no_reload_data"]);
						}else{
							$cal_db_functions->events->db_load_event_values_from_id($_SESSION["event_id"]);  // SET event VALUES FROM ID ***** 
						}
					}
					
					if($_SESSION["user_is_organizer"] == 1 || $_SESSION["user_is_admin"] == 1 || $_SESSION["user_id"] == $_SESSION["e_owner"]) {
						$event_detail_readonly = false;
					}else {
						$event_detail_readonly = true;
					} ?>

			<?php /* ******  BUTTON BAR BEGIN ****** */ ?>
				<div id="button-bar">
					<div id="button-go-back" class="rb-button3">
						Zurück
					</div>
					
					<?php
					
					if ($_SESSION["cal_view"] == 'event_detail') {?>
						<? if($_SESSION["user_is_organizer"] == 1 || $_SESSION["user_is_admin"] == 1) { 
								if($_SESSION["e_type"] == 2) {
							?>
									<div id="button-create-course-from-event" class="rb-button3">
										Kurs erstellen
									</div>
						<? 		}
							} ?>
							
					<? } ?>			
					
				</div>

			      <? /* ******  event DATA BEGIN ****** */?>
				
				<form id="e-detail-form" method="POST" action="process.php" class="input-container clear-fix">
					<div class="clearfix">
						<div id="input-column1">
							<div class="input-row" style="display: none;">
								<label for='e-event-nr'>Termin- Nr.:</label>
								<div style="display: inline-block; margin-bottom: 15px">
									<?=$event_number?>
								</div>
							</div>
							<div class="input-row">
								<label for="e-name">Termin*:</label>
								<input name="e_name" type="text" id="e-name" value="<?php echo $_SESSION["e_name"] ?>">
							</div>
							<div class="input-row">
								<label for="e-type">Termintyp*:</label>
								<select name='e_type' id='e-type'">
									<?php if($_SESSION["e_type"] == -1) {
									echo "
										  <option style='display:none' value= -1 disabled selected></option>";
									} ?>
									<option value='2'<?php echo ($_SESSION["e_type"] == 2)? " selected":"" ?>>Kursvorschlag</option>
									<? if($_SESSION["user_is_organizer"] == 1 || $_SESSION["user_is_admin"] == 1 || $_SESSION["e_type"] == 3 || $_SESSION["e_type"] == 4) { ?>
									<option value='3'<?php echo ($_SESSION["e_type"] == 3)? " selected":"" ?>>Trainertreffen</option>
									<option value='4'<?php echo ($_SESSION["e_type"] == 4)? " selected":"" ?>>Feiertage & Ferien</option>
									<? } ?>
									<option value='6'<?php echo ($_SESSION["e_type"] == 6)? " selected":"" ?>>Training / Privater Termin</option>
									<option value='1'<?php echo ($_SESSION["e_type"] == 1)? " selected":"" ?>>Sonstiges</option>
									<option value='5'<?php echo ($_SESSION["e_type"] == 5)? " selected":"" ?>>Sonstiges Spezial</option>
									<? if($_SESSION["user_is_organizer"] == 1 || $_SESSION["user_is_admin"] == 1 || $_SESSION["e_type"] == 7) { ?>
									<option value='7'<?php echo ($_SESSION["e_type"] == 7)? " selected":"" ?>>Geheim</option>
									<? } ?>
								</select>
							</div>
							<div class="input-row">
							<label for='e-location'>Standort*:</label>
								<select name='e_location' id='e-location'>
									<? $cal_db_functions->calendar_options->db_get_location_select_options($_SESSION["e_location"], false); ?>
								</select>
							</div>
							<div class="input-row">
								<label for="e-status">Status*:</label>
								<select name='e_status' id='e-status' class="select-with-symbols">
									<option value='1'<?php echo ($_SESSION["e_status"] == 1)? " selected":"" ?>>✔   aktiv</option>
									<option value='0'<?php echo ($_SESSION["e_status"] == 0)? " selected":"" ?>>✖   löschen</option>
								</select>
							</div>
							<div class="input-row">						
								
								<label for='e-trainer1'>Trainer1:</label>
								<select name='e_trainer1' id='e-trainer1'>
									<? $cal_db_functions->calendar_options->db_get_user_select_options($_SESSION["e_trainer1"], false,true,true,"-1", true); ?>
								</select>
								
							</div>
							<div class="input-row">						
								
								<label for='e-trainer2'>Trainer2:</label>
								<select name='e_trainer2' id='e-trainer2' >
									<? $cal_db_functions->calendar_options->db_get_user_select_options($_SESSION["e_trainer2"], false,true,true,"-1", true); ?>
								</select>
								
							</div>
							<div class="input-row">
								<label for="e-overlap">Kollision*:</label>
								<? if($_SESSION["user_is_organizer"] == 1 || $_SESSION["user_is_admin"] == 1) {
									$overlap_is_disabled = "";
								}else{
									$overlap_is_disabled = "disabled";
								} ?>
								<select name='e_overlap' id='e-overlap' <?=$overlap_is_disabled?>>
									<option value='1'<?php echo ($_SESSION["e_overlap"] == 1)? " selected":"" ?>>Terminüberschneidung anzeigen</option>
									<option value='0'<?php echo ($_SESSION["e_overlap"] == 0)? " selected":"" ?>>Terminüberschneidung ignorieren</option>
								</select>
							</div>
							<div class="input-row">
								<label for="e-show-unit">Einheit*:</label>
								<select name='e_show_unit' id='e-show-unit'>
									<option value='1'<?php echo ($_SESSION["e_show_unit"] == 1)? " selected":"" ?>>Einheitennummer anzeigen</option>
									<option value='0'<?php echo ($_SESSION["e_show_unit"] == 0)? " selected":"" ?>>Einheitennummer ausblenden</option>
								</select>
							</div>
							<div class="input-row">
								<label for="e-description">Notiz:</label>
								<textarea style="min-height: 122px;" id="e-description" name="description" rows="5"><?php echo $_SESSION["e_description"];?></textarea>
							</div>
							<div class="input-row">						
								
								<label for='e-owner'>Ersteller*:</label>
								<select name='e_owner' id='e-owner' disabled style="background: #e6e6e6">
									<? $cal_db_functions->calendar_options->db_get_user_select_options($_SESSION["e_owner"], false,true,true); ?>
								</select>
								
							</div>
						</div>
		
						<div id="input-column2">
							
							<div class="input-row">
								<label for="e-date1" id="e-label-datepicker">Termin 1*:</label>
								<input name="e_date1" class="date-input" type="text" id="e-date1" placeholder= "Datum" value="<?php echo $_SESSION["e_date1"] ?>">
								<input name="e_from1" class="time-input" type="text" id="e-from1" placeholder= "von" value="<?php echo $_SESSION["e_from1"] ?>">
								<input name="e_to1"   class="time-input" type="text" id="e-to1"   placeholder= "bis" value="<?php echo $_SESSION["e_to1"] ?>">
							</div>
							<div class="input-row">
								<label for="e-date2" id="e-label-datepicker">Termin 2:</label>
								<input class="date-input" name="e_date2" type="text" id="e-date2" value="<?php echo $_SESSION["e_date2"]?>">
								<input name="e_from2" class="time-input" type="text" id="e-from2" value="<?php echo $_SESSION["e_from2"] ?>">
								<input name="e_to2"   class="time-input" type="text" id="e-to2"   value="<?php echo $_SESSION["e_to2"] ?>">
								<div id="button-auto-fill-date2" class="rb-mini-button"> ◀ </div>
							</div>
							<div class="input-row">
								<label for="e-date3" id="e-label-datepicker">Termin 3:</label>
								<input class="date-input" name="e_date3" type="text" id="e-date3" value="<?php echo $_SESSION["e_date3"] ?>">
								<input name="e_from3" class="time-input" type="text" id="e-from3" value="<?php echo $_SESSION["e_from3"] ?>">
								<input name="e_to3"   class="time-input" type="text" id="e-to3"   value="<?php echo $_SESSION["e_to3"] ?>">
								<div id="button-auto-fill-date3" class="rb-mini-button"> ◀ </div>
							</div>
							<div class="input-row">
								<label for="e-date4" id="e-label-datepicker">Termin 4:</label>
								<input class="date-input" name="e_date4" type="text" id="e-date4" value="<?php echo $_SESSION["e_date4"] ?>">
								<input name="e_from4" class="time-input" type="text" id="e-from4" value="<?php echo $_SESSION["e_from4"] ?>">
								<input name="e_to4"   class="time-input" type="text" id="e-to4"   value="<?php echo $_SESSION["e_to4"] ?>">
								<div id="button-auto-fill-date4" class="rb-mini-button"> ◀ </div>
							</div>
							<div class="input-row">
								<label for="e-date5" id="e-label-datepicker">Termin 5:</label>
								<input class="date-input" name="e_date5" type="text" id="e-date5" value="<?php echo $_SESSION["e_date5"] ?>">
								<input name="e_from5" class="time-input" type="text" id="e-from5" value="<?php echo $_SESSION["e_from5"] ?>">
								<input name="e_to5"   class="time-input" type="text" id="e-to5"   value="<?php echo $_SESSION["e_to5"] ?>">
								<div id="button-auto-fill-date5" class="rb-mini-button"> ◀ </div>
							</div>
							<div class="input-row">
								<label for="e-date6" id="e-label-datepicker">Termin 6:</label>
								<input class="date-input" name="e_date6" type="text" id="e-date6" value="<?php echo $_SESSION["e_date6"] ?>">
								<input name="e_from6" class="time-input" type="text" id="e-from6" value="<?php echo $_SESSION["e_from6"] ?>">
								<input name="e_to6"   class="time-input" type="text" id="e-to6"   value="<?php echo $_SESSION["e_to6"] ?>">
								<div id="button-auto-fill-date6" class="rb-mini-button"> ◀ </div>
							</div>
							<div class="input-row">
								<label for="e-date7" id="e-label-datepicker">Termin 7:</label>
								<input class="date-input" name="e_date7" type="text" id="e-date7" value="<?php echo $_SESSION["e_date7"] ?>">
								<input name="e_from7" class="time-input" type="text" id="e-from7" value="<?php echo $_SESSION["e_from7"] ?>">
								<input name="e_to7"   class="time-input" type="text" id="e-to7"   value="<?php echo $_SESSION["e_to7"] ?>">
								<div id="button-auto-fill-date7" class="rb-mini-button"> ◀ </div>	
							</div>
							<div class="input-row">
								<label for="e-date8" id="e-label-datepicker">Termin 8:</label>
								<input class="date-input" name="e_date8" type="text" id="e-date8" value="<?php echo $_SESSION["e_date8"] ?>">
								<input name="e_from8" class="time-input" type="text" id="e-from8" value="<?php echo $_SESSION["e_from8"] ?>">
								<input name="e_to8"   class="time-input" type="text" id="e-to8"   value="<?php echo $_SESSION["e_to8"] ?>">
								<div id="button-auto-fill-date8" class="rb-mini-button"> ◀ </div>
							</div>
							<div class="input-row">
								<label for="e-date9" id="e-label-datepicker">Termin 9:</label>
								<input class="date-input" name="e_date9" type="text" id="e-date9" value="<?php echo $_SESSION["e_date9"] ?>">
								<input name="e_from9" class="time-input" type="text" id="e-from9" value="<?php echo $_SESSION["e_from9"] ?>">
								<input name="e_to9"   class="time-input" type="text" id="e-to9"   value="<?php echo $_SESSION["e_to9"] ?>">
								<div id="button-auto-fill-date9" class="rb-mini-button"> ◀ </div>
							</div>
							<div class="input-row">
								<label for="e-date10" id="e-label-datepicker">Termin 10:</label>
								<input class="date-input" name="e_date10" type="text" id="e-date10" value="<?php echo $_SESSION["e_date10"] ?>">
								<input name="e_from10" class="time-input" type="text" id="e-from10" value="<?php echo $_SESSION["e_from10"] ?>">
								<input name="e_to10"   class="time-input" type="text" id="e-to10"   value="<?php echo $_SESSION["e_to10"] ?>">
								<div id="button-auto-fill-date10" class="rb-mini-button"> ◀ </div>
							</div>
							<div class="input-row">
								<label for="e-date11" id="e-label-datepicker">Termin 11:</label>
								<input class="date-input" name="e_date11" type="text" id="e-date11" value="<?php echo $_SESSION["e_date11"] ?>">
								<input name="e_from11" class="time-input" type="text" id="e-from11" value="<?php echo $_SESSION["e_from11"] ?>">
								<input name="e_to11"   class="time-input" type="text" id="e-to11"   value="<?php echo $_SESSION["e_to11"] ?>">
								<div id="button-auto-fill-date11" class="rb-mini-button"> ◀ </div>
							</div>
							<div class="input-row">
								<label for="e-date12" id="e-label-datepicker">Termin 12:</label>
								<input class="date-input" name="e_date12" type="text" id="e-date12" value="<?php echo $_SESSION["e_date12"] ?>">
								<input name="e_from12" class="time-input" type="text" id="e-from12" value="<?php echo $_SESSION["e_from12"] ?>">
								<input name="e_to12"   class="time-input" type="text" id="e-to12"   value="<?php echo $_SESSION["e_to12"] ?>">
								<div id="button-auto-fill-date12" class="rb-mini-button"> ◀ </div>
							</div>
							<div id="more-dates" style="display: none;">
								<div class="input-row">
									<label for="e-date13" id="e-label-datepicker">Termin 13:</label>
									<input class="date-input" name="e_date13" type="text" id="e-date13" value="<?php echo $_SESSION["e_date13"] ?>">
									<input name="e_from13" class="time-input" type="text" id="e-from13" value="<?php echo $_SESSION["e_from13"] ?>">
									<input name="e_to13"   class="time-input" type="text" id="e-to13"   value="<?php echo $_SESSION["e_to13"] ?>">
									<div id="button-auto-fill-date13" class="rb-mini-button"> ◀ </div>
								</div>
								<div class="input-row">
									<label for="e-date14" id="e-label-datepicker">Termin 14:</label>
									<input class="date-input" name="e_date14" type="text" id="e-date14" value="<?php echo $_SESSION["e_date14"] ?>">
									<input name="e_from14" class="time-input" type="text" id="e-from14" value="<?php echo $_SESSION["e_from14"] ?>">
									<input name="e_to14"   class="time-input" type="text" id="e-to14"   value="<?php echo $_SESSION["e_to14"] ?>">
									<div id="button-auto-fill-date14" class="rb-mini-button"> ◀ </div>
								</div>
								<div class="input-row">
									<label for="e-date15" id="e-label-datepicker">Termin 15:</label>
									<input class="date-input" name="e_date15" type="text" id="e-date15" value="<?php echo $_SESSION["e_date15"] ?>">
									<input name="e_from15" class="time-input" type="text" id="e-from15" value="<?php echo $_SESSION["e_from15"] ?>">
									<input name="e_to15"   class="time-input" type="text" id="e-to15"   value="<?php echo $_SESSION["e_to15"] ?>">
									<div id="button-auto-fill-date15" class="rb-mini-button"> ◀ </div>
								</div>
								<div class="input-row">
									<label for="e-date16" id="e-label-datepicker">Termin 16:</label>
									<input class="date-input" name="e_date16" type="text" id="e-date16" value="<?php echo $_SESSION["e_date16"] ?>">
									<input name="e_from16" class="time-input" type="text" id="e-from16" value="<?php echo $_SESSION["e_from16"] ?>">
									<input name="e_to16"   class="time-input" type="text" id="e-to16"   value="<?php echo $_SESSION["e_to16"] ?>">
									<div id="button-auto-fill-date16" class="rb-mini-button"> ◀ </div>
								</div>
								<div class="input-row">
									<label for="e-date17" id="e-label-datepicker">Termin 17:</label>
									<input class="date-input" name="e_date17" type="text" id="e-date17" value="<?php echo $_SESSION["e_date17"] ?>">
									<input name="e_from17" class="time-input" type="text" id="e-from17" value="<?php echo $_SESSION["e_from17"] ?>">
									<input name="e_to17"   class="time-input" type="text" id="e-to17"   value="<?php echo $_SESSION["e_to17"] ?>">
									<div id="button-auto-fill-date17" class="rb-mini-button"> ◀ </div>
								</div>
								<div class="input-row">
									<label for="e-date18" id="e-label-datepicker">Termin 18:</label>
									<input class="date-input" name="e_date18" type="text" id="e-date18" value="<?php echo $_SESSION["e_date18"] ?>">
									<input name="e_from18" class="time-input" type="text" id="e-from18" value="<?php echo $_SESSION["e_from18"] ?>">
									<input name="e_to18"   class="time-input" type="text" id="e-to18"   value="<?php echo $_SESSION["e_to18"] ?>">
									<div id="button-auto-fill-date18" class="rb-mini-button"> ◀ </div>
								</div>
								<div class="input-row">
									<label for="e-date19" id="e-label-datepicker">Termin 19:</label>
									<input class="date-input" name="e_date19" type="text" id="e-date19" value="<?php echo $_SESSION["e_date19"] ?>">
									<input name="e_from19" class="time-input" type="text" id="e-from19" value="<?php echo $_SESSION["e_from19"] ?>">
									<input name="e_to19"   class="time-input" type="text" id="e-to19"   value="<?php echo $_SESSION["e_to19"] ?>">
									<div id="button-auto-fill-date19" class="rb-mini-button"> ◀ </div>
								</div>
								<div class="input-row">
									<label for="e-date20" id="e-label-datepicker">Termin 20:</label>
									<input class="date-input" name="e_date20" type="text" id="e-date20" value="<?php echo $_SESSION["e_date20"] ?>">
									<input name="e_from20" class="time-input" type="text" id="e-from20" value="<?php echo $_SESSION["e_from20"] ?>">
									<input name="e_to20"   class="time-input" type="text" id="e-to20"   value="<?php echo $_SESSION["e_to20"] ?>">
									<div id="button-auto-fill-date20" class="rb-mini-button"> ◀ </div>
								</div>
							</div>
							<div class="input-row">
								<label for="e-not-on">entfällt am:</label>
								<input name="e_not_on" type="text" id="e-not-on" placeholder="leer lassen wenn nichts ausfällt" value="<?php echo $_SESSION["e_not_on"] ?>">
							</div>
						</div>
						
						<input name="view" type="hidden" value="<?php echo $_SESSION["cal_view"] ?>">
						<input name="e_no_reload_data" type="hidden" value = "true">
	
					</div>	
					<div id="button-bar2">
						
					
					
			   <?php     /* ******  event DATA END ****** */
			   
			   
			   
			    	           /* ******  BOTTOM BUTTON BAR BEGIN ****** */
						
						if ($event_detail_readonly == false) {
							if ($_SESSION["cal_view"] == 'event_detail')
							{ ?><div>	
									<input name="event_id" type="hidden" value="<?=$_SESSION["event_id"] ?>">
									<button form="e-detail-form" type="submit" name="cal_action" value="e_update_event" class="rb-button3" style='margin-left: 0px;'>Speichern</button>
									<button form="e-detail-form" type="submit" name="cal_action" value="e_update_back"  class="rb-button3">Speichern und Zurück</button>
									<button form="e-detail-form" type="submit" name="cal_action" value="e_update_new"   class="rb-button3">Speichern und Neu</button>
								</div>
							<?
							}else{?>
								<div>		
									<button form="e-detail-form" type="submit" name="cal_action" value="e_insert_event"      class="rb-button3" style='margin-left: 0px;'>Speichern</button>
									<button form="e-detail-form" type="submit" name="cal_action" value="e_insert_event_back" class="rb-button3">Speichern und Zurück</button>
									<button form="e-detail-form" type="submit" name="cal_action" value="e_insert_event_new"  class="rb-button3">Speichern und Neu</button>
								</div>
							<?
							}
						}else { ?>
						
								<div>		
									<div id="button-info" class="rb-button3"'>Info</div>
								</div>
						<? } ?>
					</div>
					
					
			    	    <?php  /* ******  BOTTOM BUTTON BAR END ****** */    ?>
				</form>
				
				<div id="e-save-message">
					<?php     /* ******  CONFIRMATION MESSAGE BEGIN  ****** */
					
						if(isset($_SESSION["e_success_msg"]) && $_SESSION["e_success_msg"]) {
							
							echo $_SESSION["e_success_msg"];
							$_SESSION["e_success_msg"] = false;
							
						}
						if(isset($_SESSION["e_error_msg"]) && $_SESSION["e_error_msg"] ) {
							echo $_SESSION["e_error_msg"];
							unset($_SESSION["e_error_msg"]);							
						}
						
							 /* ******  CONFIRMATION MESSAGE END ****** */   ?>
				</div>
				
			</div>
		</div>
	</body>
	<div id="load-box" style="display: none;"></div>
	
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
			window.location.href = '<?php echo $rb_path_self?>?view=<?php if(isset($_SESSION["cal_view"])) echo $_SESSION["cal_view"]?>&action=reset_event_values';

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
		
		<? if ($event_detail_readonly) { ?>
			
		$("input:not([type='submit']), select, textarea").prop('readonly', true);
		$("input:not([type='submit']), select, textarea").prop('disabled', true);
		
		$("#button-info").click(function() {
			alert('Info: Nur selbst erstellte Termine können bearbeitet werden.');
		});
			
		<? }else { ?>
			
				rb_init_datepicker($( "#e-date1, #e-date2, #e-date3, #e-date4, #e-date5, #e-date6, #e-date7, #e-date8, #e-date9, #e-date10" ));
			
				$( "#button-auto-fill-name" ).click(function() {
					if($("select[name='e_subcategory'").val() == -1 ) {
						$("#e-name").val($("select[name='e_category'] option:selected").text());
					}else {
						$("#e-name").val($("select[name='e_subcategory'] option:selected").text());
					}
				});
				
				$("#button-auto-fill-date2").click(function() {
					if($("#e-date1").val().length == 0 || $("#e-date1").val() == "") {
						alert("Bitte zunächst Datum in Termin 1 eintragen.");
					}else {
						rb_new_date = get_next_week_date($("#e-date1").val());
						$("#e-date2").val(rb_new_date);
						$("#e-from2").val($("#e-from1").val());
						$("#e-to2").val($("#e-to1").val());
						
					}
				});
				$("#button-auto-fill-date3").click(function() {
					if($("#e-date2").val().length == 0 || $("#e-date2").val() == "") {
						$("#e-date3").val("");
						$("#e-from3").val("");
						$("#e-to3").val("");
					}else {
						rb_new_date = get_next_week_date($("#e-date2").val());
						$("#e-date3").val(rb_new_date);
						$("#e-from3").val($("#e-from2").val());
						$("#e-to3").val($("#e-to2").val());
					}
				});
				$("#button-auto-fill-date4").click(function() {
					if($("#e-date3").val().length == 0 || $("#e-date3").val() == "") {
						$("#e-date4").val("");
						$("#e-from4").val("");
						$("#e-to4").val("");
					}else {
						rb_new_date = get_next_week_date($("#e-date3").val());
						$("#e-date4").val(rb_new_date);
						$("#e-from4").val($("#e-from3").val());
						$("#e-to4").val($("#e-to3").val());
					}
				});	
				$("#button-auto-fill-date5").click(function() {
					if($("#e-date4").val().length == 0 || $("#e-date4").val() == "") {
						$("#e-date5").val("");
						$("#e-from5").val("");
						$("#e-to5").val("");
					}else {
						rb_new_date = get_next_week_date($("#e-date4").val());
						$("#e-date5").val(rb_new_date);
						$("#e-from5").val($("#e-from4").val());
						$("#e-to5").val($("#e-to4").val());
					}
				});
				$("#button-auto-fill-date6").click(function() {
					if($("#e-date5").val().length == 0 || $("#e-date5").val() == "") {
						$("#e-date6").val("");
						$("#e-from6").val("");
						$("#e-to6").val("");
					}else {
						rb_new_date = get_next_week_date($("#e-date5").val());
						$("#e-date6").val(rb_new_date);
						$("#e-from6").val($("#e-from5").val());
						$("#e-to6").val($("#e-to5").val());
					}
				});		
				$("#button-auto-fill-date7").click(function() {
					if($("#e-date6").val().length == 0 || $("#e-date6").val() == "") {
						$("#e-date7").val("");
						$("#e-from7").val("");
						$("#e-to7").val("");
					}else {
						rb_new_date = get_next_week_date($("#e-date6").val());
						$("#e-date7").val(rb_new_date);
						$("#e-from7").val($("#e-from6").val());
						$("#e-to7").val($("#e-to6").val());
					}
				});		
				$("#button-auto-fill-date8").click(function() {
					if($("#e-date7").val().length == 0 || $("#e-date7").val() == "") {
						$("#e-date8").val("");
						$("#e-from8").val("");
						$("#e-to8").val("");
					}else {
						rb_new_date = get_next_week_date($("#e-date7").val());
						$("#e-date8").val(rb_new_date);
						$("#e-from8").val($("#e-from7").val());
						$("#e-to8").val($("#e-to7").val());
					}
				});		
				$("#button-auto-fill-date9").click(function() {
					if($("#e-date8").val().length == 0 || $("#e-date8").val() == "") {
						$("#e-date9").val("");
						$("#e-from9").val("");
						$("#e-to9").val("");
					}else {
						rb_new_date = get_next_week_date($("#e-date8").val());
						$("#e-date9").val(rb_new_date);
						$("#e-from9").val($("#e-from8").val());
						$("#e-to9").val($("#e-to8").val());
					}
				});		
				$("#button-auto-fill-date10").click(function() {
					if($("#e-date9").val().length == 0 || $("#e-date9").val() == "") {
						$("#e-date10").val("");
						$("#e-from10").val("");
						$("#e-to10").val("");
					}else {
						rb_new_date = get_next_week_date($("#e-date9").val());
						$("#e-date10").val(rb_new_date);
						$("#e-from10").val($("#e-from9").val());
						$("#e-to10").val($("#e-to9").val());
					}
				});
				$("#button-auto-fill-date11").click(function() {
					if($("#e-date10").val().length == 0 || $("#e-date10").val() == "") {
						$("#e-date11").val("");
						$("#e-from11").val("");
						$("#e-to11").val("");
					}else {
						rb_new_date = get_next_week_date($("#e-date10").val());
						$("#e-date11").val(rb_new_date);
						$("#e-from11").val($("#e-from10").val());
						$("#e-to11").val($("#e-to10").val());
					}
				});
				$("#button-auto-fill-date12").click(function() {
					if($("#e-date11").val().length == 0 || $("#e-date11").val() == "") {
						$("#e-date12").val("");
						$("#e-from12").val("");
						$("#e-to12").val("");
					}else {
						rb_new_date = get_next_week_date($("#e-date11").val());
						$("#e-date12").val(rb_new_date);
						$("#e-from12").val($("#e-from11").val());
						$("#e-to12").val($("#e-to11").val());
					}
				});
		<? } ?>
		
		$("#button-create-course-from-event").click(function() {
			  $('#e-detail-form').append("<input type='hidden' name='cal_action' value='e_create_course_from_event'>");
		      $('#e-detail-form').submit(); 
		});
		$("#button-create-single-courses").click(function() {
			alert("Diese Funktion wird zu einem späteren Zeitpunkt verfügbar sein.");       	
		});
		
		<?php     /* ******  CLICK ACTION END  ****** */   ?>
		
	});

	</script>
</html>