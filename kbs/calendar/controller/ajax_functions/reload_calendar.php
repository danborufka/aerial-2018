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

if(isset($_GET["m"])) 		$_SESSION["cal_month"] = htmlspecialchars($_GET["m"]);
if(isset($_GET["y"])) 		$_SESSION["cal_year"] = htmlspecialchars($_GET["y"]);
if(isset($_GET["t"]))		$_SESSION["cal_filter_type"] = htmlspecialchars($_GET["t"]);
if(isset($_GET["l"])) 		$_SESSION["cal_filter_location"] = htmlspecialchars($_GET["l"]);
if(isset($_GET["p"])) 		$_SESSION["cal_filter_person"] = htmlspecialchars($_GET["p"]);
if(isset($_GET["v"])) 		$_SESSION["cal_view_type"] = htmlspecialchars($_GET["v"]);
if(isset($_GET["s"])) 		$_SESSION["cal_start_date"] = htmlspecialchars($_GET["s"]);


			
if($_SESSION["cal_view_type"] == 'd') {
	$cal_rb_functions->load_five_days_table($_SESSION['cal_start_date'],
											$_SESSION['cal_filter_type'],
											$_SESSION['cal_filter_location'],
											$_SESSION['cal_filter_person']);
}else {					
	$cal_rb_functions->load_cal_month_table($_SESSION["cal_month"],
											$_SESSION["cal_year"],
											$_SESSION["cal_filter_type"],
											$_SESSION["cal_filter_location"],
											$_SESSION["cal_filter_person"]);
}

				
//echo ".";
?>