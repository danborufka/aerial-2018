<?php
/* Coypright(2015) by Ing. Roman Breitschopf, BA 									*/
/* office@breitschopf.wien			www.breitschopf.wien 							*/
/* License for Jasmin Liska. Do not distribute to others without permission. */
session_start();    // for processing $_POST and submits!
$rb_path_self = str_replace("process.php", "", $_SERVER['PHP_SELF']);
define('main-call', 'true');
require "controller/configuration.php";
require "controller/db_functions.php";
if (isset($_POST["login"]) && $_POST["login"] == "processing") {
    $_SESSION["login"] = "processing";
}
if (isset($_SESSION["login"]) || $_SESSION["login"] == "processing") {
    if (isset($_POST["username"]) &&
        $_POST["username"] != '' &&
        isset($_POST["password"])
    ) {
        $db_functions->db_process_login($_POST["username"], $_POST["password"]);
    }
}
if (isset($_SESSION["login"]) || $_SESSION["login"] == "ok") {


    if (isset($_POST["filter_only_last_modified"])) {
        $_SESSION["filter_only_last_modified"] = true;
    } else {
        $_SESSION["filter_only_last_modified"] = false;
    }
    if (isset($_POST["filter_only_todo"])) {
        $_SESSION["filter_only_todo"] = 1;
    } else {
        $_SESSION["filter_only_todo"] = 0;
    }

    if (isset($_POST["s_filter_prename"])) $_SESSION["s_filter_prename"] = htmlspecialchars($_POST["s_filter_prename"], ENT_QUOTES, "UTF-8");
    if (isset($_POST["s_filter_surname"])) $_SESSION["s_filter_surname"] = htmlspecialchars($_POST["s_filter_surname"], ENT_QUOTES, "UTF-8");
    if (isset($_POST["s_filter_email"])) $_SESSION["s_filter_email"] = htmlspecialchars($_POST["s_filter_email"], ENT_QUOTES, "UTF-8");
    if (isset($_POST["s_filter_status"])) $_SESSION["s_filter_status"] = htmlspecialchars($_POST["s_filter_status"], ENT_QUOTES, "UTF-8");
    if (isset($_POST["s_filter_limit"])) $_SESSION["s_filter_limit"] = htmlspecialchars($_POST["s_filter_limit"], ENT_QUOTES, "UTF-8");

    if (isset($_POST["reg"])) $_SESSION["r_edit_id"] = htmlspecialchars($_POST["reg"], ENT_QUOTES, "UTF-8");
    foreach (array('filter_trainer',
                 'filter_from_date',
                 'filter_to_date',
                 'filter_location',
                 'filter_categories',
                 'filter_subcategories',
                 'filter_zeitraum',
                 'filter_status',
                 'filter_publishing',
                 'filter_course_number',
                 'filter_student_email',
                 'filter_list_view_mode',
                 'filter_course_format',
                 'filter_course_type',
                 'filter_course_level',

                 'block_filter_prename',
                 'block_filter_surname',
                 'block_filter_email',
                 'block_filter_status',
                 'block_filter_pay_status',
                 'block_filter_consumption_status',

                 'block_prename',
                 'block_surname',
                 'block_email',
                 'block_student_id',
                 'block_size',
                 'block_status',
                 'block_pay_status',
                 'block_consumption_status',
                 'block_consumption_count',
                 'block_remark',
                 'block_id',

                 'u_filter_prename',
                 'u_filter_surname',
                 'u_filter_login_name',
                 'u_filter_status',
                 'u_filter_search_code',
                 'u_filter_limit',
                 'u_filter_only_last_modified',
                 'u_admin_pw',

                 'l_filter_name',
                 'l_filter_status',

                 'location_id',
                 'l_short_name',
                 'l_location_name',
                 'l_sort_no',
                 'l_street',
                 'l_street_number',
                 'l_street',
                 'l_plz',
                 'l_ort',
                 'l_status',

                 'registration_id',
                 'r_in_rank',
                 'r_in_status',
                 'r_in_public_remark',
                 'r_in_private_remark',
                 'r_in_kid_name',
                 'r_price_payed',

                 'n_edit',
                 'n_next_edit',
                 'n_public_remark',
                 'n_private_remark',

                 'ca_filter_name',
                 'ca_filter_status',

                 'ca_name',
                 'ca_sort_no',
                 'ca_has_sub_cat',
                 'ca_status',
                 'ca_no_reload_data',

                 'sca_filter_categories',
                 'sca_filter_status',
                 'sca_name',
                 'sca_sort_no',
                 'sca_category',
                 'sca_status',
                 'sca_pictures',
                 'sca_filename_picture_1',
                 'sca_filename_picture_2',
                 'sca_filename_picture_3',
                 'sca_description',
                 'sca_no_reload_data',
                 'sca_is_kid_course',
                 'sca_auto_unsubscribe',
                 'sca_conf_text',

                 'user_id',
                 'u_login_name',
                 'u_prename',
                 'u_surname',
                 'u_email',
                 'u_trainer',
                 'u_organizer',
                 'u_admin',
                 'u_status',
                 'u_password',
                 'u_no_reload_data',

                 'm_welcome_msg',

                 'safety_termin_nr',

                 'nt_name',
                 'nt_status',
                 'nt_note_text',
                 'nt_filter_name',
                 'nt_filter_status',
                 'nt_no_reload_data',

                 'c_course_format',
                 'c_course_type',
                 'c_course_level',
                 'c_to1',
                 'c_from1',
                 'c_to2',
                 'c_from2',
                 'c_to3',
                 'c_from3',
                 'c_to4',
                 'c_from4',
                 'c_to5',
                 'c_from5',
                 'c_to6',
                 'c_from6',
                 'c_to7',
                 'c_from7',
                 'c_to8',
                 'c_from8',
                 'c_to9',
                 'c_from9',
                 'c_to10',
                 'c_from10',
                 'c_to11',
                 'c_from11',
                 'c_to12',
                 'c_from12',
                 'c_times_equal',

             ) as $var_name) {
        if (isset($_POST["$var_name"])) $_SESSION["$var_name"] = htmlspecialchars($_POST["$var_name"], ENT_QUOTES, "UTF-8");
    }


    if (isset($_POST["course_id"])) $_SESSION["course_id"] = htmlspecialchars($_POST["course_id"], ENT_QUOTES, "UTF-8");
    if (isset($_POST["c_name"])) $_SESSION["c_name"] = htmlspecialchars($_POST["c_name"], ENT_QUOTES, "UTF-8");
    if (isset($_POST["c_trainer"])) $_SESSION["c_trainer"] = htmlspecialchars($_POST["c_trainer"], ENT_QUOTES, "UTF-8");
    if (isset($_POST["c_trainer2"])) $_SESSION["c_trainer2"] = htmlspecialchars($_POST["c_trainer2"], ENT_QUOTES, "UTF-8");
    if (isset($_POST["c_location"])) $_SESSION["c_location"] = htmlspecialchars($_POST["c_location"], ENT_QUOTES, "UTF-8");
    if (isset($_POST["c_count"])) $_SESSION["c_count"] = htmlspecialchars($_POST["c_count"], ENT_QUOTES, "UTF-8");
    if (isset($_POST["c_category"])) $_SESSION["c_category"] = htmlspecialchars($_POST["c_category"], ENT_QUOTES, "UTF-8");
    if (isset($_POST["c_subcategory"])) $_SESSION["c_subcategory"] = htmlspecialchars($_POST["c_subcategory"], ENT_QUOTES, "UTF-8");
    if (isset($_POST["c_status"])) $_SESSION["c_status"] = htmlspecialchars($_POST["c_status"], ENT_QUOTES, "UTF-8");
    if (isset($_POST["c_price"])) {
        $_SESSION["c_price"] = str_replace(',', '.', $_POST["c_price"]);
        $_SESSION["c_price"] = htmlspecialchars($_SESSION["c_price"], ENT_QUOTES, "UTF-8");
    }
    if (isset($_POST["c_note1"])) $_SESSION["c_note1"] = htmlspecialchars($_POST["c_note1"], ENT_QUOTES, "UTF-8");
    if (isset($_POST["c_note2"])) $_SESSION["c_note2"] = htmlspecialchars($_POST["c_note2"], ENT_QUOTES, "UTF-8");
    if (isset($_POST["c_conf_text"])) $_SESSION["c_conf_text"] = htmlspecialchars($_POST["c_conf_text"], ENT_QUOTES, "UTF-8");
    if (isset($_POST["c_duration"])) $_SESSION["c_duration"] = htmlspecialchars($_POST["c_duration"], ENT_QUOTES, "UTF-8");
    if (isset($_POST["c_time"])) {
        if (strlen($_POST["c_time"]) == 2) $_POST["c_time"] = $_POST["c_time"] . ':00';
        $_SESSION["c_time"] = htmlspecialchars($_POST["c_time"], ENT_QUOTES, "UTF-8");
    }
    if (isset($_POST["c_date1"])) $_SESSION["c_date1"] = htmlspecialchars($_POST["c_date1"], ENT_QUOTES, "UTF-8");
    if (isset($_POST["c_date2"])) $_SESSION["c_date2"] = htmlspecialchars($_POST["c_date2"], ENT_QUOTES, "UTF-8");
    if (isset($_POST["c_date3"])) $_SESSION["c_date3"] = htmlspecialchars($_POST["c_date3"], ENT_QUOTES, "UTF-8");
    if (isset($_POST["c_date4"])) $_SESSION["c_date4"] = htmlspecialchars($_POST["c_date4"], ENT_QUOTES, "UTF-8");
    if (isset($_POST["c_date5"])) $_SESSION["c_date5"] = htmlspecialchars($_POST["c_date5"], ENT_QUOTES, "UTF-8");
    if (isset($_POST["c_date6"])) $_SESSION["c_date6"] = htmlspecialchars($_POST["c_date6"], ENT_QUOTES, "UTF-8");
    if (isset($_POST["c_date7"])) $_SESSION["c_date7"] = htmlspecialchars($_POST["c_date7"], ENT_QUOTES, "UTF-8");
    if (isset($_POST["c_date8"])) $_SESSION["c_date8"] = htmlspecialchars($_POST["c_date8"], ENT_QUOTES, "UTF-8");
    if (isset($_POST["c_date9"])) $_SESSION["c_date9"] = htmlspecialchars($_POST["c_date9"], ENT_QUOTES, "UTF-8");
    if (isset($_POST["c_date10"])) $_SESSION["c_date10"] = htmlspecialchars($_POST["c_date10"], ENT_QUOTES, "UTF-8");
    if (isset($_POST["c_date11"])) $_SESSION["c_date11"] = htmlspecialchars($_POST["c_date11"], ENT_QUOTES, "UTF-8");
    if (isset($_POST["c_date12"])) $_SESSION["c_date12"] = htmlspecialchars($_POST["c_date12"], ENT_QUOTES, "UTF-8");
    if (isset($_POST["c_todo"])) $_SESSION["c_todo"] = htmlspecialchars($_POST["c_todo"], ENT_QUOTES, "UTF-8");
    if (isset($_POST["c_publishing"])) $_SESSION["c_publishing"] = htmlspecialchars($_POST["c_publishing"], ENT_QUOTES, "UTF-8");
    if (isset($_POST["c_no_reload_data"])) $_SESSION["c_no_reload_data"] = htmlspecialchars($_POST["c_no_reload_data"], ENT_QUOTES, "UTF-8");
    if (isset($_POST["c_not_on"])) $_SESSION["c_not_on"] = htmlspecialchars($_POST["c_not_on"], ENT_QUOTES, "UTF-8");
    if (isset($_POST["c_precondition"])) $_SESSION["c_precondition"] = htmlspecialchars($_POST["c_precondition"], ENT_QUOTES, "UTF-8");
    if (isset($_POST["c_textblock_mode"])) $_SESSION["c_textblock_mode"] = htmlspecialchars($_POST["c_textblock_mode"], ENT_QUOTES, "UTF-8");
    if (isset($_POST["c_textblock"])) $_SESSION["c_textblock"] = htmlspecialchars($_POST["c_textblock"], ENT_QUOTES, "UTF-8");

    if (isset($_POST["student_id"])) $_SESSION["student_id"] = htmlspecialchars($_POST["student_id"], ENT_QUOTES, "UTF-8");
    if (isset($_POST["s_prename"])) $_SESSION["s_prename"] = htmlspecialchars($_POST["s_prename"], ENT_QUOTES, "UTF-8");
    if (isset($_POST["s_surname"])) $_SESSION["s_surname"] = htmlspecialchars($_POST["s_surname"], ENT_QUOTES, "UTF-8");
    if (isset($_POST["s_email"])) $_SESSION["s_email"] = htmlspecialchars($_POST["s_email"], ENT_QUOTES, "UTF-8");
    if (isset($_POST["s_status"])) $_SESSION["s_status"] = htmlspecialchars($_POST["s_status"], ENT_QUOTES, "UTF-8");
    if (isset($_POST["s_status"])) $_SESSION["s_merged_to"] = htmlspecialchars($_POST["s_merged_to"], ENT_QUOTES, "UTF-8");
    if (isset($_POST["s_search_code"])) $_SESSION["s_search_code"] = htmlspecialchars($_POST["s_search_code"], ENT_QUOTES, "UTF-8");
    if (isset($_POST["s_student_remark"])) $_SESSION["s_student_remark"] = htmlspecialchars($_POST["s_student_remark"], ENT_QUOTES, "UTF-8");
    if (isset($_POST["s_newsletter"])) $_SESSION["s_newsletter"] = htmlspecialchars($_POST["s_newsletter"], ENT_QUOTES, "UTF-8");
    if (isset($_POST["s_no_reload_data"])) $_SESSION["s_no_reload_data"] = htmlspecialchars($_POST["s_no_reload_data"], ENT_QUOTES, "UTF-8");

    if (isset($_POST["r_is_expanded"])) $_SESSION["r_is_expanded"] = htmlspecialchars($_POST["r_is_expanded"], ENT_QUOTES, "UTF-8");
    if (isset($_POST["r_email"])) $_SESSION["r_email"] = htmlspecialchars($_POST["r_email"], ENT_QUOTES, "UTF-8");
    if (isset($_POST["r_rank"])) $_SESSION["r_rank"] = htmlspecialchars($_POST["r_rank"], ENT_QUOTES, "UTF-8");
    if (isset($_POST["r_course"])) $_SESSION["r_course_id"] = htmlspecialchars($_POST["r_course"], ENT_QUOTES, "UTF-8");
    if (isset($_POST["r_payment_reminder"])) $_SESSION["r_payment_reminder"] = htmlspecialchars($_POST["r_payment_reminder"], ENT_QUOTES, "UTF-8");
    if (isset($_POST["r_dunning"])) $_SESSION["r_dunning"] = htmlspecialchars($_POST["r_dunning"], ENT_QUOTES, "UTF-8");
    if(isset($_POST['r_is_present1'])){ $_SESSION["r_is_present1"] = '1';} else{ $_SESSION["r_is_present1"] = '0'; }
    if(isset($_POST['r_is_present2'])){ $_SESSION["r_is_present2"] = '1';} else{ $_SESSION["r_is_present2"] = '0'; }
    if(isset($_POST['r_is_present3'])){ $_SESSION["r_is_present3"] = '1';} else{ $_SESSION["r_is_present3"] = '0'; }
    if(isset($_POST['r_is_present4'])){ $_SESSION["r_is_present4"] = '1';} else{ $_SESSION["r_is_present4"] = '0'; }
    if(isset($_POST['r_is_present5'])){ $_SESSION["r_is_present5"] = '1';} else{ $_SESSION["r_is_present5"] = '0'; }
    if(isset($_POST['r_is_present6'])){ $_SESSION["r_is_present6"] = '1';} else{ $_SESSION["r_is_present6"] = '0'; }
    if(isset($_POST['r_is_present7'])){ $_SESSION["r_is_present7"] = '1';} else{ $_SESSION["r_is_present7"] = '0'; }
    if(isset($_POST['r_is_present8'])){ $_SESSION["r_is_present8"] = '1';} else{ $_SESSION["r_is_present8"] = '0'; }
    if(isset($_POST['r_is_present9'])){ $_SESSION["r_is_present9"] = '1';} else{ $_SESSION["r_is_present9"] = '0'; }
    if(isset($_POST['r_is_present10'])){ $_SESSION["r_is_present10"] = '1';} else{ $_SESSION["r_is_present10"] = '0'; }
    if(isset($_POST['r_is_present11'])){ $_SESSION["r_is_present11"] = '1';} else{ $_SESSION["r_is_present11"] = '0'; }
    if(isset($_POST['r_is_present12'])){ $_SESSION["r_is_present12"] = '1';} else{ $_SESSION["r_is_present12"] = '0'; }



    if (isset($_POST["view"])) $_SESSION["view"] = htmlspecialchars($_POST["view"], ENT_QUOTES, "UTF-8");
    if (isset($_POST["action"])) $_SESSION["action"] = htmlspecialchars($_POST["action"], ENT_QUOTES, "UTF-8");


    if (isset($_POST["action"]) && ($_POST["action"] == "a_update_attendance" || $_POST["action"] == "a_apply_to_status")) {
        unset($_SESSION["a_attendance_data"]);
        $a_data_row = null;

        $k = 0;
        while (++$k) {
            $var_name = "a_registration_id" . $k;
            if (isset($_POST[$var_name])) {
                $a_data_fields = null;
                $a_data_fields["registration_id"] = $_POST[$var_name];
                for ($i = 1; $i <= 12; $i++) {
                    $var_name2 = "a_attendance" . $k . "-" . $i;
                    if (isset($_POST[$var_name2])) {
                        $a_data_fields["a" . $i] = 1;
                    } else {
                        $a_data_fields["a" . $i] = 0;
                    }
                }
                $a_data_row[] = $a_data_fields;
            } else {
                $_SESSION["a_attendance_data"] = $a_data_row;
                break;
            }

        }
    }
    if (isset($_POST["action"]) && ($_POST["action"] == "p_trainer_payment")) {
        $paidSave = [];
        array_walk_recursive($_POST, function ($item, $key) use (&$paidSave) {
            $split = explode("-", $key);
            if (count($split) == 2) {
                if (substr($split[1], 0, 4) == 'paid') {
                    if (array_key_exists($split[0], $paidSave)) {
                        array_push($paidSave[$split[0]], $split[1]);
                    } else {
                        $paidSave[$split[0]] = [$split[1]];
                    }
                }
            }
        });
        $_SESSION["p_trainer_payment"] = $paidSave;
    }


} else {
    if (isset($_POST["username"])) $_SESSION["temp_username"] = htmlspecialchars($_POST["username"], ENT_QUOTES, "UTF-8");
}


if (!isset($_POST["focus"])) {
    $_POST["focus"] = "";
} else {
    $_POST["focus"] = "#" . $_POST["focus"];
}

if (isset($_POST["w1"])) {
    if (isset($_POST["c1"])) {
        header('Location: ' . str_replace("process.php", "", $_SERVER['PHP_SELF']) . '?w1=' . $_POST["w1"] . '&c1=' . $_POST["c1"]);
    } else {
        header('Location: ' . str_replace("process.php", "", $_SERVER['PHP_SELF']) . '?w1=' . $_POST["w1"]);
    }
} else {
    header('Location: ' . str_replace("process.php", "", $_SERVER['PHP_SELF']) . $_POST["focus"]);
}

?>