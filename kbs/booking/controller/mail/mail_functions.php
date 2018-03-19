<?php
/* Coypright(2015) by Ing. Roman Breitschopf, BA 									*/
/* office@breitschopf.wien			www.breitschopf.wien 							*/
/* License for Jasmin Liska. Do not distribute to others without permission.        */
defined("main-call") or die("Error- RB_Functions");

//require_once "mail_configuration.php";
//require_once "../db_functions.php";

class Mail_Public_Connect
{

    public function db_connect()
    {
        global $rb_configuration;
        $mysqli = new mysqli($rb_configuration->db_url, $rb_configuration->db_user, $rb_configuration->db_pwd, $rb_configuration->db_name);
        if ($mysqli->connect_error) {
            echo "Fehler bei der Verbindung zur Datenbank.";
            exit();
        }
        if (!$mysqli->set_charset("utf8")) {
            echo "Datenbankfehler - Fehler beim Laden von UTF8.";
            exit();
        }
        return $mysqli;
    }

    public function db_close()
    {
        if (isset($mysqli)) $mysqli->close();
    }
}

class Mail_Functions extends Mail_Public_Connect
{

    public function send_system_mail($p_registration_id, $p_type_of_mail)
    {

        if ("new_membership" == $p_type_of_mail) {
            $mail_data = $this->generate_mail_membership_registration($p_registration_id);
        } else {
            $mail_data = $this->generate_mail_data($p_registration_id, $p_type_of_mail);

            $mail_data_en = $this->generate_mail_data($p_registration_id, $p_type_of_mail, 'en');

            $mail_data['content'] .= "\r\n\r\n\r\n------------------------------\r\n"  . $mail_data_en['content'];
        }
        $subject = $mail_data["subject"];
        $content = nl2br($mail_data["content"]);
        $mail_to = $mail_data["mail_to"];
        $output = $this->send_html_mail_with_signature($mail_to, $subject, $content);
    }

    public function send_mail_membership_registration($p_email, $p_prename, $p_surname)
    {
        $mail_data = $this->generate_mail_membership_registration($p_email, $p_prename, $p_surname);
        $subject = $mail_data["subject"];
        $content = nl2br($mail_data["content"]);
        $mail_to = $mail_data["mail_to"];
        $output = $this->send_html_mail_with_signature($mail_to, $subject, $content);
    }

    public function send_backoffice_mail($content, $subject)
    {

        global $mail_configuration;
//        $mail_to = $mail_configuration->backoffice;
//        $mail_to =
        $from = $mail_configuration->from;
        $return_path = $mail_configuration->return_path;
        $bcc = $mail_configuration->bcc;

        //create a boundary for the email. This
        $boundary = uniqid('np');

        //headers - specify your from email address and name here
        //and specify the boundary for the email
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "From: " . $from . " \r\n";
        $headers .= "Reply-To: " . $from . " \r\n";
        $headers .= "Return-Path: " . $return_path . " \r\n";
        $headers .= "Bcc: " . $bcc . "\r\n";
        $headers .= "Content-Type: multipart/related;boundary=" . $boundary . "\r\n";

        //here is the content body
        $message = "This is a MIME encoded message.";
        $message .= "\r\n\r\n--" . $boundary . "\r\n";
        $message .= "Content-type: text/plain;charset=utf-8\r\n\r\n";

        $message .= "Systemmail \r\n\r\n";
        $message .= $content;


        $message .= "\r\n\r\n--" . $boundary . "--";

        //invoke the PHP mail function
        $output = mail("kbs@aerialsilk.at", $subject, $message, $headers);

        return $output;
    }

    public function send_system_no_new_customer($email)
    {

        $subject = "Aerial Silk Vienna - Kursanmeldung";
        $content = "Hallo,\r\n\r\n";
        $content .= "leider hat der Anmeldeversuch als Bestandskunde auf www.aerialsilk.at nicht geklappt.\r\n";
        $content .= "Bitte melde dich erneut an, als Neukunde, unter Angabe von Vorname, Nachname und Telefonnummer.\r\n\r\n";
        $content .= "Falls du mehr als eine Email- Adresse hast, benutze für die Anmeldung bitte immer die selbe.\r\n";
        $content .= "Wenn sich deine Email- Adresse einmal ändern sollte, dann benachrichte uns bitte per Email unter office@aerialsilk.at\r\n\r\n";

        $content = nl2br($content);
        $mail_to = $email;
        $this->send_html_mail_with_signature($mail_to, $subject, $content);

    }

