<?php
/* Coypright(2015) by Ing. Roman Breitschopf, BA 									*/
/* office@breitschopf.wien			www.breitschopf.wien 							*/
/* License for Jasmin Liska. Do not distribute to others without permission. */
session_start();
$rb_path_self = str_replace("controller/ajax_functions/send_system_mail_now.php", "", $_SERVER['PHP_SELF']);
define('main-call','true');

require_once "../configuration.php";

require_once "../db_functions.php";
require_once "../rb_functions.php";
require_once "../mail/mail_configuration.php";
require_once "../mail/mail_functions.php";

$rb_functions->check_login_processing();
$rb_functions->check_session_duration(300);
$rb_functions->check_login_is_ok_or_die_for_ajax_function();

if( !($_SESSION["user_is_organizer"] == "1" || $_SESSION["user_is_admin"] == "1")) {
	echo "Fehlende Berechtigung zum Versenden von System-Mails.";
	exit;
}

if( !(isset($_GET["registration_id"]) && is_numeric($_GET["registration_id"]))) {
	echo "Fehler";
	exit;
}
if( !(isset($_GET["type"]))) {
	echo "Fehler";
	exit;
}

if(!isset($_SESSION["timer"]) ||  // avoid duplicate e-mails within 1 second
	(isset($_SESSION["timer"]) && $_SESSION["timer"] < strtotime('now'))) {
	$output = $mail_functions->send_system_mail($_GET["registration_id"], $_GET["type"]);
	$_SESSION["timer"] = strtotime('now + 1 seconds');
}else {
	echo "E-Mail versendet!";
	exit;
}



if($output) {
	//echo "E-Mail versendet1!";
}else {
	//echo "E-Mail- Versand fehlgeschlagen1!";
}
echo "E-Mail versendet!";
?>