<?php
/* Coypright(2015) by Ing. Roman Breitschopf, BA 									*/
/* office@breitschopf.wien			www.breitschopf.wien 							*/
/* License for Jasmin Liska. Do not distribute to others without permission. */
//
// action management
//
defined("main-call") or die();

if (isset($_SESSION["action"])) {



	switch ($_SESSION["action"])
	{
		case "logout":
			$rb_functions->logout();
			break;
		// ***************** COURSES *****************
		case "reset_course_values":
			$rb_functions->reset_course_values();
			break;		
		case "c_reset_course_filter_options":
			$rb_functions->reset_course_filter_options();
			break;
		case "c_insert_course":
			if ($rb_functions->insert_new_course()) $_SESSION["view"] = "course_detail_reload";
			break;
		case "c_insert_single_courses":
			if ($rb_functions->insert_new_single_courses()) $_SESSION["view"] = "course_list";
			break;
		case "c_insert_course_back":
			if ($rb_functions->insert_new_course()) {
				$_SESSION["c_success_msg"] = false;
				if (isset($_SESSION["c_no_reload_data"]) ) unset($_SESSION["c_no_reload_data"]);
				$_SESSION["view"] = "course_list";
			} 
			break;
        case "c_insert_course_delete_old":
            if ($rb_functions->delete_old_course_and_insert()) $_SESSION["view"] = "course_detail_reload";
            break;
		case "c_insert_course_new":
			if ($rb_functions->insert_new_course()) {
				$rb_functions->reset_course_values();
				$_SESSION["view"] = "course_new";
			}
			break;
		case "c_update_course":
			if ($rb_functions->update_course()) $_SESSION["view"] = "course_detail";
			break;
		case "c_update_back":
			if ($rb_functions->update_course()) {
				$_SESSION["c_success_msg"] = false;
				if (isset($_SESSION["c_no_reload_data"]) ) unset($_SESSION["c_no_reload_data"]);
				$_SESSION["view"] = "course_list";
			}
			break;
		case "c_update_new":
			if ($rb_functions->update_course()) {
				$rb_functions->reset_course_values();
				$_SESSION["view"] = "course_new";
			}
			break;
		case "e_create_course_from_event":
			$rb_functions->reset_course_values();
			$rb_functions->set_values_event_to_course();
			$_SESSION["c_no_reload_data"] = 1;
			$_SESSION["view"] = "course_new_from_event";
			
			break;
		// ***************** COURSES EXTRAS*****************
		case "a_update_attendance":
			$rb_functions->update_attendance();
			break;
		case "a_apply_to_status":
			$_SESSION["view"] = "course_registrations";
			$rb_functions->apply_to_status();
			break;
		case "update_safety_check":
			$rb_functions->update_safety_check();
			break;
		case "reset_safety_check":
			$rb_functions->reset_safety_check();
			break;
		case "n_update_notes":
			if ($rb_functions->update_notes()) $_SESSION["n_edit"] = 0;
			break;
		case "n_set_todo":
			$rb_functions->update_course_todo(true);
			break;
		case "n_remove_todo":
			$rb_functions->update_course_todo(false);
			break;
        case "p_trainer_payment":
            $rb_functions->update_trainer_payment();
            break;
		
		// ***************** STUDENTS *****************
		case "reset_student_values":
			$rb_functions->reset_student_values();
			break;	
		case "s_reset_student_filter_options":
			$rb_functions->reset_student_filter_options();
			break;	
		case "reload":
			if (isset($_SESSION["s_no_reload_data"]) )unset($_SESSION["s_no_reload_data"]);    // CHECK WHY NOT FOR OTHERS
			break;
		case "s_insert_student":
			if ($rb_functions->insert_new_student()) $_SESSION["view"] = "student_detail";
			break;
		case "s_insert_student_back":
			if ($rb_functions->insert_new_student()) {
				$_SESSION["s_success_msg"] = false;
				if (isset($_SESSION["s_no_reload_data"]) ) unset($_SESSION["s_no_reload_data"]);
				$_SESSION["view"] = "student_list";
			} 
			break;
		case "s_insert_student_new":
			if ($rb_functions->insert_new_student()) {
				$rb_functions->reset_student_values();
				$_SESSION["view"] = "student_new";
			}
			break;
		case "s_update_student":
			if ($rb_functions->update_student()) $_SESSION["view"] = "student_detail";
			break;
		case "s_update_student_back":
			if ($rb_functions->update_student()) {
				$_SESSION["s_success_msg"] = false;
				if (isset($_SESSION["s_no_reload_data"]) ) unset($_SESSION["s_no_reload_data"]);
				$_SESSION["view"] = "student_list";
			}
			break;
		case "s_update_student_new":
			if ($rb_functions->update_student()) {
				$rb_functions->reset_student_values();
				$_SESSION["view"] = "student_new";
			}
			break;	
		case "s_registrate_student":
			$rb_functions->manual_insert_registration(	$_SESSION["sr_course_id"],
														$_SESSION["student_id"]);
			break;

		// ***************** CATEGORIES *****************
		case "reset_category_values":
			$rb_functions->reset_loaded_category_values();
			break;
		case "ca_insert_category":
			if ($rb_functions->insert_new_category()) $_SESSION["view"] = "category_detail";
			break;	
		case "ca_update_category":
			if ($rb_functions->update_category()) $_SESSION["view"] = "category_detail";
			break;
		case "ca_update_category_back":
			if ($rb_functions->update_category()) {
				$_SESSION["ca_success_msg"] = false;
				if (isset($_SESSION["ca_no_reload_data"]) ) unset($_SESSION["ca_no_reload_data"]);
				$_SESSION["view"] = "category_list";
			}
			break;
		case "ca_update_category_new":
			if ($rb_functions->update_category()) {
				$rb_functions->reset_loaded_category_values();
				$_SESSION["view"] = "category_new";
			}
			break;
		case "ca_insert_category_new":
			if ($rb_functions->insert_new_category()) {
				$rb_functions->reset_loaded_category_values();
				$_SESSION["view"] = "category_new";
			}
			break;
		case "ca_insert_category_back":
			if ($rb_functions->insert_new_category()) {
				$_SESSION["ca_success_msg"] = false;
				if (isset($_SESSION["ca_no_reload_data"]) ) unset($_SESSION["ca_no_reload_data"]);
				$_SESSION["view"] = "category_list";
			} 
			break;
		// ***************** SUBCATEGORIES *****************
		case "reset_subcategory_values":
			$rb_functions->reset_loaded_subcategory_values();
			break;
		case "sca_insert_subcategory":
			if ($rb_functions->insert_new_subcategory()) $_SESSION["view"] = "subcategory_detail";
			break;	
		case "sca_update_subcategory":
			if ($rb_functions->update_subcategory()) $_SESSION["view"] = "subcategory_detail";
			break;
		case "sca_update_subcategory_back":
			if ($rb_functions->update_subcategory()) {
				$_SESSION["sca_success_msg"] = false;
				if (isset($_SESSION["sca_no_reload_data"]) ) unset($_SESSION["sca_no_reload_data"]);
				$_SESSION["view"] = "subcategory_list";
			}
			break;
		case "sca_update_subcategory_new":
			if ($rb_functions->update_subcategory()) {
				$rb_functions->reset_loaded_subcategory_values();
				$_SESSION["view"] = "subcategory_new";
			}
			break;
		case "sca_insert_subcategory_new":
			if ($rb_functions->insert_new_subcategory()) {
				$rb_functions->reset_loaded_subcategory_values();
				$_SESSION["view"] = "subcategory_new";
			}
			break;
		case "sca_insert_subcategory_back":
			if ($rb_functions->insert_new_subcategory()) {
				$_SESSION["sca_success_msg"] = false;
				if (isset($_SESSION["sca_no_reload_data"]) ) unset($_SESSION["sca_no_reload_data"]);
				$_SESSION["view"] = "subcategory_list";
			} 
			break;
		
		
		
		// ***************** LOCATIONS *****************
		
		case "reset_location_values":
			$rb_functions->reset_loaded_location_values();
			break;	
		case "l_insert_location":
			if ($rb_functions->insert_new_location()) $_SESSION["view"] = "location_detail";
			break;	
		case "l_insert_location_back":
			if ($rb_functions->insert_new_location()) {
				$_SESSION["l_success_msg"] = false;
				if (isset($_SESSION["l_no_reload_data"]) ) unset($_SESSION["l_no_reload_data"]);
				$_SESSION["view"] = "location_list";
			} 
			break;
		case "l_insert_location_new":
			if ($rb_functions->insert_new_location()) {
				$rb_functions->reset_loaded_location_values();
				$_SESSION["view"] = "location_new";
			}
			break;
		case "l_update_location":
			if ($rb_functions->update_location()) $_SESSION["view"] = "location_detail";
			break;
		case "l_update_location_back":
			if ($rb_functions->update_location()) {
				$_SESSION["l_success_msg"] = false;
				if (isset($_SESSION["l_no_reload_data"]) ) unset($_SESSION["l_no_reload_data"]);
				$_SESSION["view"] = "location_list";
			}
			break;
		case "l_update_location_new":
			if ($rb_functions->update_location()) {
				$rb_functions->reset_loaded_location_values();
				$_SESSION["view"] = "location_new";
			}
			break;
		
		// ***************** REGISTRATIONS *****************
			
		case "r_manual_registration":
			$r_email_validation_for_registration = $rb_functions->validate_student_with_email();
			if(isset($r_email_validation_for_registration["positive_result"])) {
				$rb_functions->manual_insert_registration(
										$_SESSION["r_course_id"],
										$r_email_validation_for_registration["positive_result"]["student_id"]);
				$_SESSION["r_email"] = "";
			}
			$_SESSION["view"] = "course_registrations";
			break;
		case "r_update_registration":
			if ($rb_functions->update_registration()) $_SESSION["r_edit_id"] = 0;
			break;
		case "r_update_and_edit_next":
			if ($rb_functions->update_registration()) {
				if($_SESSION["r_edit_id"] == $_SESSION["r_next_edit_id"]) {
					$_SESSION["r_edit_id"] = 0;
				}else {
					$_SESSION["r_edit_id"] = $_SESSION["r_next_edit_id"];
				}
			}
			break;
		case "r_set_to_paid":
			$rb_functions->set_to_paid();
			break;
        case "r_set_payment_reminder":
            $rb_functions->set_payment_reminder();
            break;
        case "r_set_dunning":
            $rb_functions->set_dunning();
            break;
        case "r_set_waitlist":
            $rb_functions->set_waitlist();
            break;

        // ***************** NOTES *****************
		case "reset_note_values":
			$rb_functions->reset_loaded_note_values();
			break;
		case "nt_insert_note":
			if ($rb_functions->insert_new_note()) $_SESSION["view"] = "note_detail";
			break;	
		case "nt_update_note":
			if ($rb_functions->update_note()) $_SESSION["view"] = "note_detail";
			break;
		case "nt_update_note_back":
			if ($rb_functions->update_note()) {
				$_SESSION["nt_success_msg"] = false;
				if (isset($_SESSION["nt_no_reload_data"]) ) unset($_SESSION["nt_no_reload_data"]);
				$_SESSION["view"] = "note_list";
				$rb_functions->unlock_note();
			}
			break;
		case "nt_update_note_new":
			if ($rb_functions->update_note()) {
				$rb_functions->reset_loaded_note_values();
				$_SESSION["view"] = "note_new";
			}
			break;
		case "nt_insert_note_new":
			if ($rb_functions->insert_new_note()) {
				$rb_functions->reset_loaded_note_values();
				$_SESSION["view"] = "note_new";
			}
			break;
		case "nt_insert_note_back":
			if ($rb_functions->insert_new_note()) {
				$_SESSION["nt_success_msg"] = false;
				if (isset($_SESSION["nt_no_reload_data"]) ) unset($_SESSION["nt_no_reload_data"]);
				$_SESSION["view"] = "note_list";
			} 
			break;
		case "nt_unlock_note":
			$rb_functions->unlock_note();
			break;
		case "nt_unlock_note_manually":
			$rb_functions->unlock_note_manually();
			break;
		// ***************** BLOCKS *****************
		case "reset_block_values":
			$rb_functions->reset_loaded_block_values();
			break;	
		case "block_reset_filter_options":
			$rb_functions->reset_block_filter_options();
			break;
		case "block_insert":
			if ($rb_functions->insert_new_block()) $_SESSION["view"] = "block_detail";
			break;
		case "block_insert_back":
			if ($rb_functions->insert_new_block()) {
				$_SESSION["block_success_msg"] = false;
				if (isset($_SESSION["block_no_reload_data"]) ) unset($_SESSION["block_no_reload_data"]);
				$_SESSION["view"] = "block_list";
			} 
			break;
		case "block_insert_block_new":
			if ($rb_functions->insert_new_block()) {
				$rb_functions->reset_block_values();
				$_SESSION["view"] = "block_new";
			}
			break;
		case "block_update":
			if ($rb_functions->update_block()) $_SESSION["view"] = "block_detail";
			break;
		case "block_update_back":
			if ($rb_functions->update_block()) {
				$_SESSION["block_success_msg"] = false;
				if (isset($_SESSION["block_no_reload_data"]) ) unset($_SESSION["block_no_reload_data"]);
				$_SESSION["view"] = "block_list";
			}
			break;
		case "block_update_new":
			if ($rb_functions->update_block()) {
				$rb_functions->reset_block_values();
				$_SESSION["view"] = "block_new";
			}
			break;
	}

	if($_SESSION["user_is_admin"]) {
		
		switch ($_SESSION["action"])
		{		
			// ***************** USERS *****************
			case "reset_user_values":
				$rb_functions->reset_loaded_user_values();
				break;
			case "u_reset_user_filter_options":
				$rb_functions->reset_user_filter_options();
				break;
			case "u_insert_user":
				if ($rb_functions->insert_new_user()) $_SESSION["view"] = "user_detail";
				break;
			case "u_insert_user_new":
				if ($rb_functions->insert_new_user()) {
					$rb_functions->reset_loaded_user_values();
					$_SESSION["view"] = "user_new";
				}
				break;
			case "u_insert_user_back":
				if ($rb_functions->insert_new_user()) {
					$_SESSION["u_success_msg"] = false;
					if (isset($_SESSION["u_no_reload_data"]) ) unset($_SESSION["u_no_reload_data"]);
					$_SESSION["view"] = "user_list";
				} 
				break;
			case "u_update_user":
				if ($rb_functions->update_user()) $_SESSION["view"] = "user_detail";
				break;
			case "u_update_user_back":
				if ($rb_functions->update_user()) {
					$_SESSION["u_success_msg"] = false;
					if (isset($_SESSION["u_no_reload_data"]) ) unset($_SESSION["u_no_reload_data"]);
					$_SESSION["view"] = "user_list";
				}
				break;
			case "u_update_user_new":
				if ($rb_functions->update_user()) {
					$rb_functions->reset_loaded_user_values();
					$_SESSION["view"] = "user_new";
				}
				break;
			case "u_skip_pw_change":
				if (isset($_SESSION["u_no_reload_data"]) ) unset($_SESSION["u_no_reload_data"]);
				$_SESSION["view"] = "user_detail";
				break;
			case "u_change_password":
				if ($rb_functions->change_user_password()) {
					$_SESSION["view"] = "user_detail";
					unset($_SESSION["u_password"]);
				}
				unset($_SESSION["u_admin_pw"]);
				break;
			// ***************** EXTRAS *****************
				
			case "m_update_welcome_msg":
				$db_functions->extras->db_update_welcome_msg($_SESSION["m_welcome_msg"]);
				break;
		}
	}	
	unset($_SESSION["action"]);
}
?>