    public function send_course_feedback_mail($toEmail)
    {    
        $subject = "Gratulation zum absolvierten Kurs!";
        $content = "Liebe/r Silkie!\r\n\r\nDu hast vor kurzem einen Kurs bei Aerial Silk Vienna absolviert. \r\nWir schicken dir hier einen Link zu unserem Feedbackbogen: \r\n\r\n<a href=\"https://docs.google.com/forms/d/e/1FAIpQLSffqIy1B6EVPiEyShO6BUCDoWbdKLdKPISbSFAj8njIl7NArA/viewform?usp=pp_url&entry.219327794=$toEmail\">https://docs.google.com/forms/d/e/1FAIpQLSffqIy1B6EVPiEyShO6BUCDoWbdKLdKPISbSFAj8njIl7NArA/viewform?usp=pp_url&entry.219327794=$toEmail</a>\r\n\r\n Wir freuen uns auf deine Rückmeldung, Wünsche, Anregungen und Beschwerden. Nur so können wir daran arbeiten unser Programm noch mehr nach deinen Wünschen zu gestalten. \r\n\r\nDie Umfrage erfolgt natürlich freiwillig und anonym. Du kannst selbst entscheiden ob du deine Identität bekanntgeben möchtest!\r\n\r\nVielen Dank & bis bald!\r\nDein ASV Team";
        $content = nl2br($content);
        $this->send_html_mail_with_signature($toEmail, $subject, $content);
    }

    public function send_mail_course_change($p_registration_code, $p_changemail, $p_changeid)
    {
        global $mail_configuration;

        $text = $mail_configuration->get_mail_text("course_change");
        $data = $this->db_load_registration_details_for_mailing_from_registration_code($p_registration_code);
        if ($data["error"]) {
            echo $data["error"];
            die;
        }
        $subject = $text["subject"] . " - " . $data["kursname"] . " - " . $data["begin"];
        $content = "Hallo!\r\n";
        $content .= $data["kursname"] . "\r\n";
        if ($data["one_date_only"] == 1) {
            $content .= "Datum: " . $data["begin"] . "\r\n";
        } else {
            $content .= "Beginn: " . $data["begin"] . "\r\n";
        }
        $content .= "Uhrzeit: " . $data["time"] . "\r\n" .
            "Trainer: " . $data["trainer"] . "\r\n";

        if (!($data["one_date_only"] == 1)) {
            $content .= "Termine: " . $data["termine"] . "\r\n";
        }
        if ($data["location_id"] == 3 || $data["location_id"] == 4) {
            $content .= "Ort: <b>" . $data["ort"] . "</b>\r\n";
        } else {
            $content .= "Ort: " . $data["ort"] . "\r\n";
        }
        $content .= "Kursbeitrag: " . $data["price"] . " €\r\n";
        if (!($data["precondition"] == 0 || $data["precondition"] == "")) {
            $content .= "Voraussetzungen: " . $data["precondition"] . "\r\n";
        }
        $content .= "\r\n";
        $content .= $text["text1$suffix"];
        $content .= "\r\n";
        $content .= $text["text2$suffix"];
        $content .= "\r\n";
        global $rb_configuration;
        $link = $rb_configuration->link_for_registration;
        $link .= "/kbs/anmeldung/?coursechange=" . $data['registration_code'] . "&s=" . $p_changeid . "&c=" . $data["course_id"];
        $content .= "<a href='" . $link . "'>" . $link . "</a>\r\n\r\n";


        $content .= $this->get_mail_outro();

        if (isset($_SESSION["production_mode"]) && $_SESSION["production_mode"] == 1) {
            $mail_to = $p_changemail;
        } else {
            $mail_to = "test@aerialsilks.at";
        }

        $content = nl2br($content);
        $output = $this->send_html_mail_with_signature($mail_to, $subject, $content);
    }

