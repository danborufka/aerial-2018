<?php
/* Coypright(2015) by Ing. Roman Breitschopf, BA 									*/
/* office@breitschopf.wien			www.breitschopf.wien 							*/
/* License for Jasmin Liska. Do not distribute to others without permission. */
defined("main-call") or die("Error- RB_Functions");

class RB_Functions
{
    public function logout()
    {
        $_SESSION = array();
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), "", time() - 42000, "/");
        }
        session_destroy();
        header('Location: ' . str_replace("process.php", "", (str_replace("index.php", "", $_SERVER['PHP_SELF']))));
        exit;
    }


    public function check_login_processing()
    {
        if (isset($_SESSION["login"]) && $_SESSION["login"] == "processing") {
            if (isset($_POST["username"]) &&
                $_POST["username"] != '' &&
                isset($_POST["password"])
            ) {
                $db_functions->db_check_login($_POST["username"], $_POST["password"]);
            }
        }
    }

    public function check_login_is_ok_or_die()
    {
        if (!isset($_SESSION["login"]) || $_SESSION["login"] != "ok") {
            require "view/login_view.php";
            die();
        }
    }

    public function check_login_is_ok_or_die_for_ajax_function()
    {
        global $rb_path_self;
        if (!isset($_SESSION["login"]) || $_SESSION["login"] != "ok") {
            echo "Sitzung abgelaufen, erneuter Login erforderlich.<br><div style='margin-top: 7px'><a class='rb-button3' href='" . $rb_path_self . "'>Neuladen</a><div>";
            die();
        }
    }

    public function check_session_duration($max_duration_in_minutes)
    {

        if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 60 * $max_duration_in_minutes)) {
            $this->logout();
        } else {
            $_SESSION['LAST_ACTIVITY'] = time();
        }
    }


    // *************** BEGIN RESET FILTER VALUES ***************************

    public function reset_student_filter_options()
    {

        unset($_SESSION["s_filter_prename"]);
        unset($_SESSION["s_filter_surname"]);
        unset($_SESSION["s_filter_email"]);
        unset($_SESSION["s_filter_status"]);
        unset($_SESSION["s_filter_search_code"]);
        unset($_SESSION["s_filter_limit"]);
        unset($_SESSION["filter_only_last_modified"]);
    }

    public function reset_block_filter_options()
    {

        unset($_SESSION["block_filter_email"]);
        unset($_SESSION["block_filter_prename"]);
        unset($_SESSION["block_filter_surname"]);
        unset($_SESSION["block_filter_pay_status"]);
        unset($_SESSION["block_filter_consumption_status"]);
        unset($_SESSION["block_filter_status"]);
    }

    public function reset_course_filter_options()
    {

        unset($_SESSION["filter_trainer"]);
        unset($_SESSION["filter_from_date"]);
        unset($_SESSION["filter_to_date"]);
        unset($_SESSION["filter_location"]);
        unset($_SESSION["filter_categories"]);
        unset($_SESSION["filter_subcategories"]);
        unset($_SESSION["filter_zeitraum"]);
        unset($_SESSION["filter_status"]);
        unset($_SESSION["filter_publishing"]);
        unset($_SESSION["filter_course_number"]);
        unset($_SESSION["filter_student_email"]);
        unset($_SESSION["filter_list_view_mode"]);
    }


    public function reset_user_filter_options()
    {
        foreach (array('u_filter_prename',
                     'u_filter_surname',
                     'u_filter_login_name',
                     'u_filter_status',
                     'u_filter_search_code',
                     'u_filter_limit',
                     'u_filter_only_last_modified') as $var_name) {
            unset($_SESSION["$var_name"]);
        }
    }

    // *************** END RESET FILTER VALUES ***************************

    // *************** BEGIN RESET LOADED VALUES ***************************

    public function reset_location_filter_options()
    {

        unset($_SESSION["l_filter_name"]);
        unset($_SESSION["l_filter_status"]);
    }

    public function reset_loaded_location_values()
    {
        foreach (array('l_location_name',
                     'l_short_name',
                     'l_sort_no',
                     'l_street',
                     'l_street_number',
                     'l_plz',
                     'l_ort',
                     'l_status',) as $var_name) {
            unset($_SESSION["$var_name"]);
        }
    }


    public function reset_course_values()
    {

        unset($_SESSION["c_name"]);
        unset($_SESSION["c_trainer"]);
        unset($_SESSION["c_trainer2"]);
        unset($_SESSION["c_location"]);
        unset($_SESSION["c_count"]);
        unset($_SESSION["c_category"]);
        unset($_SESSION["c_subcategory"]);
        unset($_SESSION["c_course_format"]);
        unset($_SESSION["c_course_type"]);
        unset($_SESSION["c_course_level"]);
        unset($_SESSION["c_status"]);
        unset($_SESSION["c_price"]);
        unset($_SESSION["c_note1"]);
        unset($_SESSION["c_note2"]);
        unset($_SESSION["c_duration"]);
        unset($_SESSION["c_time"]);
        unset($_SESSION["c_date1"]);
        unset($_SESSION["c_date2"]);
        unset($_SESSION["c_date3"]);
        unset($_SESSION["c_date4"]);
        unset($_SESSION["c_date5"]);
        unset($_SESSION["c_date6"]);
        unset($_SESSION["c_date7"]);
        unset($_SESSION["c_date8"]);
        unset($_SESSION["c_date9"]);
        unset($_SESSION["c_date10"]);
        unset($_SESSION["c_date11"]);
        unset($_SESSION["c_date12"]);
        unset($_SESSION["c_times_equal"]);
        unset($_SESSION["c_to1"]);
        unset($_SESSION["c_to2"]);
        unset($_SESSION["c_to3"]);
        unset($_SESSION["c_to4"]);
        unset($_SESSION["c_to5"]);
        unset($_SESSION["c_to6"]);
        unset($_SESSION["c_to7"]);
        unset($_SESSION["c_to8"]);
        unset($_SESSION["c_to9"]);
        unset($_SESSION["c_to10"]);
        unset($_SESSION["c_to11"]);
        unset($_SESSION["c_to12"]);
        unset($_SESSION["c_from1"]);
        unset($_SESSION["c_from2"]);
        unset($_SESSION["c_from3"]);
        unset($_SESSION["c_from4"]);
        unset($_SESSION["c_from5"]);
        unset($_SESSION["c_from6"]);
        unset($_SESSION["c_from7"]);
        unset($_SESSION["c_from8"]);
        unset($_SESSION["c_from9"]);
        unset($_SESSION["c_from10"]);
        unset($_SESSION["c_from11"]);
        unset($_SESSION["c_from12"]);
        unset($_SESSION["c_todo"]);
        unset($_SESSION["c_publishing"]);
        unset($_SESSION["c_conf_text"]);
        unset($_SESSION["c_not_on"]);
        unset($_SESSION["c_precondition"]);
        unset($_SESSION["c_textblock_mode"]);
        unset($_SESSION["c_textblock"]);
    }

    public function reset_student_values()
    {

        unset($_SESSION["s_prename"]);
        unset($_SESSION["s_surname"]);
        unset($_SESSION["s_email"]);
        unset($_SESSION["s_newsletter"]);
        unset($_SESSION["s_status"]);
        unset($_SESSION["s_student_remark"]);
        unset($_SESSION["s_search_code"]);
    }

    public function reset_loaded_block_values()
    {

        unset($_SESSION["block_prename"]);
        unset($_SESSION["block_surname"]);
        unset($_SESSION["block_email"]);
        unset($_SESSION["block_student_id"]);
        unset($_SESSION["block_size"]);
        unset($_SESSION["block_status"]);
        unset($_SESSION["block_remark"]);
        unset($_SESSION["block_status"]);
        unset($_SESSION["block_pay_status"]);
        unset($_SESSION["block_consumption_count"]);
        unset($_SESSION["block_consumption_status"]);
    }


    public function reset_loaded_category_values()
    {
        unset($_SESSION["ca_name"]);
        unset($_SESSION["ca_sort_no"]);
        unset($_SESSION["ca_has_sub_cat"]);
        unset($_SESSION["ca_status"]);
    }


    public function reset_loaded_subcategory_values()
    {
        unset($_SESSION["sca_name"]);
        unset($_SESSION["sca_sort_no"]);
        unset($_SESSION["sca_category"]);
        unset($_SESSION["sca_status"]);
        unset($_SESSION["sca_is_kid_course"]);
        unset($_SESSION["sca_auto_unsubscribe"]);
        unset($_SESSION["sca_conf_text"]);
        unset($_SESSION["sca_pictures"]);
        unset($_SESSION["sca_filename_picture_1"]);
        unset($_SESSION["sca_filename_picture_2"]);
        unset($_SESSION["sca_filename_picture_3"]);
        unset($_SESSION["sca_description"]);
    }


    public function reset_loaded_user_values()
    {
        foreach (array('u_login_name',
                     'u_prename',
                     'u_surname',
                     'u_email',
                     'u_trainer',
                     'u_organizer',
                     'u_admin',
                     'u_status',
                     'u_password') as $var_name) {
            unset($_SESSION["$var_name"]);
        }
    }

    // *************** END RESET LOADED VALUES ***************************

    public function delete_old_course_and_insert()
    {
        global $db_functions;
        $db_functions->courses->remove_old_event($_SESSION["event_id"]);
        return $this->insert_new_course();
    }

    public function insert_new_course()
    {

        global $db_functions;
        return $db_functions->courses->db_insert_new_course($_SESSION["c_name"],
            $_SESSION["c_category"],
            $_SESSION["c_subcategory"],
            $_SESSION["c_course_format"],
            $_SESSION["c_course_type"],
            $_SESSION["c_course_level"],
            $_SESSION["c_trainer"],
            $_SESSION["c_trainer2"],
            $_SESSION["c_location"],
            $_SESSION["c_count"],
            $_SESSION["c_status"],
            $_SESSION["c_price"],
            $_SESSION["c_note1"],
            $_SESSION["c_note2"],
            $_SESSION["c_duration"],
            $_SESSION["c_time"],
            $_SESSION["c_date1"],
            $_SESSION["c_date2"],
            $_SESSION["c_date3"],
            $_SESSION["c_date4"],
            $_SESSION["c_date5"],
            $_SESSION["c_date6"],
            $_SESSION["c_date7"],
            $_SESSION["c_date8"],
            $_SESSION["c_date9"],
            $_SESSION["c_date10"],
            $_SESSION["c_date11"],
            $_SESSION["c_date12"],
            $_SESSION["c_times_equal"],
            $_SESSION["c_from1"],
            $_SESSION["c_from2"],
            $_SESSION["c_from3"],
            $_SESSION["c_from4"],
            $_SESSION["c_from5"],
            $_SESSION["c_from6"],
            $_SESSION["c_from7"],
            $_SESSION["c_from8"],
            $_SESSION["c_from9"],
            $_SESSION["c_from10"],
            $_SESSION["c_from11"],
            $_SESSION["c_from12"],
            $_SESSION["c_to1"],
            $_SESSION["c_to2"],
            $_SESSION["c_to3"],
            $_SESSION["c_to4"],
            $_SESSION["c_to5"],
            $_SESSION["c_to6"],
            $_SESSION["c_to7"],
            $_SESSION["c_to8"],
            $_SESSION["c_to9"],
            $_SESSION["c_to10"],
            $_SESSION["c_to11"],
            $_SESSION["c_to12"],
            $_SESSION["c_publishing"],
            $_SESSION["c_todo"],
            $_SESSION["c_conf_text"],
            $_SESSION["c_not_on"],
            $_SESSION["c_precondition"],
            $_SESSION["c_textblock_mode"],
            $_SESSION["c_textblock"],
            false);

    }

    public function insert_new_single_courses()
    {
        global $db_functions;
        return $db_functions->courses->db_insert_new_course($_SESSION["c_name"],
            $_SESSION["c_category"],
            $_SESSION["c_subcategory"],
            $_SESSION["c_course_format"],
            $_SESSION["c_course_type"],
            $_SESSION["c_course_level"],
            $_SESSION["c_trainer"],
            $_SESSION["c_trainer2"],
            $_SESSION["c_location"],
            $_SESSION["c_count"],
            $_SESSION["c_status"],
            $_SESSION["c_price"],
            $_SESSION["c_note1"],
            $_SESSION["c_note2"],
            $_SESSION["c_duration"],
            $_SESSION["c_time"],
            $_SESSION["c_date1"],
            $_SESSION["c_date2"],
            $_SESSION["c_date3"],
            $_SESSION["c_date4"],
            $_SESSION["c_date5"],
            $_SESSION["c_date6"],
            $_SESSION["c_date7"],
            $_SESSION["c_date8"],
            $_SESSION["c_date9"],
            $_SESSION["c_date10"],
            $_SESSION["c_date11"],
            $_SESSION["c_date12"],
            $_SESSION["c_times_equal"],
            $_SESSION["c_from1"],
            $_SESSION["c_from2"],
            $_SESSION["c_from3"],
            $_SESSION["c_from4"],
            $_SESSION["c_from5"],
            $_SESSION["c_from6"],
            $_SESSION["c_from7"],
            $_SESSION["c_from8"],
            $_SESSION["c_from9"],
            $_SESSION["c_from10"],
            $_SESSION["c_from11"],
            $_SESSION["c_from12"],
            $_SESSION["c_to1"],
            $_SESSION["c_to2"],
            $_SESSION["c_to3"],
            $_SESSION["c_to4"],
            $_SESSION["c_to5"],
            $_SESSION["c_to6"],
            $_SESSION["c_to7"],
            $_SESSION["c_to8"],
            $_SESSION["c_to9"],
            $_SESSION["c_to10"],
            $_SESSION["c_to11"],
            $_SESSION["c_to12"],
            $_SESSION["c_publishing"],
            $_SESSION["c_todo"],
            $_SESSION["c_conf_text"],
            $_SESSION["c_not_on"],
            $_SESSION["c_precondition"],
            $_SESSION["c_textblock_mode"],
            $_SESSION["c_textblock"],
            true);

    }

    public function update_course()
    {

        global $db_functions;
        return $db_functions->courses->db_update_course($_SESSION["course_id"],
            $_SESSION["c_name"],
            $_SESSION["c_category"],
            $_SESSION["c_subcategory"],
            $_SESSION["c_course_format"],
            $_SESSION["c_course_type"],
            $_SESSION["c_course_level"],
            $_SESSION["c_trainer"],
            $_SESSION["c_trainer2"],
            $_SESSION["c_location"],
            $_SESSION["c_count"],
            $_SESSION["c_status"],
            $_SESSION["c_price"],
            $_SESSION["c_note1"],
            $_SESSION["c_note2"],
            $_SESSION["c_duration"],
            $_SESSION["c_time"],
            $_SESSION["c_date1"],
            $_SESSION["c_date2"],
            $_SESSION["c_date3"],
            $_SESSION["c_date4"],
            $_SESSION["c_date5"],
            $_SESSION["c_date6"],
            $_SESSION["c_date7"],
            $_SESSION["c_date8"],
            $_SESSION["c_date9"],
            $_SESSION["c_date10"],
            $_SESSION["c_date11"],
            $_SESSION["c_date12"],
            $_SESSION["c_times_equal"],
            $_SESSION["c_from1"],
            $_SESSION["c_from2"],
            $_SESSION["c_from3"],
            $_SESSION["c_from4"],
            $_SESSION["c_from5"],
            $_SESSION["c_from6"],
            $_SESSION["c_from7"],
            $_SESSION["c_from8"],
            $_SESSION["c_from9"],
            $_SESSION["c_from10"],
            $_SESSION["c_from11"],
            $_SESSION["c_from12"],
            $_SESSION["c_to1"],
            $_SESSION["c_to2"],
            $_SESSION["c_to3"],
            $_SESSION["c_to4"],
            $_SESSION["c_to5"],
            $_SESSION["c_to6"],
            $_SESSION["c_to7"],
            $_SESSION["c_to8"],
            $_SESSION["c_to9"],
            $_SESSION["c_to10"],
            $_SESSION["c_to11"],
            $_SESSION["c_to12"],
            $_SESSION["c_publishing"],
            $_SESSION["c_todo"],
            $_SESSION["c_conf_text"],
            $_SESSION["c_not_on"],
            $_SESSION["c_precondition"],
            $_SESSION["c_textblock_mode"],
            $_SESSION["c_textblock"]);

    }

    public function set_values_event_to_course()
    {
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
            if (isset($_SESSION['e2c_' . $var_name])) $_SESSION[str_replace('e_', 'c_', $var_name)] = htmlspecialchars($_SESSION['e2c_' . $var_name], ENT_QUOTES);
        }
        if (isset($_SESSION["e2c_e_trainer1"])) $_SESSION["c_trainer"] = htmlspecialchars($_SESSION["e2c_e_trainer1"], ENT_QUOTES);
        if (isset($_SESSION["e2c_e_description"])) $_SESSION["c_note1"] = htmlspecialchars($_SESSION["e2c_e_description"], ENT_QUOTES);
        if (isset($_SESSION["e2c_e_from1"])) $_SESSION["c_time"] = htmlspecialchars($_SESSION["e2c_e_from1"], ENT_QUOTES);


        $times_equal = 1;
        for ($i = 2; $i <= 12; $i++) {
            if (!empty($_SESSION["c_from$i"]) && $_SESSION["c_from$i"] != $_SESSION["c_from1"]) {
                $times_equal = -1;
                break;
            }
            if (!empty($_SESSION["c_to$i"]) && $_SESSION["c_to$i"] != $_SESSION["c_to1"]) {
                $times_equal = -1;
                break;
            }
        }
        $_SESSION['c_times_equal'] = $times_equal;


        $time_from = DateTime::createFromFormat('d.m.Y H:i', '01.01.2015 ' . $_SESSION["c_from1"]);
        $time_to = DateTime::createFromFormat('d.m.Y H:i', '01.01.2015 ' . $_SESSION["c_to1"]);

        if ($time_from && $time_to) {
            $time_diff = $time_from->diff($time_to);
            $_SESSION["c_duration"] = $time_diff->i + 60 * $time_diff->h;
        }

    }

    public function insert_new_student()
    {

        global $db_functions;
        if ($_SESSION["s_status"] != 3) $_SESSION["s_merged_to"] = "";

        return $db_functions->students->db_insert_new_student($_SESSION["s_prename"],
            $_SESSION["s_surname"],
            $_SESSION["s_email"],
            $_SESSION["s_newsletter"],
            $_SESSION["s_status"],
            $_SESSION["s_merged_to"],
            $_SESSION["s_student_remark"],
            $_SESSION["s_search_code"]);
    }

    public function insert_new_block()
    {

        return get_db_functions_ext()->blocks->db_insert_new_block($_SESSION["block_email"],
            $_SESSION["block_size"],
            $_SESSION["block_pay_status"]);
    }


    public function update_student()
    {

        global $db_functions;
        if ($_SESSION["s_status"] != 3) $_SESSION["s_merged_to"] = "";

        return $db_functions->students->db_update_student($_SESSION["student_id"],
            $_SESSION["s_prename"],
            $_SESSION["s_surname"],
            $_SESSION["s_email"],
            $_SESSION["s_newsletter"],
            $_SESSION["s_status"],
            $_SESSION["s_merged_to"],
            $_SESSION["s_student_remark"],
            $_SESSION["s_search_code"]);
    }

    public function insert_new_user()
    {

        global $db_functions;

        return $db_functions->users->db_insert_new_user($_SESSION["u_login_name"],
            $_SESSION["u_prename"],
            $_SESSION["u_surname"],
            $_SESSION["u_email"],
            $_SESSION["u_trainer"],
            $_SESSION["u_organizer"],
            $_SESSION["u_admin"],
            $_SESSION["u_status"],
            $_SESSION["u_password"]);
    }

    public function update_user()
    {

        global $db_functions;
        return $db_functions->users->db_update_user($_SESSION["user_id"],
            $_SESSION["u_login_name"],
            $_SESSION["u_prename"],
            $_SESSION["u_surname"],
            $_SESSION["u_email"],
            $_SESSION["u_trainer"],
            $_SESSION["u_organizer"],
            $_SESSION["u_admin"],
            $_SESSION["u_status"]);
    }

    public function change_user_password()
    {

        global $db_functions;

        return $db_functions->users->change_user_password($_SESSION["user_id"],
            $_SESSION["u_password"],
            $_SESSION["u_admin_pw"]);
    }

    public function insert_new_category()
    {

        global $db_functions;

        return $db_functions->categories->db_insert_new_category($_SESSION["ca_name"],
            $_SESSION["ca_sort_no"],
            $_SESSION["ca_has_sub_cat"],
            $_SESSION["ca_status"]);
    }

    public function insert_new_subcategory()
    {

        global $db_functions;

        return $db_functions->subcategories->db_insert_new_subcategory($_SESSION["sca_name"],
            $_SESSION["sca_category"],
            $_SESSION["sca_sort_no"],
            $_SESSION["sca_status"],
            $_SESSION["sca_pictures"],
            $_SESSION["sca_filename_picture_1"],
            $_SESSION["sca_filename_picture_2"],
            $_SESSION["sca_filename_picture_3"],
            $_SESSION["sca_description"],
            $_SESSION["sca_is_kid_course"],
            $_SESSION["sca_auto_unsubscribe"],
            $_SESSION["sca_conf_text"]);
    }

    public function update_category()
    {

        global $db_functions;

        return $db_functions->categories->db_update_category($_SESSION["cat_id"],
            $_SESSION["ca_name"],
            $_SESSION["ca_sort_no"],
            $_SESSION["ca_has_sub_cat"],
            $_SESSION["ca_status"]);
    }


    public function update_subcategory()
    {

        global $db_functions;

        return $db_functions->subcategories->db_update_subcategory($_SESSION["subcat_id"],
            $_SESSION["sca_name"],
            $_SESSION["sca_category"],
            $_SESSION["sca_sort_no"],
            $_SESSION["sca_status"],
            $_SESSION["sca_pictures"],
            $_SESSION["sca_filename_picture_1"],
            $_SESSION["sca_filename_picture_2"],
            $_SESSION["sca_filename_picture_3"],
            $_SESSION["sca_description"],
            $_SESSION["sca_is_kid_course"],
            $_SESSION["sca_auto_unsubscribe"],
            $_SESSION["sca_conf_text"]);
    }


    public function validate_student_with_email()
    {

        global $db_functions;
        if (!isset($_SESSION["r_email"])) $_SESSION["r_email"] = "";

        if (!filter_var($_SESSION["r_email"], FILTER_VALIDATE_EMAIL)) {

            return array("negative_result" => "Bitte gültige E-Mail- Adresse eingeben.");
        }
        $result = $db_functions->students->db_get_student_with_email($_SESSION["r_email"]);

        if (!$result) return array("negative_result" => "Kein Eintrag vorhanden, bitte zunächst Teilnehmer hinzufügen.");

        if (isset($result["student_id"])) {
            if (isset($result["status"]) && $result["status"] == "1") {

                if ($db_functions->students->db_student_is_registrated($result["student_id"], $_SESSION["r_course_id"])) {
                    return array("negative_result" => "Der Kursteilnehmer ist bereits angemeldet.");
                } else {
                    return array("positive_result" => $result);
                }

            } else {
                return array("negative_result" => "Status des Kursteilnehmers muss aktiv sein.");
            }
        }

        return array("negative_result" => "E-1077: Unerwarteter Fehler.");

    }


    public function insert_new_location()
    {

        global $db_functions;

        return $db_functions->locations->db_insert_new_location($_SESSION["l_location_name"],
            $_SESSION["l_short_name"],
            $_SESSION["l_sort_no"],
            $_SESSION["l_street"],
            $_SESSION["l_street_number"],
            $_SESSION["l_plz"],
            $_SESSION["l_ort"],
            $_SESSION["l_status"]);
    }


    public function update_location()
    {

        global $db_functions;
        return $db_functions->locations->db_update_location($_SESSION["location_id"],
            $_SESSION["l_location_name"],
            $_SESSION["l_short_name"],
            $_SESSION["l_sort_no"],
            $_SESSION["l_street"],
            $_SESSION["l_street_number"],
            $_SESSION["l_plz"],
            $_SESSION["l_ort"],
            $_SESSION["l_status"]);
    }


    public function manual_insert_registration($p_course_id,
                                               $p_student_id,
                                               $p_ranking_options = "last")
    {
        global $db_functions;
        $p_status = 2;  // manual registration
        $save_voucher = null;
//        if ($db_functions->registrations->db_is_voucher_course($p_course_id)) {
//            $voucher = $db_functions->registrations->db_get_student_vouchers($p_student_id);
//            if ($voucher["amount_student"] > $voucher["amount_used"]) {
//                $save_voucher = $voucher["amount_used"] + 1 . "/" . $voucher["amount_student"];
//                $p_status = "3";
//            }
//        }
        return $db_functions->registrations->db_insert_new_registration($p_course_id,
            $p_student_id,
            $p_status,
            $p_ranking_options,
            $save_voucher);
    }

    public
    function update_registration()
    {
        global $db_functions;

        $saveVoucher = null;

        //When payed with OS selected
        if ($_SESSION["r_in_status"] == "80") {
            $_SESSION["r_in_status"] = '3';
            $old_registration = $db_functions->registrations->db_get_registration_voucher($_SESSION["registration_id"]);
            if ($db_functions->registrations->db_is_voucher_course($old_registration["course_id"])) {
                if (isset($old_registration["registration_id"])) {
                    //if new status = angemeldet
                    if ($_SESSION["r_in_status"] == "3" && $old_registration["voucher"] == null) {
                        $voucher = $db_functions->registrations->db_get_student_vouchers($old_registration["student_id"]);
                        if ($voucher["amount_student"] > $voucher["amount_used"]) {
                            $saveVoucher = $voucher["amount_used"] + 1 . "/" . $voucher["amount_student"];
                        }
                    } else if ($old_registration["voucher"] != null && $_SESSION["r_in_status"] == "3") {
                        $saveVoucher = $old_registration["voucher"];
                    }
                }
            }
        }

        return $db_functions->registrations->db_update_registration($_SESSION["registration_id"],
            $_SESSION["r_course_id"],
            $_SESSION["r_in_rank"],
            $_SESSION["r_in_status"],
            $_SESSION["r_in_public_remark"],
            $_SESSION["r_in_private_remark"],
            $_SESSION["r_in_kid_name"],
            $_SESSION["r_payment_reminder"],
            $_SESSION["r_dunning"],
            $_SESSION["r_price_payed"],
            $_SESSION["r_is_present1"],
            $_SESSION["r_is_present2"],
            $_SESSION["r_is_present3"],
            $_SESSION["r_is_present4"],
            $_SESSION["r_is_present5"],
            $_SESSION["r_is_present6"],
            $_SESSION["r_is_present7"],
            $_SESSION["r_is_present8"],
            $_SESSION["r_is_present9"],
            $_SESSION["r_is_present10"],
            $_SESSION["r_is_present11"],
            $_SESSION["r_is_present12"],
            $saveVoucher
        );
    }


    public
    function update_course_todo($p_todo)
    {
        global $db_functions;
        return $db_functions->courses_extras->db_update_course_todo($_SESSION["course_id"],
            $p_todo);
    }

    public
    function remove_todo()
    {
        global $db_functions;
        return $db_functions->courses_extras->db_remove_todo($_SESSION["course_id"]);
    }

    public
    function update_safety_check()
    {
        global $db_functions;
        return $db_functions->courses_extras->db_update_safety_check($_SESSION["course_id"],
            $_SESSION["safety_termin_nr"],
            $_SESSION["user_prename"],
            $_SESSION["user_surname"]);
    }

    public
    function reset_safety_check()
    {
        global $db_functions;
        return $db_functions->courses_extras->db_reset_safety_check($_SESSION["course_id"]);
    }

    public
    function set_to_paid()
    {
        global $db_functions;
        $db_functions->registrations->db_set_to_paid($_SESSION["r_pay_id"]);
    }

    public
    function set_payment_reminder()
    {
        global $db_functions;
        $db_functions->registrations->db_set_payment_reminder($_SESSION["r_pay_id"]);
    }

    public
    function set_dunning()
    {
        global $db_functions;
        $db_functions->registrations->db_set_dunning($_SESSION["r_pay_id"]);
    }

    public
    function set_waitlist($registration_id = null)
    {
        global $db_functions;
        if ($registration_id != null) {
            $db_functions->registrations->db_set_waitlist($registration_id);
        } else {
            $db_functions->registrations->db_set_waitlist($_SESSION["r_pay_id"]);
        }
    }


    public
    function update_attendance()
    {
        global $db_functions;
        $db_functions->courses_extras->db_update_attendance($_SESSION["a_attendance_data"]);
    }

    public
    function update_trainer_payment()
    {
        global $db_functions;
        $db_functions->courses_extras->db_update_trainer_payment($_SESSION["p_trainer_payment"]);
    }

    public
    function apply_to_status()
    {
        global $db_functions;
        $db_functions->courses_extras->db_apply_to_status($_SESSION["a_attendance_data"],
            $_SESSION["course_id"]);
    }

    public
    function ignore_android($p_text)
    {
        global $is_android_used;
        if ($is_android_used) {
            return "";
        } else {
            return $p_text;
        }
    }

    public
    function update_notes()
    {
        global $db_functions;
        return $db_functions->courses_extras->db_update_notes($_SESSION["n_edit"],
            $_SESSION["course_id"],
            $_SESSION["n_public_remark"],
            $_SESSION["n_private_remark"]);
    }


// #######  NOTES  ##########################

    public
    function reset_loaded_note_values()
    {
        unset($_SESSION["nt_name"]);
        unset($_SESSION["nt_note_text"]);
        unset($_SESSION["nt_status"]);
    }

    public
    function insert_new_note()
    {

        global $db_functions;
        return $db_functions->notes->db_insert_new_note($_SESSION["nt_name"],
            $_SESSION["nt_note_text"],
            $_SESSION["nt_status"]);
    }

    public
    function update_note()
    {
        global $db_functions;
        return $db_functions->notes->db_update_note($_SESSION["note_id"],
            $_SESSION["nt_name"],
            $_SESSION["nt_note_text"],
            $_SESSION["nt_status"]);
    }

    public
    function unlock_note()
    {
        global $db_functions;
        return $db_functions->notes->db_unlock_note($_SESSION["note_id"],
            $_SESSION["user_id"]);
    }

    public
    function unlock_note_manually()
    {
        global $db_functions;
        return $db_functions->notes->db_unlock_note_manually($_SESSION["note_id"]);
    }


}

$is_android_used = strpos($_SERVER['HTTP_USER_AGENT'], "ndroid");
$rb_functions = new RB_Functions();

?>