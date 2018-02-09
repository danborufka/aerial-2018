<?php
/* Coypright(2015) by Ing. Roman Breitschopf, BA */

require_once dirname(dirname(dirname(__DIR__))) . '/kbs_conf/inc/ConfFileManager.php';

defined("main-call") or die("error-conf");

class RB_Configuration
{

    public $title_of_web_application_backend = "Kursbuchungssystem";
    public $title_of_web_application_frontend = "Aerial Silks Vienna";


    public $production_mode = 2;
    /*		"auto" = automatic detection
     * 		1 = production_mode
     * 		2 = development_mode
     *
     */
    public $db_url;
    public $db_name;
    public $db_user;
    public $db_pwd;
    public $relative_path_of_header_php;
    public $link_for_registration;

    public function __construct()
    {

        $confPath = dirname(dirname(dirname(__DIR__))) . '/kbs_conf/kbs_configuration.json';

        $conf = ConfFileManager::getConfData($confPath);

        // echo '<pre>', print_r($conf), '</pre>';
        // exit;

        if ($this->production_mode == "auto") {
            if ($_SERVER['SERVER_NAME'] == "localhost") {
                $this->production_mode = 2;
            } else {
                $this->production_mode = 1;
            }
        }

        $_SESSION["production_mode"] = $this->production_mode;

        if ($this->production_mode == 1) {
            // PRODUCTION MODE
            $_conf = $conf->prod;
            $this->db_url = $_conf->db_url;
            $this->db_name = $_conf->db_name;
            $this->db_user = $_conf->db_user;
            $this->db_pwd = $_conf->db_pwd;
            $this->relative_path_of_header_php = "view/header.php";
            $this->link_for_registration = "https://www.aerialsilk.at";
            if ($_SERVER["HTTPS"] != "on") {
                header('Location: https://' . $_SERVER["SERVER_NAME"] . $_SERVER["PHP_SELF"]);
            }
        } elseif ($this->production_mode == 2) {
            // DEV1 MODE
            $_conf = $conf->dev;
            $this->db_url = $_conf->db_url;
            $this->db_name = $_conf->db_name;
            $this->db_user = $_conf->db_user;
            $this->db_pwd = $_conf->db_pwd;
            $this->db_port = isset($_conf->db_port) ? $_conf->db_port : NULL;
            $this->relative_path_of_header_php = "view/header.php";
            $this->link_for_registration = "http://test.aerialsilks.at";
        }
    }


    public function get_datepicker_options()
    {

        /*	echo 	'<script>
                    $( "#datepicker" ).datepicker({';*/
        echo "		  changeMonth: true,
					  changeYear: true,
					  prevText:  'zurück',
					  nextText:  'vor',
					  firstDay:  1,
					  showButtonPanel: true,
					  closeText: 'Zuklappen',
					  currentText: 'heute',
					  showWeek: true,
					  weekHeader: 'KW',
					  dateFormat: 'dd.mm.yy',
					  monthNames: ['Januar','Februar','März','April','Mai','Juni',
						'Juli','August','September','Oktober','November','Dezember'],
					  monthNamesShort: ['Jan','Feb','Mär','Apr','Mai','Jun',
						'Jul','Aug','Sep','Okt','Nov','Dez'],
					  dayNames: ['Sonntag','Montag','Dienstag','Mittwoch','Donnerstag','Freitag','Samstag'],
					  dayNamesShort: ['So','Mo','Di','Mi','Do','Fr','Sa'],
					  dayNamesMin: ['So','Mo','Di','Mi','Do','Fr','Sa']";
        /*	echo "	});
                    </script>";


                          dateFormat: 'dd.mm.yy',
                          dateFormat: 'yy-mm-dd',

                          */
    }
}

$rb_configuration = new RB_Configuration;

?>