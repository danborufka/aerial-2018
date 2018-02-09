<?php
defined("main-call") or die();
?>

<!DOCTYPE html>
<head>
	<title>Aerial Silk Booking</title>
	<link href='http://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700' rel='stylesheet' type='text/css'>
	<meta name="viewport" content="width=device-width" />
	<meta name="author" content="Ing. Roman Breitschopf, BA">
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<link href="./lib/jqueryui/jquery-ui.css"  rel="stylesheet" type="text/css" />
	<link href="./css/general.css" rel="stylesheet" type="text/css" />
	<link href="./css/header.css" rel="stylesheet" type="text/css" />
	<link href="./css/course_level_view.css" rel="stylesheet" type="text/css" />
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>
	<script src="./lib/jquery.js"></script>
	<script src="./lib/jquery.mobile.min.js"></script>
	<script src="./lib/jqueryui/jquery-ui.js"></script>
	<script src="./_v2/js/clientManager.js"></script>
</head>
<body>
	<div id="rb-container">
		<?php require $rb_configuration->relative_path_of_header_php;
			$view_name_begin = 'course_formats';
			$pre = 'cf';  // = entity pre abbreviation
		?>
		<div id="container">
			
			<div class="container-overview">
				<div class="title">
					<h2>Kurs-Ebenen</h2>
				</div>
				<div id="button-bar">
					<div id="button-go-back" onclick="window.location.href ='<?=$rb_path_self?>?view=main_menu'" class="rb-button">Zurück</div>
					<div id="button-detail" class="rb-button">Details</div>
					<div id="button-new" onclick="cm.CourseLevels.new();" class="rb-button">Neu</div>
				</div>
	
				<div id="search-option-container">	
					<form class="filter-form">
						<div id="search-option-row1">
							<label for='filter_name'>Kurslevelname:</label>
							<input type="text" id="filter_name" name="filter_name" />
							<label for="filter_status">Status:</label>
							<select name='filter_status' id='filter_status' class='select-with-symbols'>
								<option value='1'>✔   aktiv</option>
								<option value='0'>✖   deaktiviert</option>
								<option value='-2'>Ⓐ  alle</option>
							</select>
							<input type="button" onclick="cm.CourseLevels.getSearchResult(0);" id="search-button" class="rb-button" value="Suchoptionen anwenden">
						</div>
					</form>
					
				</div>
	
				<div class='rb-table'>
					Loading ...
				</div>
			</div>
			
			<div class="container-detail" style="display:none">
				<?
					require_once(__DIR__.'/detail_form.php');
				?>
			</div>

		</div>
	</div>
</body>
		
<script type="text/javascript">
	$(function() {
		
		cm.CourseLevels.getSearchResult(0);
		cm.CourseLevels.getCourseFormatSelectOptions();
		cm.CourseLevels.getCourseTypeSelectOptions();
		
		$( "#button-detail" ).click(function() {
			var $id = cm.SelectableManager.getSelectedId;
			if(!$id) {
				alert("Bitte Auswahl treffen.");
			}else{
				cm.CourseLevels.editWithId($id);
			}
		});
		
		$("#cl_course_format").on('change', function () {
			$('#cl_course_type').val('');
			$('#cl_course_type option:not(:first-child)').hide();
			var course_format_id = $("#cl_course_format").val();
			$('#cl_course_type option[course_format_id="' + course_format_id + '"]').show();
		});
		
		$(".filter-form select").on('change', function () {
			cm.CourseLevels.getSearchResult(0);
		}); 
		
		$(".filter-form input[type='text']").on('keyup', function () {
			cm.CourseLevels.getSearchResult(400);
		}); 
		
	    $("form input, form select").keypress(function (e) {
	        if ((e.which && e.which == 13) || (e.keyCode && e.keyCode == 13)) {
	            cm.CourseLevels.getSearchResult(0);
	            return false;
	        } else {
	            return true;
	        }
	    });
	});
</script>

</html>