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

if ((isset($_GET["code"]))) {
    $details = $db_public_functions->public_coursechange->public_db_getdetails(($_GET["code"]));
} else {
    $result_message = "Ungültiger Link";
}

if (isset($_POST["coursechange_mail"]) && isset($_GET["code"])) {
    $result = $db_public_functions->public_coursechange->public_db_validatemember($_POST["coursechange_mail"], $_GET["code"]);
    if (isset($result["success"]) && $result["success"] == true) {
        $mail_functions->send_mail_course_change($_GET["code"], $result["email"], $result["student_id"]);
    }
    if (isset($result["security"])) {
        $result_message = $result["security"];
    } else {
        $result_message = $result["error"];
    }
}

//if ((isset($_POST["unsubscribe_mail"]))) {
//    if (!empty($_POST["unsubscribe_mail"])) {
//        $unsubscribe_result = $db_public_functions->public_unsubsribe->public_db_unsubscribe($_POST["unsubscribe_mail"]);
//        if (isset($unsubscribe_result["error"])) {
//            $result_message = $unsubscribe_result["error"] . "<br/>Fehler bei Abmeldung";
//        } else if (isset($unsubscribe_result["alreadyunsubscribed"])) {
//            $result_message = "Bereits von Newsletter abgemeldet";
//        } else {
//            $result_message = "Abmeldung erfolgreich";
//        }
//    }
//
//    unset($_POST["unsubscribe_mail"]);
//}

?>
<!DOCTYPE html>
<head>
    <title>Aerial Silk Vienna</title>
    <link rel="stylesheet" type="text/css"
          href="https://fonts.googleapis.com/css?family=Open+Sans:100,100italic,200,200italic,300,300italic,400,400italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic">
    <meta name="viewport" content="width=device-width"/>
    <meta name="author" content="JK Informatik">
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <link rel="stylesheet" href="css/coursechange.css" type="text/css"/>
    <link rel="shortcut icon" href="./favicon.ico" type="image/x-icon"/>
    <script src="../lib/jquery-ui.min.js"></script>
    <script src="../lib/external/jquery/jquery.js"></script>
</head>
<body>
<div id="header">
    <a href="http://www.aerialsilks.at"><img id="img-logo" src="../res/images/logo.png"></a>
</div>

<div id="coursechange-box">
    <h3>OS Kurs tauschen</h3>
    <br/>
    <?php
    if (isset($details)) {
        if (isset($details["course_member"])) {
            echo "Mitglied : " . $details["course_member"] . "</br>" .
                "Kurs : " . $details["course_title"] . "</br>" .
                "Datum : " . $details["course_date"] . "</br>" .
                "Ort : " . $details["course_location"] . "</br></br>";
        } else {
            echo "Fehler: " . $details["error"];
        }
    }
    if (!(isset($details) && isset($details["error"]))) {
        if (!isset($result_message)) {
            echo "Um einen Kurs zu tauschen musst du die Email Adresse des Mitgliedes mit dem du tauschen möchtest angeben. </br></br>
              Das Mitglied erhält eine Email, welche bestätigt werden muss um den Tausch durchzuführen.</br>
    <form action='' name='coursechange-form' method='post'>
        <input name='coursechange_mail' id='coursechange_mail' class='coursechange_mail' placeholder='E-Mail' required/>
        <input type='submit' name='coursechange_submit' class='coursechange-button' value='Kurs Tauschen'>
    </form>";
        } else {
            echo $result_message;
        }
    }
    ?>


</div>
</body>
</html>

<script type="application/javascript">

</script>