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
	<link href="./css/course_notes_view.css" rel="stylesheet" type="text/css" />
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
		<?php require $rb_configuration->relative_path_of_header_php;
			  if (!(isset($_SESSION["n_edit"]))) $_SESSION["n_edit"] = 0; ?>
		<div id="container">
			<div id="title">
				<h2>Kursnotizen</h2>
			</div>
			<div id="button-bar">
				<div id="button-go-back" class="rb-button3">
					Zurück
				</div>
				<div id="button-attendance" class="rb-button3">
					Anwesenheitsliste
				</div>
				<div id="button-safety-check" class="rb-button3">
					Sicherheitsüberprüfung
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
			<div id="default-button-bar-container" <? if($_SESSION["n_edit"] != 0) echo "style='display:none'"?>>
				<div id="button-edit" class='rb-button3'>
					Bearbeiten
				</div>
				<? if($_SESSION["todo"] == 0) { ?>
					<div id="button-set-todo" class='rb-button3'>
						Setze offene Aufgabe
					</div>
				<?}else{?>
					<div id="button-remove-todo" class='rb-button3'>
						Entferne offene Aufgabe
					</div>
				<?}?>
			</div>
			<div id="edit-mode-button-bar-container" <? if(!($_SESSION["n_edit"] != 0)) echo "style='display:none'"?>>
				<button form="notes-form" name="action" value="n_update_notes" class='id-button-save rb-button3' >
					Speichern
				</button>
				<div id="button-edit-cancel" class='rb-button3' >
					Änderung verwerfen
				</div>
			</div>
			<form id="notes-form" method="POST" action="process.php">
				<input type="hidden" name="view" value="course_notes">
				<input type="hidden" name="n_edit" value="<?=$_SESSION["n_edit"]?>">
				<input type="hidden" name="course_id" value="<?=$_SESSION["course_id"]?>">
				<?
				$db_functions->courses_extras->db_load_table_course_notes($_SESSION["course_id"], $_SESSION["n_edit"]); ?>
			</form>

		</div>
	</div>
	
	<div style="display: none;" id="dialog-info1" title="Kurs auswählen">
	  <p>Bitte zunächst eine Anmeldung auswählen.</p>
	</div>
	<div style="display: none;" id="dialog-save-info" title="Änderung speichern?">
	  <p>Sollen Änderungen gespeichert werden?</p>
	</div>
</body>
		
<script type="text/javascript">
	$(document).ready(function() {
		var course_id = <?php echo $_SESSION["course_id"]; ?>;
		
		function rb_init_selectable(p_element) {				
			p_element.selectable({
			filter: 'tr',
			cancel: 'th',
			selecting: function(event, ui){ /* avoid multiple selecting */
							if( $(".ui-selected, .ui-selecting").length > 1){
								$(ui.selecting).removeClass("ui-selecting");
					   		}
					   }
			});
		};
		
		
		<? if(!(isset($_SESSION["n_edit"]) && $_SESSION["n_edit"] != '0')) echo "rb_init_selectable($('.table-notes'));"; ?>		
		
		function rb_set_loading_effect() {
				$("#rb-container, .rb-button, table, label, select, input, a").addClass("loading");
		};
		
		$("form").submit(function(){   // loading effect
				rb_set_loading_effect();
		});	
		
		// ***************************************
		
		$( ".table-notes-disabled  tr:not(:first-child)" ).dblclick(function() {  // doubleclick
			
			<? if($_SESSION["n_edit"] == '0') { ?>
				rb_set_loading_effect();
				window.location.href = '<?php echo $rb_path_self?>?view=course_notes&course=' + course_id + '&edit=' + $(this).attr('edit');
			<? } else { ?>
					get_dialog_save($(this).attr('edit'));
			<? } ?>	
		});

		$("#button-course-detail" ).click(function() {
			rb_set_loading_effect();
			window.location.href = '<?=$rb_path_self?>?view=course_detail&course=' + course_id;
		});
		$("#button-registrations" ).click(function() {
			rb_set_loading_effect();
			window.location.href = '<?=$rb_path_self?>?view=course_registrations&r_course=' + course_id;
		});
		
		$("#button-attendance" ).click(function() {
			rb_set_loading_effect();
			window.location.href = '<?=$rb_path_self?>?view=attendance&course=' + course_id;
		});
		
		$("#button-safety-check" ).click(function() {
			rb_set_loading_effect();
			window.location.href = '<?=$rb_path_self?>?view=safety_check&course=' + course_id;
		});
		
		$("#button-edit-cancel" ).click(function() {
			rb_set_loading_effect();
			window.location.href = '<?=$rb_path_self?>?view=course_notes&course=' + course_id + '&edit=0';
		});
		
		$( "#button-edit" ).click(function() {
			var edit = $(".ui-selected, ui-selecting").first().attr('edit');
			if(!edit) {	
				get_dialog_info1();
			}else{
				rb_set_loading_effect();
				window.location.href = '<?php echo $rb_path_self?>?view=course_notes&course=' + course_id + '&edit=' + edit;
			}
		});
		$( "#button-set-todo" ).click(function() {
				rb_set_loading_effect();
				window.location.href = '<?php echo $rb_path_self?>?view=course_notes&edit=0&course=' + course_id + '&action=n_set_todo';
		});
		$( "#button-remove-todo" ).click(function() {
				rb_set_loading_effect();
				window.location.href = '<?php echo $rb_path_self?>?view=course_notes&edit=0&course=' + course_id + '&action=n_remove_todo';
		});

		function get_dialog_info1() {
		    $( "#dialog-info1" ).dialog({
		  	  position: { my: 'top', at: 'top+80' },
		      resizable: true,
		      width: 450,
		      modal: true,
		      buttons: {
		        "Okay": function() {
		          $( this ).dialog( "close" );
		        }
		      }
		    });		
		};
		
		function get_dialog_save(p_edit) {
		    $( "#dialog-save-info" ).dialog({
		  	  position: { my: 'top', at: 'top+80' },
		      resizable: true,
		      width: 450,
		      modal: true,
		      buttons: {
		        "Speichern": function() {
		          $( this ).dialog( "close" );
			      $('#notes-form').append("<input type='hidden' name='n_next_edit' value='" + p_edit + "'/>");
			      $('#notes-form').append("<input type='hidden' name='action' value='n_update_and_edit_next' />");
			      $('#notes-form').submit(); 
		          
		        },
		        "Verwerfen": function() {
					$( this ).dialog( "close" );					
					if(p_edit != <?=$_SESSION["n_edit"]?>) {
						window.location.href = '<?php echo $rb_path_self?>?view=course_notes&course=' + course_id + '&edit=' + p_edit;
					}else {
						window.location.href = '<?php echo $rb_path_self?>?view=course_notes&course=' + course_id + '&edit=0';	
					}
		        }
		      }
		    });		
		};
	});

</script>

</html>