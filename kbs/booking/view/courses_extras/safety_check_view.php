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
	<link href="./css/safety_check_view.css" rel="stylesheet" type="text/css" />
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>
	<script src="./lib/jquery.js"></script>
	<script src="./lib/jqueryui/jquery-ui.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$( "#button-go-back").click(function() {
				$("#rb-container, .rb-button, table, label, select, input, a").addClass("loading");
				window.location.href = '<?=$rb_path_self?>?view=course_list';	
			});
		});
	</script>
	</head>
<body>
	<div id="rb-container">
		<?php require $rb_configuration->relative_path_of_header_php; ?>
		<div id="container">
			<div id="title">
				<h2>Sicherheitsüberprüfung</h2>
			</div>
			<div id="button-bar">
				<div id="button-go-back" class="rb-button3">
					Zurück
				</div>
				<div id="button-attendance" class="rb-button3">
					Anwesenheitsliste
				</div>
				<div id="button-notes" class="rb-button3">
					Kursnotizen
				</div>
				<? if($_SESSION["user_is_organizer"] == 1 || $_SESSION["user_is_admin"] == 1) { ?>
					<div style="display: block">
						<div id="button-course-detail" class="rb-button3">
							Kursdetails
						</div>
						<div id="button-registrations" class="rb-button3">
							Anmeldungen
						</div>
						<div id="button-reset-safety-check" class="rb-button3">
							Bestätigung zurücksetzen
						</div>
					</div>
				<? } ?>
			</div>
			<div id="course-box-container" class="clearfix">
				<div id="course-detail-container">
					<?
					$dates = null;
					$dates = $db_functions->courses_extras->db_load_course_detail($_SESSION["course_id"]);
					?>
				</div>
			</div>
			<form id="safety-check-form" method="POST" action="process.php">
				<input type="hidden" name="view" value="safety_check">
				<input type="hidden" name="course" value="<?=$_SESSION["course_id"];?>">
				<input type="hidden" name="action" value="update_safety_check">
				
				
				
				<div class='table-safety-check'><table>
				<tr><th><div style='min-width:100px'>Termin</div></th><th><div style='min-width:480px'>Bestätigung: Sicherheitsüberprüfung durchgeführt</div></th></tr>
				
				<?
				for ($i=1; $i <=12; $i++) {
					if(!empty($dates[$i-1])) {
						
						if(empty($_SESSION["safety_check" . $i])) {
							echo "<tr><td class='td-center'>Termin " . $i . "</br>" . $dates[$i-1] . "</td><td class='td-center'><div termin_nr= '" . $i . "' class='button-confirm rb-button3'>Bestätigen</div></td></tr>";
						}else {
							echo "<tr><td class='td-center'>Termin " . $i . "</br>" . $dates[$i-1] . "</td><td class='td-center'>" . $_SESSION["safety_check" . $i] . "</td></tr>";
						}
					}
				} ?>
					
				</table></div>
				
			</form>
		</div>
	</div>
</body>
<div id="dialog-reset-safety-check" title= "Alle Bestätigungen zurücksetzen" style="display:none">
	Sollen wirklich alle Bestätigungen dieses Kurses zurückgesetzt werden?
</div>
<div id="dialog-confirm-safety-check" title= "Jetzt bestätigen" style="display:none">
	Soll die Durchführung der Sicherheitsüberprüfung jetzt bestätigt werden?
</div>
		
<script type="text/javascript">
	$(document).ready(function() {
		var course_id = <?php echo $_SESSION["course_id"]; ?>;

		function rb_set_loading_effect() {
				$("#rb-container, .rb-button, .rb-button3, button, table, label, select, input, a").addClass("loading");
		};
		
		$("form").submit(function(){   // loading effect
				rb_set_loading_effect();
		});	
		
		// ***************************************
		

		$("#button-course-detail" ).click(function() {
			rb_set_loading_effect();
			window.location.href = '<?=$rb_path_self?>?view=course_detail&course=' + course_id;
		});

		$("#button-registrations" ).click(function() {
			rb_set_loading_effect();
			window.location.href = '<?=$rb_path_self?>?view=course_registrations&r_course=' + course_id;
		});
		
		$(".button-confirm" ).click(function() {
			  var p_termin_nr = $(this).attr("termin_nr");
			  $( "#dialog-confirm-safety-check" ).dialog({
		  	  position: { my: 'top', at: 'top+80' },
		      resizable: true,
		      width: 450,
		      modal: true,
		      buttons: {
		        "Ja, jetzt bestätigen": function() {
		            $( this ).dialog( "close" );
					confirm_safety_check(p_termin_nr);
		        },
		        "Abbrechen": function() {
					$( this ).dialog( "close" );	
		        }
		      }
		    });
			
			
			
			
			
		});

		function confirm_safety_check(p_termin_nr) {
			$('#safety-check-form').append("<input type='hidden' name='safety_termin_nr' value='" + p_termin_nr + "'/>");
			$('#safety-check-form').submit();
		}

		 $("#button-attendance" ).click(function() {

			rb_set_loading_effect();
			window.location.href = '<?php echo $rb_path_self?>?view=attendance&course=' +
																'<?php if(isset($_SESSION["course_id"])) echo $_SESSION["course_id"] ?>';
		});
		$("#button-notes" ).click(function() {
			rb_set_loading_effect();
			window.location.href = '<?=$rb_path_self?>?view=course_notes&course=' + course_id;
		});
		
		$( "#button-registration-link" ).click(function() {
			<? if(!(isset($_SESSION['r_registration_code']))) $_SESSION['r_registration_code'] = 'undefined'; ?>
        	window.open('<?=str_replace("booking/", "", $rb_path_self)?>anmeldung/?kurs=' + course_id + '&code=<?=$_SESSION['r_registration_code']?>','_blank');
		});
	
		$( "#button-reset-safety-check" ).click(function() {
			get_dialog_reset_safety_check();
		});
	
		function get_dialog_reset_safety_check() {
		    $( "#dialog-reset-safety-check" ).dialog({
		  	  position: { my: 'top', at: 'top+80' },
		      resizable: true,
		      width: 450,
		      modal: true,
		      buttons: {
		        "Alle Bestätigungen zurücksetzen": function() {
		          $( this ).dialog( "close" );
				  window.location.href = '<?=$rb_path_self?>?action=reset_safety_check&view=safety_check&course=' + course_id;
		          
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