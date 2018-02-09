<?php
/* Coypright(2015) by Ing. Roman Breitschopf, BA 									*/
/* office@breitschopf.wien			www.breitschopf.wien 							*/
/* License for Jasmin Liska. Do not distribute to others without permission. */
defined("main-call") or die("Error- RB_Functions");

class RB_Functions{
	public function logout() {
	$_SESSION = array();
	if (isset($_COOKIE[session_name()])) {
		setcookie(session_name(), "", time()-42000,"/");
	}
	session_destroy();
	header('Location: ' . str_replace("process.php", "", (str_replace("index.php", "", $_SERVER['PHP_SELF']))));
	exit;
	}
	
	
	public function check_login_processing () {
		if (isset($_SESSION["login"]) && $_SESSION["login"] == "processing") {
			if (isset($_POST["username"]) &&
				$_POST["username"] != ''  &&
				isset($_POST["password"])  )
			{
					$db_functions->db_check_login($_POST["username"], $_POST["password"]);
			}
		}
	}
	
	public function check_login_is_ok_or_die () {
		if (!isset($_SESSION["login"]) || $_SESSION["login"] != "ok") {
	$_SESSION["view"] = "login_view";
	header('Location: ' . str_replace("calendar", "booking", str_replace("process.php", "", (str_replace("index.php", "", $_SERVER['PHP_SELF'])))));
			die();
		}
	}

	public function check_login_is_ok_or_die_for_ajax_function () {
		global $rb_path_self;
		if (!isset($_SESSION["login"]) || $_SESSION["login"] != "ok") {
			echo "Sitzung abgelaufen, erneuter Login erforderlich.<br><div style='margin-top: 7px'><a class='rb-button3' href='" . $rb_path_self . "'>Neuladen</a><div>";
			die();
		}
	}

