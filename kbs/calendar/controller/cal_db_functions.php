<?php
/* Coypright(2015) by Ing. Roman Breitschopf, BA 									*/
/* office@breitschopf.wien			www.breitschopf.wien 							*/
/* License for Jasmin Liska. Do not distribute to others without permission. */
defined("main-call") or die();

class DB_Connect {
	
	public function db_connect(){
		global $rb_configuration;
		$mysqli = new mysqli($rb_configuration->db_url, $rb_configuration->db_user, $rb_configuration->db_pwd, $rb_configuration->db_name);
		if ($mysqli->connect_error) {
			echo "E-1005: Fehler bei der Verbindung zur Datenbank.";
			exit();
		}
		if (!$mysqli->set_charset("utf8")) {
			echo "E-1006: Datenbankfehler - Fehler beim Laden von UTF8.";
			exit();
		}
		return $mysqli;
	}

	public function db_close() {
		if (isset($mysqli)) $mysqli->close();
	}
}


class DB_Functions extends DB_Connect{
	
	public $calendar_retrieve_data = null;
	public $calendar_options = null;
	public $events = null;
	
	public function __construct() {
		$this->calendar_retrieve_data = new DB_Functions_Calendar_Retrieve_Data;
		$this->calendar_options = new DB_Functions_Calender_Options();
		$this->events = new DB_Functions_Calender_Events();
	}
	

	public function db_check_login($login, $pw){
		$db=$this->db_connect();
		$login = $db->real_escape_string($login);
		$result = $db->query("SELECT login_name,
									e_mail,
									user_id,
									is_trainer,
									is_organizer,
									is_admin,
									prename,
									surname
									from as_users
									where (login_name = '" . $login ."'
											or e_mail = '" . $login ."')
									  and is_enabled = true
									  and password = '" . md5($pw."b_14") ."';");
		
		if ($result && $line=$result->fetch_array()) {
			$_SESSION["user_name"] = $line["login_name"];
			$_SESSION["user_mail"] = $line["e_mail"];
			$_SESSION["user_id"] = $line["user_id"];
			$_SESSION["user_is_trainer"] = $line["is_trainer"];
			$_SESSION["user_is_organizer"] = $line["is_organizer"];
			$_SESSION["user_is_admin"] = $line["is_admin"];
			$_SESSION["user_prename"] = $line["prename"];
			$_SESSION["user_surname"] = $line["surname"];
			if($_SESSION["user_is_organizer"] == "1" || $_SESSION["user_is_admin"] == "1") {
				$_SESSION["view_mode"] = "full";
			}else {
				$_SESSION["view_mode"] = "simple";
			}	
			$_SESSION["login"] = "ok";
			if (!isset($_SESSION["view"])) $_SESSION["view"] = "main_menu";
			if (!isset($_SESSION["last_view"])) $_SESSION["last_view"] = "main_menu";
		}else{
			$_SESSION["login"] = "failed";
		}
		$db->close();		
	}
}

class DB_Functions_Calender_Options extends DB_Connect {