    public function generate_mail_data($p_registration_id, $p_type_of_mail, $lang=false)
    {
        global $db_public_functions;
        global $mail_configuration;

        $labels = [
            'de' => [
                'subject'       => 'Aerial Silk Vienna Kursbuchung',
                'greeting'      => 'Hallo %s !',
                'date'          => 'Datum',
                'start'         => 'Beginn',
                'time'          => 'Uhrzeit',
                'dates'         => 'Termine',
                'location'      => 'Ort',
                'fee'           => 'Kursbeitrag',
                'reqs'          => 'Voraussetzungen',
                'courseNr'      => 'Kurs-Nr.',
                'verifyLink'    => "​Achtung, der Verfizierungslink ist nur 4 Stunden gültig. \r\nSolltest du ​in dieser Zeit deinen Kursplatz per Klick auf den Verifizierungslink nicht bestätigen ist deine Voranmeldung gelöscht und die Reservierung ungültig.\r\n",
                'outro'         => 'Alles Liebe',
                'outro-formal'  => 'Freundliche Grüße',
                'signature'     => 'Euer Aerial Silk Vienna Team'
            ],
            'en' => [
                'subject'       => 'Aerial Silk Vienna Course Booking',
                'greeting'      => 'Hello %s !',
                'date'          => 'Date',
                'start'         => 'Starting Date',
                'time'          => 'Time',
                'dates'         => 'Dates',
                'location'      => 'Location',
                'fee'           => 'Course Fee',
                'reqs'          => 'Requirements',
                'courseNr'      => 'Course Number',
                'verifyLink'    => "If you don’t confirm within 4 hours your booking will be voided.\r\n",
                'outro'         => 'Yours truly',
                'outro-formal'  => 'All the best',
                'signature'     => 'Your Aerial Silk Vienna Team'
            ]
        ];

        if($lang) {
            $suffix = "_$lang";
        }

        $language = $lang ? $lang : 'de';
        $currentLabel = $labels[$language];

        $text = $mail_configuration->get_mail_text($p_type_of_mail);


        $data = $this->db_load_registration_details_for_mailing($p_registration_id);
        if ($data["error"]) {
            echo $data["error"];
            die;
        }
        $location_id = $data['location_id'];

        $subject = $currentLabel['subject'];

        if(isset($text['subject'])) {
            $subject = "$text[subject] - $data[kursname] - $data[begin]";
        }

        $content = sprintf($currentLabel['greeting'], $data['prename']) .
            (empty($text["formal"]) ? "" : ":)") . "\r\n\r\n";

        $content .= $text["text1$suffix"] . "\r\n\r\n";
//		if($data["textblock_mode"] == 1) {
//			$content .= $data["textblock"] . "\r\n\r\n";
//		}else {
        $content .= $data["kursname"] . "\r\n";
        if ($data["one_date_only"] == 1) {
            $content .= "$currentLabel[date]: " . $data['begin'] . "\r\n";
        } else {
            $content .= "$currentLabel[start]: " . $data['begin'] . "\r\n";
        }
        $content .= "$currentLabel[time]: " . $data['time'] . "\r\n" .
            "Trainer: " . $data['trainer'] . "\r\n";

        if (!($data["one_date_only"] == 1)) {
            $content .= "$currentLabel[dates]: " . $data["termine"] . "\r\n";
        }
        if ($data["location_id"] == 3 || $data["location_id"] == 4) {
            $content .= "$currentLabel[location]: <b>" . $data["ort"] . "</b>\r\n";
        } else {
            $content .= "$currentLabel[location]: " . $data["ort"] . "\r\n";
        }
        $content .= "currentLabel[fee]: " . $data["price"] . " €\r\n";
        if (!($data["precondition"] == 0 || $data["precondition"] == "")) {
            $content .= "$currentLabel[reqs]: " . $data["precondition"] . "\r\n";
        }
        $content .= "\r\n";
//		}

        if ($data["textblock_mode"] == 1) {
            $content .= $data["textblock$suffix"] . "\r\n\r\n";
        }

        if ($p_type_of_mail == "standard_confirmation" && !empty($data["confirmation_text"])) {
            $content .= $data["confirmation_text"] . "\r\n";
        } elseif ($p_type_of_mail == "standard_confirmation" && !empty($data["sub_conf_text"])) {
            $content .= $data["sub_conf_text"] . "\r\n";
        } else {
            if ($p_type_of_mail == "standard_confirmation") {        // checke location_id for confirmations

                if (isset($text["text2_" . $location_id])) {
                    $content .= $text["text2_" . $location_id];    // text for location_id found, use certain location text
                } else {
                    $content .= $text["text2$suffix"];                // text for location_id not found, use general text
                }
            } else {
                $content .= $text["text2$suffix"];
            }
        }

        if (isset($text['reference']) && $text['reference']) {
            $content .= "$currentLabel[courseNr]: " . $data['course_id'];
        }

        if (isset($text["text3$suffix"])) {                                    // checke location_id for regular payment
            if (isset($text['text3_' . $location_id])) {
                $content .= $text['text3_' . $location_id];            // text for location_id found, use certain location text
            } else {
                $content .= $text["text3$suffix"];                            // text for location_id not found, use general text
            }

        }

        $content .= "\r\n\r\n";

        if (isset($text['link']) && $text['link']) {
            global $rb_configuration;
            $link = $rb_configuration->link_for_registration;

            if ($text['link'] == 'verification') {
                $link .= "/kbs/anmeldung/?confirm=" . $data['registration_code'] . "&s=" . $data["student_id"] . "&c=" . $data["course_id"] . "&r=" . $p_registration_id;
                $content .= "<a href='" . $link . "'>" . $link . "</a>\r\n\r\n";
                $content .= $currentLabel['verifyLink'];

            } else if ($text["link"] == "waitlist") {
                $link .= "/kbs/anmeldung/?waitlist=" . $data['registration_code'] . "&s=" . $data["student_id"] . "&c=" . $data["course_id"] . "&r=" . $p_registration_id;
                $content .= "<a href='" . $link . "'>" . $link . "</a>\r\n\r\n";

                $content .= "Achtung, der Kursplatz ist nur 48 Stunden für dich reserviert.\r\nSolltest du ​in dieser Zeit deinen Kursplatz per Klick auf den Verifizierungslink nicht 
                             bestätigen wird dein Platz für einen anderen Teilnehmer freigegeben und die Reservierung ist ungültig.\r\n";
            }
        }

        if (($p_type_of_mail == "standard_confirmation" || $p_type_of_mail == "regular_payment") && $data["auto_unsubscribe"] == 1) {
            //Abmelden von Kurs
            $content .= "\r\n"
                . "Falls du den Platz für andere Personen freigeben willst, dann kannst du dich bis 48 Stunden vor Kursbeginn hier wieder abmelden:\r\n"
                . "Die Abmeldefunktion ist nur für ausgewählte Open Trainings verfügbar.\r\n"
                . "Hier abmelden: ";
            global $rb_configuration;
            $link = $rb_configuration->link_for_registration;
            $link .= "/kbs/anmeldung/?unsubscribe=" . $data['registration_code'] . "&s=" . $data["student_id"] . "&c=" . $data["course_id"] . "&r=" . $p_registration_id;
            $content .= "<a href='" . $link . "'>" . $link . "</a>\r\n\r\n";

            //Kurs tauschen
            $content .= "\r\n"
                . "Falls du den Platz mit einer anderen Person tauschen möchtest, kannst du das mit folgendem Link machen.\r\n"
                . "Du kannst nur mit einer Person tauschen, die das Sicherheitstraining bei uns absolviert hat.\r\n"
                . "Hier tauschen: ";
            global $rb_configuration;
            $link = $rb_configuration->link_for_registration;
            $link .= "/kbs/coursechange/?code=" . $data['registration_code'];
            $content .= "<a href='" . $link . "'>" . $link . "</a>\r\n\r\n";
        }

        //Voucher in Bestätigungs Mail
        if ($p_type_of_mail == "voucher_payment") {
//        if (($p_type_of_mail == "standard_confirmation" || $p_type_of_mail == "regular_payment") && isset($data["voucher"])) {
            $content .= "\r\n"
//                . "Für diesen Kurs wird dir eine Einheit von deinem Open Silk Block abgebucht.\r\n"
                . "Dein aktueller Open Silk Block Stand ist: " . $data["voucher"] . "\r\n\r\n";
        }

        $content .= "\r\n" .
            (empty($text["formal"]) ? $currentLabel['outro'] : $currentLabel['outro-formal']) .
            ",\r\n\r\n\r\n" .
            "&nbsp;&nbsp;&nbsp;$currentLabel[signature]";

        if (isset($_SESSION["production_mode"]) && $_SESSION["production_mode"] == 1) {
            $mail_to = $data["email"];
        } else {
            $mail_to = "test@aerialsilks.at";
        }

        return array('subject' => $subject,
            'content' => $content,
            'mail_to' => $mail_to);
    }


