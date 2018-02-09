<?php
/* Coypright(2015) by Ing. Roman Breitschopf, BA 									*/
/* office@breitschopf.wien			www.breitschopf.wien 							*/
/* License for Jasmin Liska. Do not distribute to others without permission. */
//
// view management
//
defined("main-call") or die();

$view_not_found1 = false;
$view_not_found2 = false;

if(!isset($_SESSION["cal_view"])) {
	$_SESSION["cal_view"] = "month_view";
}


switch ($_SESSION["cal_view"]) {

	case "month_view":
		require_once "view/cal_overview.php";
		break;
	case "event_new":
		require_once "view/events/event_detail_view.php";
		break;
	case "event_detail":
		require_once "view/events/event_detail_view.php";
		break;
	case "event_detail_reload":
		if (isset($_SESSION["e_no_reload_data"]) ) unset($_SESSION["e_no_reload_data"]);
		$_SESSION["cal_view"] = "event_detail";
		require_once "view/events/event_detail_view.php";
		break;
	default:
		$view_not_found1 = true;
		require_once "view/cal_overview.php";
		break;
}

if($_SESSION["user_is_admin"]) {
		
	switch ($_SESSION["view"])
	{	
		// ***************** USERS *****************
	case "user_list":
	default:
		$view_not_found2 = true;
		break;
	}
}else {
	$view_not_found2 = true;
}


if($view_not_found1 && $view_not_found2) {
	echo "E-3034: Fehler - Die Anwendungs-Ansicht wurde nicht gefunden.<br><br>";
	require_once "view/main_menu_view.php";
	$_SESSION["view"] = "main_menu";
}

?>