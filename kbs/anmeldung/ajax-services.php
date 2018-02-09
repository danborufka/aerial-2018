<?php
define('main-call', 'true');

require_once dirname(dirname(__DIR__)) . "/kbs_frontend/inc/new_conf.php";
require_once dirname(dirname(__DIR__)) . "/kbs_frontend/inc/dbcourse.php";
require_once dirname(dirname(__DIR__)) . "/kbs_frontend/inc/result.php";
require_once dirname(dirname(__DIR__)) . "/kbs_frontend/inc/utilities.php";
require_once dirname(dirname(__DIR__)) . "/kbs_frontend/inc/rb_functions.php";


require_once "../booking/controller/configuration.php";
require_once "controller/db_public_functions.php";

require_once "../booking/controller/mail/mail_configuration.php";
require_once "../booking/controller/mail/mail_functions.php";


$cmd = $_GET['cmd'];

$allowed_commands = array('Courses.SubmitRegistration', 'Voucher.SubmitRequest');

if (in_array($cmd, $allowed_commands)) {
    call_user_func(str_replace('.', '', $cmd));
}

function VoucherSubmitRequest()
{
    $result = new Result();
    $parameterNames = array('voucher_amount',
        'voucher_price',
        'email',
        'terms_accepted');
    $parameter = utilities::initParameter($parameterNames);
    try {
        if (!intval($parameter["terms_accepted"])) {
            $result->errtxt = "Für eine erfolgreiche OS Block Anforderung müssen die AGB akzeptiert werden.";
            utilities::output($result);
        }
        if (empty($parameter["email"]) || !filter_var($parameter["email"], FILTER_VALIDATE_EMAIL)) {
            $result->errtxt = "Bitte gültige Email angeben.";
            utilities::output($result);
        } else {
            $parameter["email"] = htmlspecialchars($parameter["email"]);
        }

        global $db_public_functions;

        $student = $db_public_functions->public_registration->public_db_get_student($parameter["email"]);
        if (!isset($student["student_id"]) || $student["student_id"] == "") {
            $result->errtxt = "Email Adresse nicht gefunden.";
            utilities::output($result);
        }
        if ((!$student || $student["security_training"] == "0")) {
            $result->info = "Für das buchen eines Open Silk Blocks musst du ein registrierter Teilnehmer sein und unser Sicherheitstraining absolviert haben. 
                            Wenn du das Sicherheitstraining bereits absolviert hast und diese Meldung siehst\nmelde dich bitte mit der Bestätigung über das Sicherheitstraining unter 
                            office@aerialsilk.at.  
                            Ab dem 1.10.2017 kannst du sonst keine OS Blöcke mehr anfordern.";
        }
        //All Checks validated
        $dbReturn = false;
        switch ($parameter["voucher_amount"]) {
            case '10':
                $dbReturn = $db_public_functions->public_voucher_reguest->requestVoucher("10er OS Block", $student["student_id"], 10);
                break;
            case '20':
                $dbReturn = $db_public_functions->public_voucher_reguest->requestVoucher("20er OS Block", $student["student_id"], 20);
                break;
        }
        if ($dbReturn != true) {
            $result->errtxt = "Fehler bei Verarbeitung von Anfrage. " . $dbReturn;
            utilities::output($result);
        }

        $result->data = 'Open Silk Block erfolgreich angefordert. Wir haben dir eine Email mit den Zahlungsinformationen zugesendet.';
        $result->error = 0;
        utilities::output($result);

    } catch (Exception $e) {
    }
    utilities::output($result);
}