    private function get_mail_greeting($p_prename, $p_surname) 
    {
        return "Hallo $p_prename $p_surname, \r\n\r\n";
    }
    private function get_mail_outro()
    {
        return "\r\n" . "Alles Liebe" .
            ",\r\n\r\n\r\n" .
            "&nbsp;&nbsp;&nbsp;Euer Aerial Silk Vienna Team";
    }

    public function generate_mail_membership_registration($p_email, $p_prename, $p_surname)
    {
        $subject = "Anmeldung Mitgliedschaft Aerial Silk Vienna";
        $content = $this->get_mail_greeting($p_prename, $p_surname);
        $content .= "du hast dich für eine Mitgliedschaft bei Aerial Silk Vienna angemeldet." . "\r\n";
        $content .= "Um die Anmeldung abzuschließen überweise bitte den Mitgliedsbeitrag von 50 Euro an:" . "\r\n";
        $content .= "Aerial Silk Vienna" . "\r\n";
        $content .= "IBAN: AT732011182865639000" . "\r\n";
        $content .= "BIC: GIBAATWWXXX" . "\r\n";
        $content .= "Verwendungszweck: Member: " . $p_email . "\r\n";

        $content .= "\r\n" .
            (empty($text["formal"]) ? "Alles Liebe" : "Freundliche Grüße") .
            ",\r\n\r\n\r\n" .
            "&nbsp;&nbsp;&nbsp;Euer Aerial Silk Vienna Team";

        if (isset($_SESSION["production_mode"]) && $_SESSION["production_mode"] == 1) {
            $mail_to = $p_email;
        } else {
            $mail_to = "test@aerialsilks.at";
        }

        return array('subject' => $subject,
            'content' => $content,
            'mail_to' => $mail_to);
    }

