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

switch ($_SESSION["view"]) {

	case "login_view":
		require_once "view/login_view.php";
		break;
	case "main_menu":
		require_once "view/main_menu_view.php";
		break;
		// ***************** COURSES *****************
	case "course_list":
		if($_SESSION["view_mode"] == "simple") {
			require_once "view/courses/simple_course_list_view.php";
		}else {
			require_once "view/courses/course_list_view.php";
		}
		break;
	case "course_new":
		require_once "view/courses/course_detail_view.php";
		break;
	case "course_detail":
		require_once "view/courses/course_detail_view.php";
		break;
	case "course_detail_reload":
		if (isset($_SESSION["c_no_reload_data"]) ) unset($_SESSION["c_no_reload_data"]);
		$_SESSION["view"] = "course_detail";
		require_once "view/courses/course_detail_view.php";
		break;
	case "course_copy":
		require_once "view/courses/course_detail_view.php";
		break;
	case "course_new_from_event":
		require_once "view/courses/course_detail_view.php";
		break;
		// ***************** COURSES EXTRAS *****************
	case "attendance":
		require_once "view/courses_extras/attendance_view.php";
		break;
	case "safety_check":
		require_once "view/courses_extras/safety_check_view.php";
		break;
	case "course_notes":
		require_once "view/courses_extras/course_notes_view.php";
		break;
    case "trainer_payment":
        require_once "view/courses_extras/trainer_payment.php";
        break;
		// ***************** COURSE REGISTRATIONS *****************
	case "course_registrations":
		require_once "view/courses/course_registrations_view.php";
		break;
		// ***************** STUDENTS *****************
	case "student_list":
		require_once "view/students/student_list_view.php";
		break;
	case "student_detail":
		require_once "view/students/student_detail_view.php";
		break;
	case "student_detail_reload":
		if (isset($_SESSION["s_no_reload_data"]) ) unset($_SESSION["s_no_reload_data"]);
		$_SESSION["view"] = "student_detail";
		require_once "view/students/student_detail_view.php";
		break;
	case "student_new":
		require_once "view/students/student_detail_view.php";
		break;
	case "student_newsletter":
		require_once "view/students/student_newsletter_view.php";
		break;
		// ***************** STUDENTS V2 *****************
	case "student_list_v2":
		require_once "_v2/views/students/students_view.php";
		break;
    case "membership":
        require_once "_v2/views/membership/membership_view.php";
        break;
    case "voucherrequest":
        require_once "_v2/views/voucherrequest/voucherrequest_view.php";
        break;
	// ***************** COURSE CLASSIFICATIONS *****************
	 case "course_format":
		require_once "_v2/views/course_classifications/course_formats/course_formats_view.php";
		break;
	 case "course_type":
		require_once "_v2/views/course_classifications/course_types/course_types_view.php";
		break;
	 case "course_level":
		require_once "_v2/views/course_classifications/course_levels/course_levels_view.php";
		break;
		// ***************** CATEGORIES *****************
	case "category_list":
		require_once "view/categories/category_list_view.php";
		break;
	case "category_detail":
		require_once "view/categories/category_detail_view.php";
		break;
	case "category_detail_reload":		
		if (isset($_SESSION["ca_no_reload_data"]) ) unset($_SESSION["ca_no_reload_data"]);
		$_SESSION["view"] = "category_detail";
		require_once "view/categories/category_detail_view.php";
		break;
	case "category_new":
		require_once "view/categories/category_detail_view.php";
		break;
		// ***************** SUBCATEGORIES *****************
	case "subcategory_list":
		require_once "view/subcategories/subcategory_list_view.php";
		break;
	case "subcategory_detail":
		require_once "view/subcategories/subcategory_detail_view.php";
		break;
	case "subcategory_detail_reload":
		if (isset($_SESSION["sca_no_reload_data"]) ) unset($_SESSION["sca_no_reload_data"]);
		$_SESSION["view"] = "subcategory_detail";
		require_once "view/subcategories/subcategory_detail_view.php";
		break;
	case "subcategory_new":
		require_once "view/subcategories/subcategory_detail_view.php";
		break;
		// ***************** LOCATIONS *****************
	case "location_list":
		require_once "view/locations/location_list_view.php";
		break;
	case "location_detail":
		require_once "view/locations/location_detail_view.php";
		break;
	case "location_detail_reload":		
		if (isset($_SESSION["l_no_reload_data"]) ) unset($_SESSION["l_no_reload_data"]);
		$_SESSION["view"] = "location_detail";
		require_once "view/locations/location_detail_view.php";
		break;
		// ***************** NOTES *****************
	case "note_list":
		require_once "view/notes/note_list_view.php";
		break;
	case "note_detail":
		require_once "view/notes/note_detail_view.php";
		break;
	case "note_detail_reload":		
		if (isset($_SESSION["nt_no_reload_data"]) ) unset($_SESSION["nt_no_reload_data"]);
		$_SESSION["view"] = "note_detail";
		require_once "view/notes/note_detail_view.php";
		break;
	case "note_new":
		require_once "view/notes/note_detail_view.php";
		break;
		// ***************** BLOCKS *****************
	case "block_list":
		require_once "view/payment/blocks/block_list_view.php";
		break;
	case "block_detail":
		require_once "view/payment/blocks/block_detail_view.php";
		break;
	case "block_detail_reload":
		if (isset($_SESSION["block_no_reload_data"]) ) unset($_SESSION["block_no_reload_data"]);
		$_SESSION["view"] = "block_detail";
		require_once "view/payment/blocks/block_detail_view.php";
		break;
	case "block_new":
		require_once "view/payment/blocks/block_detail_view.php";
		break;
	default:
		$view_not_found1 = true;
		break;
}

if($_SESSION["user_is_admin"]) {
		
	switch ($_SESSION["view"])
	{	
		// ***************** USERS *****************
	case "user_list":
		require_once "view/users/user_list_view.php";
		break;
	case "user_new":
		require_once "view/users/user_detail_view.php";
		break;
	case "user_detail":
		require_once "view/users/user_detail_view.php";
		break;
	case "user_detail_reload":		
		if (isset($_SESSION["u_no_reload_data"]) ) unset($_SESSION["u_no_reload_data"]);
		$_SESSION["view"] = "user_detail";
		require_once "view/users/user_detail_view.php";
		break;
	case "user_change_password":
		require_once "view/users/user_detail_view.php";
		break;
	case "location_new":
		require_once "view/locations/location_detail_view.php";
		break;
	default:
		$view_not_found2 = true;
		break;
	}
}else {
	$view_not_found2 = true;
}


if($view_not_found1 && $view_not_found2) {
	echo "E-3033: Fehler - Die Anwendungs-Ansicht wurde nicht gefunden.<br><br>";
	require_once "view/main_menu_view.php";
	$_SESSION["view"] = "main_menu";
}
if(isset($_GET["w1"])) $_SESSION["view"] = "course_registrations";

?>