	public function db_get_user_select_options($p_selected, $p_option_all, $p_initial_blank, $p_option_none = false, $p_none_value = 'none', $p_trainer_only=0){

		if(empty($p_trainer_only)) {
			$p_trainer_only = 0;
		}else {
			$p_trainer_only = 1;
			
		}
		if(empty($p_option_none)) $p_option_none = 0;
		$db=$this->db_connect();
		echo "SELECT 
					user_id as trainer_id,
					Concat
					(
						prename,
						' ',
						surname
					) as trainer_name
			   FROM as_users
			  WHERE ($p_trainer_only != 1
			        OR (is_trainer = 1
			          AND is_enabled = 1))
					AND (user_id != -1
							OR
						$p_option_none = 1);";
		$result = $db->query(
			"SELECT 
					user_id as trainer_id,
					Concat
					(
						prename,
						' ',
						surname
					) as trainer_name
			   FROM as_users
			  WHERE ($p_trainer_only != 1
			        OR (is_trainer = 1
			          AND is_enabled = 1))
					AND (user_id != -1
							OR
						$p_option_none = 1);");
					  
		if(!$result) echo $db->error;	
		
		
		if ($p_initial_blank && empty($p_selected) && !$p_option_none) {
			echo "
				<option style='display:none' value disabled selected></option>";
		}
		if ($p_option_all) {
			echo "
				<option value='all'" . (($p_selected=="all")? " selected":"") . ">alle</option>";
		}
		while($line = $result->fetch_array()) {
			echo "<option value='" . htmlspecialchars($line["trainer_id"])   . "'" .
								(($p_selected==$line["trainer_id"])? " selected":"") .
							  ">" . htmlspecialchars($line["trainer_name"]) . "</option>";
		}
		$db->close();

	}

	public function db_get_location_select_options($p_selected, $p_option_all){

	
		$db=$this->db_connect();
		$result = $db->query(
		
			"SELECT 
					location_id,
					short_name as location_name
			   FROM as_locations
			  WHERE is_enabled=1
			  ORDER BY sort_no, location_name");
		if(!$result) echo $db->error;	
		

		
		while($line = $result->fetch_array()) {
			echo "<option value='" . htmlspecialchars($line["location_id"])   . "'" .
								(($p_selected==$line["location_id"])? " selected":"") .
							  ">" . htmlspecialchars($line["location_name"]) . "</option>";
		}
		if ($p_option_all) {
			echo "
				  <option value='-2'" . (($p_selected==-2)? " selected":"") . ">alle (Kollisionen ausgeblendet)</option>";
		}
		$db->close();

	}
}

class DB_Functions_Calendar_Retrieve_Data extends DB_Connect {
		
	public function db_get_events_for_month($p_month,
											$p_year,
											$p_filter_type,
											$p_location,
											$p_person) {
										   	
		$p_month = intval($p_month);
		$p_year = intval($p_year);
		$last_day_of_month = intval(cal_days_in_month(CAL_GREGORIAN, $p_month, $p_year));
		
		if(empty($p_person)) $p_person = -2;
		
		if(!($_SESSION["user_is_organizer"] == 1 || $_SESSION["user_is_admin"] == 1)) {
			$p_person = $_SESSION["user_id"];
		}
		
		$db=$this->db_connect();
		
		$result = $db->query("
		SELECT course_id,
			   name,
			   l.location_name as location,
			   if(c.date1 IS NULL, '', date_format(c.date1, '%d.%m.%Y %H:%i')) as date1,
			   if(c.date2 IS NULL, '', date_format(c.date2, '%d.%m.%Y %H:%i')) as date2,
			   if(c.date3 IS NULL, '', date_format(c.date3, '%d.%m.%Y %H:%i')) as date3,
			   if(c.date4 IS NULL, '', date_format(c.date4, '%d.%m.%Y %H:%i')) as date4,
			   if(c.date5 IS NULL, '', date_format(c.date5, '%d.%m.%Y %H:%i')) as date5,
			   if(c.date6 IS NULL, '', date_format(c.date6, '%d.%m.%Y %H:%i')) as date6,
			   if(c.date7 IS NULL, '', date_format(c.date7, '%d.%m.%Y %H:%i')) as date7,
			   if(c.date8 IS NULL, '', date_format(c.date8, '%d.%m.%Y %H:%i')) as date8,
			   if(c.date9 IS NULL, '', date_format(c.date9, '%d.%m.%Y %H:%i')) as date9,
			   if(c.date10 IS NULL, '', date_format(c.date10, '%d.%m.%Y %H:%i')) as date10,
			   if(c.date11 IS NULL, '', date_format(c.date11, '%d.%m.%Y %H:%i')) as date11,
			   if(c.date12 IS NULL, '', date_format(c.date12, '%d.%m.%Y %H:%i')) as date12,
			   date_format(c.date1, '%H:%i') as time1,
			   date_format(c.date2, '%H:%i') as time2,
			   date_format(c.date3, '%H:%i') as time3,
			   date_format(c.date4, '%H:%i') as time4,
			   date_format(c.date5, '%H:%i') as time5,
			   date_format(c.date6, '%H:%i') as time6,
			   date_format(c.date7, '%H:%i') as time7,
			   date_format(c.date8, '%H:%i') as time8,
			   date_format(c.date9, '%H:%i') as time9,
			   date_format(c.date10, '%H:%i') as time10,
			   date_format(c.date11, '%H:%i') as time11,
			   date_format(c.date12, '%H:%i') as time12,
			   date_format(c.date1_end, '%H:%i') as time_end1,
			   date_format(c.date2_end, '%H:%i') as time_end2,
			   date_format(c.date3_end, '%H:%i') as time_end3,
			   date_format(c.date4_end, '%H:%i') as time_end4,
			   date_format(c.date5_end, '%H:%i') as time_end5,
			   date_format(c.date6_end, '%H:%i') as time_end6,
			   date_format(c.date7_end, '%H:%i') as time_end7,
			   date_format(c.date8_end, '%H:%i') as time_end8,
			   date_format(c.date9_end, '%H:%i') as time_end9,
			   date_format(c.date10_end, '%H:%i') as time_end10,
			   date_format(c.date11_end, '%H:%i') as time_end11,
			   date_format(c.date12_end, '%H:%i') as time_end12,
			   date_format(c.date1, '%H:%i') as time,
			   date_format(DATE_ADD(c.date1, INTERVAL c.duration MINUTE), '%H:%i') as time_end,
			   duration,
			   Concat
					(
					u.prename,
					if(u2.prename IS NULL, '', Concat(' & ', u2.prename))
					) as trainer,
			   status,
			   trainer_id,
			   trainer_id2,
			   l.abbrevation_name as location_abbrevation,
			   if(c.date2 IS NULL, 1, 0) as is_single_date_event,
			   IFNULL(ca.show_unit_in_calendar, 1) as show_unit
		FROM as_courses c
		INNER JOIN as_users u		on c.trainer_id = u.user_id
		inner join as_locations     l			on c.location_id = l.location_id
		left join as_categories ca on c.cat_id = ca.cat_id
		left  join as_users u2		on c.trainer_id2 = u2.user_id
		WHERE (
				(STR_TO_DATE('01.$p_month.$p_year', '%d.%m.%Y') >= begin
					AND STR_TO_DATE('01.$p_month.$p_year', '%d.%m.%Y') <= end)
		      OR
			 	(STR_TO_DATE('$last_day_of_month.$p_month.$p_year', '%d.%m.%Y') + INTERVAL 1 DAY  >= begin
					AND STR_TO_DATE('$last_day_of_month.$p_month.$p_year', '%d.%m.%Y') <= end)
		      OR
			 	(STR_TO_DATE('01.$p_month.$p_year', '%d.%m.%Y') <= begin
					AND (STR_TO_DATE('$last_day_of_month.$p_month.$p_year', '%d.%m.%Y') + INTERVAL 1 DAY) >= end)
			  )
			  AND (	($p_filter_type = 1 AND ($p_location = -2 OR c.location_id = $p_location))
			  			OR
			  		($p_filter_type = 2 AND (c.trainer_id = $p_person OR c.trainer_id2 = $p_person))
			  	  )
			  				
			  AND c.status != 0;");
		
		if(!$result) echo $db->error;
		
		unset($event_array);
		
		while($line = $result->fetch_array()) {
			for($i = 1; $i <= 12; $i++) {
				if(empty($line["date$i"])){
					break;
				}else {
					
					$processing_month = intval(DateTime::createFromFormat('d.m.Y H:i', $line["date$i"])->format('m'));
					$processing_year  = intval(DateTime::createFromFormat('d.m.Y H:i', $line["date$i"])->format('Y'));
					$processing_day   = intval(DateTime::createFromFormat('d.m.Y H:i', $line["date$i"])->format('d'));
					
					if($processing_month == $p_month && $processing_year == $p_year) {
						
						if($line["trainer_id"] == $_SESSION["user_id"] || $line["trainer_id2"] == $_SESSION["user_id"]) {
							$is_my_course = "y";
						}else {
							$is_my_course = "n";
						}
						$event_array[$processing_day][] = array('event' 		=> "c",
																'unit_nr'		=> $i,
																'show_u'		=> $line["show_unit"],
																'single'		=> $line["is_single_date_event"],
																'c_id'			=> $line["course_id"],
																'n'				=> $line["name"],
																'tr'			=> $line["trainer"],
																'l'				=> $line["location"],
																'la'			=> $line["location_abbrevation"],
																'ti'			=> $line["time$i"],
																'te'			=> $line["time_end$i"],
																's'				=> $line["status"],
																'overlap'		=> 0,
																'is_my_course'	=> $is_my_course);
					}					
				}
			}
		}


		$result = $db->query("
		SELECT e.event_id,
			   e.name,
			   e.event_type,
			   l.location_name as location,
			   if(e.date1_begin IS NULL, '', date_format(e.date1_begin, '%d.%m.%Y %H:%i')) as date1,
			   if(e.date2_begin IS NULL, '', date_format(e.date2_begin, '%d.%m.%Y %H:%i')) as date2,
			   if(e.date3_begin IS NULL, '', date_format(e.date3_begin, '%d.%m.%Y %H:%i')) as date3,
			   if(e.date4_begin IS NULL, '', date_format(e.date4_begin, '%d.%m.%Y %H:%i')) as date4,
			   if(e.date5_begin IS NULL, '', date_format(e.date5_begin, '%d.%m.%Y %H:%i')) as date5,
			   if(e.date6_begin IS NULL, '', date_format(e.date6_begin, '%d.%m.%Y %H:%i')) as date6,
			   if(e.date7_begin IS NULL, '', date_format(e.date7_begin, '%d.%m.%Y %H:%i')) as date7,
			   if(e.date8_begin IS NULL, '', date_format(e.date8_begin, '%d.%m.%Y %H:%i')) as date8,
			   if(e.date9_begin IS NULL, '', date_format(e.date9_begin, '%d.%m.%Y %H:%i')) as date9,
			   if(e.date10_begin IS NULL, '', date_format(e.date10_begin, '%d.%m.%Y %H:%i')) as date10,
			   if(e.date11_begin IS NULL, '', date_format(e.date11_begin, '%d.%m.%Y %H:%i')) as date11,
			   if(e.date12_begin IS NULL, '', date_format(e.date12_begin, '%d.%m.%Y %H:%i')) as date12,
			   date_format(e.date1_begin, '%H:%i') as time1,
			   date_format(e.date2_begin, '%H:%i') as time2,
			   date_format(e.date3_begin, '%H:%i') as time3,
			   date_format(e.date4_begin, '%H:%i') as time4,
			   date_format(e.date5_begin, '%H:%i') as time5,
			   date_format(e.date6_begin, '%H:%i') as time6,
			   date_format(e.date7_begin, '%H:%i') as time7,
			   date_format(e.date8_begin, '%H:%i') as time8,
			   date_format(e.date9_begin, '%H:%i') as time9,
			   date_format(e.date10_begin, '%H:%i') as time10,
			   date_format(e.date11_begin, '%H:%i') as time11,
			   date_format(e.date12_begin, '%H:%i') as time12,
			   date_format(e.date1_end, '%H:%i') as time_end1,
			   date_format(e.date2_end, '%H:%i') as time_end2,
			   date_format(e.date3_end, '%H:%i') as time_end3,
			   date_format(e.date4_end, '%H:%i') as time_end4,
			   date_format(e.date5_end, '%H:%i') as time_end5,
			   date_format(e.date6_end, '%H:%i') as time_end6,
			   date_format(e.date7_end, '%H:%i') as time_end7,
			   date_format(e.date8_end, '%H:%i') as time_end8,
			   date_format(e.date9_end, '%H:%i') as time_end9,
			   date_format(e.date10_end, '%H:%i') as time_end10,
			   date_format(e.date11_end, '%H:%i') as time_end11,
			   date_format(e.date12_end, '%H:%i') as time_end12,
			   u.prename as owner,
			   Concat
					(
					if(u2.prename IS NULL, '', u2.prename),
					if(u3.prename IS NULL, '', Concat(' & ', u3.prename))
					) as trainer,
			   status,
			   e.status,
			   l.abbrevation_name as location_abbrevation,
			   e.show_overlap,			   
			   if(e.date2_begin IS NULL, 1, 0) as is_single_date_event,
			   e.show_unit
		FROM as_events e
		INNER JOIN as_users u		on e.owner_id = u.user_id
		inner join as_locations     l			on e.location_id = l.location_id
		left  join as_users u2		on e.trainer_id = u2.user_id
		left  join as_users u3		on e.trainer_id2 = u3.user_id 
		WHERE (
				(STR_TO_DATE('01.$p_month.$p_year', '%d.%m.%Y') >= begin
					AND STR_TO_DATE('01.$p_month.$p_year', '%d.%m.%Y') <= end)
		      OR
			 	(STR_TO_DATE('$last_day_of_month.$p_month.$p_year', '%d.%m.%Y') + INTERVAL 1 DAY >= begin
					AND STR_TO_DATE('$last_day_of_month.$p_month.$p_year', '%d.%m.%Y') <= end)
		      OR
			 	(STR_TO_DATE('01.$p_month.$p_year', '%d.%m.%Y') <= begin
					AND STR_TO_DATE('$last_day_of_month.$p_month.$p_year', '%d.%m.%Y') + INTERVAL 1 DAY >= end)
			  )
			  AND  (	($p_filter_type = 1 AND ($p_location = -2 OR e.location_id = $p_location))
			  			OR
			  		($p_filter_type = 2 AND (e.trainer_id = $p_person OR e.trainer_id2 = $p_person))
			  	   )
			  AND e.status != 0;");
		
		if(!$result) echo $db->error;
		
		while($line = $result->fetch_array()) {
			for($i = 1; $i <= 12; $i++) {
				if(empty($line["date$i"])){
					break;
				}else {
					
					$processing_month = intval(DateTime::createFromFormat('d.m.Y H:i', $line["date$i"])->format('m'));
					$processing_year  = intval(DateTime::createFromFormat('d.m.Y H:i', $line["date$i"])->format('Y'));
					$processing_day   = intval(DateTime::createFromFormat('d.m.Y H:i', $line["date$i"])->format('d'));
					
					if($processing_month == $p_month && $processing_year == $p_year) {
						$event_array[$processing_day][] = array('event' 	=> "e",
																'unit_nr'	=> $i,
																'show_u'	=> $line["show_unit"],
																'single'	=> $line["is_single_date_event"],
																'e_id'		=> $line["event_id"],
																'n'			=> $line["name"],
																'type'		=> $line["event_type"],
																'tr'		=> $line["trainer"],
																'l'			=> $line["location"],
																'la'		=> $line["location_abbrevation"],
																'ti'		=> $line["time$i"],
																'te'		=> $line["time_end$i"],
																's'			=> $line["status"],
																'overlap'	=> 0,
																'show_overlap'	=> $line["show_overlap"]);
					}					
				}
			}
		}



		if(!isset($event_array)) {
			return null;
		}		
		// Sortierung:
		
		if (!function_exists('compare_for_sorting')){
			function compare_for_sorting($a, $b) {
				if($a["ti"] == $b["ti"]) {
					return 0;
				}elseif ($a["ti"] < $b["ti"]) {
					return -1;
				}else {
					return 1;
				}
			}
		}
		
		for($day=1; $day <= $last_day_of_month; $day++) {
			if(isset($event_array[$day])) {
				usort($event_array[$day], "compare_for_sorting");
			}
		}
		
		// Check Overlapping:
		
		foreach($event_array as $day_key => $event_days) {
			$begin = DateTime::createFromFormat('d.m.Y H:i', '01.01.2015 00:00');
			$end   = DateTime::createFromFormat('d.m.Y H:i', '01.01.2015 00:00');
			$last_event_key = null;
			foreach($event_days as $event_key => $event){
				if($event["event"] == "e" && $event["show_overlap"] == false) {
					continue;  // ignore this event for overlap collision detection
				}
				$last_begin = $begin;
				$last_end = $end;
				$begin = DateTime::createFromFormat('d.m.Y H:i', '01.01.2015 ' . $event["ti"]);
				$end   = DateTime::createFromFormat('d.m.Y H:i', '01.01.2015 ' . $event["te"]);
				
				if($begin < $last_end) {
					$event_array[$day_key][$event_key]['overlap'] = 1;
					if(isset($last_event_key)) {
						$event_array[$day_key][$last_event_key]['overlap'] = 1;
					}
				} 
				$last_event_key = $event_key;
			}
		}
		
		
		return $event_array;					   	
	}
	
	
	public function db_get_events_for_five_days($p_start_date,
												$p_filter_type,
												$p_location,
												$p_person) {
		$end_date = clone($p_start_date);
		$day2 = clone($p_start_date);
		$day2 = $day2->add(new DateInterval('P1D'));
		$day3 = clone($p_start_date);
		$day3 = $day3->add(new DateInterval('P2D'));
		$day4 = clone($p_start_date);
		$day4 = $day4->add(new DateInterval('P3D'));
		$day5 = clone($p_start_date);
		$day5 = $day5->add(new DateInterval('P4D'));
		
		$end_date->add(new DateInterval('P5D')); // 5. day and 24 hours
		
		$start = date('d.m.Y', ($p_start_date->getTimeStamp()));
		$end = date('d.m.Y', ($end_date->getTimeStamp()));
		
		$requested_days = array($start,
								$day2->format('d.m.Y'),
								$day3->format('d.m.Y'),
								$day4->format('d.m.Y'),
								$day5->format('d.m.Y'));
		
		if(empty($p_person)) $p_person = -2;
	
		if(!($_SESSION["user_is_organizer"] == 1 || $_SESSION["user_is_admin"] == 1)) {
			$p_person = $_SESSION["user_id"];
		}
		
		$db=$this->db_connect();
		
		$result = $db->query("
		SELECT course_id,
			   name,
			   l.location_name as location,
			   if(c.date1 IS NULL, '', date_format(c.date1, '%d.%m.%Y')) as date1,
			   if(c.date2 IS NULL, '', date_format(c.date2, '%d.%m.%Y')) as date2,
			   if(c.date3 IS NULL, '', date_format(c.date3, '%d.%m.%Y')) as date3,
			   if(c.date4 IS NULL, '', date_format(c.date4, '%d.%m.%Y')) as date4,
			   if(c.date5 IS NULL, '', date_format(c.date5, '%d.%m.%Y')) as date5,
			   if(c.date6 IS NULL, '', date_format(c.date6, '%d.%m.%Y')) as date6,
			   if(c.date7 IS NULL, '', date_format(c.date7, '%d.%m.%Y')) as date7,
			   if(c.date8 IS NULL, '', date_format(c.date8, '%d.%m.%Y')) as date8,
			   if(c.date9 IS NULL, '', date_format(c.date9, '%d.%m.%Y')) as date9,
			   if(c.date10 IS NULL, '', date_format(c.date10, '%d.%m.%Y')) as date10,
			   if(c.date11 IS NULL, '', date_format(c.date11, '%d.%m.%Y')) as date11,
			   if(c.date12 IS NULL, '', date_format(c.date12, '%d.%m.%Y')) as date12,
			   date_format(c.date1, '%H:%i') as time1,
			   date_format(c.date2, '%H:%i') as time2,
			   date_format(c.date3, '%H:%i') as time3,
			   date_format(c.date4, '%H:%i') as time4,
			   date_format(c.date5, '%H:%i') as time5,
			   date_format(c.date6, '%H:%i') as time6,
			   date_format(c.date7, '%H:%i') as time7,
			   date_format(c.date8, '%H:%i') as time8,
			   date_format(c.date9, '%H:%i') as time9,
			   date_format(c.date10, '%H:%i') as time10,
			   date_format(c.date11, '%H:%i') as time11,
			   date_format(c.date12, '%H:%i') as time12,
			   date_format(c.date1_end, '%H:%i') as time_end1,
			   date_format(c.date2_end, '%H:%i') as time_end2,
			   date_format(c.date3_end, '%H:%i') as time_end3,
			   date_format(c.date4_end, '%H:%i') as time_end4,
			   date_format(c.date5_end, '%H:%i') as time_end5,
			   date_format(c.date6_end, '%H:%i') as time_end6,
			   date_format(c.date7_end, '%H:%i') as time_end7,
			   date_format(c.date8_end, '%H:%i') as time_end8,
			   date_format(c.date9_end, '%H:%i') as time_end9,
			   date_format(c.date10_end, '%H:%i') as time_end10,
			   date_format(c.date11_end, '%H:%i') as time_end11,
			   date_format(c.date12_end, '%H:%i') as time_end12,
			   date_format(c.date1, '%H:%i') as time,
			   date_format(DATE_ADD(c.date1, INTERVAL c.duration MINUTE), '%H:%i') as time_end,
			   duration,
			   Concat
					(
					u.prename,
					if(u2.prename IS NULL, '', Concat(' & ', u2.prename))
					) as trainer,
			   status,
			   trainer_id,
			   trainer_id2,
			   l.abbrevation_name as location_abbrevation,
			   if(c.date2 IS NULL, 1, 0) as is_single_date_event,
			   IFNULL(ca.show_unit_in_calendar, 1) as show_unit
		FROM as_courses c
		INNER JOIN as_users u		on c.trainer_id = u.user_id
		inner join as_locations     l			on c.location_id = l.location_id
		left join as_categories ca on c.cat_id = ca.cat_id
		left  join as_users u2		on c.trainer_id2 = u2.user_id
		WHERE (
				(STR_TO_DATE('$start', '%d.%m.%Y') >= begin
					AND STR_TO_DATE('$start', '%d.%m.%Y') <= end)
		      OR
			 	(STR_TO_DATE('$end', '%d.%m.%Y') + INTERVAL 1 DAY  >= begin
					AND STR_TO_DATE('$end', '%d.%m.%Y') <= end)
		      OR
			 	(STR_TO_DATE('$start', '%d.%m.%Y') <= begin
					AND (STR_TO_DATE('$end', '%d.%m.%Y') + INTERVAL 1 DAY) >= end)
			  )
			  AND (	($p_filter_type = 1 AND ($p_location = -2 OR c.location_id = $p_location))
			  			OR
			  		($p_filter_type = 2 AND (c.trainer_id = $p_person OR c.trainer_id2 = $p_person))
			  	  )
			  				
			  AND c.status != 0;");
		
		if(!$result) echo $db->error;
		
		unset($event_array);
		
		
		while($line = $result->fetch_array()) {
			for($i = 1; $i <= 12; $i++) {
				if(empty($line["date$i"])){
					break;
				}else {
					
					if(in_array($line["date$i"], $requested_days)) {
						
						if($line["trainer_id"] == $_SESSION["user_id"] || $line["trainer_id2"] == $_SESSION["user_id"]) {
							$is_my_course = "y";
						}else {
							$is_my_course = "n";
						}
						$event_array[$line["date$i"]][] = array('event' 		=> "c",
																'unit_nr'		=> $i,
																'show_u'		=> $line["show_unit"],
																'single'		=> $line["is_single_date_event"],
																'c_id'			=> $line["course_id"],
																'n'				=> $line["name"],
																'tr'			=> $line["trainer"],
																'l'				=> $line["location"],
																'la'			=> $line["location_abbrevation"],
																'ti'			=> $line["time$i"],
																'te'			=> $line["time_end$i"],
																's'				=> $line["status"],
																'overlap'		=> 0,
																'is_my_course'	=> $is_my_course);
					}					
				}
			}
		}


		$result = $db->query("
		SELECT e.event_id,
			   e.name,
			   e.event_type,
			   l.location_name as location,
			   if(e.date1_begin IS NULL, '', date_format(e.date1_begin, '%d.%m.%Y')) as date1,
			   if(e.date2_begin IS NULL, '', date_format(e.date2_begin, '%d.%m.%Y')) as date2,
			   if(e.date3_begin IS NULL, '', date_format(e.date3_begin, '%d.%m.%Y')) as date3,
			   if(e.date4_begin IS NULL, '', date_format(e.date4_begin, '%d.%m.%Y')) as date4,
			   if(e.date5_begin IS NULL, '', date_format(e.date5_begin, '%d.%m.%Y')) as date5,
			   if(e.date6_begin IS NULL, '', date_format(e.date6_begin, '%d.%m.%Y')) as date6,
			   if(e.date7_begin IS NULL, '', date_format(e.date7_begin, '%d.%m.%Y')) as date7,
			   if(e.date8_begin IS NULL, '', date_format(e.date8_begin, '%d.%m.%Y')) as date8,
			   if(e.date9_begin IS NULL, '', date_format(e.date9_begin, '%d.%m.%Y')) as date9,
			   if(e.date10_begin IS NULL, '', date_format(e.date10_begin, '%d.%m.%Y')) as date10,
			   if(e.date11_begin IS NULL, '', date_format(e.date11_begin, '%d.%m.%Y')) as date11,
			   if(e.date12_begin IS NULL, '', date_format(e.date12_begin, '%d.%m.%Y')) as date12,
			   date_format(e.date1_begin, '%H:%i') as time1,
			   date_format(e.date2_begin, '%H:%i') as time2,
			   date_format(e.date3_begin, '%H:%i') as time3,
			   date_format(e.date4_begin, '%H:%i') as time4,
			   date_format(e.date5_begin, '%H:%i') as time5,
			   date_format(e.date6_begin, '%H:%i') as time6,
			   date_format(e.date7_begin, '%H:%i') as time7,
			   date_format(e.date8_begin, '%H:%i') as time8,
			   date_format(e.date9_begin, '%H:%i') as time9,
			   date_format(e.date10_begin, '%H:%i') as time10,
			   date_format(e.date11_begin, '%H:%i') as time11,
			   date_format(e.date12_begin, '%H:%i') as time12,
			   date_format(e.date1_end, '%H:%i') as time_end1,
			   date_format(e.date2_end, '%H:%i') as time_end2,
			   date_format(e.date3_end, '%H:%i') as time_end3,
			   date_format(e.date4_end, '%H:%i') as time_end4,
			   date_format(e.date5_end, '%H:%i') as time_end5,
			   date_format(e.date6_end, '%H:%i') as time_end6,
			   date_format(e.date7_end, '%H:%i') as time_end7,
			   date_format(e.date8_end, '%H:%i') as time_end8,
			   date_format(e.date9_end, '%H:%i') as time_end9,
			   date_format(e.date10_end, '%H:%i') as time_end10,
			   date_format(e.date11_end, '%H:%i') as time_end11,
			   date_format(e.date12_end, '%H:%i') as time_end12,
			   u.prename as owner,
			   Concat
					(
					if(u2.prename IS NULL, '', u2.prename),
					if(u3.prename IS NULL, '', Concat(' & ', u3.prename))
					) as trainer,
			   status,
			   e.status,
			   l.abbrevation_name as location_abbrevation,
			   e.show_overlap,			   
			   if(e.date2_begin IS NULL, 1, 0) as is_single_date_event,
			   e.show_unit
		FROM as_events e
		INNER JOIN as_users u		on e.owner_id = u.user_id
		inner join as_locations     l			on e.location_id = l.location_id
		left  join as_users u2		on e.trainer_id = u2.user_id
		left  join as_users u3		on e.trainer_id2 = u3.user_id 
		WHERE (
				(STR_TO_DATE('$start', '%d.%m.%Y') >= begin
					AND STR_TO_DATE('$start', '%d.%m.%Y') <= end)
		      OR
			 	(STR_TO_DATE('$end', '%d.%m.%Y') + INTERVAL 1 DAY >= begin
					AND STR_TO_DATE('$end', '%d.%m.%Y') <= end)
		      OR
			 	(STR_TO_DATE('$start', '%d.%m.%Y') <= begin
					AND STR_TO_DATE('$end', '%d.%m.%Y') + INTERVAL 1 DAY >= end)
			  )
			  AND  (	($p_filter_type = 1 AND ($p_location = -2 || e.location_id = $p_location))
			  			OR
			  		($p_filter_type = 2 AND (e.trainer_id = $p_person OR e.trainer_id2 = $p_person))
			  	   )
			  AND e.status != 0;");
		
		if(!$result) echo $db->error;
		
		while($line = $result->fetch_array()) {
			for($i = 1; $i <= 12; $i++) {
				if(empty($line["date$i"])){
					break;
				}else {
						
					if(in_array($line["date$i"], $requested_days)) {
						
						$event_array[$line["date$i"]][] = array('event' 	=> "e",
																'unit_nr'	=> $i,
																'show_u'	=> $line["show_unit"],
																'single'	=> $line["is_single_date_event"],
																'e_id'		=> $line["event_id"],
																'n'			=> $line["name"],
																'type'		=> $line["event_type"],
																'tr'		=> $line["trainer"],
																'l'			=> $line["location"],
																'la'		=> $line["location_abbrevation"],
																'ti'		=> $line["time$i"],
																'te'		=> $line["time_end$i"],
																's'			=> $line["status"],
																'overlap'	=> 0,
																'show_overlap'	=> $line["show_overlap"]);
					}					
				}
			}
		}
    

		if(!isset($event_array)) {
			return null;
		}		
		// Sortierung:
		
		if (!function_exists('compare_for_sorting')){
			function compare_for_sorting($a, $b) {
				if($a["ti"] == $b["ti"]) {
					return 0;
				}elseif ($a["ti"] < $b["ti"]) {
					return -1;
				}else {
					return 1;
				}
			}
		}
		
		foreach($requested_days as $d) {
			if(isset($event_array[$d])) {
				usort($event_array[$d], "compare_for_sorting");
			}
		}
		
		//Check Overlapping:
		
		foreach($event_array as $day_key => $event_days) {
			$begin = DateTime::createFromFormat('d.m.Y H:i', '01.01.2015 00:00');
			$end   = DateTime::createFromFormat('d.m.Y H:i', '01.01.2015 00:00');
			$last_event_key = null;
			foreach($event_days as $event_key => $event){
				if($event["event"] == "e" && $event["show_overlap"] == false) {
					continue;  // ignore this event for overlap collision detection
				}
				$last_begin = $begin;
				$last_end = $end;
				$begin = DateTime::createFromFormat('d.m.Y H:i', '01.01.2015 ' . $event["ti"]);
				$end   = DateTime::createFromFormat('d.m.Y H:i', '01.01.2015 ' . $event["te"]);
				
				if($begin < $last_end) {
					$event_array[$day_key][$event_key]['overlap'] = 1;
					if(isset($last_event_key)) {
						$event_array[$day_key][$last_event_key]['overlap'] = 1;
					}
				} 
				$last_event_key = $event_key;
			}
		}
		
		
		return $event_array;					   	
	}
	


	public function db_load_course_details($p_id){
	

			$db=$this->db_connect();
			$result = $db->query(
			
				"SELECT 
						c.course_id,
						c.status,
						c.name as kursname,
						Concat
							(
							u.prename,
							if(u2.prename IS NULL, '', Concat(', ', u2.prename))
							)
						as trainer,
						l.location_name as ort,
						Concat
							(
								(CASE date_format(c.date1, '%w')
									WHEN 1 THEN 'Mo'
									WHEN 2 THEN 'Di'
									WHEN 3 THEN 'Mi'
									WHEN 4 THEN 'Do'
									WHEN 5 THEN 'Fr'
									WHEN 6 THEN 'Sa'
									WHEN 0 THEN 'So'
								END),
								', ',
								date_format(c.date1, '%d.%m.%Y')
							)	as begin1,
						Concat
							(
								date_format(c.date1, '%H:%i'),
								' - ',
								date_format(DATE_ADD(c.date1, INTERVAL c.duration MINUTE), '%H:%i')
								
							)	as time,
						Concat
							(
								date_format(c.date1, '%d.%m'),
								if(c.date2 IS NULL, '', date_format(c.date2, ', %d.%m')),
								if(c.date3 IS NULL, '', date_format(c.date3, ', %d.%m')),
								if(c.date4 IS NULL, '', date_format(c.date4, ', %d.%m')),
								if(c.date5 IS NULL, '', date_format(c.date5, ', %d.%m')),
								if(c.date6 IS NULL, '', date_format(c.date6, ', %d.%m')),
								if(c.date7 IS NULL, '', date_format(c.date7, ', %d.%m')),
								if(c.date8 IS NULL, '', date_format(c.date8, ', %d.%m.')),
								if(c.date9 IS NULL, '', date_format(c.date9, ', %d.%m.')),
								if(c.date10 IS NULL, '', date_format(c.date10, ', %d.%m.')),
								if(c.not_on IS NULL, '', Concat(' (entfällt am ', c.not_on, ')'))
							
							) as termine,
						Concat
							(
								c.registration_count,
								' / ',
								c.max_count						
							) as anmeldungen0,
						c.pre_reg_count as voranmeldungen,
						c.registration_count as anmeldungen,
						c.max_count	as kursplatzanzahl,
						replace(format(c.price, 2), '.00', '' ) as price,
						public_remark,
						private_remark,
						c.begin,
						c.end,
						c.registration_code,
						if(c.date1 IS NULL, NULL, date_format(c.date1, '%d.%m.')) as date1,
						if(c.date2 IS NULL, NULL, date_format(c.date2, '%d.%m.')) as date2,
						if(c.date3 IS NULL, NULL, date_format(c.date3, '%d.%m.')) as date3,
						if(c.date4 IS NULL, NULL, date_format(c.date4, '%d.%m.')) as date4,
						if(c.date5 IS NULL, NULL, date_format(c.date5, '%d.%m.')) as date5,
						if(c.date6 IS NULL, NULL, date_format(c.date6, '%d.%m.')) as date6,
						if(c.date7 IS NULL, NULL, date_format(c.date7, '%d.%m.')) as date7,
						if(c.date8 IS NULL, NULL, date_format(c.date8, '%d.%m.')) as date8,
						if(c.date9 IS NULL, NULL, date_format(c.date9, '%d.%m.')) as date9,
						if(c.date10 IS NULL, NULL, date_format(c.date10, '%d.%m.')) as date10,
						ca.title as category,
						ca.only_cash_allowed,
						c.todo,
						c.precondition,
						c.textblock_mode,
						c.textblock
				   from
							  as_courses c
				   inner join as_users u			on c.trainer_id = u.user_id
				   inner join as_locations l		on c.location_id = l.location_id
				   left  join as_categories ca		on c.cat_id = ca.cat_id
				   left  join as_users u2			on c.trainer_id2 = u2.user_id
				   where c.course_id = " . $p_id . ";");
				  			
			if(!$result) echo $db->error;	
			
			$load_date = true;
			$dates = null;
			if ($line = $result->fetch_array()) {
						
									
				$load_date = false;
			
				$_SESSION["todo"] = $line["todo"];
				
				global $cal_rb_functions;
				
				switch ($line["status"]) {
					case 0:  // deaktiviert
						$line["status"] = "<span " . $cal_rb_functions->ignore_android("style='color: red'") .  ">✖</span>";
						break;
					case 1:  // in Bearbeitung
						$line["status"] = "<span " . $cal_rb_functions->ignore_android("style='color: #E69400'") .  ">✎</span>";
						break;
					case 2:  // aktiviert
						$line["status"] = "<span " . $cal_rb_functions->ignore_android("style='color: green'") .  ">✔</span>";
						break;
					case 3:  // aktiviert, alle Teilnehmer haben bezahlt
						$line["status"] = "<span " . $cal_rb_functions->ignore_android("style='color: green'") .  ">✔✔</span>";
						break;
					case 4: // aktiviert, Teilnehmer und Trainer bezahlt
						$line["status"] = "<span " . $cal_rb_functions->ignore_android("style='color: green'") .  ">✔✔✔</span>";
						break;
					default:
						$line["status"] = "?";
						break;
				}
				
				?>
				<div id='course-detail-container' class="clearfix">
					<ul style="margin-bottom: 0px;">
						<li><b>Kurs- Nr.: <?=$line['course_id']?></b></li>
						<li><?=$line['kursname']?></li>
						<li>Beginn: <?=$line['begin1']?></li>
						<li>Uhrzeit: <?=$line['time']?></li>
						<li>Trainer: <?=$line['trainer']?></li>
						<li>Termine: <?=$line['termine']?></li>
						<li>Kursbeitrag: <?=$line['price']?> €</li>
					<? if(!empty($line['precondition'])) { ?>
						<li>Voraussetzungen: <?=$line['precondition']?></li>
					<? } ?>
						<li>Kategorie: <?=$line['category']?></li>
						<li>Ort: <?=$line['ort']?></li>
						<li>Voranmeldungen: <?=$line['voranmeldungen']?></li>
						<li>Anmeldungen: <?=$line['anmeldungen']?> / <?=$line['kursplatzanzahl']?></li>
						<li>Status: <?=$line['status']?></li>
					</ul>
                    <? if ($line['textblock_mode'] == 1) {
                        echo nl2br("<ul style='margin-bottom: 0px;'><li>" . $line['textblock'] . "</li></ul>");
                    } ?>
				</div>
				<br />
				
				<?
			
			}
			if($result->num_rows === 0) {
		  		echo '<br>Keinen Kurs gefunden.';
			}
		
			$db->close();
		
	}


}


class DB_Functions_Calender_Events extends DB_Connect {
	
	
	public function db_insert_new_event(  $p_name,
										  $p_type,
										  $p_owner,
										  $p_trainer1,
										  $p_trainer2,
										  $p_location,
										  $p_status,
										  $p_overlap,
										  $p_show_unit,
										  $p_description,
										  
										  $p_date1,
										  $p_date2,
										  $p_date3,
										  $p_date4,
										  $p_date5,
										  $p_date6,
										  $p_date7,
										  $p_date8,
										  $p_date9,
										  $p_date10,
										  $p_date11,
										  $p_date12,
										  
										  $p_from1,
										  $p_from2,
										  $p_from3,
										  $p_from4,
										  $p_from5,
										  $p_from6,
										  $p_from7,
										  $p_from8,
										  $p_from9,
										  $p_from10,
										  $p_from11,
										  $p_from12,
										  
										  $p_to1,
										  $p_to2,
										  $p_to3,
										  $p_to4,
										  $p_to5,
										  $p_to6,
										  $p_to7,
										  $p_to8,
										  $p_to9,
										  $p_to10,
										  $p_to11,
										  $p_to12,
										  
										  $p_not_on)									  
									  
									  
	{
		
		// Validation

		
		$_SESSION["e_error_msg"] = "";
		if(empty($p_name)) {
			$_SESSION["e_error_msg"] .= "Bitte einen Namen für den Termin eingeben.<br>";
		}else{
			$p_name = "'" . $p_name . "'";
		}
		if(empty($p_type)) {
			$_SESSION["e_error_msg"] .= "Bitte Termintyp wählen.<br>";
			$p_type = "NULL";
		}
		if($p_trainer1 == -1 || empty($p_trainer1)) {
			if($p_trainer2 != '-1' && !empty($p_trainer2)) {
				$_SESSION["e_error_msg"] .= "Bitte Trainer 1 angeben oder Trainer 2 entfernen.<br>";
			}
		}
		if(empty($p_owner)) {
			$_SESSION["e_error_msg"] .= "Bitte einen User als Owner auswählen.<br>";
			$p_owner = "NULL";
		}
		if(empty($p_location)) {
			$p_location = "NULL";
			$_SESSION["e_error_msg"] .= "Bitte Standort wählen.<br>";
		}
		if(empty($p_status)) $p_status = 0;
		if(empty($p_description)) {
			$p_description = "NULL";
		}else{
			$p_description = "'" . $p_description . "'";
		}	
		
		
		if($p_trainer2 == -1) $p_trainer2 = 'NULL';
		
		if(strlen($p_from1)  == 2) $p_from1  = $p_from1  . ':00';
		if(strlen($p_from2)  == 2) $p_from2  = $p_from2  . ':00';
		if(strlen($p_from3)  == 2) $p_from3  = $p_from3  . ':00';
		if(strlen($p_from4)  == 2) $p_from4  = $p_from4  . ':00';
		if(strlen($p_from5)  == 2) $p_from5  = $p_from5  . ':00';
		if(strlen($p_from6)  == 2) $p_from6  = $p_from6  . ':00';
		if(strlen($p_from7)  == 2) $p_from7  = $p_from7  . ':00';
		if(strlen($p_from8)  == 2) $p_from8  = $p_from8  . ':00';
		if(strlen($p_from9)  == 2) $p_from9  = $p_from9  . ':00';
		if(strlen($p_from10) == 2) $p_from10 = $p_from10 . ':00';
		if(strlen($p_from11) == 2) $p_from11 = $p_from11 . ':00';
		if(strlen($p_from12) == 2) $p_from12 = $p_from12 . ':00';
		
		if(strlen($p_to1)  == 2) $p_to1  = $p_to1  . ':00';
		if(strlen($p_to2)  == 2) $p_to2  = $p_to2  . ':00';
		if(strlen($p_to3)  == 2) $p_to3  = $p_to3  . ':00';
		if(strlen($p_to4)  == 2) $p_to4  = $p_to4  . ':00';
		if(strlen($p_to5)  == 2) $p_to5  = $p_to5  . ':00';
		if(strlen($p_to6)  == 2) $p_to6  = $p_to6  . ':00';
		if(strlen($p_to7)  == 2) $p_to7  = $p_to7  . ':00';
		if(strlen($p_to8)  == 2) $p_to8  = $p_to8  . ':00';
		if(strlen($p_to9)  == 2) $p_to9  = $p_to9  . ':00';
		if(strlen($p_to10) == 2) $p_to10 = $p_to10 . ':00';
		if(strlen($p_to11) == 2) $p_to11 = $p_to11 . ':00';
		if(strlen($p_to12) == 2) $p_to12 = $p_to12 . ':00';
		
		if(empty($p_from2) && !empty($p_date2)) $p_from2 = $p_from1;
		if(empty($p_from3) && !empty($p_date3)) $p_from3 = $p_from2;
		if(empty($p_from4) && !empty($p_date4)) $p_from4 = $p_from3;
		if(empty($p_from5) && !empty($p_date5)) $p_from5 = $p_from4;
		if(empty($p_from6) && !empty($p_date6)) $p_from6 = $p_from5;
		if(empty($p_from7) && !empty($p_date7)) $p_from7 = $p_from6;
		if(empty($p_from8) && !empty($p_date8)) $p_from8 = $p_from7;
		if(empty($p_from9) && !empty($p_date9)) $p_from9 = $p_from8;
		if(empty($p_from10) && !empty($p_date10)) $p_from10 = $p_from9;
		if(empty($p_from11) && !empty($p_date11)) $p_from11 = $p_from10;
		if(empty($p_from12) && !empty($p_date12)) $p_from12 = $p_from11;
		
		if(empty($p_to2) && !empty($p_date2)) $p_to2 = $p_to1;
		if(empty($p_to3) && !empty($p_date3)) $p_to3 = $p_to2;
		if(empty($p_to4) && !empty($p_date4)) $p_to4 = $p_to3;
		if(empty($p_to5) && !empty($p_date5)) $p_to5 = $p_to4;
		if(empty($p_to6) && !empty($p_date6)) $p_to6 = $p_to5;
		if(empty($p_to7) && !empty($p_date7)) $p_to7 = $p_to6;
		if(empty($p_to8) && !empty($p_date8)) $p_to8 = $p_to7;
		if(empty($p_to9) && !empty($p_date9)) $p_to9 = $p_to8;
		if(empty($p_to10) && !empty($p_date10)) $p_to10 = $p_to9;
		if(empty($p_to11) && !empty($p_date11)) $p_to11 = $p_to10;
		if(empty($p_to12) && !empty($p_date12)) $p_to12 = $p_to11;

		$p_date1_begin = "NULL";
		$p_date2_begin = "NULL";
		$p_date3_begin = "NULL";
		$p_date4_begin = "NULL";
		$p_date5_begin = "NULL";
		$p_date6_begin = "NULL";
		$p_date7_begin = "NULL";
		$p_date8_begin = "NULL";
		$p_date9_begin = "NULL";
		$p_date10_begin = "NULL";
		$p_date11_begin = "NULL";
		$p_date12_begin = "NULL";
		
		$p_date1_end = "NULL";
		$p_date2_end = "NULL";
		$p_date3_end = "NULL";
		$p_date4_end = "NULL";
		$p_date5_end = "NULL";
		$p_date6_end = "NULL";
		$p_date7_end = "NULL";
		$p_date8_end = "NULL";
		$p_date9_end = "NULL";
		$p_date10_end = "NULL";
		$p_date11_end = "NULL";
		$p_date12_end = "NULL";
		
		$date_test = DateTime::createFromFormat('d.m.Y', $p_date1);
		if ((!$date_test) || empty($p_date1)) {
			$_SESSION["e_error_msg"] .= "Bitte gültiges Datum für Termin 1 angeben.<br>";
		}else{
			$p_date1_begin = "STR_TO_DATE('$p_date1 " . $p_from1 . "','%d.%m.%Y %H:%i')";
			$p_date1_end   = "STR_TO_DATE('$p_date1 " . $p_to1 . "','%d.%m.%Y %H:%i')";
		}
		$time_test = DateTime::createFromFormat('d.m.Y H:i', '01.01.2015 ' . $p_from1);
			if(!$time_test) {
				$_SESSION["e_error_msg"] .= "Bitte gültige Uhrzeit (von) für Termin 1 eingeben.<br>";
			}
		$time_test = DateTime::createFromFormat('d.m.Y H:i', '01.01.2015 ' . $p_to1);
			if(!$time_test) {
				$_SESSION["e_error_msg"] .= "Bitte gültige Uhrzeit (bis) für Termin 1 eingeben.<br>";
			}
			
		$p_begin = $p_date1_begin;
		$p_end = $p_date1_end;
		
		if(empty($p_date2)) {
			$p_date2 = "NULL";
		}else{
			$p_date2_begin = "STR_TO_DATE('$p_date2 " . $p_from2 . "','%d.%m.%Y %H:%i')";
			$p_date2_end   = "STR_TO_DATE('$p_date2 " . $p_to2 . "','%d.%m.%Y %H:%i')";
			$p_end = $p_date2_end;
		}
		if(empty($p_date3)) {
			$p_date3 = "NULL";
		}else{
			if(!isset($p_date2) || $p_date2 == "NULL") $_SESSION["e_error_msg"] .= "Bitte gültigen Termin 2 angeben.<br>";
			$p_date3_begin = "STR_TO_DATE('$p_date3 " . $p_from3 . "','%d.%m.%Y %H:%i')";
			$p_date3_end   = "STR_TO_DATE('$p_date3 " . $p_to3 . "','%d.%m.%Y %H:%i')";
			$p_end = $p_date3_end;
		}
		if(empty($p_date4)) {
			$p_date4 = "NULL";
		}else{
			if(!isset($p_date3) || $p_date3 == "NULL") $_SESSION["e_error_msg"] .= "Bitte gültigen Termin 3 angeben.<br>";
			$p_date4_begin = "STR_TO_DATE('$p_date4 " . $p_from4 . "','%d.%m.%Y %H:%i')";
			$p_date4_end   = "STR_TO_DATE('$p_date4 " . $p_to4 . "','%d.%m.%Y %H:%i')";
			$p_end = $p_date4_end;
		}
		if(empty($p_date5)) {
			$p_date5 = "NULL";
		}else{
			if(!isset($p_date4) || $p_date4 == "NULL") $_SESSION["e_error_msg"] .= "Bitte gültigen Termin 4 angeben.<br>";
			$p_date5_begin = "STR_TO_DATE('$p_date5 " . $p_from5 . "','%d.%m.%Y %H:%i')";
			$p_date5_end   = "STR_TO_DATE('$p_date5 " . $p_to5 . "','%d.%m.%Y %H:%i')";
			$p_end = $p_date5_end;
		}
		if(empty($p_date6)) {
			$p_date6 = "NULL";
		}else{
			if(!isset($p_date5) || $p_date5 == "NULL") $_SESSION["e_error_msg"] .= "Bitte gültigen Termin 5 angeben.<br>";
			$p_date6_begin = "STR_TO_DATE('$p_date6 " . $p_from6 . "','%d.%m.%Y %H:%i')";
			$p_date6_end   = "STR_TO_DATE('$p_date6 " . $p_to6 . "','%d.%m.%Y %H:%i')";
			$p_end = $p_date6_end;
		}
		if(empty($p_date7)) {
			$p_date7 = "NULL";
		}else{
			if(!isset($p_date6) || $p_date6 == "NULL") $_SESSION["e_error_msg"] .= "Bitte gültigen Termin 6 angeben.<br>";
			$p_date7_begin = "STR_TO_DATE('$p_date7 " . $p_from7 . "','%d.%m.%Y %H:%i')";
			$p_date7_end   = "STR_TO_DATE('$p_date7 " . $p_to7 . "','%d.%m.%Y %H:%i')";
			$p_end = $p_date7_end;
		}
		if(empty($p_date8)) {
			$p_date8 = "NULL";
		}else{
			if(!isset($p_date7) || $p_date7 == "NULL") $_SESSION["e_error_msg"] .= "Bitte gültigen Termin 7 angeben.<br>";
			$p_date8_begin = "STR_TO_DATE('$p_date8 " . $p_from8 . "','%d.%m.%Y %H:%i')";
			$p_date8_end   = "STR_TO_DATE('$p_date8 " . $p_to8 . "','%d.%m.%Y %H:%i')";
			$p_end = $p_date8_end;
		}
		if(empty($p_date9)) {
			$p_date9 = "NULL";
		}else{
			if(!isset($p_date8) || $p_date8 == "NULL") $_SESSION["e_error_msg"] .= "Bitte gültigen Termin 8 angeben.<br>";
			$p_date9_begin = "STR_TO_DATE('$p_date9 " . $p_from9 . "','%d.%m.%Y %H:%i')";
			$p_date9_end   = "STR_TO_DATE('$p_date9 " . $p_to9 . "','%d.%m.%Y %H:%i')";
			$p_end = $p_date9_end;
		}
		if(empty($p_date10)) {
			$p_date10 = "NULL";
		}else{
			if(!isset($p_date9) || $p_date9 == "NULL") $_SESSION["e_error_msg"] .= "Bitte gültigen Termin 9 angeben.<br>";
			$p_date10_begin = "STR_TO_DATE('$p_date10 " . $p_from10 . "','%d.%m.%Y %H:%i')";
			$p_date10_end   = "STR_TO_DATE('$p_date10 " . $p_to10 . "','%d.%m.%Y %H:%i')";
			$p_end = $p_date10_end;
		}
		if(empty($p_date11)) {
			$p_date11 = "NULL";
		}else{
			if(!isset($p_date10) || $p_date10 == "NULL") $_SESSION["e_error_msg"] .= "Bitte gültigen Termin 10 angeben.<br>";
			$p_date11_begin = "STR_TO_DATE('$p_date11 " . $p_from11 . "','%d.%m.%Y %H:%i')";
			$p_date11_end   = "STR_TO_DATE('$p_date11 " . $p_to11 . "','%d.%m.%Y %H:%i')";
			$p_end = $p_date11_end;
		}
		if(empty($p_date12)) {
			$p_date12 = "NULL";
		}else{
			if(!isset($p_date11) || $p_date11 == "NULL") $_SESSION["e_error_msg"] .= "Bitte gültigen Termin 11 angeben.<br>";
			$p_date12_begin = "STR_TO_DATE('$p_date12 " . $p_from12 . "','%d.%m.%Y %H:%i')";
			$p_date12_end   = "STR_TO_DATE('$p_date12 " . $p_to12 . "','%d.%m.%Y %H:%i')";
			$p_end = $p_date12_end;
		}
		if(empty($p_not_on)) {
			$p_not_on = "NULL";
		}else {
			$p_not_on = "'$p_not_on'";
		}
										
		$db=$this->db_connect();
		
		$e_statement = "
			INSERT INTO as_events (name,
								   event_type,
								   owner_id,
								   trainer_id,
								   trainer_id2,
								   location_id,
								   status,
								   show_overlap,
								   show_unit,
								   event_description,
								   
								   date1_begin,
								   date2_begin,
								   date3_begin,
								   date4_begin,
								   date5_begin,
								   date6_begin,
								   date7_begin,
								   date8_begin,
								   date9_begin,
								   date10_begin,
								   date11_begin,
								   date12_begin,
								   
								   date1_end,
								   date2_end,
								   date3_end,
								   date4_end,
								   date5_end,
								   date6_end,
								   date7_end,
								   date8_end,
								   date9_end,
								   date10_end,
								   date11_end,
								   date12_end,
								   
								   begin,
								   end,
								   not_on)
								   
			VALUES 				  ($p_name,
								   $p_type,
								   $p_owner,
								   $p_trainer1,
								   $p_trainer2,
								   $p_location,
								   $p_status,
								   $p_overlap,
								   $p_show_unit,
								   $p_description,
								
								   $p_date1_begin,
								   $p_date2_begin,
								   $p_date3_begin,
								   $p_date4_begin,
								   $p_date5_begin,
								   $p_date6_begin,
								   $p_date7_begin,
								   $p_date8_begin,
								   $p_date9_begin,
								   $p_date10_begin,
								   $p_date11_begin,
								   $p_date12_begin,
								   
								   $p_date1_end,
								   $p_date2_end,
								   $p_date3_end,
								   $p_date4_end,
								   $p_date5_end,
								   $p_date6_end,
								   $p_date7_end,
								   $p_date8_end,
								   $p_date9_end,
								   $p_date10_end,
								   $p_date11_end,
								   $p_date12_end,
								   
								   $p_begin,
								   $p_end,
								   $p_not_on);";
		
		
		
		$result = false;
		$_SESSION["e_success_msg"] = false;
		if(empty($_SESSION["e_error_msg"])) {
			$result = $db->query($e_statement);
								   
			if(!$result) {
				$_SESSION["e_error_msg"] = $db->error . "<br><br>" . $e_statement;
				$db->close();
				return false;
			}else {
				$_SESSION["event_id"]= $db->insert_id;
				$_SESSION["e_success_msg"] = "<p id='saved_done' style='color: green; font-weight: bold; font-size: 30px; margin-top: 10px '>Gespeichert ✔</p>";
				$db->close();
				return true;
			}
		}
	
	}


	public function db_update_event(      $p_event_id,
										  $p_name,
										  $p_type,
										  $p_owner,
										  $p_trainer1,
										  $p_trainer2,
										  $p_location,
										  $p_status,
										  $p_overlap,
										  $p_show_unit,
										  $p_description,
										  
										  $p_date1,
										  $p_date2,
										  $p_date3,
										  $p_date4,
										  $p_date5,
										  $p_date6,
										  $p_date7,
										  $p_date8,
										  $p_date9,
										  $p_date10,
										  $p_date11,
										  $p_date12,
										  
										  $p_from1,
										  $p_from2,
										  $p_from3,
										  $p_from4,
										  $p_from5,
										  $p_from6,
										  $p_from7,
										  $p_from8,
										  $p_from9,
										  $p_from10,
										  $p_from11,
										  $p_from12,
										  
										  $p_to1,
										  $p_to2,
										  $p_to3,
										  $p_to4,
										  $p_to5,
										  $p_to6,
										  $p_to7,
										  $p_to8,
										  $p_to9,
										  $p_to10,
										  $p_to11,
										  $p_to12,
										  
										  $p_not_on)									  
									  
									  
	{
		
		// Validation

		
		$_SESSION["e_error_msg"] = "";
		if(empty($p_name)) {
			$_SESSION["e_error_msg"] .= "Bitte einen Namen für den Termin eingeben.<br>";
		}else{
			$p_name = "'" . $p_name . "'";
		}
		if(empty($p_type)) {
			$_SESSION["e_error_msg"] .= "Bitte Termintyp wählen.<br>";
			$p_type = "NULL";
		}
		if($p_trainer1 == -1 || empty($p_trainer1)) {
			if($p_trainer2 != -1 && !empty($p_trainer2)) {
				$_SESSION["e_error_msg"] .= "Bitte Trainer 1 angeben oder Trainer 2 entfernen.<br>";
			}
		}
		if(empty($p_owner)) {
			$_SESSION["e_error_msg"] .= "Bitte einen User als Owner auswählen.<br>";
			$p_owner = "NULL";
		}
		if(empty($p_location)) {
			$p_location = "NULL";
			$_SESSION["e_error_msg"] .= "Bitte Standort wählen.<br>";
		}
		if(empty($p_status)) $p_status = 0;
		if(empty($p_description)) {
			$p_description = "NULL";
		}else{
			$p_description = "'" . $p_description . "'";
		}	
		
		if($p_trainer2 == -1) $p_trainer2 = 'NULL';
		
		if(strlen($p_from1)  == 2) $p_from1  = $p_from1  . ':00';
		if(strlen($p_from2)  == 2) $p_from2  = $p_from2  . ':00';
		if(strlen($p_from3)  == 2) $p_from3  = $p_from3  . ':00';
		if(strlen($p_from4)  == 2) $p_from4  = $p_from4  . ':00';
		if(strlen($p_from5)  == 2) $p_from5  = $p_from5  . ':00';
		if(strlen($p_from6)  == 2) $p_from6  = $p_from6  . ':00';
		if(strlen($p_from7)  == 2) $p_from7  = $p_from7  . ':00';
		if(strlen($p_from8)  == 2) $p_from8  = $p_from8  . ':00';
		if(strlen($p_from9)  == 2) $p_from9  = $p_from9  . ':00';
		if(strlen($p_from10) == 2) $p_from10 = $p_from10 . ':00';
		if(strlen($p_from11) == 2) $p_from11 = $p_from11 . ':00';
		if(strlen($p_from12) == 2) $p_from12 = $p_from12 . ':00';
		
		if(strlen($p_to1)  == 2) $p_to1  = $p_to1  . ':00';
		if(strlen($p_to2)  == 2) $p_to2  = $p_to2  . ':00';
		if(strlen($p_to3)  == 2) $p_to3  = $p_to3  . ':00';
		if(strlen($p_to4)  == 2) $p_to4  = $p_to4  . ':00';
		if(strlen($p_to5)  == 2) $p_to5  = $p_to5  . ':00';
		if(strlen($p_to6)  == 2) $p_to6  = $p_to6  . ':00';
		if(strlen($p_to7)  == 2) $p_to7  = $p_to7  . ':00';
		if(strlen($p_to8)  == 2) $p_to8  = $p_to8  . ':00';
		if(strlen($p_to9)  == 2) $p_to9  = $p_to9  . ':00';
		if(strlen($p_to10) == 2) $p_to10 = $p_to10 . ':00';
		if(strlen($p_to11) == 2) $p_to11 = $p_to11 . ':00';
		if(strlen($p_to12) == 2) $p_to12 = $p_to12 . ':00';
		
		if(empty($p_from2) && !empty($p_date2)) $p_from2 = $p_from1;
		if(empty($p_from3) && !empty($p_date3)) $p_from3 = $p_from2;
		if(empty($p_from4) && !empty($p_date4)) $p_from4 = $p_from3;
		if(empty($p_from5) && !empty($p_date5)) $p_from5 = $p_from4;
		if(empty($p_from6) && !empty($p_date6)) $p_from6 = $p_from5;
		if(empty($p_from7) && !empty($p_date7)) $p_from7 = $p_from6;
		if(empty($p_from8) && !empty($p_date8)) $p_from8 = $p_from7;
		if(empty($p_from9) && !empty($p_date9)) $p_from9 = $p_from8;
		if(empty($p_from10) && !empty($p_date10)) $p_from10 = $p_from9;
		if(empty($p_from11) && !empty($p_date11)) $p_from11 = $p_from10;
		if(empty($p_from12) && !empty($p_date12)) $p_from12 = $p_from11;
		
		if(empty($p_to2) && !empty($p_date2)) $p_to2 = $p_to1;
		if(empty($p_to3) && !empty($p_date3)) $p_to3 = $p_to2;
		if(empty($p_to4) && !empty($p_date4)) $p_to4 = $p_to3;
		if(empty($p_to5) && !empty($p_date5)) $p_to5 = $p_to4;
		if(empty($p_to6) && !empty($p_date6)) $p_to6 = $p_to5;
		if(empty($p_to7) && !empty($p_date7)) $p_to7 = $p_to6;
		if(empty($p_to8) && !empty($p_date8)) $p_to8 = $p_to7;
		if(empty($p_to9) && !empty($p_date9)) $p_to9 = $p_to8;
		if(empty($p_to10) && !empty($p_date10)) $p_to10 = $p_to9;
		if(empty($p_to11) && !empty($p_date11)) $p_to11 = $p_to10;
		if(empty($p_to12) && !empty($p_date12)) $p_to12 = $p_to11;

		$p_date1_begin = "NULL";
		$p_date2_begin = "NULL";
		$p_date3_begin = "NULL";
		$p_date4_begin = "NULL";
		$p_date5_begin = "NULL";
		$p_date6_begin = "NULL";
		$p_date7_begin = "NULL";
		$p_date8_begin = "NULL";
		$p_date9_begin = "NULL";
		$p_date10_begin = "NULL";
		$p_date11_begin = "NULL";
		$p_date12_begin = "NULL";
		
		$p_date1_end = "NULL";
		$p_date2_end = "NULL";
		$p_date3_end = "NULL";
		$p_date4_end = "NULL";
		$p_date5_end = "NULL";
		$p_date6_end = "NULL";
		$p_date7_end = "NULL";
		$p_date8_end = "NULL";
		$p_date9_end = "NULL";
		$p_date10_end = "NULL";
		$p_date11_end = "NULL";
		$p_date12_end = "NULL";
		
		$date_test = DateTime::createFromFormat('d.m.Y', $p_date1);
		if ((!$date_test) || empty($p_date1)) {
			$_SESSION["e_error_msg"] .= "Bitte gültiges Datum für Termin 1 angeben.<br>";
		}else{
			$p_date1_begin = "STR_TO_DATE('$p_date1 " . $p_from1 . "','%d.%m.%Y %H:%i')";
			$p_date1_end   = "STR_TO_DATE('$p_date1 " . $p_to1 . "','%d.%m.%Y %H:%i')";
		}
		$time_test = DateTime::createFromFormat('d.m.Y H:i', '01.01.2015 ' . $p_from1);
			if(!$time_test) {
				$_SESSION["e_error_msg"] .= "Bitte gültige Uhrzeit (von) für Termin 1 eingeben.<br>";
			}
		$time_test = DateTime::createFromFormat('d.m.Y H:i', '01.01.2015 ' . $p_to1);
			if(!$time_test) {
				$_SESSION["e_error_msg"] .= "Bitte gültige Uhrzeit (bis) für Termin 1 eingeben.<br>";
			}
			
		$p_begin = $p_date1_begin;
		$p_end = $p_date1_end;
		if(empty($p_date2)) {
			$p_date2 = "NULL";
		}else{
			$p_date2_begin = "STR_TO_DATE('$p_date2 " . $p_from2 . "','%d.%m.%Y %H:%i')";
			$p_date2_end   = "STR_TO_DATE('$p_date2 " . $p_to2 . "','%d.%m.%Y %H:%i')";
			$p_end = $p_date2_end;
		}
		if(empty($p_date3)) {
			$p_date3 = "NULL";
		}else{
			if(!isset($p_date2) || $p_date2 == "NULL") $_SESSION["e_error_msg"] .= "Bitte gültigen Termin 2 angeben.<br>";
			$p_date3_begin = "STR_TO_DATE('$p_date3 " . $p_from3 . "','%d.%m.%Y %H:%i')";
			$p_date3_end   = "STR_TO_DATE('$p_date3 " . $p_to3 . "','%d.%m.%Y %H:%i')";
			$p_end = $p_date3_end;
		}
		if(empty($p_date4)) {
			$p_date4 = "NULL";
		}else{
			if(!isset($p_date3) || $p_date3 == "NULL") $_SESSION["e_error_msg"] .= "Bitte gültigen Termin 3 angeben.<br>";
			$p_date4_begin = "STR_TO_DATE('$p_date4 " . $p_from4 . "','%d.%m.%Y %H:%i')";
			$p_date4_end   = "STR_TO_DATE('$p_date4 " . $p_to4 . "','%d.%m.%Y %H:%i')";
			$p_end = $p_date4_end;
		}
		if(empty($p_date5)) {
			$p_date5 = "NULL";
		}else{
			if(!isset($p_date4) || $p_date4 == "NULL") $_SESSION["e_error_msg"] .= "Bitte gültigen Termin 4 angeben.<br>";
			$p_date5_begin = "STR_TO_DATE('$p_date5 " . $p_from5 . "','%d.%m.%Y %H:%i')";
			$p_date5_end   = "STR_TO_DATE('$p_date5 " . $p_to5 . "','%d.%m.%Y %H:%i')";
			$p_end = $p_date5_end;
		}
		if(empty($p_date6)) {
			$p_date6 = "NULL";
		}else{
			if(!isset($p_date5) || $p_date5 == "NULL") $_SESSION["e_error_msg"] .= "Bitte gültigen Termin 5 angeben.<br>";
			$p_date6_begin = "STR_TO_DATE('$p_date6 " . $p_from6 . "','%d.%m.%Y %H:%i')";
			$p_date6_end   = "STR_TO_DATE('$p_date6 " . $p_to6 . "','%d.%m.%Y %H:%i')";
			$p_end = $p_date6_end;
		}
		if(empty($p_date7)) {
			$p_date7 = "NULL";
		}else{
			if(!isset($p_date6) || $p_date6 == "NULL") $_SESSION["e_error_msg"] .= "Bitte gültigen Termin 6 angeben.<br>";
			$p_date7_begin = "STR_TO_DATE('$p_date7 " . $p_from7 . "','%d.%m.%Y %H:%i')";
			$p_date7_end   = "STR_TO_DATE('$p_date7 " . $p_to7 . "','%d.%m.%Y %H:%i')";
			$p_end = $p_date7_end;
		}
		if(empty($p_date8)) {
			$p_date8 = "NULL";
		}else{
			if(!isset($p_date7) || $p_date7 == "NULL") $_SESSION["e_error_msg"] .= "Bitte gültigen Termin 7 angeben.<br>";
			$p_date8_begin = "STR_TO_DATE('$p_date8 " . $p_from8 . "','%d.%m.%Y %H:%i')";
			$p_date8_end   = "STR_TO_DATE('$p_date8 " . $p_to8 . "','%d.%m.%Y %H:%i')";
			$p_end = $p_date8_end;
		}
		if(empty($p_date9)) {
			$p_date9 = "NULL";
		}else{
			if(!isset($p_date8) || $p_date8 == "NULL") $_SESSION["e_error_msg"] .= "Bitte gültigen Termin 8 angeben.<br>";
			$p_date9_begin = "STR_TO_DATE('$p_date9 " . $p_from9 . "','%d.%m.%Y %H:%i')";
			$p_date9_end   = "STR_TO_DATE('$p_date9 " . $p_to9 . "','%d.%m.%Y %H:%i')";
			$p_end = $p_date9_end;
		}
		if(empty($p_date10)) {
			$p_date10 = "NULL";
		}else{
			if(!isset($p_date9) || $p_date9 == "NULL") $_SESSION["e_error_msg"] .= "Bitte gültigen Termin 9 angeben.<br>";
			$p_date10_begin = "STR_TO_DATE('$p_date10 " . $p_from10 . "','%d.%m.%Y %H:%i')";
			$p_date10_end   = "STR_TO_DATE('$p_date10 " . $p_to10 . "','%d.%m.%Y %H:%i')";
			$p_end = $p_date10_end;
		}
		if(empty($p_date11)) {
			$p_date11 = "NULL";
		}else{
			if(!isset($p_date10) || $p_date10 == "NULL") $_SESSION["e_error_msg"] .= "Bitte gültigen Termin 10 angeben.<br>";
			$p_date11_begin = "STR_TO_DATE('$p_date11 " . $p_from11 . "','%d.%m.%Y %H:%i')";
			$p_date11_end   = "STR_TO_DATE('$p_date11 " . $p_to11 . "','%d.%m.%Y %H:%i')";
			$p_end = $p_date11_end;
		}
		if(empty($p_date12)) {
			$p_date12 = "NULL";
		}else{
			if(!isset($p_date11) || $p_date11 == "NULL") $_SESSION["e_error_msg"] .= "Bitte gültigen Termin 11 angeben.<br>";
			$p_date12_begin = "STR_TO_DATE('$p_date12 " . $p_from12 . "','%d.%m.%Y %H:%i')";
			$p_date12_end   = "STR_TO_DATE('$p_date12 " . $p_to12 . "','%d.%m.%Y %H:%i')";
			$p_end = $p_date12_end;
		}
		if(empty($p_not_on)) {
			$p_not_on = "NULL";
		}else {
			$p_not_on = "'$p_not_on'";
		}
										
		$db=$this->db_connect();
		
		$e_statement = "
		   UPDATE as_events
		   SET    name = $p_name,
				  event_type = $p_type,
				  owner_id = $p_owner,
				  trainer_id = $p_trainer1,
				  trainer_id2 = $p_trainer2,
				  location_id = $p_location,
				  status = $p_status,
				  show_overlap = $p_overlap,
				  show_unit = $p_show_unit,
				  event_description = $p_description,
				  
				  date1_begin = $p_date1_begin,
				  date2_begin = $p_date2_begin,
				  date3_begin = $p_date3_begin,
				  date4_begin = $p_date4_begin,
				  date5_begin = $p_date5_begin,
				  date6_begin = $p_date6_begin,
				  date7_begin = $p_date7_begin,
				  date8_begin = $p_date8_begin,
				  date9_begin = $p_date9_begin,
				  date10_begin = $p_date10_begin,
				  date11_begin = $p_date11_begin,
				  date12_begin = $p_date12_begin,
				  
				  date1_end = $p_date1_end,
				  date2_end = $p_date2_end,
				  date3_end = $p_date3_end,
				  date4_end = $p_date4_end,
				  date5_end = $p_date5_end,
				  date6_end = $p_date6_end,
				  date7_end = $p_date7_end,
				  date8_end = $p_date8_end,
				  date9_end = $p_date9_end,
				  date10_end = $p_date10_end,
				  date11_end = $p_date11_end,
				  date12_end = $p_date12_end,
				  
				  begin = $p_begin,
				  end = $p_end,
				  not_on = $p_not_on,
				  mod_dat = now()
			WHERE event_id = $p_event_id;";
				  
		$result = false;
		$_SESSION["e_success_msg"] = false;
		if(empty($_SESSION["e_error_msg"])) {
			$result = $db->query($e_statement);
								   
			if(!$result) {
				$_SESSION["e_error_msg"] = $db->error . "<br><br>" . $e_statement;
				$db->close();
				return false;
			}else {
				$_SESSION["e_success_msg"] = "<p id='saved_done' style='color: green; font-weight: bold; font-size: 30px; margin-top: 10px '>Gespeichert ✔</p>";
				$db->close();
				return true;
			}
		}
	
	}

	public function db_load_event_values_from_id($p_id){

			$db=$this->db_connect();
			$result = $db->query(
			
				"SELECT 
						e.name			as e_name,
						e.event_id,
						e.event_type	as e_type,
						e.owner_id		as e_owner,
						e.trainer_id	as e_trainer1,
						e.trainer_id2	as e_trainer2,
						e.location_id	as e_location,
						e.status		as e_status,
						e.show_overlap		as e_overlap,
						e.event_description	as e_description,
						date_format(e.date1_begin, '%d.%m.%Y') as e_date1,
						if(e.date2_begin IS NULL, '', date_format(e.date2_begin, '%d.%m.%Y')) as e_date2,
						if(e.date3_begin IS NULL, '', date_format(e.date3_begin, '%d.%m.%Y')) as e_date3,
						if(e.date4_begin IS NULL, '', date_format(e.date4_begin, '%d.%m.%Y')) as e_date4,
						if(e.date5_begin IS NULL, '', date_format(e.date5_begin, '%d.%m.%Y')) as e_date5,
						if(e.date6_begin IS NULL, '', date_format(e.date6_begin, '%d.%m.%Y')) as e_date6,
						if(e.date7_begin IS NULL, '', date_format(e.date7_begin, '%d.%m.%Y')) as e_date7,
						if(e.date8_begin IS NULL, '', date_format(e.date8_begin, '%d.%m.%Y')) as e_date8,
						if(e.date9_begin IS NULL, '', date_format(e.date9_begin, '%d.%m.%Y')) as e_date9,
						if(e.date10_begin IS NULL, '', date_format(e.date10_begin, '%d.%m.%Y')) as e_date10,
						if(e.date11_begin IS NULL, '', date_format(e.date11_begin, '%d.%m.%Y')) as e_date11,
						if(e.date12_begin IS NULL, '', date_format(e.date12_begin, '%d.%m.%Y')) as e_date12,						
						
						date_format(e.date1_begin, '%H:%i') as e_from1,
						if(e.date2_begin IS NULL, '', date_format(e.date2_begin, '%H:%i')) as e_from2,
						if(e.date3_begin IS NULL, '', date_format(e.date3_begin, '%H:%i')) as e_from3,
						if(e.date4_begin IS NULL, '', date_format(e.date4_begin, '%H:%i')) as e_from4,
						if(e.date5_begin IS NULL, '', date_format(e.date5_begin, '%H:%i')) as e_from5,
						if(e.date6_begin IS NULL, '', date_format(e.date6_begin, '%H:%i')) as e_from6,
						if(e.date7_begin IS NULL, '', date_format(e.date7_begin, '%H:%i')) as e_from7,
						if(e.date8_begin IS NULL, '', date_format(e.date8_begin, '%H:%i')) as e_from8,
						if(e.date9_begin IS NULL, '', date_format(e.date9_begin, '%H:%i')) as e_from9,
						if(e.date10_begin IS NULL, '', date_format(e.date10_begin, '%H:%i')) as e_from10,
						if(e.date11_begin IS NULL, '', date_format(e.date11_begin, '%H:%i')) as e_from11,
						if(e.date12_begin IS NULL, '', date_format(e.date12_begin, '%H:%i')) as e_from12,
						
						date_format(e.date1_end, '%H:%i') as e_to1,
						if(e.date2_end IS NULL, '', date_format(e.date2_end, '%H:%i')) as e_to2,
						if(e.date3_end IS NULL, '', date_format(e.date3_end, '%H:%i')) as e_to3,
						if(e.date4_end IS NULL, '', date_format(e.date4_end, '%H:%i')) as e_to4,
						if(e.date5_end IS NULL, '', date_format(e.date5_end, '%H:%i')) as e_to5,
						if(e.date6_end IS NULL, '', date_format(e.date6_end, '%H:%i')) as e_to6,
						if(e.date7_end IS NULL, '', date_format(e.date7_end, '%H:%i')) as e_to7,
						if(e.date8_end IS NULL, '', date_format(e.date8_end, '%H:%i')) as e_to8,
						if(e.date9_end IS NULL, '', date_format(e.date9_end, '%H:%i')) as e_to9,
						if(e.date10_end IS NULL, '', date_format(e.date10_end, '%H:%i')) as e_to10,
						if(e.date11_end IS NULL, '', date_format(e.date11_end, '%H:%i')) as e_to11,
						if(e.date12_end IS NULL, '', date_format(e.date12_end, '%H:%i')) as e_to12,
						e.show_unit as e_show_unit,
						e.not_on as e_not_on
				   from
							  as_events e
					where e.event_id = " . $p_id . ";");
				  			
			if(!$result) echo $db->error;	
			

			$line = $result->fetch_array();
			
			if($result->num_rows === 0) {
  		   		echo '<br>Keinen Termin gefunden.';
				$db->close();
  		   		exit();
		  
			}else{				
					
				foreach(array("event_id",
							  "e_name",
							  "e_type",
							  "e_owner",
							  "e_location",
							  "e_status",
							  "e_overlap",
							  "e_show_unit",
							  "e_description",
							  "e_trainer1",
							  "e_trainer2",
							  
							  "e_date1",
							  "e_date2",
							  "e_date3",
							  "e_date4",
							  "e_date5",
							  "e_date6",
							  "e_date7",
							  "e_date8",
							  "e_date9",
							  "e_date10",
							  "e_date11",
							  "e_date12",
							  
							  "e_from1",
							  "e_from2",
							  "e_from3",
							  "e_from4",
							  "e_from5",
							  "e_from6",
							  "e_from7",
							  "e_from8",
							  "e_from9",
							  "e_from10",
							  "e_from11",
							  "e_from12",
							  
							  "e_to1",
							  "e_to2",
							  "e_to3",
							  "e_to4",
							  "e_to5",
							  "e_to6",
							  "e_to7",
							  "e_to8",
							  "e_to9",
							  "e_to10",
							  "e_to11",
							  "e_to12",
							  
							  "e_not_on") as $var_name) {
					
					$_SESSION["$var_name"] = $line["$var_name"];
					
				}
						
			}
			$db->close();
		
	}


	
}

$cal_db_functions = new DB_Functions();

?>