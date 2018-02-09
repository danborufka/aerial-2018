<?php
/* Coypright(2015) by JK Informatik*/
/* License for Jasmin Liska. Do not distribute to others without permission. */

// start with: booking/controller/cronjobs/update_preregistrations.php?code=1a4y834p96tj7433hxc1tj7435hxa5

session_start();
define('main-call', 'true');

require_once "../configuration.php";
require_once "../db_functions.php";
require_once "../rb_functions.php";
require_once "./db_functions_cronjobs.php";
require_once "../mail/mail_functions.php";
require_once "../mail/mail_configuration.php";


if (isset($_GET["code"]) && $_GET["code"] == "1a4y834p96tj7433hxc1tj7435hxa5") {

    //Get old Waitlist Registrations
    $old_waitlist = $db_functions_cronjobs->backoffice->get_storno_old_waitlist();
    foreach ($old_waitlist as $to_storno) {
        //Storno each registration
        if (!isset($_GET["course"]) || ($_GET["course"] == $to_storno['course_id'])) {
            $res = $db_functions_cronjobs->backoffice->set_storno_old_waitlist($to_storno["registration_id"]);
            if ($res == true) {
                //Write Mail to next on Waitlist
                $new_waitlist = $db_functions_cronjobs->backoffice->db_get_registrations($to_storno["course_id"], 1);
                foreach ($new_waitlist as $waitlist_member) {
                    $rb_functions->set_waitlist($waitlist_member["registration_id"]);
                    $mail_functions->send_system_mail($waitlist_member["registration_id"], "wait_list_place_available");
                }
            }
        }
    }

////Old Code
//    //Waitlist after 2 Days set to storno (expired)
//    $db_functions_cronjobs->backoffice->set_storno_old_waitlist();
//    //Calculate Nr open course space
//    $ret = $db_functions_cronjobs->backoffice->db_get_open_course_spaces();
//    foreach ($ret as $open_place) {
//        if (!isset($_GET["course"]) || ($_GET["course"] == $open_place['course_id'])) {
//            $open_waitlist = (intval($open_place["open_for_waitlist"]) * -1);
//            if ($open_waitlist > 0) {
//                $new_waitlist = $db_functions_cronjobs->backoffice->db_get_registrations($open_place["course_id"], $open_waitlist);
//                foreach ($new_waitlist as $waitlist_member) {
//                    $rb_functions->set_waitlist($waitlist_member["registration_id"]);
//                    $mail_functions->send_system_mail($waitlist_member["registration_id"], "wait_list_place_available");
//                }
//            }
//        }
//    }
}

?>