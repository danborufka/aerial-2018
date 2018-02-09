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
	<link href="./css/simple_course_list_view.css" rel="stylesheet" type="text/css" />
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>
	<script src="./lib/jquery.js"></script>
	<script src="./lib/jquery.mobile.min.js"></script>
	<script src="./lib/jqueryui/jquery-ui.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$( "#button-go-back").click(function() {
				$("#rb-container, .rb-button, table, label, select, input, a").addClass("loading");
				window.location.href = '<?php echo $rb_path_self?>?view=main_menu';	
			});
		});
	</script>
	</head>
<body>
	<div id="rb-container">
		<?php require $rb_configuration->relative_path_of_header_php; ?>
		<div id="container">
			<div id="title">
				<h2>Kurse</h2>
			</div>
			<?php /* ******  BUTTON BAR BEGIN ****** */ ?>
			<div id="button-bar">
				<div id="button-go-back" class="rb-button3">
					Zurück
				</div>
				<div id="button-full-mode" class="rb-button3">
					Vollmodus
				</div>
				<div style="display:block">
					<div id="button-attendance" class="rb-button3">
						Anwesenheitsliste
					</div>
					<div id="button-safety-check" class="rb-button3">
						Sicherheitsüberprüfung
					</div>
					<div id="button-course-notes" class="rb-button3">
						Kursnotizen
					</div>
				</div>
			</div>

			</br>
			
			<?php  	   /* ******  COURSE TABLE BEGIN  ****** */
		
			$db_functions->courses->db_load_simple_table_courses($_SESSION["user_id"], "aktuell",date('d.m.Y',strtotime('today - 2 day')) , date('d.m.Y',strtotime('today + 3 month')),
										  "all", "all", -1, "", false, false);
			
			
			
					   /* ******  COURSE TABLE END  ****** */    ?>

		</div>
	</div>

	<div style="display: none;" id="dialog-info1" title="Kurs auswählen">
	  <p>Bitte zunächst einen Kurs auswählen.</p>
	</div>
</body>


<script type="text/javascript">
	$(document).ready(function() {
		
		function rb_init_datepicker(p_element) {				
			p_element.datepicker({
				<?php $rb_configuration->get_datepicker_options(); ?>
			});
			
		};
		
		function rb_init_selectable(p_element) {			
			
			$(p_element + ' tr').addClass('rb-selectable');			
			$('.rb-selectable:not(:first-child)').on('click mousedown', function () {
				
				$('.ui-selected').toggleClass('ui-selected');
				$('.ui-selecting').toggleClass('ui-selecting');				
				$(this).addClass('ui-selected ui-selecting');				
				
			});
			
		};
				
		function rb_get_selected_id(){
			return $(".ui-selected, ui-selecting").first().attr('course_id');
		};	
		
		function rb_set_loading_effect() {
				$("#rb-container, .rb-button, .rb-button3, table, label, select, input, a").addClass("loading");
		};
		
		$("form").submit(function(){   // loading effect
				rb_set_loading_effect();
		});	
		
		// ***************************************
		
		rb_init_selectable(".courses-list-table");
		
		
		
		<?php		 /* ******  CLICK ACTION BEGIN  ****** */    ?>
		
		<? if($_SESSION["user_is_organizer"] == 1 || $_SESSION["user_is_admin"] == 1) { ?>
			$( ".courses-list-table  tr:not(:first-child)" ).dblclick(function() {  // doubleclick
				rb_set_loading_effect();
				window.location.href = '<?php echo $rb_path_self?>?view=course_detail_reload&course=' + ($(this).attr('course_id'));
			});
		<? } ?>
		$( "#button-attendance" ).click(function() {
			var course_id = rb_get_selected_id();
			if(!course_id) {
				get_dialog_info1();
			}else{
				rb_set_loading_effect();
			window.location.href = '<?php echo $rb_path_self?>?view=attendance&course=' + (course_id) + '&reg=0';
			}
		});
		$( "#button-full-mode" ).click(function() {
			rb_set_loading_effect();
			window.location.href = '<?php echo $rb_path_self?>?view=course_list&view_mode=full';				
		});
		$( "#button-course-detail" ).click(function() {
			var course_id = rb_get_selected_id();
			if(!course_id) {
				get_dialog_info1();
			}else{
				rb_set_loading_effect();
				window.location.href = '<?php echo $rb_path_self?>?view=course_detail_reload&course=' + (course_id);
			}
		});
		$( "#button-safety-check" ).click(function() {
			var course_id = rb_get_selected_id();
			if(!course_id) {
				get_dialog_info1();
			}else{
				rb_set_loading_effect();
				window.location.href = '<?php echo $rb_path_self?>?view=safety_check&course=' + (course_id);
			}
		});
		$( "#button-course-notes" ).click(function() {
			var course_id = rb_get_selected_id();
			if(!course_id) {
				get_dialog_info1();
			}else{
				rb_set_loading_effect();
				window.location.href = '<?php echo $rb_path_self?>?view=course_notes&course=' + (course_id);
			}
		});

		$( "#button-create-course" ).click(function() {
			rb_set_loading_effect();
			window.location.href = '<?php echo $rb_path_self?>?view=course_new&action=reset_course_values';				
		});
		
		
		$(".button-registrations").click(function() {
			rb_set_loading_effect();
			window.location.href = '<?php echo $rb_path_self?>?view=course_registrations&r_course=' + $(this).attr('course_id')+ '&reg=0';			
		});
		$(".button-course-name").click(function() {
			rb_set_loading_effect();
			window.location.href = '<?php echo $rb_path_self?>?view=course_detail_reload&course=' + $(this).attr('course_id');			
		});
		
		<?php		 /* ******  CLICK ACTION END  ****** */    ?>


		function get_dialog_info1() {
		    $( "#dialog-info1" ).dialog({
		  	  position: { my: 'top', at: 'top+80' },
		      resizable: true,
		      width: 300,
		      modal: true,
		      buttons: {
		        "Okay": function() {
		          $( this ).dialog( "close" );
		        }
		      }
		    });		
		};
		
	    $(function () {
	    	$("form input, form select, body").keypress(function (e) {
		        if ((e.which && e.which == 13) || (e.keyCode && e.keyCode == 13)) {
		            $('#search-button').click();
		            return false;
		        } else {
		            return true;
		        }
		    });
		});


		$( ".courses-list-table  tr:not(:first-child)" ).on('taphold', function() {
			rb_set_loading_effect();
			<? if($_SESSION["user_is_organizer"] == 1 || $_SESSION["user_is_admin"] == 1) { ?>
				window.location.href = '<?php echo $rb_path_self?>?view=course_detail_reload&course=' + ($(this).attr('course_id'));
			<? } else { ?>
				window.location.href = '<?php echo $rb_path_self?>?view=attendance&course=' + ($(this).attr('course_id'));
			<? } ?>
			return false;
		});
	});
</script>

</html>