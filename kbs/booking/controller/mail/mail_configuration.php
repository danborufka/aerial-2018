<?php
/* Coypright(2015) by Ing. Roman Breitschopf, BA */

defined("main-call") or die("error-conf");

class Mail_Configuration
{

    public $prod_from = "office@aerialsilk.at <office@aerialsilk.at>";
    public $return_path = "office@aerialsilk.at";
    public $prod_bcc = "systemnachrichten@aerialsilk.at";

    public $prod_backoffice = "kbs@aerialsilk.at";
    public $test_backoffice = "test@aerialsilk.at";

    public $test_from = "test@aerialsilk.at <test@aerialsilk.at>";
    public $test_return_path = "test@aerialsilk.at";
    public $test_bcc = "test@aerialsilk.at";

    public $from;
    public $bcc;
    public $backoffice;


    public function __construct()
    {

        if (isset($_SESSION["production_mode"]) && $_SESSION["production_mode"] == 1) {
            $this->from = $this->prod_from;
            $this->bcc = $this->prod_bcc;
            $this->backoffice = $this->prod_backoffice;
        } else {
            $this->from = $this->test_from;
            $this->bcc = $this->test_bcc;
            $this->backoffice = $this->test_backoffice;
        }

    }

    public function get_mail_text($p_type)
    {
        switch ($p_type) {

            case "verification":
                // Vorgemerkt
                $subject = "Vormerkung / Signup";
                $text1 = "Die Vormerkung zu folgendem Kurs war erfolgreich:";
                $text2 = "Bitte bestätige innerhalb von 4 Stunden den folgenden Verifizierungs- Link, damit wir dir den reservierten Platz garantieren können:";
                $text3 = "Achtung! Der Link ist nur 4 Stunden gültig!\r\nSolltest du in dieser Zeit deinen Kursplatz nicht bestätigen, wird deine Voranmeldung gelöscht.";

                $text1_en = "You have successfully signed up for the following course:";
                $text2_en = "Please use the link below to confirm your booking within the next 4 hours. ";
                $text3_en = "If you don’t confirm within the given timeframe your booking will be voided.";
                
                $link = "verification";
                return array("subject" => $subject,
                    "text1" => $text1,
                    "text2" => $text2,
                    "text3" => $text3,

                    "text1_en" => $text1_en,
                    "text2_en" => $text2_en,
                    "text3_en" => $text3_en,

                    "link" => $link);
                break;

            case "regular_payment":

                // Angemeldet aber noch nicht bezahlt:
                $subject = "Anmeldung / Signup";
                $text1 = "Hiermit bist du erfolgreich und verbindlich für folgenden Kurs angemeldet:";
                $text2 = "Bitte überweise den Kursbeitrag in den nächsten 5 Tagen auf folgendes Konto:\r\n" .
                    "IBAN: AT732011182865639000\r\n" . // new
                    "BIC: GIBAATWWXXX\r\n" .
                    "Inhaber: Aerial Silk Vienna\r\n" .
                    "Verwendungszweck: ";
                $text3 = "\r\n\r\nSobald wir den Betrag erhalten haben, bekommst du eine Bestätigungsmail und erst dann ist der Platz fixiert :)\r\n\r\n" .
                    "Bitte zu allen Kursen eine lange, enge Hose und ein T-Shirt, welches über die Ellbogen reicht, mitnehmen.";


                $text3_1 = /* Stephansplatz*/
                    "\r\n\r\nSobald wir den Betrag erhalten haben, bekommst du eine Bestätigungsmail und erst dann ist der Platz fixiert :)\r\n\r\n" .
                    "Bitte zu allen Kursen eine lange, enge Hose und ein T-Shirt, welches über die Ellbogen reicht, mitnehmen.";


                $text3_2 = /* Graz*/
                    "\r\n\r\nSobald wir den Betrag erhalten haben, bekommst du eine Bestätigungsmail und erst dann ist der Platz fixiert :)\r\n\r\n" .
                    "Bitte zu allen Kursen eine lange, enge Hose und ein T-Shirt, welches über die Ellbogen reicht, mitnehmen.";


                $text3_3 = /* Neustiftgasse Hauptr.*/
                    "\r\n\r\nSobald wir den Betrag erhalten haben, bekommst du eine Bestätigungsmail und erst dann ist der Platz fixiert :)\r\n\r\n" .
                    "Der Kurs findet im Studio – Neustiftgasse statt, im Hauptraum.\r\n" .
                    "Leicht zu erreichen mit der U3 Volkstheater und ca. 8 min zu Fuß stadteinwärts.\r\n" .
                    "Neustiftgasse 20, 1070 Wien ist die genaue Adresse - auch zu erreichen mit dem Bus 48A Haltestelle\r\n" .
                    "„Kellermanngasse“.\r\n\r\n";
                "Bitte zu allen Kursen eine lange, enge Hose und ein T-Shirt, welches über die Ellbogen reicht, mitnehmen.";


                $text3_4 = /* Neustiftgasse Stretchr.*/
                    "\r\n\r\nSobald wir den Betrag erhalten haben, bekommst du eine Bestätigungsmail und erst dann ist der Platz fixiert :)\r\n\r\n" .
                    "Der Kurs findet im Studio – Neustiftgasse statt, im Stretchingraum.\r\n" .
                    "Leicht zu erreichen mit der U3 Volkstheater und ca. 8 min zu Fuß stadteinwärts.\r\n" .
                    "Neustiftgasse 20, 1070 Wien ist die genaue Adresse - auch zu erreichen mit dem Bus 48A Haltestelle\r\n" .
                    "„Kellermanngasse“.\r\n\r\n";
                "Bitte zu allen Kursen eine lange, enge Hose und ein T-Shirt, welches über die Ellbogen reicht, mitnehmen.";
                $link = false;

                $text1_en = "You are now signed up for the following course:";
                $text2_en = "
Please transfer the course fee to our bank account:\r\n" .
                    "IBAN: AT732011182865639000\r\n" . // new
                    "BIC: GIBAATWWXXX\r\n" .
                    "Account Name: Aerial Silk Vienna\r\n" .
                    "Reference: Course Number";
                $text3_en = "\r\n\r\nAs soon as we receive the money we will send you an e-mail confirmation. \r\nOnly then your booking will be guaranteed.\r\n\r\n" .
                    "Please bring a pair of long tight pants (leggings or similar) and a long sleeve shirt to your class.";

                return array("subject" => $subject,
                    "text1" => $text1,
                    "text2" => $text2,
                    "text3" => $text3,
                    "text1_en" => $text1_en,
                    "text2_en" => $text2_en,
                    "text3_en" => $text3_en,
                    "text3_1" => $text3_1,
                    "text3_2" => $text3_2,
                    "text3_3" => $text3_3,
                    "text3_4" => $text3_4,
                    "reference" => true,  // Verwendungszweck
                    "link" => $link);

            case "standard_confirmation":
                // Angemeldet und hat bereits bezahlt:
                $subject = "Anmeldebestätigung";
                $text1 = "Hiermit bestätigen wir deine Anmeldung zu folgendem Kurs:";

                $text2 = "Empfohlene Trainingskleidung:\r\n" .
                    "Bitte zu allen Einheiten eine lange, enge Hose (zB: Leggins) und ein anliegendes T-Shirt, welches über die Ellbogen reicht, mitnehmen.";


                $text2_1 =  /* Stephansplatz*/
                    "Empfohlene Trainingskleidung:\r\n\r\n" .
                    "Bitte zu allen Einheiten eine lange, enge Hose (zB: Leggins) und ein anliegendes T-Shirt, welches über die Ellbogen reicht, mitnehmen.";


                $text2_2 =  /* Graz*/
                    "Empfohlene Trainingskleidung:\r\n" .
                    "Bitte zu allen Einheiten eine lange, enge Hose (zB: Leggins) und ein anliegendes T-Shirt, welches über die Ellbogen reicht, mitnehmen.";


                $text2_3 =  /* Neustiftgasse Hauptr.*/
                    "Der Kurs findet im Studio – Neustiftgasse statt, im Hauptraum.\r\n" .
                    "Leicht zu erreichen mit der U3 Volkstheater und ca. 8 min zu Fuß stadteinwärts.\r\n" .
                    "Neustiftgasse 20, 1070 Wien ist die genaue Adresse - auch zu erreichen mit dem Bus 48A Haltestelle\r\n" .
                    "„Kellermanngasse“.\r\n\r\n" .
                    "Empfohlene Trainingskleidung:\r\n" .
                    "Bitte zu allen Einheiten eine lange, enge Hose (zB: Leggins) und ein anliegendes T-Shirt, welches über die Ellbogen reicht, mitnehmen.";


                $text2_4 =  /* Neustiftgasse Stretchr..*/
                    "Der Kurs findet im Studio – Neustiftgasse statt, im Stretchingraum.\r\n" .
                    "Leicht zu erreichen mit der U3 Volkstheater und ca. 8 min zu Fuß stadteinwärts.\r\n" .
                    "Neustiftgasse 20, 1070 Wien ist die genaue Adresse - auch zu erreichen mit dem Bus 48A Haltestelle\r\n" .
                    "„Kellermanngasse“.\r\n\r\n" .
                    "Empfohlene Trainingskleidung:\r\n" .
                    "Bitte zu allen Einheiten eine lange, enge Hose (zB: Leggins) und ein anliegendes T-Shirt, welches über die Ellbogen reicht, mitnehmen.";


                $link = false;

                return array("subject" => $subject,
                    "text1" => $text1,
                    "text2" => $text2,
                    "text2_1" => $text2_1,
                    "text2_2" => $text2_2,
                    "text2_3" => $text2_3,
                    "text2_4" => $text2_4,
                    "link" => $link);

            case "payment_reminder":
                // Zahlungserinnerung:
                $subject = "Zahlungserrinnerung";
                $text1 = "Die Zahlung für den folgenden Kurs, für den du dich verbindlich angemeldet hast, ist noch nicht eingegangen:";
                $text2 = "Bitte überweise den Kursbeitrag in den nächsten 3 Tagen auf folgendes Konto:\r\n" .
                    "IBAN: AT732011182865639000\r\n" . // new
                    "BIC: GIBAATWWXXX\r\n" .
                    "Inhaber: Aerial Silk Vienna\r\n" .
                    "Verwendungszweck: ";
                $text3 = "\r\n\r\nSobald wir den Betrag erhalten haben, bekommst du eine Bestätigungsmail und erst dann ist der Platz fixiert :)\r\n\r\n" .

                    "Bitte zu allen Kursen eine lange, enge Hose und ein T-Shirt, welches über die Ellbogen reicht, mitnehmen.";
                $link = false;

                return array("subject" => $subject,
                    "text1" => $text1,
                    "text2" => $text2,
                    "text3" => $text3,
                    "reference" => true,  // Verwendungszweck
                    "link" => $link,
                    "formal" => true);  // no smiley in payment_reminder);

            case "dunning_letter":
                // Mahnung:
                $subject = "Letzte Zahlungserrinnerung";
                $text1 = "Die Zahlung für den folgenden Kurs ist noch nicht eingegangen:";
                $text2 = "Du bist für den Kurs verbindlich angemeldet, wir haben jedoch noch keinen Kursbeitrag von dir erhalten!\r\n" .
                    "Bitte um rasche Antwort an anmeldung@aerialsilk.at, ob du deinen Platz behalten möchtest.\r\n\r\n" .

                    "Kontodaten:\r\n" .
                    "IBAN: AT732011182865639000\r\n" . // new
                    "BIC: GIBAATWWXXX\r\n" .
                    "Inhaber: Aerial Silk Vienna\r\n" .
                    "Verwendungszweck: ";
                $link = false;

                return array("subject" => $subject,
                    "text1"     => $text1,
                    "text2"     => $text2,
                    "reference" => true,  // Verwendungszweck
                    "link"      => $link,
                    "formal"    => true);  // no smiley in dunning letter

            case "wait_list_verification":
                // Warteliste Vormerkung:
                $subject = "Vormerkung Warteliste";
                $text1 = "Deine Anmeldung für die Warteliste zu folgendem Kurs wurde vorgemerkt:";
                $text2 = "Bitte bestätige innerhalb von 4 Stunden den folgenden Verifizierungs- Link, damit dein Platz auf der Warteliste gesichert ist:";
                $link = "verification";

                return array("subject" => $subject,
                    "text1" => $text1,
                    "text2" => $text2,
                    "link"  => $link);

            case "wait_list_confirmation":
                // Warteliste
                $subject = "Eintragung in Warteliste";
                $text1 = "Hiermit bist du in die Warteliste eingetragen für folgenden Kurs:";
                $text2 = "Wir werden dich kontaktieren sobald/falls ein Platz für dich frei wird.";
                $link = false;

                return array("subject" => $subject,
                    "text1" => $text1,
                    "text2" => $text2,
                    "link"  => $link);

            case "wait_list_place_available":
                // Warteliste: Platz frei
                $subject = "Warteliste: Platz frei geworden";
                $text1 = "Für folgenden Kurs, für den du auf der Warteliste bist, ist ein Platz frei geworden:";
                $text2 = "Falls du dich nun für den Kurs anmelden möchtest, bestätige in den nächsten 48 Stunden den folgenden Verifizierungs-Link.";
                $link = "waitlist";

                return array("subject" => $subject,
                    "text1" => $text1,
                    "text2" => $text2,
                    "link" => $link);

            case "course_change":
                $subject = "Tauschanfrage";
                $text1 = "Ein Mitglied möchte diesen Kurs mit dir tauschen.";
                $text2 = "Falls du an diesem Kurs teilnehmen möchtest, bestätige den folgenden Tausch-Link:";
                $link = "course_change";

                return array("subject" => $subject,
                    "text1" => $text1,
                    "text2" => $text2,
                    "link" => $link);

            case "coursechange_unsubscribe":
                $subject = "Tausch erfolgreich";
                $text1 = "Das Mitglied hat deinen Tausch bestätigt.";
                $text2 = "Du bist hiermit von dem Kurs abgemeldet.";

                return array("subject" => $subject,
                    "text1" => $text1,
                    "text2" => $text2);

            case "coursechange_finish":
                $subject = "Tausch erfolgreich";
                $text1 = "Du hast gerade erfolgreich diesen Kurs getauscht.";
                $text2 = "Wir freuen uns auf dein Kommen.";

                return array("subject" => $subject,
                    "text1" => $text1,
                    "text2" => $text2);

            case "voucher_payment":
                $subject = "Open Silk Teilnahmebestätigung";
                $text1 = "In weniger wie 30 Minuten beginnt dein Open Silk Kurs. Wir haben dir bereits einen offenen Open Silk Block abgebucht.";
                $text2 = "Wir freuen uns auf dein Kommen.";

                return array("subject" => $subject,
                    "text1" => $text1,
                    "text2" => $text2);

            case "new_membership":
                $subject = 'ASV Mitgliedschaft – Herzlich Willkommen in der Familie!';
                $text = "Wir gratulieren dir ganz herzlich: Du bist ab sofort Mitglied bei Aerial Silk Vienna!\nDer Mitgliedsbeitrag ist bei uns eingetroffen und du bist nun für alle Vorteile freigeschalten.\n\nZur Erinnerung hier nochmal im Überblick:\n\n<strong>Welche Vorteile habe ich als Mitglied?</strong>\n• erhalte den Newsletter 2 Tage vor allen anderen\n• Vergünstigte Level-Kurse: zahle 106 Euro statt 118 Euro pro Kurs \n(gültig für alle 6-wöchigen Level-Kurse, auch Trapez, Hoop, Rope, usw.)\n• erhalte einen Open Silk 10-er Block gratis\n• Rig-Miete - du kannst unser Rig für 70 Euro statt 100 Euro/Tag mieten\n• du erhältst exklusive Einladungen zu “Mitglieder-Get Togethers” \n\nWir freuen uns, dass du 2018 ein Teil unserer Aerial Family bist.\nBei Fragen stehen wir jederzeit zur Verfügung.\n\n---\n";
                return array('subject' => $subject, 'text' => $text);

            default;
                echo "System-Email-Art nicht gefunden!";
                die;
                break;

        }


    }


}


$mail_configuration = new Mail_Configuration;

?>