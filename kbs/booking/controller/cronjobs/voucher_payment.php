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

    $ret = $db_functions_cronjobs->backoffice->db_get_open_voucher_registrations();
    foreach ($ret as $voucher_registration) {
        $voucher = $db_functions->registrations->db_get_student_vouchers($voucher_registration["student_id"]);
        //If student of this registration has open voucher
        if ($voucher["amount_student"] > $voucher["amount_used"]) {
            $saveVoucher =  $voucher["amount_used"] + 1 . "/" . $voucher["amount_student"];
            //Save in registration && send Email
            $db_functions_cronjobs->backoffice->db_set_voucher_payment($voucher_registration["registration_id"], $saveVoucher);
            $mail_functions->send_system_mail($voucher_registration["registration_id"], "voucher_payment");
        }

    }
}

?>