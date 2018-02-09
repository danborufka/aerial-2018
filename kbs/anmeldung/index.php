<?php
/* Coypright(2015) by Ing. Roman Breitschopf, BA 									*/
/* office@breitschopf.wien			www.breitschopf.wien 							*/
/* License for Jasmin Liska. Do not distribute to others without permission.        */

session_start();
$rb_path_self = str_replace("index.php", "", $_SERVER['PHP_SELF']);
define('main-call', 'true');

require_once "../booking/controller/configuration.php";
require_once "controller/db_public_functions.php";

require_once "../booking/controller/mail/mail_configuration.php";
require_once "../booking/controller/mail/mail_functions.php";

require_once "../booking/controller/rb_functions.php";
require_once "../booking/controller/db_functions.php";

$error_message = "";
$error = false;
$result_message = false;
$process_finished = false;
$title2 = 'Anmeldung für';
if (!((isset($_GET["confirm"]) || isset($_GET["unsubscribe"]) || isset($_GET["waitlist"]) || isset($_GET["coursechange"]))
    && isset($_GET["s"]) && isset($_GET["c"]))
) {

    if (!(isset($_GET["kurs"]))) {  //  init
        $_GET["kurs"] = 0;
    } else {
        $_GET["kurs"] = htmlspecialchars($_GET["kurs"]);
    }
    if (!(isset($_GET["code"]))) {
        $_GET["code"] = 0;
    } else {
        $_GET["code"] = htmlspecialchars($_GET["code"]);
    }

    if (isset($_POST["anmeldung_submit"])) {  // ########    Server-side validation
        if (empty($_POST["kid_name"])) {
            $error_message .= "Bitte Vornamen des Kindes angeben.<br/>";
            $error = true;
        } else {
            $_POST["kid_name"] = htmlspecialchars($_POST["kid_name"]);
        }
        if (empty($_POST["prename"])) {
            $error_message .= "Bitte Vornamen angeben.<br/>";
            $error = true;
        } else {
            $_POST["prename"] = htmlspecialchars($_POST["prename"]);
        }
        if (empty($_POST["surname"])) {
            $error_message .= "Bitte Nachnamen angeben.<br/>";
            $error = true;
        } else {
            $_POST["surname"] = htmlspecialchars($_POST["surname"]);
        }
        if (empty($_POST["email"]) || !filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
            $error_message .= "Bitte gültige Email angeben.<br/>";
            $error = true;
        } else {
            $_POST["email"] = htmlspecialchars($_POST["email"]);
        }
        if (!isset($_POST["agb_akzeptiert"])) {
            $error_message .= "Für eine erfolgreiche Kursanmeldung müssen die AGB akzeptiert werden.<br/>";
            $error = true;
        }

        // ########   RECAPTCHA    ############

        /*if(isset($_POST["g-recaptcha-response"])) {

            $sec = "6LcV7Q4TAAAAANNYIBpoaZO5tYlF4iVaE3bJceqY";
            $ip = $_SERVER["REMOTE_ADDR"];
            $cap = $_POST["g-recaptcha-response"];
            $rsp = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$sec&response=$cap&remoteip=$ip");

            $result=json_decode($rsp, true);
            if(isset($result["success"]) && $result["success"]) {
                //sucess
            }else {
                $error_message .= "Bitte bestätige, dass du kein Roboter bist.<br/>";
                $error = true;
            }

        }else {
            $error_message .= "Bitte bestätige, dass du kein Roboter bist.<br/>";
            $error = true;
        }*/

        if ($error_message != "") $error_message .= "<br/>";

        if (isset($_POST["newsletter"])) {
            $newsletter = true;
        } else {
            $newsletter = false;
        }                         // ########    validation end

        if (!$error) {    // check if validation failed

            $student = $db_public_functions->public_registration->public_db_get_student($_POST["email"]);

            if (!$student) {
                $student = $db_public_functions->public_registration->public_db_insert_new_student($_POST["prename"], $_POST["surname"], $_POST["email"], $newsletter);
            }

            if (!$student) {
                $result_message = "Die Anmeldung ist leider fehlgeschlagen.";
            } elseif ($student["status"] == 4) {
                $result_message = "Die Anmeldung mit dieser E-Mail- Adresse ist leider nicht möglich."; // Student ist gesperrt
            } else {
                if (isset($student["prename"]) && $student["prename"] == '#Vorname') { // Name unvollständig -> befülle Name
                    $db_public_functions->public_registration->public_db_update_student($student["email"], $_POST["prename"], $_POST["surname"], 1);
                }
                if ($newsletter && !$student["newsletter"]) {
                    $db_public_functions->public_registration->public_db_subscribe_for_newsletter($student["email"]);
                }
                $kursdaten = array('error' => 'E7592 - unerwarteter Fehler');
                $kursdaten = $db_public_functions->public_registration->public_db_load_course($_GET["kurs"], $_GET["code"], true);
                if ($kursdaten['error'] == true) {
                    $result_message = $kursdaten['error'];
                } else {
                    if (isset($_SERVER['REMOTE_ADDR'])) {
                        $ip_address = $_SERVER['REMOTE_ADDR'];
                    } else {
                        $ip_address = "unknown";
                    }
                    $result = $db_public_functions->public_registration->public_db_insert_new_registration($student["student_id"], $_GET["kurs"], $ip_address, $_POST["kid_name"]);
                    if ($result['error'] == true) {
                        $result_message = $result['error'];
                    } else {
                        if ($result['status'] == 1) {
                            $title2 = "Erfolgreich vorgemerkt!";
                            $mail_functions->send_system_mail($result["registration_id"], "verification");
                            $result_message = "Deine Anmeldung wurde vorgemerkt. <b>Bitte bestätige innerhalb von 4 Stunden den Verifizierungs- Link</b>, den du per E-Mail erhalten hast, damit wir dir deinen reservierten Platz garantieren können.";
                        } elseif ($result['status'] == 4) {
                            $title2 = "Für Warteliste vorgemerkt!";
                            $mail_functions->send_system_mail($result["registration_id"], "wait_list_verification");
                            $result_message = "Du wurdest für die Warteliste vorgemerkt. <b>Bitte bestätige innerhalb von 4 Stunden den Verifizierungs- Link</b>, den du per E-Mail erhalten hast, sonst verfällt dein Platz auf der Warteliste.";
                        } else {
                            $result_message = "Fehler E-0816. Bitte versuche es später erneut";
                        }
                    }
                }
            }
        }
    }
} else if (isset($_GET["unsubscribe"])) {
    // ########   unsubscribe process ##############
    $title2 = "Abmeldung";
    $unsubscribe_result = $db_public_functions->public_registration->public_db_unsubscribe_registration($_GET["c"], $_GET["s"], $_GET["unsubscribe"]);
    if (isset($unsubscribe_result["msg"]) && isset($unsubscribe_result["result"])) {
        $result_message = $unsubscribe_result["msg"];
        $title2 = $unsubscribe_result["title"];
        if ($unsubscribe_result["result"] == true) {
            $new_waitlist = $db_public_functions->public_registration->db_get_registrations($_GET["c"], 1);
            foreach ($new_waitlist as $waitlist_member) {
                $rb_functions->set_waitlist($waitlist_member["registration_id"]);
                $mail_functions->send_system_mail($waitlist_member["registration_id"], "wait_list_place_available");
            }
        }
    } else {
        $result_message = "Abmeldung konnte nicht durchgeführt werden.";
    }

} else if (isset($_GET["waitlist"])) {
    $confirm_result = $db_public_functions->public_registration->public_db_waitlist($_GET["c"], $_GET["s"], $_GET["waitlist"]);
    $title2 = "Anmeldung fehlgeschlagen";
    if (isset($confirm_result["msg"]) && isset($confirm_result["result"])) {
        $result_message = $confirm_result["msg"];
        if ($confirm_result["result"] == true && ($confirm_result["status"] == 2 || $confirm_result["status"] == 3)) {
            $title2 = "Bestätigung der Anmeldung";
            if (isset($confirm_result["only_cash_allowed"]) && $confirm_result["only_cash_allowed"]) {
                $mail_functions->send_system_mail($confirm_result["registration_id"], "standard_confirmation");
                $only_cash_allowed = $confirm_result["only_cash_allowed"];
            } else {
                $mail_functions->send_system_mail($confirm_result["registration_id"], "regular_payment");
            }
        } else if (isset($confirm_result["already_used"]) && $confirm_result["already_used"]) {
            $title2 = "Anmeldung bereits durchgeführt.";
        }

    } else {
        $result_message = "Anmeldung konnte nicht durchgeführt werden.";
    }
} else if (isset($_GET["coursechange"])) {
    $confirm_result = $db_public_functions->public_registration->public_db_change_course($_GET["coursechange"], $_GET["s"], $_GET["c"]);
    $title2 = "Tausch fehlgeschlagen";
    if (isset($confirm_result["old_registration_id"]) && isset($confirm_result["new_registration_id"])) {
        $title2 = "Tausch erfolgreich Bestätigungsmail versandt";
        $result_message = "Tausch erfolgreich";
        //Mail an alte ID
        $mail_functions->send_system_mail($confirm_result["old_registration_id"], "coursechange_unsubscribe");
        //Mail an neue ID
        $mail_functions->send_system_mail($confirm_result["new_registration_id"], "coursechange_finish");
    } else if (isset($confirm_result["error"])) {
        $title2 = $confirm_result["error"];
        $result_message = "Tausch konnte nicht durchgeführt werden.";
    } else {
        $result_message = "Tausch konnte nicht durchgeführt werden.";
    }
} else {
    // ########   confirmation process ##############
    $title2 = "Bestätigung fehlgeschlagen";
    $confirm_result = $db_public_functions->public_registration->public_db_confirm_registration($_GET["c"], $_GET["s"], $_GET["confirm"]);
    if (isset($confirm_result["msg"]) && isset($confirm_result["result"])) {
        $result_message = $confirm_result["msg"];
        if ($confirm_result["result"] == true) {
            if ($confirm_result["status"] == 2 || $confirm_result["status"] == 3) {
                $title2 = "Bestätigung der Anmeldung";
                if (isset($confirm_result["only_cash_allowed"]) && $confirm_result["only_cash_allowed"]) {
                    $mail_functions->send_system_mail($confirm_result["registration_id"], "standard_confirmation");
                    $only_cash_allowed = $confirm_result["only_cash_allowed"];
                } else {
                    $mail_functions->send_system_mail($confirm_result["registration_id"], "regular_payment");
                }
            } elseif ($confirm_result["status"] == 5) {
                $title2 = "Bestätigung Warteliste";
                $mail_functions->send_system_mail($confirm_result["registration_id"], "wait_list_confirmation");
            }
        } else {
            if (isset($confirm_result["already_used"]) && $confirm_result["already_used"]) {
                $title2 = "Bestätigung bereits durchgeführt.";
            }
        }
    } else {
        $result_message = "Bestätigung konnte nicht durchgeführt werden.";
    }
}

