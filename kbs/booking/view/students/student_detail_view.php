<?php
defined("main-call") or die();
?>

<!DOCTYPE html>
<head>
	<title>Kursteilnehmer - <?=$rb_configuration->title_of_web_application_backend?></title>
	<link href='https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700' rel='stylesheet' type='text/css'>
	<meta name="viewport" content="width=device-width" />
	<meta name="author" content="Ing. Roman Breitschopf, BA">
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<link href="./lib/jqueryui/jquery-ui.css"  rel="stylesheet" type="text/css" />
	<link href="./css/general.css"           rel="stylesheet" type="text/css" />
	<link href="./css/header.css"            rel="stylesheet" type="text/css" />
	<link href="./css/student_detail_view.css" rel="stylesheet" type="text/css" />
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>
	<script src="./lib/jquery.js"></script>
	<script src="./lib/jqueryui/jquery-ui.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {		 
			<? if(isset($_GET["w1"]) && $_GET["w1"] == "small") {
				echo "expand_get_string = '&w1=" . $_GET["w1"] . "';";
			}else{
				echo "expand_get_string = '';";
				
			} ?>
			$( "#button-go-back").click(function() {
				$("#rb-container, .rb-button, table, label, select, input, a").addClass("loading");
				window.location.href = '<?php echo $rb_path_self?>?view=student_list' + expand_get_string;
			});
		});
	</script>
	</head>