    public function send_membership_activation_mail($p_email, $p_prename, $p_surname)
    {
        global $mail_configuration;

        $messages = $mail_configuration->get_mail_text('new_membership');

        $subject = $messages['subject'];
        $content = $this->get_mail_greeting($p_prename, $p_surname);
        $content .= $messages['text'];
        $content .= $this->get_mail_outro();

        $content = nl2br($content);
        $this->send_html_mail_with_signature($p_email, $subject, $content);
    }

    public function send_html_mail_with_signature($p_mail_to, $p_subject, $p_html_message_content)
    {

        $mail_to = $p_mail_to;
        $subject = $p_subject;
        global $mail_configuration;
        $from = $mail_configuration->from;
        $return_path = $mail_configuration->return_path;
        $bcc = $mail_configuration->bcc;

        //create a boundary for the email. This
        $boundary = uniqid('np');

        //headers - specify your from email address and name here
        //and specify the boundary for the email
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "From: " . $from . " \r\n";
        $headers .= "Reply-To: " . $from . " \r\n";
        $headers .= "Return-Path: " . $return_path . " \r\n";
        $headers .= "Bcc: " . $bcc . "\r\n";
        $headers .= "Content-Type: multipart/related;boundary=" . $boundary . "\r\n";

        //here is the content body
        $message = "This is a MIME encoded message.";
        $message .= "\r\n\r\n--" . $boundary . "\r\n";
        $message .= "Content-type: text/html;charset=utf-8\r\n\r\n";


        //Html body
        $message .= $p_html_message_content;
        $message .= "
		<br>
		<br>
		<a href=\"http://www.aerialsilk.at/\" style=\"text-decoration: none;\"><img style=\"margin-left: 30px\" src=\"cid:php-cid_logo-$boundary\"></a>
		<br>
		<br />Aerial Silk Vienna<br />
		Domgasse 8, 1010 Wien<br />
		Österreich<br />
		<br />
		Tel: +43 677 - 620 360 76<br />
		<a style=\"text-decoration: none; color:#000\" href='mailto:office@aerialsilk.at' target='_blank'>anmeldung@aerialsilk.at</a><br />
		<a style=\"text-decoration: none; color:#000\" href='http://www.aerialsilk.at/' target='_blank'>www.aerialsilk.at</a><br />" .
            //<a style=\"text-decoration: none; color:#000\" href='http://www.facebook.com/AerialSilkVienna' target='_blank'>www.facebook.com/AerialSilkVienna</a><br />" .

            "<br />ZVR-Zahl: 933975971</p><br /><br />

		<a href='http://www.facebook.com/AerialSilkVienna' target='_blank'><img style=\"margin: 4px 0 0 0;\" src=\"cid:php-cid_facebook-$boundary\"></a>
		<a href='http://forwarding.aerialsports.at/youtube' target='_blank'><img style=\"margin: 4px 0 0 1px;\" src=\"cid:php-cid_youtube-$boundary\"></a>
		<a href='https://instagram.com/aerialsilkvienna/' target='_blank'><img style=\"margin: 4px 0 0 1px;\" src=\"cid:php-cid_instagram-$boundary\"></a>
		<a href='https://de.pinterest.com/aerialsilkvie/' target='_blank'><img style=\"margin: 4px 0 0 1px;\" src=\"cid:php-cid_pinterest-$boundary\"></a><br /><br />";


        $message .= "<span style=\"color: gray; font-size: 0.75em\">This e-mail and attached file(s) are intended exclusively for the addressee(s) and may not be passed on to, or made available for use by any person other than the addressee(s). If an addressing or a transmission error has misdirected this e-mail, please notify the author by replying to this e-mail.</span><br />";

        $message .= "<br /><img src=\"cid:php-cid_tree-$boundary\"> <span style=\"color: green; font-size: 0.85em\">Think before you print. Do you really need to print this e-mail. Please consider the environment.</span>";

        $message .= "\r\n\r\n--" . $boundary . "\r\n";
        $message .= "Content-type: image/png\r\n";
        $message .= "Content-Transfer-Encoding: base64\r\n";
        $message .= "Content-ID: <php-cid_logo-$boundary>\r\n\r\n";
        $message .= chunk_split(base64_encode(file_get_contents(__DIR__ . '/images-mail/logo.png')));

        $message .= "\r\n\r\n--" . $boundary . "\r\n";
        $message .= "Content-type: image/png\r\n";
        $message .= "Content-Transfer-Encoding: base64\r\n";
        $message .= "Content-ID: <php-cid_facebook-$boundary>\r\n\r\n";
        $message .= chunk_split(base64_encode(file_get_contents(__DIR__ . '/images-mail/facebook.png')));

        $message .= "\r\n\r\n--" . $boundary . "\r\n";
        $message .= "Content-type: image/png\r\n";
        $message .= "Content-Transfer-Encoding: base64\r\n";
        $message .= "Content-ID: <php-cid_youtube-$boundary>\r\n\r\n";
        $message .= chunk_split(base64_encode(file_get_contents(__DIR__ . '/images-mail/youtube.png')));

        $message .= "\r\n\r\n--" . $boundary . "\r\n";
        $message .= "Content-type: image/png\r\n";
        $message .= "Content-Transfer-Encoding: base64\r\n";
        $message .= "Content-ID: <php-cid_instagram-$boundary>\r\n\r\n";
        $message .= chunk_split(base64_encode(file_get_contents(__DIR__ . '/images-mail/instagram.png')));
        $message .= "\r\n\r\n--" . $boundary . "\r\n";
        $message .= "Content-type: image/png\r\n";
        $message .= "Content-Transfer-Encoding: base64\r\n";
        $message .= "Content-ID: <php-cid_pinterest-$boundary>\r\n\r\n";
        $message .= chunk_split(base64_encode(file_get_contents(__DIR__ . '/images-mail/pinterest.png')));
        $message .= "\r\n\r\n--" . $boundary . "\r\n";
        $message .= "Content-type: image/png\r\n";
        $message .= "Content-Transfer-Encoding: base64\r\n";
        $message .= "Content-ID: <php-cid_tree-$boundary>\r\n\r\n";
        $message .= chunk_split(base64_encode(file_get_contents(__DIR__ . '/images-mail/tree.png')));

        $message .= "\r\n\r\n--" . $boundary . "--";

        //invoke the PHP mail function
        $output = mail($mail_to, $subject, $message, $headers);

        return $output;

    }


