<?php
/* Coypright(2015) by Ing. Roman Breitschopf, BA 									*/
/* office@breitschopf.wien			www.breitschopf.wien 							*/
/* License for Jasmin Liska. Do not distribute to others without permission. */
session_start();
$rb_path_self = str_replace("controller/ajax_functions/reload_students.php", "", $_SERVER['PHP_SELF']);
define('main-call','true');

require_once "../configuration.php";

require_once "../db_functions.php";
require_once "../rb_functions.php";

$rb_functions->check_login_processing();
$rb_functions->check_session_duration(30);
$rb_functions->check_login_is_ok_or_die_for_ajax_function();

if(isset($_GET["v"])) 		$_SESSION["s_filter_prename"] = htmlspecialchars($_GET["v"]);
if(isset($_GET["n"])) 		$_SESSION["s_filter_surname"] = htmlspecialchars($_GET["n"]);
if(isset($_GET["e"]))		$_SESSION["s_filter_email"]   = htmlspecialchars($_GET["e"]);
if(isset($_GET["s"])) 		$_SESSION["s_filter_status"]  = htmlspecialchars($_GET["s"]);

/*
echo "Vorname: " . $_GET["v"] . "<br>";
echo "Nachname: " . $_GET["n"] . "<br>";
echo "Email: " . $_GET["e"] . "<br>";
echo "Status: " . $_GET["s"] . "<br>";*/



$db_functions->students->db_load_table_students($_SESSION["s_filter_prename"],
												$_SESSION["s_filter_surname"],
												$_SESSION["s_filter_email"],
												$_SESSION["s_filter_status"]);


//echo ".";
?>