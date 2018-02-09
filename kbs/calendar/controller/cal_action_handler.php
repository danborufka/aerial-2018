<?php
/* Coypright(2015) by Ing. Roman Breitschopf, BA 									*/
/* office@breitschopf.wien			www.breitschopf.wien 							*/
/* License for Jasmin Liska. Do not distribute to others without permission. */
//
// action management
//
defined("main-call") or die();

if (isset($_SESSION["cal_action"])) {


	switch ($_SESSION["cal_action"])
	{
		case "logout":
			$cal_rb_functions->logout();
			break;
			
			// ***************** EVENTS *****************
			case "reset_event_values":
			$cal_rb_functions->reset_loaded_event_values();
			break;	
		case "e_insert_event":
			if ($cal_rb_functions->insert_new_event()) $_SESSION["cal_view"] = "event_detail_reload";
			break;
		case "e_insert_event_back":
			if ($cal_rb_functions->insert_new_event()) {
				$_SESSION["e_success_msg"] = false;
				if (isset($_SESSION["e_no_reload_data"]) ) unset($_SESSION["e_no_reload_data"]);
				$_SESSION["cal_view"] = "month_view";
			} 
			break;
		case "e_insert_event_new":
			if ($cal_rb_functions->insert_new_event()) {
				$cal_rb_functions->reset_loaded_event_values();
				$_SESSION["cal_view"] = "event_new";
			}
			break;
		case "e_update_event":
			if ($cal_rb_functions->update_event()) $_SESSION["cal_view"] = "event_detail_reload";
			break;
		case "e_update_back":
			if ($cal_rb_functions->update_event()) {
				$_SESSION["e_success_msg"] = false;
				if (isset($_SESSION["e_no_reload_data"]) ) unset($_SESSION["e_no_reload_data"]);
				$_SESSION["cal_view"] = "month_view";
			}
			break;
		case "e_update_new":
			if ($cal_rb_functions->update_event()) {
				$cal_rb_functions->reset_loaded_event_values();
				$_SESSION["cal_view"] = "event_new";
			}
			break;
	}

	if($_SESSION["user_is_admin"]) {
		
		switch ($_SESSION["cal_action"])
		{		
			
		case "e_disabled":
			if ($cal_rb_functions->update_event()) {
				$cal_rb_functions->reset_loaded_event_values();
				$_SESSION["cal_view"] = "event_new";
			}
			break;
		}

	}	
	unset($_SESSION["cal_action"]);
}
?>