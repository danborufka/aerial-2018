<?php
/* Coypright(2015) by Ing. Roman Breitschopf, BA 									*/
/* office@breitschopf.wien			www.breitschopf.wien 							*/
/* License for Jasmin Liska. Do not distribute to others without permission. */
session_start();    // for processing $_POST and submits!
$rb_path_self = str_replace("process.php", "", $_SERVER['PHP_SELF']);
define('main-call','true');
require "controller/cal_configuration.php";
require "controller/cal_db_functions.php";

if(isset($_POST["login"]) && $_POST["login"] == "processing") {
	$_SESSION["login"] = "processing";
}
if (isset($_SESSION["login"]) || $_SESSION["login"] == "processing") {
	if (isset($_POST["username"]) &&
		$_POST["username"] != ''  &&
		isset($_POST["password"])  )
	{
			$cal_db_functions->db_process_login($_POST["username"], $_POST["password"]);
	}
}

if (isset($_SESSION["login"]) || $_SESSION["login"] == "ok") {
	
	foreach (array('cal_month',
				   'cal_year',
				   'cal_start_date',
				   'cal_filter_event_type',
				   'cal_view_type',
				   'cal_filter_type',
				   'cal_filter_location',
				   'cal_filter_person',
				   'cal_action',
				   'cal_view',
				   'view',
				   'action',
				   'course_id',
				   
				   'e_name',
				   'e_type',
				   'e_owner',
				   'e_trainer1',
				   'e_trainer2',
				   'e_location',
				   'e_status',
				   'e_overlap',
				   'e_show_unit',
				   'e_description',
				   'e_not_on',
				   
				   'e_date1',
				   'e_date2',
				   'e_date3',
				   'e_date4',
				   'e_date5',
				   'e_date6',
				   'e_date7',
				   'e_date8',
				   'e_date9',
				   'e_date10',
				   'e_date11',
				   'e_date12',
				   
				   'e_from1',
				   'e_from2',
				   'e_from3',
				   'e_from4',
				   'e_from5',
				   'e_from6',
				   'e_from7',
				   'e_from8',
				   'e_from9',
				   'e_from10',
				   'e_from11',
				   'e_from12',
				   
				   'e_to1',
				   'e_to2',
				   'e_to3',
				   'e_to4',
				   'e_to5',
				   'e_to6',
				   'e_to7',
				   'e_to8',
				   'e_to9',
				   'e_to10',
				   'e_to11',
				   'e_to12',
				   'e_no_reload_data'
				   		   
				   ) as $var_name) {	
		if (isset($_POST["$var_name"])) $_SESSION["$var_name"] = htmlspecialchars($_POST["$var_name"], ENT_QUOTES);			
	}
	if (isset($_POST["event"])) $_SESSION["event_id"] = htmlspecialchars($_POST["event"], ENT_QUOTES);	
	
	if(isset($_POST['cal_action']) && $_POST['cal_action'] == 'e_create_course_from_event') {
		//echo $_POST['e_date2'] . "xyz"; die; //todo1 
			foreach (array(
					   
					   'e_name',
					   'e_trainer1',
					   'e_trainer2',
					   'e_location',
					   'e_description',
					   'e_not_on',
					   
					   'e_date1',
					   'e_date2',
					   'e_date3',
					   'e_date4',
					   'e_date5',
					   'e_date6',
					   'e_date7',
					   'e_date8',
					   'e_date9',
					   'e_date10',
					   'e_date11',
					   'e_date12',
					   
					   'e_from1',
					   'e_from2',
					   'e_from3',
					   'e_from4',
					   'e_from5',
					   'e_from6',
					   'e_from7',
					   'e_from8',
					   'e_from9',
					   'e_from10',
					   'e_from11',
					   'e_from12',
					   
					   'e_to1',
					   'e_to2',
					   'e_to3',
					   'e_to4',
					   'e_to5',
					   'e_to6',
					   'e_to7',
					   'e_to8',
					   'e_to9',
					   'e_to10',
					   'e_to11',
					   'e_to12',
					   		   
					   ) as $var_name) {
			if (isset($_POST[$var_name])) {
				$_SESSION["e2c_$var_name"] = htmlspecialchars($_POST[$var_name], ENT_QUOTES);	
			}
		}	
			
			$_SESSION['action'] = 'e_create_course_from_event';
			unset($_SESSION['cal_action']);
			header('Location: ' . str_replace('calendar', 'booking',  str_replace("process.php", "", $_SERVER['PHP_SELF'])));
			die;
		
	
	}


}
header('Location: ' . str_replace("process.php", "", $_SERVER['PHP_SELF']));

?>