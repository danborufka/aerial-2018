<?php
/* Coypright(2015) by Ing. Roman Breitschopf, BA 									*/
/* office@breitschopf.wien			www.breitschopf.wien 							*/
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

    if(isset($_GET['debugging'])) {
        echo '<pre>';
        
        var_dump('done.');
        //var_dump($updated);
        echo '</pre>';
        exit;
    }
    
    $updated = $db_functions_cronjobs->backoffice->update_student_statii();            // Memberships abhängig von letzter Zahlung updaten

    $db_functions_cronjobs->backoffice->db_update_preregistrations_waitlist();  // Status vorgemerkt warteliste updaten


    $old_preregistrations = $db_functions_cronjobs->backoffice->db_get_old_preregistrations();
    foreach ($old_preregistrations as $to_storno) {
        //Storno each preregistration registration
            $res = $db_functions_cronjobs->backoffice->db_update_preregistration($to_storno["registration_id"]);
            if ($res == true) {
                //Write Mail to next on Waitlist
                $new_waitlist = $db_functions_cronjobs->backoffice->db_get_registrations($to_storno["course_id"], 1);
                foreach ($new_waitlist as $waitlist_member) {
                    $rb_functions->set_waitlist($waitlist_member["registration_id"]);
                    $mail_functions->send_system_mail($waitlist_member["registration_id"], "wait_list_place_available");
                }
            }

    }
    echo "done update preregistrations<br>";


    //Registration without Payment after 14 Days
    $open_registrations = $db_functions_cronjobs->backoffice->get_open_payments();
    $mailcontent = "Offene Zahlungen\r\n\r\n";
    $mailcontent = "Kurs\t\t Vorname\t Nachname\t Email \t Anmeldedatum". "\r\n";
    $ids = "";

    foreach ($open_registrations as $line) {
        $ids .= $line['reg_id'] . ",";
        $mailcontent .= $line['course_id'] . " " . $line['course_name'] . "\t";
        $mailcontent .= $line['prename'] . "\t" . $line['surname'] . "\t" . $line['email'] . "\t" . $line['date'] . "\r\n";
    }

    $ids = substr($ids, 0, -1);
    if (strlen($ids) > 0) {
        $db_functions_cronjobs->backoffice->set_mail_pay($ids);
        $mail_functions->send_backoffice_mail($mailcontent, "Offene Zahlungen");
    }
    echo "done open payments";

    //Open Payment Reminder after 5 Days
    $open_registrations = $db_functions_cronjobs->backoffice->get_open_payments_reminder();
    $mailcontent = "Offene Zahlungen nach Zahlungserinnerung\r\n\r\n";
    $mailcontent = "Kurs\t\t Vorname\t Nachname\t Email \t Anmeldedatum\r\n";
    $ids = "";

    foreach ($open_registrations as $line) {
        $ids .= $line['reg_id'] . ",";
        $mailcontent .= $line['course_id'] . " " . $line['course_name'] . "\t";
        $mailcontent .= $line['prename'] . "\t" . $line['surname'] . "\t" . $line['email'] . "\t" . $line['date'] . "\r\n";
    }

    $ids = substr($ids, 0, -1);
    if (strlen($ids) > 0) {
        $db_functions_cronjobs->backoffice->set_mail_payment_reminder($ids);
//        echo $mailcontent . "<br>";
        $mail_functions->send_backoffice_mail($mailcontent, "Offene Zahlungen nach Zahlungserinnerung");
    }
    echo "done open payments 2";

    //Open Dunnings after 3
    $open_registrations = $db_functions_cronjobs->backoffice->get_open_dunnings();
    $mailcontent = "Offene Zahlungen nach Mahnung\r\n\r\n";
    $mailcontent = "Kurs\t\t Vorname\t Nachname\t Email \t Anmeldedatum\r\n";
    $ids = "";

    foreach ($open_registrations as $line) {
        $ids .= $line['reg_id'] . ",";
        $mailcontent .= $line['course_id'] . " " . $line['course_name'] . "\t";
        $mailcontent .= $line['prename'] . "\t" . $line['surname'] . "\t" . $line['email'] . "\t" . $line['date'] . "\t\r\n";
    }

    $ids = substr($ids, 0, -1);
    if (strlen($ids) > 0) {
        $db_functions_cronjobs->backoffice->set_mail_dunning($ids);
        $mail_functions->send_backoffice_mail($mailcontent, "Offene Zahlungen nach Mahnung");
    }
    echo "done open payments 3";

}

?>