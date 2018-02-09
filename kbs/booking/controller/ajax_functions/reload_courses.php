<?php
/* Coypright(2015) by Ing. Roman Breitschopf, BA 									*/
/* office@breitschopf.wien			www.breitschopf.wien 							*/
/* License for Jasmin Liska. Do not distribute to others without permission. */
session_start();
$rb_path_self = str_replace("controller/ajax_functions/reload_courses.php", "", $_SERVER['PHP_SELF']);
define('main-call','true');

require_once "../configuration.php";

require_once "../db_functions.php";
require_once "../rb_functions.php";

$rb_functions->check_login_processing();
$rb_functions->check_session_duration(30);
$rb_functions->check_login_is_ok_or_die_for_ajax_function();

if(isset($_GET["t"])) 			$_SESSION["filter_trainer"] = htmlspecialchars($_GET["t"]);
if(isset($_GET["z"])) 		$_SESSION["filter_zeitraum"] = htmlspecialchars($_GET["z"]);
if(isset($_GET["b"]))		$_SESSION["filter_from_date"] = htmlspecialchars($_GET["b"]);
if(isset($_GET["e"])) 			$_SESSION["filter_to_date"] = htmlspecialchars($_GET["e"]);
if(isset($_GET["l"])) 		$_SESSION["filter_location"] = htmlspecialchars($_GET["l"]);
if(isset($_GET["c"])) 		$_SESSION["filter_categories"] = htmlspecialchars($_GET["c"]);
if(isset($_GET["sc"])) 		$_SESSION["filter_subcategories"] = htmlspecialchars($_GET["sc"]);
if(isset($_GET["s"])) 			$_SESSION["filter_status"] = htmlspecialchars($_GET["s"]);
if(isset($_GET["p"])) 			$_SESSION["filter_publishing"] = htmlspecialchars($_GET["p"]);
if(isset($_GET["n"])) 	$_SESSION["filter_course_number"] = htmlspecialchars($_GET["n"]);
if(isset($_GET["se"])) 	$_SESSION["filter_student_email"] = htmlspecialchars($_GET["se"]);
if(isset($_GET["sv"])) 	$_SESSION["filter_student_vorname"] = htmlspecialchars($_GET["sv"]);
if(isset($_GET["sn"])) 	$_SESSION["filter_student_nachname"] = htmlspecialchars($_GET["sn"]);
if(isset($_GET["lvm"])) 	$_SESSION["filter_list_view_mode"] = htmlspecialchars($_GET["lvm"]);
// new
if(isset($_GET["cf"])) $_SESSION["filter_course_format"] = htmlspecialchars($_GET["cf"]);
if(isset($_GET["ct"])) $_SESSION["filter_course_type"] = htmlspecialchars($_GET["ct"]);
if(isset($_GET["cl"])) $_SESSION["filter_course_level"] = htmlspecialchars($_GET["cl"]);
/*
echo "Trainer: " . $_GET["t"] . "<br>";
echo "Standord: " . $_GET["l"] . "<br>";
echo "Kategorie: " . $_GET["c"] . "<br>";
echo "Status: " . $_GET["s"] . "<br>";
echo "Zeitraum: " . $_GET["z"] . "<br>";
echo "von: " . $_GET["b"] . "<br>";
echo "bis: " . $_GET["e"] . "<br>";
echo "Kursnummer: " . $_GET["n"] . "<br>"; */

$db_functions->courses->db_load_table_courses($_SESSION["filter_trainer"],
											  $_SESSION["filter_zeitraum"],
											  $_SESSION["filter_from_date"],
											  $_SESSION["filter_to_date"],
											  $_SESSION["filter_location"],
											  $_SESSION["filter_categories"],
											  $_SESSION["filter_subcategories"],
											  $_SESSION["filter_status"],
											  $_SESSION["filter_publishing"],
											  $_SESSION["filter_course_number"],
											  $_SESSION["filter_student_email"],
											  $_SESSION["filter_list_view_mode"],
											  $_SESSION["filter_course_format"], // new
											  $_SESSION["filter_course_type"],
											  $_SESSION["filter_course_level"],
											  false, // $_SESSION["filter_only_last_modified"],
											  false,  //$_SESSION["filter_only_todo"]
											  $_SESSION["filter_student_vorname"],
                                              $_SESSION["filter_student_nachname"]);
//echo ".";
?>