<body>
	<div id="rb-container">
		<?php
			require $rb_configuration->relative_path_of_header_php;
			
			switch ($_SESSION["view"]) {
				case "student_new":
					$c_title = "Neuen Teilnehmer anlegen";
					break;
				case "student_detail":
					$c_title = "Teilnehmer- Detail";
					break;
				default:
					$c_title = "View nicht gefunden - Fehler";
					break;
			}
		?>
			<div id="container">
				<div id="title">
					<h2><?php echo $c_title?></h2>
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
					
					
					<?if (isset($_GET["c1"]) && is_numeric($_GET["c1"]) && $_SESSION["view"] == 'student_detail') {?>
					<?/*<div id="button-registrate" class="rb-button">
						Anmelden für Kurs-Nr. <?=$_GET["c1"]?>
					</div>*/?>
					<?}?>
					
								
					
				</div>
	
			<?php /* ******  BUTTON BAR END ****** */ ?>
			
			
				<?php /* ******  INIT STUDENT VALUES BEGIN ****** */
				
		
					if(!isset($_SESSION["s_prename"])) {
						$_SESSION["s_prename"] = "";
					}
					if(!isset($_SESSION["s_surname"])) {
						$_SESSION["s_surname"] = "";
					}
					if(!isset($_SESSION["s_email"])) {
						$_SESSION["s_email"] = "";
					}
					if(!isset($_SESSION["s_merged_to"])) {
						$_SESSION["s_merged_to"] = "";
					}
					if(!isset($_SESSION["s_status"]) ) {
						$_SESSION["s_status"] = "1";
					}
					if(!isset($_SESSION["s_search_code"]) ) {
						$_SESSION["s_search_code"] = "";
					}
					if(!isset($_SESSION["s_student_remark"]) ) {
						$_SESSION["s_student_remark"] = "";
					}
					if(!isset($_SESSION["s_newsletter"]) ) {
						$_SESSION["s_newsletter"] = "0";
					}
								
			       /* ******  INIT STUDENT VALUES END ****** */
								  
								  
					if ($_SESSION["view"] == "student_detail") {
						if(isset($_SESSION["s_no_reload_data"]) && $_SESSION["s_no_reload_data"] == "true")	{
							unset($_SESSION["s_no_reload_data"]);
						}else{							
							$db_functions->students->db_load_student_values_from_id($_SESSION["student_id"]);  // SET STUDENT VALUES FROM ID *****
						}
					}
					
				
			       /* ******  STUDENT DATA BEGIN ****** */?>
				
				<form id="s-detail-form" method="POST" action="process.php" class="input-container clear-fix">
					<div class="clearfix">
						<div id="input-column1">
							<div class="input-row">
								<label for="s_prename">Vorname*:</label>
								<input name="s_prename" type="text" id="s_prename" value="<?php echo $_SESSION["s_prename"] ?>">
							</div>
							<div class="input-row">
								<label for="s_surname">Nachname*:</label>
								<input name="s_surname" type="text" id="s_surname" value="<?php echo $_SESSION["s_surname"] ?>">
							</div>
							<div class="input-row">
								<label for="s_email">E-Mail*:</label>
								<input name="s_email" type="text" id="s_email" value="<?php echo $_SESSION["s_email"] ?>">
							</div>

							<div class="input-row">
								<label for="s_newsletter">Newsletter*:</label>
								
								<select name='s_newsletter' id='s_newsletter' class='select-with-symbols'>
								
									<?php if($_SESSION["s_newsletter"] == "-1") {
									echo "
										  <option style='display:none' value= '-1' disabled selected></option>";
									} ?>
									<option value='1'<?php echo ($_SESSION["s_newsletter"] == '1')? " selected":"" ?>>✔   abonniert</option>
									<option value='0'<?php echo ($_SESSION["s_newsletter"] == '0')? " selected":"" ?>>✖   nicht abonniert</option>
								</select>
							</div>
							<div class="input-row">
								
								<label for="s_status">Status*:</label>
								<select name='s_status' id='s_status' class='select-with-symbols'>
								
									<?php if($_SESSION["s_status"] == -1) {
									echo "
										  <option style='display:none' value= -1 disabled selected></option>";
									} ?>
									<option value='1'<?php echo ($_SESSION["s_status"] == 1)? " selected":"" ?>>✔   aktiv</option>
									<option value='2'<?php echo ($_SESSION["s_status"] == 2)? " selected":"" ?>>✖   deaktiviert</option>
									<option value='3'<?php echo ($_SESSION["s_status"] == 3)? " selected":"" ?>>➥  fusioniert</option>
									<option value='4'<?php echo ($_SESSION["s_status"] == 4)? " selected":"" ?>>✖✖   gesperrt</option>
									<option value='5'<?php echo ($_SESSION["s_status"] == 5)? " selected":"" ?>>@ unverifizierte Email</option>
								</select>
							</div>
							<div class="input-row" id="merged-to-container" <?php echo ($_SESSION["s_status"] != 3)? " style='display: none'":"" ?>>
								<label for="s_merged_to">Verbinde zu*:</label>
								<input name="s_merged_to" type="text" id="s_merged_to" placeholder="E-Mail des verbundenen Teilnehmers." value="<?php echo $_SESSION["s_merged_to"] ?>">
							</div>
							<div class="input-row">
								<label for="s_student_remark">Teilnehmer- Vermerk:</label>
								<textarea id="s_student_remark" name="s_student_remark" rows="3"><?php echo $_SESSION["s_student_remark"];?></textarea>
							</div>
						</div>
	
					</div>	
					<div id="button-bar2">
						
					
					<?php if(isset($_GET["w1"]) && $_GET["w1"] == "small") { ?>
					
						<input name="w1" type="hidden" value="small">
						
					<?php }?>
					<?php if(isset($_GET["c1"]) && is_numeric($_GET["c1"])) { ?>
					
						<input name="c1" type="hidden" value="<?=$_GET["c1"]?>">
						
					<?php }?>
					
					<input name="view" type="hidden" value="<?php echo $_SESSION["view"] ?>">
					<input name="s_no_reload_data" type="hidden" value = "true">
					
					
			   <?php     /* ******  STUDENT DATA END ****** */
			   
			   
			   
			    	           /* ******  BOTTOM BUTTON BAR BEGIN ****** */
						
					if ($_SESSION["view"] == 'student_detail')
					{ ?>	
						<input name="student_id" type="hidden" value="<?php echo $_SESSION["student_id"] ?>">
						<button form="s-detail-form" type="submit" name="action"
								value="s_update_student" class="rb-button">Speichern</button>
						<button form="s-detail-form" type="submit" name="action"
								value="s_update_student_back" class="rb-button">Speichern und Zurück</button>
						<button form="s-detail-form" type="submit" name="action"
								value="s_update_student_new" class="rb-button">Speichern und Neu</button>
					<?php
					}else{?>	
						<button form="s-detail-form" type="submit" name="action"
								value="s_insert_student" class="rb-button">Speichern</button>
						<button form="s-detail-form" type="submit" name="action"
								value="s_insert_student_back" class="rb-button">Speichern und Zurück</button>
						<button form="s-detail-form" type="submit" name="action"
								value="s_insert_student_new" class="rb-button">Speichern und Neu</button>
					<?php
					} ?>
					</div>
					
					
			    	    <?php  /* ******  BOTTOM BUTTON BAR END ****** */    ?>
				</form>
				
				<div id="c-save-message">
					<?php     /* ******  CONFIRMATION MESSAGE BEGIN  ****** */
					
						if(isset($_SESSION["s_success_msg"]) && $_SESSION["s_success_msg"]) {
							
							echo $_SESSION["s_success_msg"];
							$_SESSION["s_success_msg"] = false;
							
						}
						if(isset($_SESSION["s_error_msg"]) && $_SESSION["s_error_msg"] ) {
							echo $_SESSION["s_error_msg"];
							unset($_SESSION["s_error_msg"]);							
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
		rb_init_datepicker($( "#c-date1, #c-date2, #c-date3, #c-date4, #c-date5, #c-date6, #c-date7, #c-date8" ));
		

		function rb_set_loading_effect() {
				$("#rb-container, .rb-button, table, label, select, input, a").addClass("loading");
		};
		
		$("form").submit(function(){   // loading effect
				rb_set_loading_effect();
		});	
		
		 $( "#saved_done" ).fadeOut( 5000, function() {});
		 
		<?php if(isset($_GET["w1"]) && $_GET["w1"] == "small") {
			if(isset($_GET["c1"]) && is_numeric($_GET["c1"])) {
				echo "expand_get_string = '&w1=" . $_GET["w1"] . "&c1=" . $_GET["c1"] . "';";
			}else{
				echo "expand_get_string = '&w1=" . $_GET["w1"] . "';";				
			}
		}else{
			echo "expand_get_string = '';";
			
		} ?>
		 
		$("select[name='s_status']").on('change', function () {
    		if($(this).val() == 3) {
    			$("#merged-to-container").show();
    		}else{
    			$("#merged-to-container").hide();
    		}
		}); 
		 
		 
		<?php     /* ******  CLICK ACTION BEGIN  ****** */   ?>
		 

		
		$( "#button-registrate" ).click(function() {
			rb_set_loading_effect();
			window.location.href = '<?php echo $rb_path_self?>?view=student_detail&action=s_registrate_student<?if(isset($_GET["c1"])) echo "&c1=" . $_GET["c1"]?>&student=<?=$_SESSION["student_id"]?>' + expand_get_string;
			
		});
		
		
		$( "#button-close").click(function() {
			window.close();			
		});
		
		 $("#button-registrations_disabled" ).click(function() {

			rb_set_loading_effect();
			window.location.href = '<?php echo $rb_path_self?>?view=student_registrations&student=' +
																'<?php if (isset($_SESSION["student_id"])) echo $_SESSION["student_id"] ?>';
		});
		
		
		<?php     /* ******  CLICK ACTION END  ****** */   ?>
		
	});

	</script>
</html>