?>
<!DOCTYPE html>
<head>
    <title>Aerial Silk Vienna</title>
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700' rel='stylesheet' type='text/css'>
    <meta name="viewport" content="width=device-width"/>
    <meta name="author" content="Ing. Roman Breitschopf, BA">
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <link rel="stylesheet" href="./css/anmeldung.css" type="text/css"/>
    <link rel="shortcut icon" href="./favicon.ico" type="image/x-icon"/>
    <script src="../lib/jquery-ui.min.js"></script>
    <script src="../lib/external/jquery/jquery.js"></script>
    <?php /*<script src="https://www.google.com/recaptcha/api.js" async defer></script> */ ?>
</head>
<body>
<div id="header">
    <a href="http://www.aerialsilks.at"><img id="img-logo" src="images/logo.png"></a>
    <div id="header-container-mobile">
        <h1>Aerial Silk Vienna</h1>
        <a href="http://www.aerialsilks.at"><img id="img-logo-mobile" src="images/logo_mobile.png"></a>
    </div>
</div>
<div id="registration-box-layer-behind">
    <div id="registration-box-layer-top">
        <div id="course-description">
            <h2><?= $title2 ?></h2> <?php // Anmeldung für ?>
            <?php
            $kursdaten = array('error' => 'E7591 - unerwarteter Fehler');
            if ((isset($_GET["confirm"]) || isset($_GET["unsubscribe"]) || isset($_GET["waitlist"]) || isset($_GET["coursechange"]))
                && isset($_GET["s"]) && isset($_GET["c"])
            ) {
                $kursdaten = $db_public_functions->public_registration->public_db_load_course($_GET["c"], 0, false);
            } else {
                $kursdaten = $db_public_functions->public_registration->public_db_load_course($_GET["kurs"], $_GET["code"], true);
            }
            $show_form = true;
            $show_course_data = true;
            if ($kursdaten['error']) {
                $show_form = false;
                if (!isset($kursdaten['show_data'])) $kursdaten['show_data'] = false;
                $show_course_data = $kursdaten['show_data'];
            }
            ?>
            <?php if ($show_course_data) { ?>

                <ul>
                    <li><?= $kursdaten['kursname'] ?></li>
                    <li><? echo(($kursdaten['one_date_only'] == 1) ? "Datum" : "Beginn"); ?>
                        : <?= $kursdaten['begin'] ?></li>
                    <li>Uhrzeit: <?= $kursdaten['time'] ?></li>
                    <li>Trainer: <?= $kursdaten['trainer'] ?></li>
                    <? if (!($kursdaten['one_date_only'] == 1)) echo "<li>Termine: " . $kursdaten['termine'] . "</li>"; ?>
                    <? if ($kursdaten['location_id'] == 3 || $kursdaten['location_id'] == 4) { ?>
                        <li>Ort: <b><?= $kursdaten['ort'] ?></b></li>
                    <? } else { ?>
                        <li>Ort: <?= $kursdaten['ort'] ?></li>
                    <? } ?>
                    <li>Kursbeitrag: <?= $kursdaten['price'] ?> €</li>
                    <?php if (!empty($kursdaten['precondition'])) { ?>
                        <li>Voraussetzungen: <?= $kursdaten['precondition'] ?></li>
                    <?php } ?>
                    <li><b><?= $kursdaten['ausgebucht'] ?></b></li>
                </ul>
                <?php if ($kursdaten['textblock_mode'] != 1 || empty($kursdaten['textblock'])) {
                    echo nl2br("<ul><li>" . $kursdaten['textblock'] . "</ul></li>");
                }
            }
            if ($kursdaten['error']) echo $kursdaten['error'];
            if ($result_message == true) {
                echo "<br/>" . $result_message;
                $show_form = false;
            }
            if ($show_form == false) echo "<br/><br/><br/><a class='rb-button' href='http://www.aerialsilks.at'>Zurück zur Homepage</a><br/><br/>";

            ?>
        </div>
        <? if ($show_form) {
            if (!(isset($_POST["prename"]))) $_POST["prename"] = "";
            if (!(isset($_POST["surname"]))) $_POST["surname"] = "";
            if (!(isset($_POST["email"]))) $_POST["email"] = "";
            if (!(isset($_POST["kid_name"]))) $_POST["kid_name"] = "";
            ?>
            <div style="color: red">
                <?= $error_message ?>
            </div>
            <form action="?kurs=<?= $_GET["kurs"] ?>&code=<?= $_GET["code"] ?>" name="registration-form" method="post">
                <? if (isset($kursdaten['is_kid_course']) && $kursdaten['is_kid_course'] == 1) { ?>
                    <input type="text" name="kid_name" id="kid_name" class="rb-input" placeholder="Vorname des Kindes"
                           value=<?= $_POST["kid_name"] ?>>
                    <? $placeholder_vorname = "Vorname der Ansprechperson";
                    $placeholder_nachname = "Nachname der Ansprechperson";
                } else { ?>
                    <input type="hidden" name="kid_name" id="kid_name" value="-">
                    <? $placeholder_vorname = "Vorname";
                    $placeholder_nachname = "Nachname";
                } ?>
                <input type="text" name="prename" id="prename" class="rb-input"
                       placeholder="<?= $placeholder_vorname ?>" required value=<?= $_POST["prename"] ?>>
                <input type="text" name="surname" id="surname" class="rb-input"
                       placeholder="<?= $placeholder_nachname ?>" required value=<?= $_POST["surname"] ?>>
                <input type="email" name="email" id="email" class="rb-input" placeholder="Email" required
                       value=<?= $_POST["email"] ?>>
                <input type="hidden" name="kurs" class="rb-input" value=<?= $_GET["kurs"] ?>>
                <input type="hidden" name="code" class="rb-input" value=<?= $_GET["code"] ?>>
                <div id="newsletter-row">
                    <div style="display:block">
                        <input type="checkbox" name="newsletter" checked style="display:inline-block; float:left">
                        <div class="checkbox-text">Newsletter abonnieren</div>
                    </div>
                    <div style="display:block">
                        <input type="checkbox" name="agb_akzeptiert" style="display:inline-block; float:left">
                        <div class="checkbox-text"><a href="http://aerialsilks.at/index.php?lang=de&fetch=page3_4"
                                                      target="_blank">AGB</a> gelesen und akzeptiert
                        </div>
                    </div>
                </div>
                <?php /*<div class="g-recaptcha" data-sitekey="6LcV7Q4TAAAAAM1mv4VoUOee1Xb05RXI2_KXVB05"></div> */
                ?>

                <input type="submit" name="anmeldung_submit"
                       value="<? echo (isset($kursdaten['ausgebucht']) && $kursdaten['ausgebucht'] == 'AUSGEBUCHT') ? 'für Warteliste anmelden' : 'Anmelden' ?>">
            </form>
            <?
        } ?>
    </div>
</div>
<!--<div id="developed-by" class="clearfix">-->
<!--    <a href="http://www.breitschopf.wien" target="_blank">Kursbuchungssystem entwickelt von Breitschopf IT Solutions</a>-->
<!--</div>-->

<img id="img-gregor" src="images/overlay_gregor.png">

<script>
    $("#img-gregor").css({
        position: "absolute",
        left: "8%",
        top: "-115px"
    });
    $(window).load(function () {
        function start_animation() {

            $("#img-gregor").animate({
                top: "+=15",
                height: "show"
            }, 900, function () {
                // Animation complete.
            }).delay(300).animate({
                top: "+=100",
                height: "show"
            }, 300, function () {
                // Animation complete.
            });
        };
        <? if (isset($_POST["anmeldung_submit"])) {
        unset($_POST["anmeldung_submit"]);
    } else {?>
        if (!window.matchMedia('(max-width: 900px)').matches) {
            start_animation();
        }
        ;
        <? } ?>
    });
</script>
</body>
</html>