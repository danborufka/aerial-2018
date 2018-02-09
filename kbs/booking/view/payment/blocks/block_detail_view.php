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
	<link href="./css/block_detail_view.css" rel="stylesheet" type="text/css" />
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
				window.location.href = '<?php echo $rb_path_self?>?view=block_list' + expand_get_string;
			});
		});
	</script>
	</head>
<body>
	<div id="rb-container">
		<?php
			require $rb_configuration->relative_path_of_header_php;
			
			switch ($_SESSION["view"]) {
				case "block_new":
					$title = "Neuen Block erzeugen";
					break;
				case "block_detail":
					$title = "Block- Details";
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
					<div id="button-go-back" class="rb-button">
						Zurück
					</div>
					
					<? if($_SESSION["view"] == 'block_new') { ?>
						
						<div id="button-student-list" class="rb-button3">
							zur Teilnehmersuche
						</div>
					<? } ?>
					
				</div>
	
			<?php /* ******  BUTTON BAR END ****** */ ?>
			
			
				<?php /* ******  INIT VALUES BEGIN ****** */
				
		
					if(!isset($_SESSION["block_prename"])) {
						$_SESSION["block_prename"] = "";
					}
					if(!isset($_SESSION["block_surname"])) {
						$_SESSION["block_surname"] = "";
					}
					if(!isset($_SESSION["block_email"])) {
						$_SESSION["block_email"] = "";
					}
					if(!isset($_SESSION["block_student_id"])) {
						$_SESSION["block_student_id"] = "5";
					}
					if(!isset($_SESSION["block_size"]) ) {
						$_SESSION["block_size"] = "10";
					}
					if(!isset($_SESSION["block_status"]) ) {
						$_SESSION["block_status"] = "1";
					}
					if(!isset($_SESSION["block_pay_status"]) ) {
						$_SESSION["block_pay_status"] = "0";
					}
					if(!isset($_SESSION["block_consumption_count"]) ) {
						$_SESSION["block_consumption_count"] = "0";
					}
					if(!isset($_SESSION["block_consumption_status"]) ) {
						$_SESSION["block_consumption_status"] = "0";
					}
					if(!isset($_SESSION["block_remark"]) ) {
						$_SESSION["block_remark"] = "";
					}
								
			       /* ******  INIT VALUES END ****** */
								  
								  
					if ($_SESSION["view"] == "block_detail") {
						if(isset($_SESSION["block_no_reload_data"]) && $_SESSION["block_no_reload_data"] == "true")	{
							unset($_SESSION["block_no_reload_data"]);
						}else{							
							get_db_functions_ext()->blocks->db_load_block_values_from_id($_SESSION["block_id"]);  // SET VALUES FROM ID *****
						}
					}
					
				
			       /* ******  DATA BEGIN ****** */?>
				
				<form id="detail-form" method="POST" action="process.php" class="input-container clear-fix">
					<div class="clearfix">
						<div id="input-column1">
						<? if ($_SESSION["view"] == "block_detail") { ?>
						
							<div class="input-row">
								<label for="block_prename">Vorname:</label>
								<div style="display: inline-block; margin-left: 3px;margin-bottom: 10px;"><?=$_SESSION["block_prename"]?></div>
							</div>
							<div class="input-row">
								<label for="block_surname">Nachname:</label>
								<div style="display: inline-block; margin-left: 3px;margin-bottom: 10px;"><?=$_SESSION["block_surname"]?></div>
							</div>
						<? } ?>
							<div class="input-row">
								<label for="block_email">E-Mail*:</label>
								<input name="block_email" type="text" id="block_email" value="<?php echo $_SESSION["block_email"] ?>">
							</div>
							<div class="input-row">
								<label for="block_size">Blockgröße:</label>						
								<select name='block_size' id='block_filter_pay_status'>
									<option value='10'<?php echo ($_SESSION["block_size"] == 10  )? " selected":"" ?>>10er Block</option>
									<option value='20'<?php echo ($_SESSION["block_size"] == 20  )? " selected":"" ?>>20er Block</option>
								</select>
							</div>
							<div class="input-row">
								<label for="block_pay_status">Zahlstatus:</label>						
								<select name='block_pay_status' id='block_pay_status'>
									<option value='0'<?php echo ($_SESSION["block_pay_status"] == 0  )? " selected":"" ?>>noch nicht bezahlt</option>
									<option value='1'<?php echo ($_SESSION["block_pay_status"] == 1  )? " selected":"" ?>>bezahlt</option>
								</select>
							</div>
							<div class="input-row">
								<label for="block_remark">Vermerk:</label>
								<textarea id="block_remark" name="block_remark" rows="10"><?php echo $_SESSION["block_remark"];?></textarea>
							</div>
						<? if ($_SESSION["view"] == "block_detail") { ?>
							<div class="input-row">								
								<label for="block_status">Status*:</label>
								<select name='block_status' id='block_status' class='select-with-symbols'>
									<option value='1'<?php echo ($_SESSION["block_status"] == 1)? " selected":"" ?>>✔   aktiv</option>
									<option value='0'<?php echo ($_SESSION["block_status"] == 0)? " selected":"" ?>>✖   storniert</option>
								</select>
							</div>
						<? } ?>
						</div>
							<div class="input-row">
								<label for="block_surname">Verbraucht:</label>
								<input style='width: 50px; margin-right: 10px;' id="block_consumption_count" type="number" value='<?=$_SESSION['block_consumption_count']?>' disabled ?>
								<?//<div style="display: inline-block; margin: 0px 10px 10px 3px"><? echo $_SESSION['block_consumption_count'] . ' / ' . $_SESSION['block_size']? ></div>?>
								<div id='button-increase-count' class='rb-mini-button'>+</div>
								<div id='button-decrease-count' class='rb-mini-button'>-</div>
							</div>
						<div id="input-column2">
							<div class="input-row">
								<label for="block_remark">Verbrauchs-<br>Auflistung:</label>
								<textarea id="block_remark" name="block_remark" rows="20"><?php echo $_SESSION["block_remark"];?></textarea>
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
					<input name="block_no_reload_data" type="hidden" value = "true">
					
					
			   <?php     /* ******  DATA END ****** */
			   
			   
			   
			    	           /* ******  BOTTOM BUTTON BAR BEGIN ****** */
						
					if ($_SESSION["view"] == 'block_detail')
					{ ?>	
						<input name="block_id" type="hidden" value="<?php echo $_SESSION["block_id"] ?>">
						<button form="detail-form" type="submit" name="action"
								value="block_update_block" class="rb-button">Speichern</button>
						<button form="detail-form" type="submit" name="action"
								value="block_update_block_back" class="rb-button">Speichern und Zurück</button>
						<button form="detail-form" type="submit" name="action"
								value="block_update_block_new" class="rb-button">Speichern und Neu</button>
					<?php
					}else{?>	
						<button form="detail-form" type="submit" name="action"
								value="block_insert" class="rb-button">Erstellen</button>
						<button form="detail-form" type="submit" name="action"
								value="block_insert_back" class="rb-button">Erstellen und zurück</button>
						<button form="detail-form" type="submit" name="action"
								value="block_insert_new" class="rb-button">Erstellen und neu</button>
					<?php
					} ?>
					</div>
					
					
			    	    <?php  /* ******  BOTTOM BUTTON BAR END ****** */    ?>
				</form>
				
				<div id="c-save-message">
					<?php     /* ******  CONFIRMATION MESSAGE BEGIN  ****** */
					
						if(isset($_SESSION["block_success_msg"]) && $_SESSION["block_success_msg"]) {
							
							echo $_SESSION["block_success_msg"];
							$_SESSION["block_success_msg"] = false;
							
						}
						if(isset($_SESSION["block_error_msg"]) && $_SESSION["block_error_msg"] ) {
							echo $_SESSION["block_error_msg"];
							unset($_SESSION["block_error_msg"]);							
						}
						
							 /* ******  CONFIRMATION MESSAGE END ****** */   ?>
				</div>
				
			</div>
		</div>
	</body>
	<script type="text/javascript">
	$(document).ready(function() {
		
		$('#button-decrease-count').click(function () {
			var count = parseInt($('#block_consumption_count').val());
			if(count <= 0) {
				count = 0;
			} else {
				$('#block_consumption_count').val(--count);
			}
		});
		$('#button-increase-count').click(function () {

			$('#block_consumption_count').val(parseInt($('#block_consumption_count').val()) + 1);
		
		});
		
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
		 		
		$("#button-student-list").click(function() {
			window.open('<?=$rb_path_self?>?view=student_list&w1=small&r_course=100', 'NewWin',
            											'menubar=no, resizeable=no,toolbar=no,status=no,width=1150,height=665');	
		});
		
		<?php     /* ******  CLICK ACTION END  ****** */   ?>
		
	});

	</script>
</html>