<?php
defined("main-call") or die();


 // ########### set default values ########

if(!isset($_SESSION["cal_month"])) {
	$_SESSION["cal_month"] = date("m");
}
if(!isset($_SESSION["cal_year"])) {
	$_SESSION["cal_year"] = date("Y");
}
if(!isset($_SESSION["cal_filter_event"])) {
	$_SESSION["cal_filter_event"] = 1;
}
if(!isset($_SESSION["cal_filter_location"])) {
	$_SESSION["cal_filter_location"] = 1;
}
if(!isset($_SESSION["cal_view_type"])) {
	$_SESSION["cal_view_type"] = "m";
}
if(!isset($_SESSION["cal_filter_type"])) {
	$_SESSION["cal_filter_type"] = 1;
}
if(!isset($_SESSION["cal_start_date"])) {
	$_SESSION["cal_start_date"] = date("d.m.Y");
}
if(!isset($_SESSION["cal_filter_person"])) {
	if($_SESSION["user_is_trainer"]) {
		$_SESSION["cal_filter_person"] = $_SESSION["user_id"];
	}else {
		$_SESSION["cal_filter_person"] = "-9";
	}
}

?>


<!DOCTYPE html>
<head>
	<title>Kalender</title>
	<link href='https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700' rel='stylesheet' type='text/css'>
	<meta name="viewport" content="width=device-width" />
	<meta name="author" content="Ing. Roman Breitschopf, BA">
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<link href="../booking/lib/jqueryui/jquery-ui.css"  rel="stylesheet" type="text/css" />
	<link rel="stylesheet" href="./css/general.css" type="text/css" />
	<link rel="stylesheet" href="./css/header.css" type="text/css" />
	<link rel="stylesheet" href="./css/cal_overview.css" type="text/css" />
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>
	<script src="../booking/lib/jquery.js"></script>
	<script src="../booking/lib/jqueryui/jquery-ui.js"></script>