	public function check_session_duration($max_duration_in_minutes) {
	
		if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 60 * $max_duration_in_minutes))
		{
			$this->logout();
		}else
		{
			$_SESSION['LAST_ACTIVITY'] = time();
		}
	}
	
	public function get_month_calendar_frame($p_month,    
											 $p_year) {		
		
		$m = intval($p_month);
		$y = intval($p_year);
		
		$calendar_frame = array();
		$row_counter = 0;
		$day_counter = 0;
		$row_day_counter = 0;
		$days_in_month = cal_days_in_month(CAL_GREGORIAN, $m, $y);
		
		$week_day_start = date("w", mktime(0, 0, 0, $m, 1, $y));
		if($week_day_start == 0) $week_day_start = 7; // Sunday
		
		while($day_counter < $days_in_month){
			
			$frame_row = array();
			
			for($i = 1; $i <= 7; $i++) { // row processing
				if(($row_counter == 0 && $i <= $week_day_start-1) || $day_counter >= $days_in_month) {
					$frame_row[$i] = "none";
				}else {
					$frame_row[$i] = ++$day_counter;
				}
			}
			
			$row_day_counter = $day_counter;
		
			$calendar_frame[++$row_counter] = $frame_row;
		}
		
		return $calendar_frame;
	}


	public function get_five_days_frame($p_day,
										$p_month,
										$p_year){
		
		$m = intval($p_month);
		$y = intval($p_year);
		
		$calendar_frame = array();
		$row_counter = 0;
		$day_counter = 0;
		$row_day_counter = 0;
		$days_in_month = cal_days_in_month(CAL_GREGORIAN, $m, $y);
		
		$week_day_start = date("w", mktime(0, 0, 0, $m, 1, $y));
		if($week_day_start == 0) $week_day_start = 7; // Sunday
		
		while($day_counter < $days_in_month) {
			
			$frame_row = array();
			
			for($i = 1; $i <= 7; $i++) { // row processing
				if(($row_counter == 0 && $i <= $week_day_start-1) || $day_counter >= $days_in_month) {
					$frame_row[$i] = "none";
				}else {
					$frame_row[$i] = ++$day_counter;
				}
			}
			
			$row_day_counter = $day_counter;

			$calendar_frame[++$row_counter] = $frame_row;
		}
		return $calendar_frame;
	}
											 							 
	public function get_week_number($p_day,
									$p_month,
									$p_year) {
		return date("W", mktime(0, 0, 0, intval($p_month), intval($p_day), $p_year));
	}
	
	public function load_cal_month_table( $p_month,
										  $p_year,
										  $p_filter_type,
										  $p_location,
										  $p_person) {
		
		?>  	<table id="month-table">
					<tr>
						<th style="width:38px;">KW</th>
						<th>Montag</th>
						<th>Dienstag</th>
						<th>Mittwoch</th>
						<th>Donnerstag</th>
						<th>Freitag</th>
						<th>Samstag</th>
						<th>Sonntag</th>
					</tr>
					<?
						if($_SESSION["user_is_organizer"] == 1 || $_SESSION["user_is_admin"] == 1) {
							$show_secret_event = true;
						}else {
							$show_secret_event = false;
						}
						
						$p_month = intval($p_month);
						
						global $cal_db_functions;
						$month_event_data = $cal_db_functions->calendar_retrieve_data->db_get_events_for_month($p_month, $p_year, $p_filter_type, $p_location, $p_person);

						global $cal_rb_functions;
						$frame = $cal_rb_functions->get_month_calendar_frame($p_month, $p_year);
						$row_count = 0;
						
						$day_today = intval(date("d"));
						
						$is_actual_month = ($p_month == intval(date("m")) && $p_year == date("Y"));
						
						while(isset($frame[++$row_count])) {
							$frame_row = $frame[$row_count];

							echo "<tr>";
							for($column = 0; $column <= 7; $column++) {
								if($column == 0) {
									echo "<td class='week-number'>";
									echo $cal_rb_functions->get_week_number($frame_row[1], $p_month, $p_year);
								}else {
									if($frame_row[$column] != "none") {
										
										$day = $frame_row[$column];
										if($is_actual_month && $day == $day_today) {
											echo "<td class='today-cell'>";
										} else {
											echo "<td>";
										}
										
										echo "<div class='day-number'>" . $day . "</div>";
										if(isset($month_event_data[$day])) {
											foreach ($month_event_data[$day] as $event) {
												if($event["event"] != "c" && $event["type"] == 7 && $show_secret_event == false) {
													continue; // do not show secret events
												}
												if($event["overlap"] == "1" && $p_location != -2) {
													$overlap = " event-overlap";
												}else {
													$overlap = "";
												}
												echo "<div class='nothing $overlap'>";
												if($event["event"] == "c") {
													echo "<div class='course-box my-course-" . $event["is_my_course"] . "' my_course= '" . $event["is_my_course"] . "' course_id='" . $event["c_id"] . "'>";
												}else {
													if($event["ti"] == "00:00" && $event["te"] == "00:00") {
														$is_full_day = " full-day"; // add css class
													}else {
														$is_full_day = "";
													}
													echo "<div class='event-box event-box-" . $event["type"] . $is_full_day . "' event_id='" . $event["e_id"] . "'>";
												}
												echo $event["n"] . "<br/>";
												$trainer = "";
												$trainer = trim($event["tr"]);
												if(!empty($trainer)) {
													echo $trainer . "<br/>";
												}
													
												if($event["show_u"] == 1) {
													
														if($event["single"] == 1) {
															echo "einmal<br/>";
														}else {
															echo $event["unit_nr"] . ". Einheit<br/>";
														}													
												}
																							
												
												if($event["ti"] == "00:00" && $event["te"] == "00:00") {
													//echo "ganztägig";
												}else {
													echo $event["ti"] . " - " . $event["te"] . "<br/>" ;													
												}
												if($p_filter_type == 2 || $p_location == -2) {
													echo $event["la"] . "<br/>";
												}
												echo "</div>";
												echo "</div>";
											}
										}
									}else {
										echo "<td>"; // leeres Feld, kein Tag (außerhalb des Monats)
									}
								}
								echo "</td>";
							}
							
							echo "</tr>";
						}
					
					?>
				</table><?
		
		
		
	}


	public function load_five_days_table( $p_start_date,
										  $p_filter_type,
										  $p_location,
										  $p_person) {
										  		
		$start_date = DateTime::createFromFormat ( 'd.m.Y' , $p_start_date);
		$time_stamp = $start_date->getTimeStamp();
		$start_day = date('d', $time_stamp);
		$start_month = date('m', $time_stamp);
		$start_year = date('Y', $time_stamp);
		
		$week_day = date('w', $time_stamp);
		if($week_day == 0) $week_day = 7;
		
		
		$days_row = null;		
		$process_date = clone ($start_date);
		$process_stamp = $process_date->getTimeStamp();
		for ($i = 1; $i <= 5; $i++) {			
			$week_day = date('w', $process_stamp);
			switch ($week_day) {
				case 1:
					$week_day = 'Mo';
					break;
				case 2:
					$week_day = 'Di';
					break;
				case 3:
					$week_day = 'Mi';
					break;
				case 4:
					$week_day = 'Do';
					break;
				case 5:
					$week_day = 'Fr';
					break;
				case 6:
					$week_day = 'Sa';
					break;
				case 0:
					$week_day = 'So';
					break;
			}
			$days_row[$i]['title'] = $week_day . ', ' . date('d.m.Y', $process_stamp);
			$days_row[$i]['day'] = date('d.m.Y', $process_stamp);
			$process_date->add(new DateInterval('P1D'));			
			$process_stamp = $process_date->getTimeStamp();
		}
		
		?>  	<table id="five-days-table">
					<tr><?
					
						foreach($days_row as $d) {
							echo '<th><div style="display: inline-block; min-width:150px;">' . $d['title'] . '</div></th>';
						}
							?>
					</tr>
					
					<?
						if($_SESSION["user_is_organizer"] == 1 || $_SESSION["user_is_admin"] == 1) {
							$show_secret_event = true;
						}else {
							$show_secret_event = false;
						}
												
						global $cal_db_functions;
						
						$five_days_event_data = $cal_db_functions->calendar_retrieve_data->db_get_events_for_five_days($start_date, $p_filter_type, $p_location, $p_person);

						
						//$day_today = intval(date("d"));
												
						

						echo "<tr>";
						for($column = 1; $column <= 5; $column++) {
							
							
							echo '<td>';
							/*if($is_actual_month && $day == $day_today) {
								echo "<td class='today-cell'>";
							} else {
								echo "<td>";
							}*/
							
							if(isset($five_days_event_data[($days_row[$column]['day'])])) {
								foreach ($five_days_event_data[($days_row[$column]['day'])] as $event) {
									if($event["event"] != "c" && $event["type"] == 7 && $show_secret_event == false) {
										continue; // do not show secret events
									}
									if($event["overlap"] == "1" && $p_location != -2) {
										$overlap = " event-overlap";
									}else {
										$overlap = "";
									}
									echo "<div class='nothing $overlap'>";
									if($event["event"] == "c") {
										echo "<div class='course-box my-course-" . $event["is_my_course"] . "' my_course= '" . $event["is_my_course"] . "' course_id='" . $event["c_id"] . "'>";
									}else {
										if($event["ti"] == "00:00" && $event["te"] == "00:00") {
											$is_full_day = " full-day"; // add css class
										}else {
											$is_full_day = "";
										}
										echo "<div class='event-box event-box-" . $event["type"] . $is_full_day . "' event_id='" . $event["e_id"] . "'>";
									}
									echo $event["n"] . "<br/>";
									$trainer = "";
									$trainer = trim($event["tr"]);
									if(!empty($trainer)) {
										echo $trainer . "<br/>";
									}
										
									if($event["show_u"] == 1) {
										
											if($event["single"] == 1) {
												echo "einmal<br/>";
											}else {
												echo $event["unit_nr"] . ". Einheit<br/>";
											}													
									}
																				
									
									if($event["ti"] == "00:00" && $event["te"] == "00:00") {
										//echo "ganztägig";
									}else {
										echo $event["ti"] . " - " . $event["te"] . "<br/>" ;													
									}
									if($p_filter_type == 2 || ($p_location == -2 && ($event["event"] == "c" || $event["type"] != 4))) { // 4 ... Feiertage
										echo $event["la"] . "<br/>";
									}
									echo "</div>";
									echo "</div>";
								}
							}
							
							echo "</td>";
						}
						
						echo "</tr>";
						
					
					?>
				</table><?
		
		
		
	}

  // ############################ EVENTS #############################################
  // #################################################################################


	public function reset_loaded_event_values() {
		
		unset($_SESSION["e_name"]);
		unset($_SESSION["e_type"]);
		unset($_SESSION["e_owner"]);
		unset($_SESSION["e_trainer1"]);
		unset($_SESSION["e_trainer2"]);
		unset($_SESSION["e_location"]);
		unset($_SESSION["e_status"]);
		unset($_SESSION["e_overlap"]);
		unset($_SESSION["e_show_unit"]);
		unset($_SESSION["e_description"]);
		
		unset($_SESSION["e_date1"]);
		unset($_SESSION["e_date2"]);
		unset($_SESSION["e_date3"]);
		unset($_SESSION["e_date4"]);
		unset($_SESSION["e_date5"]);
		unset($_SESSION["e_date6"]);
		unset($_SESSION["e_date7"]);
		unset($_SESSION["e_date8"]);
		unset($_SESSION["e_date9"]);
		unset($_SESSION["e_date10"]);
		unset($_SESSION["e_date11"]);
		unset($_SESSION["e_date12"]);
		
		unset($_SESSION["e_from1"]);
		unset($_SESSION["e_from2"]);
		unset($_SESSION["e_from3"]);
		unset($_SESSION["e_from4"]);
		unset($_SESSION["e_from5"]);
		unset($_SESSION["e_from6"]);
		unset($_SESSION["e_from7"]);
		unset($_SESSION["e_from8"]);
		unset($_SESSION["e_from9"]);
		unset($_SESSION["e_from10"]);
		unset($_SESSION["e_from11"]);
		unset($_SESSION["e_from12"]);
		
		unset($_SESSION["e_to1"]);
		unset($_SESSION["e_to2"]);
		unset($_SESSION["e_to3"]);
		unset($_SESSION["e_to4"]);
		unset($_SESSION["e_to5"]);
		unset($_SESSION["e_to6"]);
		unset($_SESSION["e_to7"]);
		unset($_SESSION["e_to8"]);
		unset($_SESSION["e_to9"]);
		unset($_SESSION["e_to10"]);
		unset($_SESSION["e_to11"]);
		unset($_SESSION["e_to12"]);
		
		unset($_SESSION["e_not_on"]);
	}


	public function insert_new_event() {
	
		global $cal_db_functions;
		return $cal_db_functions->events->db_insert_new_event    ($_SESSION["e_name"],
																  $_SESSION["e_type"],
																  $_SESSION["e_owner"],
																  $_SESSION["e_trainer1"],
																  $_SESSION["e_trainer2"],
																  $_SESSION["e_location"],
																  $_SESSION["e_status"],
																  $_SESSION["e_overlap"],
																  $_SESSION["e_show_unit"],
																  $_SESSION["e_description"],
																  
																  $_SESSION["e_date1"],
																  $_SESSION["e_date2"],
																  $_SESSION["e_date3"],
																  $_SESSION["e_date4"],
																  $_SESSION["e_date5"],
																  $_SESSION["e_date6"],
																  $_SESSION["e_date7"],
																  $_SESSION["e_date8"],
																  $_SESSION["e_date9"],
																  $_SESSION["e_date10"],
																  $_SESSION["e_date11"],
																  $_SESSION["e_date12"],
																  
																  $_SESSION["e_from1"],
																  $_SESSION["e_from2"],
																  $_SESSION["e_from3"],
																  $_SESSION["e_from4"],
																  $_SESSION["e_from5"],
																  $_SESSION["e_from6"],
																  $_SESSION["e_from7"],
																  $_SESSION["e_from8"],
																  $_SESSION["e_from9"],
																  $_SESSION["e_from10"],
																  $_SESSION["e_from11"],
																  $_SESSION["e_from12"],
																  
																  $_SESSION["e_to1"],
																  $_SESSION["e_to2"],
																  $_SESSION["e_to3"],
																  $_SESSION["e_to4"],
																  $_SESSION["e_to5"],
																  $_SESSION["e_to6"],
																  $_SESSION["e_to7"],
																  $_SESSION["e_to8"],
																  $_SESSION["e_to9"],
																  $_SESSION["e_to10"],
																  $_SESSION["e_to11"],
																  $_SESSION["e_to12"],
																  
																  $_SESSION["e_not_on"]);
								  
	}
	
	public function update_event() {
	
		global $cal_db_functions;
		return $cal_db_functions->events->db_update_event    ($_SESSION["event_id"],
														  $_SESSION["e_name"],
														  $_SESSION["e_type"],
														  $_SESSION["e_owner"],
														  $_SESSION["e_trainer1"],
														  $_SESSION["e_trainer2"],
														  $_SESSION["e_location"],
														  $_SESSION["e_status"],
														  $_SESSION["e_overlap"],
														  $_SESSION["e_show_unit"],
														  $_SESSION["e_description"],
														  
														  $_SESSION["e_date1"],
														  $_SESSION["e_date2"],
														  $_SESSION["e_date3"],
														  $_SESSION["e_date4"],
														  $_SESSION["e_date5"],
														  $_SESSION["e_date6"],
														  $_SESSION["e_date7"],
														  $_SESSION["e_date8"],
														  $_SESSION["e_date9"],
														  $_SESSION["e_date10"],
														  $_SESSION["e_date11"],
														  $_SESSION["e_date12"],
														  
														  $_SESSION["e_from1"],
														  $_SESSION["e_from2"],
														  $_SESSION["e_from3"],
														  $_SESSION["e_from4"],
														  $_SESSION["e_from5"],
														  $_SESSION["e_from6"],
														  $_SESSION["e_from7"],
														  $_SESSION["e_from8"],
														  $_SESSION["e_from9"],
														  $_SESSION["e_from10"],
														  $_SESSION["e_from11"],
														  $_SESSION["e_from12"],
														  
														  $_SESSION["e_to1"],
														  $_SESSION["e_to2"],
														  $_SESSION["e_to3"],
														  $_SESSION["e_to4"],
														  $_SESSION["e_to5"],
														  $_SESSION["e_to6"],
														  $_SESSION["e_to7"],
														  $_SESSION["e_to8"],
														  $_SESSION["e_to9"],
														  $_SESSION["e_to10"],
														  $_SESSION["e_to11"],
														  $_SESSION["e_to12"],
														  
														  $_SESSION["e_not_on"]);
							  
	}

	
  // #################################################################################

	public function ignore_android($p_text) {
		global $is_android_used;
		if($is_android_used) {
			return "";
		}else {
			return $p_text;
		}
	}

}



$cal_rb_functions = new RB_Functions();

?>