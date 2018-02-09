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
	<link href="./css/attendance_view.css" rel="stylesheet" type="text/css" />
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
				<h2>Anwesenheitsliste</h2>
			</div>
			<div id="button-bar">
				<div id="button-go-back" class="rb-button3">
					Zurück
				</div>
				<div id="button-safety-check" class="rb-button3">
					Sicherheitsüberprüfung
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
			<form id="attendance-form" method="POST" action="process.php">
				<button type="submit" name="action" value="a_update_attendance" class="rb-button3">
					Speichern
				</button>
				<? if(($_SESSION["user_is_organizer"] == 1 || $_SESSION["user_is_admin"] == 1)
				       && empty($dates[1])) { ?>
				<div id="button-apply-to-status" class="rb-button3" >
					Bezahltstatus übernehmen
				</div>
				<? } ?>
				<input type="hidden" name="view" value="attendance">
				<input type="hidden" name="course" value="<?=$_SESSION["course_id"];?>">
				<input type="hidden" name="r_course" value="<?=$_SESSION["course_id"];?>">
				<?
				$db_functions->courses_extras->db_load_table_attendance($_SESSION["course_id"], $dates); ?>
			<div style="padding-top: 12px;">
				<button type="submit" name="action" value="a_update_attendance" class="rb-button3">
					Speichern
				</button>
			</div>
			</form>
		</div>
	</div>
</body>
<div id="dialog-apply-to-status" title= "Anwesenheiten auf Status übertragen" style="display:none">
	Soll der Status der Anmeldungen, die derzeit auf "angemeldet" stehen, entsprechend der Anwesenheit auf "bestätigt" bzw. auf "abgemeldet" aktualisiert werden?
	<br />"Nachholer" und "Sonstiges" werden nicht aktualisiert.
	<br />Der Vorgang wechselt zur Ansicht "Anmeldungen".
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
			window.location.href = '<?=$rb_path_self?>?view=course_registrations&r_course=' + course_id + '&reg=0';
		});
		$("#button-safety-check" ).click(function() {
			rb_set_loading_effect();
			window.location.href = '<?=$rb_path_self?>?view=safety_check&course=' + course_id;
		});
		$("#button-notes" ).click(function() {
			rb_set_loading_effect();
			window.location.href = '<?=$rb_path_self?>?view=course_notes&course=' + course_id + '&edit=0';
		});


		
		$( "#button-registration-link" ).click(function() {
			<? if(!(isset($_SESSION['r_registration_code']))) $_SESSION['r_registration_code'] = 'undefined'; ?>
        	window.open('<?=str_replace("booking/", "", $rb_path_self)?>anmeldung/?kurs=' + course_id + '&code=<?=$_SESSION['r_registration_code']?>','_blank');
		});
	
	
		$( "#button-apply-to-status" ).click(function() {
			get_dialog_apply_to_status();
		});
	
		function get_dialog_apply_to_status() {
		    $( "#dialog-apply-to-status" ).dialog({
		  	  position: { my: 'top', at: 'top+80' },
		      resizable: true,
		      width: 450,
		      modal: true,
		      buttons: {
		        "Vorgang durchführen": function() {
		          $( this ).dialog( "close" );
			      $('#attendance-form').append("<input type='hidden' name='reg' value='0' />");
			      $('#attendance-form').append("<input type='hidden' name='action' value='a_apply_to_status' />");
			      $('#attendance-form').submit();
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