<?php
/* Coypright(2015) by Ing. Roman Breitschopf, BA 									*/
/* office@breitschopf.wien			www.breitschopf.wien 							*/
/* License for Jasmin Liska. Do not distribute to others without permission. */
session_start();
$rb_path_self = str_replace("controller/ajax_functions/reload_calendar.php", "", $_SERVER['PHP_SELF']);
define('main-call','true');

require_once "../cal_configuration.php";

require_once "../cal_db_functions.php";
require_once "../cal_rb_functions.php";

$cal_rb_functions->check_login_processing();
$cal_rb_functions->check_session_duration(30);
$cal_rb_functions->check_login_is_ok_or_die_for_ajax_function();

$cal_db_functions->calendar_retrieve_data->db_load_course_details($_GET["c"]);
				
//echo ".";
?>