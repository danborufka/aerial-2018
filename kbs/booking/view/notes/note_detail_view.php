<?php
defined("main-call") or die();
?>

<!DOCTYPE html>
<head>
	<title>Verwaltungsnotizen - Aerial Silk Booking</title>
	<link href='http://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700' rel='stylesheet' type='text/css'>
	<meta name="viewport" content="width=device-width" />
	<meta name="author" content="Ing. Roman Breitschopf, BA">
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<link href="./lib/jqueryui/jquery-ui.css"  rel="stylesheet" type="text/css" />
	<link href="./css/general.css"           rel="stylesheet" type="text/css" />
	<link href="./css/header.css"            rel="stylesheet" type="text/css" />
	<link href="./css/note_detail_view.css" rel="stylesheet" type="text/css" />
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>
	<script src="./lib/jquery.js"></script>
	<script src="./lib/jqueryui/jquery-ui.js"></script>
	<script src="./lib/jqueryui/jquery-ui.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$( "#button-go-back").click(function() {
				$("#rb-container, .rb-button, .rb-button3, table, label, select, input, a").addClass("loading");
			window.location.href = '<?php echo $rb_path_self?>?view=note_list&note=' + '<? echo empty($_SESSION["note_id"]) ? 0 : $_SESSION["note_id"]?>' + '&action=nt_unlock_note';
			});
		});
	</script>
	</head>