    public function db_load_registration_details_for_mailing_from_registration_code($p_registration_code)
    {
        $db = $this->db_connect();
        $result = $db->query("SELECT registration_id from as_registrations WHERE registration_code = '$p_registration_code'");
        if (!$result) return array('error' => $db->error);
        $line = $result->fetch_array();
        if ($result->num_rows === 0) {
            $db->close();
            return array('error' => "Fehler bei Laden von ID");
        }
        $db->close();
        return $this->db_load_registration_details_for_mailing($line["registration_id"]);

    }

    public function db_load_registration_details_for_mailing($p_registration_id)
    {

        $db = $this->db_connect();
        $result = $db->query(

            "SELECT 
					c.name as kursname,
					u.prename as trainer,
					l.location_name as location,
					Concat
						(
							(CASE date_format(c.date1, '%w')
								WHEN 1 THEN 'Mo'
								WHEN 2 THEN 'Di'
								WHEN 3 THEN 'Mi'
								WHEN 4 THEN 'Do'
								WHEN 5 THEN 'Fr'
								WHEN 6 THEN 'Sa'
								WHEN 0 THEN 'So'
							END),
							', ',
							date_format(c.date1, '%d.%m.%Y')
						)	as begin,
					Concat
						(
							date_format(c.date1, '%H:%i'),
							' - ',
							date_format(DATE_ADD(c.date1, INTERVAL c.duration MINUTE), '%H:%i')
							
						)	as time,
					Concat(
                        date_format(c.date1, '%d.%m'),
                        if(c.date2 IS NULL, '', date_format(c.date2, ', %d.%m')),
                        if(c.date3 IS NULL, '', date_format(c.date3, ', %d.%m')),
                        if(c.date4 IS NULL, '', date_format(c.date4, ', %d.%m')),
                        if(c.date5 IS NULL, '', date_format(c.date5, ', %d.%m')),
                        if(c.date6 IS NULL, '', date_format(c.date6, ', %d.%m')),
                        if(c.date7 IS NULL, '', date_format(c.date7, ', %d.%m')),
                        if(c.date8 IS NULL, '', date_format(c.date8, ', %d.%m')),
                        if(c.date9 IS NULL, '', date_format(c.date9, ', %d.%m')),
                        if(c.date10 IS NULL, '', date_format(c.date10, ', %d.%m')),
                        if(c.date11 IS NULL, '', date_format(c.date11, ', %d.%m')),
                        if(c.date12 IS NULL, '', date_format(c.date12, ', %d.%m')),
                        if(c.not_on IS NULL, '', Concat(' (entfällt am ', c.not_on, ')'))
                    )
                    ,Concat (
                        date_format(c.date1, '%d.%m %H:%i'),
                        if(c.date2 IS NULL, '', date_format(c.date2, ', %d.%m %H:%i')),
                        if(c.date3 IS NULL, '', date_format(c.date3, ', %d.%m %H:%i')),
                        if(c.date4 IS NULL, '', date_format(c.date4, ', %d.%m %H:%i')),
                        if(c.date5 IS NULL, '', date_format(c.date5, ', %d.%m %H:%i')),
                        if(c.date6 IS NULL, '', date_format(c.date6, ', %d.%m %H:%i')),
                        if(c.date7 IS NULL, '', date_format(c.date7, ', %d.%m %H:%i')),
                        if(c.date8 IS NULL, '', date_format(c.date8, ', %d.%m %H:%i')),
                        if(c.date9 IS NULL, '', date_format(c.date9, ', %d.%m %H:%i')),
                        if(c.date10 IS NULL, '', date_format(c.date10, ', %d.%m %H:%i')),
                        if(c.date11 IS NULL, '', date_format(c.date11, ', %d.%m %H:%i')),
                        if(c.date12 IS NULL, '', date_format(c.date12, ', %d.%m %H:%i')),
                        if(c.not_on IS NULL, '', Concat(' (entfällt am ', c.not_on, ')')),
                        '.'
                    ) as termine,
					if(c.pre_reg_count + c.registration_count >= c.max_count, 'AUSGEBUCHT', '') as ist_ausgebucht,	
					replace(format(IF(s.membership = 1, cl.member_price, cl.price), 2), '.00', '' ) as price,
					c.status,
					s.prename,
					s.surname,
					s.email,
					s.student_id,
					r.registration_code,
					c.course_id,
					c.confirmation_text,
					c.textblock_mode,
					c.textblock,
					c.precondition,
					if(c.date2 IS NULL, 1, -1) as one_date_only,
					cl.auto_unsubscribe,
					sca.confirmation_text as sub_conf_text,
					l.location_id,
					r.voucher
					
					
			   from
						  as_registrations r
			   inner join as_courses c    		on r.course_id = c.course_id
			   inner join as_users u			on c.trainer_id = u.user_id
			   inner join as_locations l		on c.location_id = l.location_id
			   inner join as_students s			on r.student_id = s.student_id
			   left  join as_categories ca		on c.cat_id = ca.cat_id
			   left  join as_subcategories sca	on c.subcat_id = sca.subcat_id
			   left join as_course_levels  cl      on c.course_level_id = cl.id
			  where r.registration_id = '$p_registration_id'
			 LIMIT 1;");


        if (!$result) return array('error' => $db->error);

        $line = $result->fetch_array();
        $db->close();
        if ($result->num_rows === 0) {
            return array('error' => "<br/>Keine Anmeldung mit diesem Code gefunden.");
        } else {

            return array('error' => false,
                'kursname' => $line["kursname"],
                'begin' => $line["begin"],
                'time' => $line["time"],
                'trainer' => $line["trainer"],
                'termine' => $line["termine"],
                'ort' => $line["location"],
                'location_id' => $line["location_id"],
                'ausgebucht' => $line["ist_ausgebucht"],
                'price' => $line["price"],
                'prename' => $line["prename"],
                'surname' => $line["surname"],
                'email' => $line["email"],
                'student_id' => $line["student_id"],
                'registration_code' => $line["registration_code"],
                'course_id' => $line["course_id"],
                'textblock_mode' => $line["textblock_mode"],
                'textblock' => $line["textblock"],
                'confirmation_text' => $line["confirmation_text"],
                'precondition' => $line["precondition"],
                'one_date_only' => $line["one_date_only"],
                'auto_unsubscribe' => $line["auto_unsubscribe"],
                'sub_conf_text' => $line["sub_conf_text"],
                'voucher' => $line["voucher"]
            );
        }
    }

    /*

    public function mail_without_logo($p_mail_to, $p_subject, $p_message) {

        $p_subject = 'Test-Mail';
        $p_header = 'From: anmeldung@aerialsports.at <anmeldung@aerialsports.at>' . "\r\n" .
            'Reply-To: anmeldung@aerialsports.at\r\n' .
            'MIME-Version: 1.0' . "\r\n" .
            'Content-type: text/plain; charset=UTF-8' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();

        $p_empfaenger = 'test@aerialsports.at'; // $p_mail_to
        if (mail($p_empfaenger, $p_subject, $p_message, $p_header)) {
            $p_message .= "\n\nWarnung: Versand der E-Mail ist fehlgeschlagen.";
        }
        $p_empfaenger = 'copy@aerialsports.at';
        mail($p_empfaenger, $p_subject, $p_message, $p_header);

    } */

}

$mail_functions = new Mail_Functions();
?>