</head>
<body>
	<div id="super-container-cal-overview" class="clearfix">
		<?php require "header.php";?>
		<div id="container">
			<div id="top-container">
				<div id="main-title">
					<h2>Kalender</h2>
				</div>
				<div id="controller-box" class="clearfix">
					<form name="form-cal-controller" id="form-cal-controller" action="process.php" method="POST">						
						<div id="controller-left">
							<select id="cal-filter-type" name="cal_filter_type">
								<option value="1" <? echo ($_SESSION["cal_filter_type"] == 1) ? " selected": "" ?>>Zeige Ort:</option>
								<option value="2" <? echo ($_SESSION["cal_filter_type"] == 2) ? " selected": "" ?>>Zeige Person:</option>
							</select>
							<br />
							<select id="cal-filter-location" name="cal_filter_location"> 
								<? $cal_db_functions->calendar_options->db_get_location_select_options($_SESSION["cal_filter_location"], true); ?>
							</select>
							<?  if($_SESSION["user_is_organizer"] == 1 || $_SESSION["user_is_admin"] == 1) {
									$is_disabled = "";
								}else {
									$is_disabled = " disabled";
								}
							?>
							<select id="cal-filter-person" name="cal_filter_person" <?=$is_disabled?>>
								<? $cal_db_functions->calendar_options->db_get_user_select_options($_SESSION["cal_filter_person"], false,false,false,-2,true); ?>
							</select>
							
						</div>
						<div id="controller-mid" class='clearfix'>
							<div id="button-month-prev" class="button-month">
								<
							</div>
							<div id="month-year-box">
								<select id="cal-month" name="cal_month">
									<option value="1" <? echo (intval($_SESSION["cal_month"]) == 1) ? " selected": "" ?>>Jänner</option>
									<option value="2" <? echo (intval($_SESSION["cal_month"]) == 2) ? " selected": "" ?>>Februar</option>
									<option value="3" <? echo (intval($_SESSION["cal_month"]) == 3) ? " selected": "" ?>>März</option>
									<option value="4" <? echo (intval($_SESSION["cal_month"]) == 4) ? " selected": "" ?>>April</option>
									<option value="5" <? echo (intval($_SESSION["cal_month"]) == 5) ? " selected": "" ?>>Mai</option>
									<option value="6" <? echo (intval($_SESSION["cal_month"]) == 6) ? " selected": "" ?>>Juni</option>
									<option value="7" <? echo (intval($_SESSION["cal_month"]) == 7) ? " selected": "" ?>>Juli</option>
									<option value="8" <? echo (intval($_SESSION["cal_month"]) == 8) ? " selected": "" ?>>August</option>
									<option value="9" <? echo (intval($_SESSION["cal_month"]) == 9) ? " selected": "" ?>>September</option>
									<option value="10" <? echo (intval($_SESSION["cal_month"]) == 10) ? " selected": "" ?>>Oktober</option>
									<option value="11" <? echo (intval($_SESSION["cal_month"]) == 11) ? " selected": "" ?>>November</option>
									<option value="12" <? echo (intval($_SESSION["cal_month"]) == 12) ? " selected": "" ?>>Dezember</option>
								</select>
								<br />
								<select id="cal-year" name="cal_year">
								<?
									$year = date("Y");
									for($i= $year-10; $i <= $year+10; $i++) {
										if($i < 2015) $i = 2015;
										if(intval($_SESSION["cal_year"]) == $i) {
											$selected = 'selected';
										}else {
											$selected = '';
										}
										echo "<option value='$i' $selected >$i</option>";
									}
								?>
									
								</select>
							</div>
							<div id='start-date-box'>
								<input name="cal_start_date" type="text" id="cal-start-date" value="<?php echo $_SESSION["cal_start_date"] ?>" readonly>
							</div>
							<div id="button-month-next" class="button-month">
								>
							</div>
							
						</div>
						<div id="controller-right">
							
							<div class="mini-button-view-type" id="mini-button-days">5d</div>
							<div class="mini-button-view-type" id="mini-button-month">M</div>
							<select id="cal-view-type" name="cal_view_type">
								<option value="m" <? echo ($_SESSION["cal_view_type"]) == "m" ? " selected": ""; ?> >Monatsansicht</option>
								<option value="d" <? echo ($_SESSION["cal_view_type"]) == "d" ? " selected": ""; ?> >5-Tages-Ansicht</option>
							</select>
							<div id="button-container">							
								<div id= "button-reload-cal" class="button-cal-control button-cal-control-first">Ok</div>
								<div id= "button-new-event" class="button-cal-control">Neu</div>
							</div>
						</div>
					</form>
				</div>
			</div>
			<div id="cal-view-container">
				<?
				global $cal_rb_functions;
				if($_SESSION["cal_view_type"] == 'd') {
					$cal_rb_functions->load_five_days_table($_SESSION['cal_start_date'],$_SESSION['cal_filter_type'],$_SESSION['cal_filter_location'], $_SESSION['cal_filter_person']);
				}else {					
					$cal_rb_functions->load_cal_month_table($_SESSION["cal_month"],$_SESSION["cal_year"],$_SESSION["cal_filter_type"],$_SESSION["cal_filter_location"], $_SESSION["cal_filter_person"]);
				}
				?>
			</div>
			<div id="legend-box" class="clearfix">
				<div class="legend-column1 legend-title">Legende:</div>
				<div class="legend-column2">
					<div class="legend-all legend-c">Kurse aus dem KBS</div>
					<div class="legend-all legend-my-c">Meine Kurse aus dem KBS</div>
					<div class="legend-all legend-2">Kursvorschlag</div>	
					<div class="legend-all legend-3">Trainertreffen</div>				
				</div>
				<div class="legend-column3">
					<div class="legend-all legend-4">Feiertage & Ferien</div>
					<div class="legend-all legend-6">Training / Privater Termin</div>
					<div class="legend-all legend-1">Sonstiges</div>
					<div class="legend-all legend-5">Sonstiges Spezial</div>
				</div>
			</div>
		</div>
	</div>
	<div id="center-box-wrapper"><div id="center-box"></div></div>