<body>
	<div id="rb-container">
		<?php
			require $rb_configuration->relative_path_of_header_php;
			
			switch ($_SESSION["view"]) {
				case "note_new":
					$title = "Neue Verwaltungsnotiz anlegen";
					$_SESSION["note_id"] = 0;
					break;
				case "note_detail":
					$title = "Notiz- Detail";
					break;
				default:
					$title = "View nicht gefunden - Fehler";
					break;
			}
			
			$lock_data = false;
			
			$lock_data = $db_functions->notes->db_lock_note($_SESSION["note_id"], $_SESSION["user_id"]);
			
		?>
			<div id="container" class="clearfix" style="position: relative;">
				<div id="title">
					<h2><?php echo $title?></h2>
				</div>
	
			<?php /* ******  BUTTON BAR BEGIN ****** */ ?>
				<div id="button-bar">
					
				<?php if(isset($_GET["w1"]) && $_GET["w1"] == "small") { ?>
					<div id="button-close" class="rb-button3">
						Schließen
					</div>
				<?php } ?>
					<div id="button-go-back" class="rb-button3">
						Zurück
					</div>
				<? if(isset($lock_data) && ($lock_data)) { ?>
					<div class="button-unlock-note rb-button3">
						Sperre aufheben
					</div>
				<? } else {
								if ($_SESSION["view"] == 'note_detail')
								{ ?>	
									<input name="note_id" type="hidden" value="<?php echo $_SESSION["note_id"] ?>">
									<button form="form" type="submit" name="action"
											value="nt_update_note" class="rb-button3">Speichern</button>
									<button form="form" type="submit" name="action"
											value="nt_update_note_back" class="rb-button3">Speichern und zurück</button>
								<?php
								}else{?>	
									<button form="form" type="submit" name="action"
											value="nt_insert_note" class="rb-button3">Speichern</button>
									<button form="form" type="submit" name="action"
											value="nt_insert_note_back" class="rb-button3">Speichern und Zurück</button>
								<?php
								}
					} ?>
				</div>
	
			<?php /* ******  BUTTON BAR END ****** */ ?>
			
			
				<?php /* ******  INIT VALUES BEGIN ****** */
				
		
					if(!isset($_SESSION["nt_name"])) {
						$_SESSION["nt_name"] = "";
					}
					if(!isset($_SESSION["nt_status"]) ) {
						$_SESSION["nt_status"] = "1";
					}
					if(!isset($_SESSION["nt_note_text"]) ) {
						$_SESSION["nt_note_text"] = "";
					}
								
			       /* ******  INIT VALUES END ****** */
								  
								  
					if ($_SESSION["view"] == "note_detail") {
						if(isset($_SESSION["nt_no_reload_data"]) && $_SESSION["nt_no_reload_data"] == "true")	{
							unset($_SESSION["nt_no_reload_data"]);
						}else{							
							$db_functions->notes->db_load_note_values_from_id($_SESSION["note_id"]);  // SET VALUES FROM ID *****
						}
					}
					
				
			       /* ******  DATA BEGIN ****** */?>
				
				<form id="form" method="POST" action="process.php" class="input-container clear-fix">
					<div>
						<div id="input-column1 clearfix">
								<div class="input-row">
									<label for="nt_name">Notizname*:</label>
									<input name="nt_name" type="text" id="nt_name" value="<?php echo $_SESSION["nt_name"] ?>">
						
									
									<label for="nt_status" style="width: 60px">Status*:</label>
									<select name='nt_status' id='nt_status' class='select-with-symbols' style="width: 100px">
										<option value='1'<?php echo ($_SESSION["nt_status"] == 1)? " selected":"" ?>>✔   aktiv</option>
										<option value='0'<?php echo ($_SESSION["nt_status"] == 0)? " selected":"" ?>>✖   deaktiviert</option>
									</select>
								</div>
						</div>
								
										<?	if(isset($_SESSION["nt_error_msg"]) && $_SESSION["nt_error_msg"] ) { ?>
						<div class="input-row clearfix">
													
							<div id="nt-error-message" style="display:block; color: red">
														<p></p><?=$_SESSION["nt_error_msg"]?></p>
							</div"> <?		
												unset($_SESSION["nt_error_msg"]);	?>
								
						</div>		
						<br />		
											<? } ?>
						<div class="input-row" >
							<div class="input-row">
										<textarea id="nt_note_text" name="nt_note_text"><?=$_SESSION["nt_note_text"]?></textarea>
							</div>
						</div>
							
						
					</div>
					<div id="button-bar2 clearfix">
					<? if(isset($lock_data) && ($lock_data)) { ?>
						<div class="button-unlock-note rb-button3">
							Sperre aufheben
						</div>
					<? } else {
						
								
								   if ($_SESSION["view"] == 'note_detail')
									{ ?>	
										<button form="form" type="submit" name="action"
												value="nt_update_note" class="rb-button3">Speichern</button>
										<button form="form" type="submit" name="action"
												value="nt_update_note_back" class="rb-button3">Speichern und zurück</button>
									<?
									}else{?>	
										<button form="form" type="submit" name="action"
												value="nt_insert_note" class="rb-button3">Speichern</button>
										<button form="form" type="submit" name="action"
												value="nt_insert_note_back" class="rb-button3">Speichern und Zurück</button>
									<?
									} 
							}
					 if(isset($_GET["w1"]) && $_GET["w1"] == "small") { ?>
						<input name="w1" type="hidden" value="small">
					<? }?>
					<input name="view" type="hidden" value="<?php echo $_SESSION["view"] ?>">
					<input name="nt_no_reload_data" type="hidden" value = "true">
			   <?php     /* ******  DATA END ****** */ ?>
			   
			   
			  
					</div>
			    	   
				</form>
				
				
			</div>
												<?php     /* ******  CONFIRMATION MESSAGE BEGIN  ****** */
										
											if(isset($_SESSION["nt_success_msg"]) && $_SESSION["nt_success_msg"]) { ?>
												 	<div id="nt-save-message">
													<?=$_SESSION["nt_success_msg"]?>
												 	</div"> <?
												$_SESSION["nt_success_msg"] = false;
												
											}
												 /* ******  CONFIRMATION MESSAGE END ****** */   ?>
		</div>
	</body>
	
	<div id="dialog-unlock" title= "Sperre aufheben?" style="display:none">
		Die Notiz ist gesperrt, weil ein anderer User vor kurzem daran gearbeitet hat oder gerade daran arbeitet.
		Soll die Notiz trotzdem entsperrt werden?
	</div>
	
	<script type="text/javascript">
	$(document).ready(function() {
		
		function rb_set_loading_effect() {
				$("#rb-container, .rb-button, .rb-button3, table, label, select, input, a").addClass("loading");
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
		
		<? if(isset($lock_data) && ($lock_data)) { ?>
			$("input:not([type='submit']), select, textarea").prop('readonly', true);
			
			$("input:not([type='submit']), select, textarea").css('background','#b3b3b3');	
		<? } ?>
		
		$( "#button-close").click(function() {
			window.close();
		});
		
		$(".button-unlock-note").click(function() {
			get_dialog_unlock(<?=$_SESSION["note_id"]?>);
		});
		
		
		function get_dialog_unlock(p_note_id) {
		    $( "#dialog-unlock" ).dialog({
		  	  position: { my: 'top', at: 'top+80' },
		      resizable: true,
		      width: 450,
		      modal: true,
		      buttons: {
		        "Sperre aufheben": function() {
		          $( this ).dialog( "close" );					
				  window.location.href = '<?php echo $rb_path_self?>?view=note_detail_reload&note=' + p_note_id + '&action=nt_unlock_note_manually';
		        },
		        "Abbrechen": function() {
					$( this ).dialog( "close" );
		        }
		      }
		    });		
		};
		
	});

	</script>
</html>