function CoursesSubmitRegistration()
{
    if (isset($_POST["newsletter"]) && $_POST["newsletter"] == "true") {
        $newsletter = true;
    } else {
        $newsletter = false;
    }
    $kid_name = "-";

    $result = new Result();
    $parameterNames = array('new_customer',
        'prename',
        'surname',
        'phone',
        'email',
        'terms_accepted',
        'course_id',
        'registration_code');
    $parameter = utilities::initParameter($parameterNames);
    try {
        // $dbCourse = new DbCourse();
// 		
        // $student_exist = $dbCourse->checkStudent($parameter['email']);
// 		
        // if( ! $student_exist) {
        // $student = $dbCourse->createNewStudent($parameter);
        // }else {
        // $student = $dbCourse->getStudent($parameter['email']);
        // }
        // $result = $dbCourse->registrateCourse($parameter['course_id'], $parameter['email']);
        $parameter["new_customer"] = intval($parameter["new_customer"]);

        if ($parameter["new_customer"] && empty($parameter["prename"])) {
            $result->errtxt = "Bitte Vornamen angeben.";
            utilities::output($result);
        } else {
            $parameter["prename"] = htmlspecialchars($parameter["prename"]);
        }
        if ($parameter["new_customer"] && empty($parameter["surname"])) {
            $result->errtxt = "Bitte Nachnamen angeben.";
            utilities::output($result);
        } else {
            $parameter["surname"] = htmlspecialchars($parameter["surname"]);
        }
        if (empty($parameter["email"]) || !filter_var($parameter["email"], FILTER_VALIDATE_EMAIL)) {
            $result->errtxt = "Bitte gültige Email angeben.";
            utilities::output($result);
        } else {
            $parameter["email"] = htmlspecialchars($parameter["email"]);
        }
        if (!intval($parameter["terms_accepted"])) {
            $result->errtxt = "Für eine erfolgreiche Kursanmeldung müssen die AGB akzeptiert werden.";
            utilities::output($result);
        }


        global $db_public_functions;


        $student = $db_public_functions->public_registration->public_db_get_student($parameter["email"]);


        $kursdaten = $db_public_functions->public_registration->public_db_load_course($parameter["course_id"],
            $parameter["registration_code"],
            true);

//        $success_message = var_dump($kursdaten) . " " . var_dump($student);
//        $result->data = $success_message;
//        $result->error = 0;
//        utilities::output($result);
        if ($kursdaten["security_training"] == "1" && (!$student || $student["security_training"] == "0")) {
            //Disable error Message until 1.9.2017
//            $success_message = "Für diesen Kurs musst du ein registrierter Teilnehmer sein und unser Sicherheitstraining absolviert haben.<br/>" .
//                "Solltest du das Sicherheitstraining bereits absolviert haben melde dich unter office@aerialsilk.at.";

            $result->info = "Für diesen Kurs musst du ein registrierter Teilnehmer sein und unser Sicherheitstraining absolviert haben. 
                            Wenn du das Sicherheitstraining bereits absolviert hast und diese Meldung siehst\nmelde dich bitte mit der Bestätigung über das Sicherheitstraining unter 
                            office@aerialsilk.at.  
                            Ab dem 1.10.2017 kannst du dich sonst nicht mehr für einen Open Silk Kurs anmelden.";
//            $result->data = $success_message;
//            $result->error = 0;
//            utilities::output($result);
        }

        if (!$student) {

            if (!$parameter["new_customer"]) {
                global $mail_functions;
                $mail_functions->send_system_no_new_customer($parameter["email"]);
                $success_message = "Deine Anmeldung wurde vorgemerkt. <b>Bitte bestätige innerhalb von 4 Stunden " .
                    "den Verifizierungs-Link</b>, den du per E-Mail erhalten hast, damit wir dir " .
                    "deinen reservierten Platz garantieren können.";
                $result->data = $success_message;
                $result->error = 0;
                utilities::output($result);

            }

            $student = $db_public_functions->public_registration->public_db_insert_new_student($parameter["prename"],
                $parameter["surname"],
                $parameter["email"],
                $newsletter);
        }

        if (isset($student["status"]) && $student["status"] == 4) {
            $success_message = "Dein Account ist gesperrt, die Anmeldung ist leider fehlgeschlagen. Falls dein Account versehentlich gesperrt wurde, dann melde dich bitte per Email an office@aerialsilk.at";
            $result->data = $success_message;
            $result->error = 0;
            utilities::output($result);
        }


        if (isset($student["prename"]) && $student["prename"] == '#Vorname') { // Name unvollständig -> befülle Name
            if (!$parameter["new_customer"]) {
                global $mail_functions;
                $mail_functions->send_system_no_new_customer($parameter["email"]);
                $success_message = "Deine Anmeldung wurde vorgemerkt. <b>Bitte bestätige innerhalb von 4 Stunden " .
                    "den Verifizierungs-Link</b>, den du per E-Mail erhalten hast, damit wir dir " .
                    "deinen reservierten Platz garantieren können.";
                $result->data = $success_message;
                $result->error = 0;
                utilities::output($result);

            }
            $db_public_functions->public_registration->public_db_update_student($student["email"], $parameter["prename"], $parameter["surname"], 1);
        }
        if ($newsletter && !$student["newsletter"]) {
            $db_public_functions->public_registration->public_db_subscribe_for_newsletter($student["email"]);
        }


        if ($kursdaten['error'] == true) {
            $result->errtxt = $kursdaten['error'];
        } else {
            if (isset($_SERVER['REMOTE_ADDR'])) {
                $ip_address = $_SERVER['REMOTE_ADDR'];
            } else {
                $ip_address = "unknown";
            }
            $registration_result = $db_public_functions->public_registration->public_db_insert_new_registration($student["student_id"],
                $parameter["course_id"], $ip_address, $kid_name);
            if ($registration_result['error'] == true) {
                $result->errtxt = $registration_result['error'];
                utilities::output($result);
            } else {
                if ($registration_result['status'] == 1) {
                    $title2 = "Erfolgreich vorgemerkt!";
                    global $mail_functions;
                    $mail_functions->send_system_mail($registration_result["registration_id"],
                        "verification");
                    $success_message = "Deine Anmeldung wurde vorgemerkt. <b>Bitte bestätige innerhalb von 4 Stunden " .
                        "den Verifizierungs- Link</b>, den du per E-Mail erhalten hast, damit wir dir " .
                        "deinen reservierten Platz garantieren können.";
                    $result->data = $success_message;
                    $result->error = 0;
                } elseif ($registration_result['status'] == 4) {
                    $title2 = "Für Warteliste vorgemerkt!";
                    global $mail_functions;
                    $mail_functions->send_system_mail($registration_result["registration_id"],
                        "wait_list_verification");

                    $result->data = "Du wurdest für die Warteliste vorgemerkt. <b>Bitte bestätige innerhalb von 4 Stunden " .
                        "den Verifizierungs- Link</b>, den du per E-Mail erhalten hast, sonst verfällt dein Platz " .
                        "auf der Warteliste.";
                    $result->error = 0;
                } else {
                    $result->errtxt = "Fehler. Bitte versuche es später erneut";
                    utilities::output($result);
                }
            }
        }

    } catch (Exception $e) {
    }
    utilities::output($result);
}


$result = new Result();
$result->errtxt = "Die Anfrage >$cmd< konnte nicht identifiziert werden.";
utilities::output($result);
?>