</body>
<div id="dialog-course-details" title= "Kurs- Details" style="display:none">
	Kurs nicht gefunden.
</div>
<div id="dialog-today" title= "Today" style="display:none">
	Today is a nice day!
</div>

<script type="text/javascript">
	$(document).ready(function() {
		
		function rb_set_loading_effect() {
				$("#super-container, #container, div, table, label, select, input, a").addClass("loading");
		};
		
		$("#mini-button-days").click(function() {
			$('#cal-view-type').val("d");
			update_controller_date_box_visibility();
			reload_table(0);
		});
		$("#mini-button-month").click(function() {
			$('#cal-view-type').val("m");
			update_controller_date_box_visibility();
			reload_table(0);
		});
		
		$("#button-month-prev").click(function() {
			if($('#cal-view-type').val() == 'm') {
				if(isNaN(parseInt($('#cal-year').val()))) {
					$('#cal-year').val(2016);
				}
				var m = $('#cal-month').val();
				if(m == 1) {
					$('#cal-month').val(12);
					y = parseInt($('#cal-year').val()) - 1;
					$('#cal-year').val(y);
				}else {
					$('#cal-month').val(--m);
				}	
			}else {
				$('#cal-start-date').val(get_prev_day($('#cal-start-date').val()));	
			}
			
			reload_table(0);
			
		});
		$("#button-month-next").click(function() {
			if($('#cal-view-type').val() == 'm') {
				if(isNaN(parseInt($('#cal-year').val()))) {
					$('#cal-year').val(2016);
				}
				var m = $('#cal-month').val();
				if(m == 12) {
					$('#cal-month').val(1);
					y = parseInt($('#cal-year').val()) + 1;
					$('#cal-year').val(y);
				}else {
					$('#cal-month').val(++m);
				}
			}else {
				$('#cal-start-date').val(get_next_day($('#cal-start-date').val()));				
			}
			reload_table(0);
			
		});
		
		function set_events() {
			
			$(".course-box").click(function() {
				var c_id = $(this).first().attr("course_id");
				<? if($_SESSION["user_is_organizer"] == 1 || $_SESSION["user_is_admin"] == 1) { ?>
					window.open("<? echo str_replace("calendar", "booking", $rb_path_self)?>?view=course_registrations&r_course=" + c_id,"_blank");
				<? } else { ?>
					window.open("<? echo str_replace("calendar", "booking", $rb_path_self)?>?view=attendance&course=" + c_id,"_blank");
				<? } ?>
			});
			
			$(".event-box").click(function() {
				var e_id = $(this).first().attr("event_id");
				window.location.href = "<?=$rb_path_self?>?cal_view=event_detail_reload&event="+ e_id;
	
			});	
		}
		set_events();

		
		$("#button-reload-cal").click(function() {
			rb_set_loading_effect();
			$("#form-cal-controller").append("<input type='hidden' name='cal_action' value='reload_cal' >");
			$("#form-cal-controller").append("<input type='hidden' name='cal_view' value='month_view' >");
			$("#form-cal-controller").submit();
		});
		
		$("#button-new-event").click(function() {
			rb_set_loading_effect();
			window.location.href = '<?php echo $rb_path_self?>?cal_view=event_new&cal_action=reset_event_values';	
		});
		
		$("select").on('change', function () {
			reload_table(0);
		}); 
		
		function set_events() {
			
			$(".course-box").click(function() {
				var c_id = $(this).first().attr("course_id");
				var is_my_course = $(this).first().attr("my_course");
				if(is_my_course == 'y') {
					load_course_details_my(c_id);
				}else {
					load_course_details(c_id);
				}
				return false;
			});
			
			$(".event-box").click(function() {
				var e_id = $(this).first().attr("event_id");
				window.location.href = "<?=$rb_path_self?>?cal_view=event_detail_reload&event="+ e_id;
	
			});
			
			$(".today-cell div.day-number").click(function() {
		        $("#center-box-wrapper").css('display','block');
	    		$( "#dialog-today").dialog({
				  	  position: { 	of: $('#center-box'),
				  	  				my: 'center',
				  	  				at: 'center' },
				      resizable: true,
				      width: 400,
				      modal: true,
				      buttons: {
				        "Schließen": function() {
							$( this ).dialog( "close" );
				        }
				      }
			    });
		        $("#center-box-wrapper").css('display','none');
		        return false;
			});
		};
		
		set_events();
		
		function set_min_height() {						
	        $("#cal-view-container table").css('min-height', $("#cal-view-container table").css('height'));
		}
		
		set_min_height();
		
		var delayTimer;
		function reload_table(p_delay_in_ms) {
		    clearTimeout(delayTimer);
		    delayTimer = setTimeout(function(){
		    	var url = "controller/ajax_functions/reload_calendar.php";
		    	url = url + '?m=' + $("#cal-month").val();
		    	url = url + '&y=' + $("#cal-year").val();
		    	url = url + '&l=' + $("#cal-filter-location").val();
		    	url = url + '&p=' + $("#cal-filter-person").val();
		    	url = url + '&t=' + $("#cal-filter-type").val();
		    	url = url + '&v=' + $("#cal-view-type").val();
		    	url = url + '&s=' + $('#cal-start-date').val();
		    	$( "#cal-view-container" ).load(url, function() {		
					set_events();
					set_min_height();
				});
		    	
		    }, p_delay_in_ms);
		}
		
		function load_course_details(p_course_id) {
				var url = "controller/ajax_functions/load_course_details.php";
	    		url = url + '?c=' + p_course_id;
	         	$("#dialog-course-details").load(url, function() {
	         		$("#center-box-wrapper").css('display','block');
		        	$( "#dialog-course-details" ).dialog({
				  	  position: { 	of: $('#center-box'),
				  	  				my: 'center',
				  	  				at: 'center' },
				      resizable: true,
				      width: 540,
				      modal: true,
				      buttons: {
				      	<? if($_SESSION["user_is_organizer"] == 1 || $_SESSION["user_is_admin"] == 1) { ?>
				        "Details": function() {
				        	window.open("<? echo str_replace("calendar", "booking", $rb_path_self)?>?view=course_detail&course=" + p_course_id,"_blank");
							$( this ).dialog( "close" );
				        },
				        "Anmeldungen": function() {
				        	window.open("<? echo str_replace("calendar", "booking", $rb_path_self)?>?view=course_registrations&r_course=" + p_course_id,"_blank");
							$( this ).dialog( "close" );
				        },				        
				        "Anwesenheiten": function() {
				        	window.open("<? echo str_replace("calendar", "booking", $rb_path_self)?>?view=attendance&course=" + p_course_id,"_blank");
							$( this ).dialog( "close" );
				        },
				        <? }?>
				        "Schließen": function() {
							$( this ).dialog( "close" );
				        }
				      }
				    });
	         		$("#center-box-wrapper").css('display','none');
	         	});
		};
		
		function load_course_details_my(p_course_id) {
				var url = "controller/ajax_functions/load_course_details.php";
	    		url = url + '?c=' + p_course_id;
	         	$("#dialog-course-details").load(url, function() {
	         		$("#center-box-wrapper").css('display','block');
		        	$( "#dialog-course-details" ).dialog({
				  	  position: { 	of: $('#center-box'),
				  	  				my: 'center',
				  	  				at: 'center' },
				      resizable: true,
				      width: 540,
				      modal: true,
				      buttons: {
				      	<? if($_SESSION["user_is_organizer"] == 1 || $_SESSION["user_is_admin"] == 1) { ?>
				        "Details": function() {
				        	window.open("<? echo str_replace("calendar", "booking", $rb_path_self)?>?view=course_detail&course=" + p_course_id,"_blank");
							$( this ).dialog( "close" );
				        },
				        "Anmeldungen": function() {
				        	window.open("<? echo str_replace("calendar", "booking", $rb_path_self)?>?view=course_registrations&r_course=" + p_course_id,"_blank");
							$( this ).dialog( "close" );
				        },
				        <? }?>			        
				        "Anwesenheiten": function() {
				        	window.open("<? echo str_replace("calendar", "booking", $rb_path_self)?>?view=attendance&course=" + p_course_id,"_blank");
							$( this ).dialog( "close" );
				        },
				        "Schließen": function() {
							$( this ).dialog( "close" );
				        }
				      }
				    });
	         		$("#center-box-wrapper").css('display','none');
	         	});
		};
		
		update_controller_box_visibility();
		
		function update_controller_box_visibility() {
			if($("#cal-filter-type").val() == 1) {
				$("#cal-filter-person").hide();
				$("#cal-filter-location").show();
			}else {
				$("#cal-filter-location").hide();
				$("#cal-filter-person").show();
				
			}
		}
		
		$("#cal-filter-type").on('change', function() {
			update_controller_box_visibility();
		});
		
		update_controller_date_box_visibility();
		
		function update_controller_date_box_visibility() {
			if($("#cal-view-type").val() == 'm') {
				$("#start-date-box").hide();
				$("#month-year-box").show();
			}else {
				$("#month-year-box").hide();
				$("#start-date-box").show();
				
			}
		}
		
		$("#cal-view-type").on('change', function() {
			update_controller_date_box_visibility();
		});
		
		$("#cal-start-date").on('change', function() {
			reload_table(0);
		});
		
		
		function get_next_day(p_date) {
			var rb_date = new Date(p_date.substring(6,10) + '-' + p_date.substring(3,5) + '-' + p_date.substring(0,2));
			rb_date = new Date (rb_date.getTime() + 24*60*60*1000);
			var m = (rb_date.getMonth()+1).toString();
			var d = rb_date.getDate().toString();
			if(m.length == 1) {m = '0' + m;}
			if(d.length == 1) {d = '0' + d;}
			return(d + '.' + m + '.' + rb_date.getFullYear());
		}
		
		function get_prev_day(p_date) {
			var rb_date = new Date(p_date.substring(6,10) + '-' + p_date.substring(3,5) + '-' + p_date.substring(0,2));
			rb_date = new Date (rb_date.getTime() - 24*60*60*1000);
			var m = (rb_date.getMonth()+1).toString();
			var d = rb_date.getDate().toString();
			if(m.length == 1) {m = '0' + m;}
			if(d.length == 1) {d = '0' + d;}
			return(d + '.' + m + '.' + rb_date.getFullYear());
		}
		
		$("#cal-start-date" ).datepicker({
					  changeMonth: true,
					  changeYear: true,
					  prevText:  'zurück',
					  nextText:  'vor',
					  firstDay:  1,
					  showButtonPanel: true,
					  closeText: 'Zuklappen',
					  currentText: 'heute',
					  showWeek: true,
					  weekHeader: 'KW',
					  dateFormat: 'dd.mm.yy',
					  monthNames: ['Januar','Februar','März','April','Mai','Juni',
						'Juli','August','September','Oktober','November','Dezember'],
					  monthNamesShort: ['Jan','Feb','Mär','Apr','Mai','Jun',
						'Jul','Aug','Sep','Okt','Nov','Dez'],
					  dayNames: ['Sonntag','Montag','Dienstag','Mittwoch','Donnerstag','Freitag','Samstag'],
					  dayNamesShort: ['So','Mo','Di','Mi','Do','Fr','Sa'],
					  dayNamesMin: ['So','Mo','Di','Mi','Do','Fr','Sa']
		});
	});
</script>
</html>