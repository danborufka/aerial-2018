<?php
/* Coypright(2015) by Ing. Roman Breitschopf, BA */

	defined("main-call") or die("error-conf");
	
	class RB_Configuration{


		public $title_of_web_application_backend = "Aerial Silks Kursbuchungssystem";
		public $title_of_web_application_frontend = "Aerial Silks Vienna";


		public $production_mode = "auto";
											/*		auto = automatic detection
											 * 		1 = production_mode
											 * 		2 = test_mode
											 *
											 */
		public $db_url;
		public $db_name;
		public $db_user;
		public $db_pwd;
		public $relative_path_of_header_php;
		public $link_for_registration;

		public function __construct() {
		
			if($this->production_mode == "auto") {
				if($_SERVER['SERVER_NAME'] == "localhost") {
					$this->production_mode = 2;
				} else {
					$this->production_mode = 1;
				}
			}
			
			$_SESSION["production_mode"] = $this->production_mode;
			
			if ($this->production_mode == 1) {
				// PRODUCTION MODE
				$this->db_url="mysql5.aerialsports.at";
				$this->db_name="db468841_1";
				$this->db_user="db468841_1";
				$this->db_pwd="7,4hydFteQ.j";	
				$this->relative_path_of_header_php =	"view/header.php";
				$this->link_for_registration="https://www.aerialsports.at";	
				if ($_SERVER["HTTPS"] != "on") {
					header('Location: https://' . $_SERVER["SERVER_NAME"] . $_SERVER["PHP_SELF"]);
				}
			}else{
				// DEVELOPMENT MODE
				$this->db_url="localhost";
				$this->db_name="aerialsilk";
				$this->db_user="dev";
				$this->db_pwd="supersecure";
				$this->relative_path_of_header_php =	"view/header.php";	
				$this->link_for_registration="localhost/booking";
			}
		}

	
		
		
		
		public function get_datepicker_options() {
		
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

	$rb_configuration      = new RB_Configuration;

?>