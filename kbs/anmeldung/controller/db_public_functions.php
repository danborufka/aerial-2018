<?php
/* Coypright(2015) by Ing. Roman Breitschopf, BA 									*/
/* office@breitschopf.wien			www.breitschopf.wien 							*/
/* License for Jasmin Liska. Do not distribute to others without permission. */
defined("main-call") or die();

class DB_Public_Connect
{

    public function db_connect()
    {
        global $rb_configuration;
        $mysqli = new mysqli($rb_configuration->db_url, $rb_configuration->db_user, $rb_configuration->db_pwd, $rb_configuration->db_name, $rb_configuration->db_port);
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

class DB_Public_Functions extends DB_Public_Connect
{


    public $public_registration = null;
    public $public_voucher_reguest = null;

    public function __construct()
    {
        $this->public_registration = new DB_Functions_Public_Registration();
        $this->public_voucher_reguest = new DB_Functions_Public_Voucher_Request();
    }


}


class DB_Functions_Public_Registration extends DB_Public_Connect
{

    public function public_db_load_course($p_id,
                                          $p_code,
                                          $p_code_required)
    {
        $db = $this->db_connect();
        $statement =

            "SELECT 
					c.name as kursname,
					u.prename as trainer,
					l.location_name as location,
					l.location_id,
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
					Concat
						(
							date_format(c.date1, '%d.%m'),
							if(c.date2 IS NULL, '', date_format(c.date2, ', %d.%m')),
							if(c.date3 IS NULL, '', date_format(c.date3, ', %d.%m')),
							if(c.date4 IS NULL, '', date_format(c.date4, ', %d.%m')),
							if(c.date5 IS NULL, '', date_format(c.date5, ', %d.%m')),
							if(c.date6 IS NULL, '', date_format(c.date6, ', %d.%m')),
							if(c.date7 IS NULL, '', date_format(c.date7, ', %d.%m')),
							if(c.date8 IS NULL, '', date_format(c.date8, ', %d.%m.')),
							if(c.date9 IS NULL, '', date_format(c.date9, ', %d.%m.')),
							if(c.date10 IS NULL, '', date_format(c.date10, ', %d.%m.')),
							if(c.date11 IS NULL, '', date_format(c.date11, ', %d.%m.')),
							if(c.date12 IS NULL, '', date_format(c.date12, ', %d.%m.')),
							if(c.not_on IS NULL, '', Concat(' (entfällt am ', c.not_on, ')'))
						
						) as termine,
					if(c.pre_reg_count + c.registration_count >= c.max_count, 'AUSGEBUCHT', '') as ist_ausgebucht,	
					replace(format(c.price, 2), '.00', '' ) as price,
					c.status,
					c.precondition,
					c.textblock_mode,
					c.textblock,
					if(c.date2 IS NULL, 1, -1) as one_date_only,
					sca.is_kid_course,
					cl.security_training as security_training
			   from
						  as_courses c
			   inner join as_users u					on c.trainer_id = u.user_id
			   inner join as_locations l			on c.location_id = l.location_id
			   left  join as_categories ca		on c.cat_id = ca.cat_id
			   left  join as_subcategories sca on sca.subcat_id = c.subcat_id 
			   left join as_course_levels cl on c.course_level_id = cl.id
			  where c.course_id = $p_id
			  ";

        if ($p_code_required) $statement .= " and c.registration_code = '$p_code' ";

        $statement .= " LIMIT 1;";

        $result = $db->query($statement);

        if (!$result) return array('error' => $db->error);

        $line = $result->fetch_array();
        $db->close();
        if ($result->num_rows === 0) {
            return array('error' => "<br/>Keinen Kurs mit diesem Code gefunden.");
        } else {
            if ($line["status"] == 1) return array('error' => "<br/>Die Anmeldung zu diesem Kurs ist noch nicht freigegeben.",
                'show_data' => false);

            if ($line["status"] != 2) {


                return array('error' => "<br/>Die Anmeldefrist für diesen Kurs ist bereits abgelaufen.",
                    'show_data' => true,
                    'kursname' => $line["kursname"],
                    'begin' => $line["begin"],
                    'time' => $line["time"],
                    'trainer' => $line["trainer"],
                    'termine' => $line["termine"],
                    'ort' => $line["location"],
                    'location_id' => $line["location_id"],
                    'ausgebucht' => $line["ist_ausgebucht"],
                    'price' => $line["price"],
                    'precondition' => $line["precondition"],
                    'textblock_mode' => $line["textblock_mode"],
                    'textblock' => $line["textblock"],
                    'one_date_only' => $line["one_date_only"],
                    'security_training' => $line["security_training"]);


            }

            return array('error' => false,
                'show_data' => true,
                'kursname' => $line["kursname"],
                'begin' => $line["begin"],
                'time' => $line["time"],
                'trainer' => $line["trainer"],
                'termine' => $line["termine"],
                'ort' => $line["location"],
                'location_id' => $line["location_id"],
                'ausgebucht' => $line["ist_ausgebucht"],
                'price' => $line["price"],
                'precondition' => $line["precondition"],
                'textblock_mode' => $line["textblock_mode"],
                'textblock' => $line["textblock"],
                'one_date_only' => $line["one_date_only"],
                'is_kid_course' => $line["is_kid_course"],
                'security_training' => $line["security_training"]);
        }
    }


    public function public_db_load_course_for_confirmation_disabled($p_id)
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
					Concat
						(
							date_format(c.date1, '%d.%m'),
							if(c.date2 IS NULL, '', date_format(c.date2, ', %d.%m')),
							if(c.date3 IS NULL, '', date_format(c.date3, ', %d.%m')),
							if(c.date4 IS NULL, '', date_format(c.date4, ', %d.%m')),
							if(c.date5 IS NULL, '', date_format(c.date5, ', %d.%m')),
							if(c.date6 IS NULL, '', date_format(c.date6, ', %d.%m')),
							if(c.date7 IS NULL, '', date_format(c.date7, ', %d.%m')),
							if(c.date8 IS NULL, '', date_format(c.date8, ', %d.%m.')),
							if(c.date9 IS NULL, '', date_format(c.date9, ', %d.%m.')),
							if(c.date10 IS NULL, '', date_format(c.date10, ', %d.%m.')),
							if(c.date11 IS NULL, '', date_format(c.date11, ', %d.%m.')),
							if(c.date12 IS NULL, '', date_format(c.date12, ', %d.%m.')),
							'.'
						
						) as termine,
					if(c.pre_reg_count + c.registration_count >= c.max_count, 'AUSGEBUCHT', '') as ist_ausgebucht,	
					replace(format(c.price, 2), '.00', '' ) as price,
					c.status
			   from
						  as_courses c
			   inner join as_users u					on c.trainer_id = u.user_id
			   inner join as_locations l			on c.location_id = l.location_id
			   left  join as_categories ca		on c.cat_id = ca.cat_id
			  where c.course_id = $p_id
			 LIMIT 1;");


        if (!$result) return array('error' => $db->error);

        $line = $result->fetch_array();
        $db->close();
        if ($result->num_rows === 0) {
            return array('error' => "<br/>Keinen Kurs mit diesem Code gefunden.");
        } else {
            if ($line["status"] == 1) return array('error' => "<br/>Die Anmeldung zu diesem Kurs ist noch nicht freigegeben.",
                'show_data' => false);

            if ($line["status"] != 2) {


                return array('error' => "<br/>Die Anmeldefrist für diesen Kurs ist bereits abgelaufen.",
                    'show_data' => true,
                    'kursname' => $line["kursname"],
                    'begin' => $line["begin"],
                    'time' => $line["time"],
                    'trainer' => $line["trainer"],
                    'termine' => $line["termine"],
                    'ort' => $line["location"],
                    'ausgebucht' => $line["ist_ausgebucht"],
                    'price' => $line["price"]);


            }

            return array('error' => false,
                'show_data' => true,
                'kursname' => $line["kursname"],
                'begin' => $line["begin"],
                'time' => $line["time"],
                'trainer' => $line["trainer"],
                'termine' => $line["termine"],
                'ort' => $line["location"],
                'ausgebucht' => $line["ist_ausgebucht"],
                'price' => $line["price"]);
        }
    }

    public function public_db_get_student($p_email)
    {

        $db = $this->db_connect();
        $result = $db->query(
            "SELECT student_id,
					status,
					email,
					prename,
					newsletter,
					security_training
			   FROM as_students
			  WHERE email = '$p_email'
			    AND status != 3
			 UNION
			 SELECT linked_students.student_id,
			 		linked_students.status,
			 		linked_students.email,
			 		linked_students.prename,
					linked_students.newsletter,
					linked_students.security_training
			  FROM  as_students as origin_students,
			  		as_students as linked_students
			  WHERE origin_students.email = '$p_email'
			    AND origin_students.status = 3
			    AND origin_students.merged_to = linked_students.email
			    AND linked_students.status != 3;");

        if (!$result) echo $db->error;

        if ($result->num_rows == 0) {
            $db->close();
            return false;
        }

        $line = $result->fetch_array();
        $db->close();
        return array('student_id' => $line["student_id"],
            'email' => $line["email"],
            'prename' => $line["prename"],
            'status' => $line["status"],
            'newsletter' => $line["newsletter"],
            'security_training' => $line["security_training"]);


    }

    public function public_db_update_student($p_email, $p_prename, $p_surname, $p_status)
    {

        $db = $this->db_connect();
        $result = $db->query(
            "
				UPDATE as_students
				   SET prename = '$p_prename',
				   	   surname = '$p_surname',
				   	   status = $p_status
				 WHERE email = '$p_email';
			 ");

        $db->close();
    }


    public function public_db_subscribe_for_newsletter($p_email)
    {

        $db = $this->db_connect();
        $result = $db->query(
            "
				UPDATE as_students
				   SET newsletter = TRUE
				 WHERE email = '$p_email';
			 ");

        $db->close();
    }

    public function public_db_insert_new_student($p_prename,
                                                 $p_surname,
                                                 $p_email,
                                                 $p_newsletter)
    {


        // Validation

        $_SESSION["s_error_msg"] = "";
        if (empty($p_prename)) {
            return false;
        } else {
            $p_prename = "'" . $p_prename . "'";
        }
        if (empty($p_surname)) {
            return false;
        } else {
            $p_surname = "'" . $p_surname . "'";
        }
        if (empty($p_email) || !filter_var($p_email, FILTER_VALIDATE_EMAIL)) {
            return false;
        } else {
            $p_email = "'" . $p_email . "'";
        }
        $p_status = '5';  // Status ungeprüft

        if (empty($p_newsletter) || !$p_newsletter) {
            $p_newsletter = "0";
        } else {
            $p_newsletter = "1";
        }

        $db = $this->db_connect();

        $statement = "
		
			INSERT INTO as_students(prename,
								   surname,
								   email,
								   newsletter,
								   status)
								   
			VALUES 				  ($p_prename,
								   $p_surname,
								   $p_email,
								   $p_newsletter,
								   $p_status);";
        $result = $db->query($statement);
        if (!$result) {
            echo $db->error . "<br><br>" . $statement;
            $db->close();
            return false;
        } else {
            $student_id = $db->insert_id;
            $db->close();
            return array('student_id' => $student_id,
                'email' => $p_email,
                'status' => $p_status,
                'newsletter' => false);
        }


    }


    public function public_db_insert_new_registration($p_student_id,
                                                      $p_course_id,
                                                      $p_ip_address,
                                                      $p_kid_name = "-")
    {

        $db = $this->db_connect();


        $statement0 = "
			
			SELECT status,
				   registration_id
			  FROM as_registrations
			 WHERE course_id = $p_course_id
			   AND student_id = $p_student_id
			   AND kid_name = '$p_kid_name';";


        $result0 = $db->query($statement0);
        $registration_id = 0;
        $storno = false;
        if (!($result0->num_rows === 0)) {

            $line0 = $result0->fetch_array();
            $registration_id = $line0["registration_id"];
            switch ($line0["status"]) {
                case 1:  // vorgemerkt
                case 2:  // angemeldet
                case 3:  // bestätigt
                case 4:  // vorgemerkt Warteliste
                case 5:  // Warteliste
                case 6:  // Nachholer
                case 7:  // Sonstiges
                    return array('error' => "Bereits gebucht.");
                    break;
                case 20:  // storniert(abgelaufen)
                case 21:  // storniert
                    $storno = true;
                    break;
                case 22:  // gesperrt
                    return array('error' => "Gesperrt für diesen Kurs.");
                    break;
                default:
                    return array('error' => "E6016: Unerwarteter Fehler: Kursstatus unbekannt");
                    break;
            }

        }
        if ($storno == false) {

            $statement = "
			
				INSERT INTO as_registrations(  student_id,
											   course_id,
											   status,
											   rank,
											   ip_address,
											   kid_name)
											   
						VALUES 			   (   $p_student_id,
											   $p_course_id,
											   if( ((SELECT pre_reg_count FROM as_courses c1 WHERE c1.course_id = $p_course_id) + (SELECT registration_count FROM as_courses c2 WHERE c2.course_id = $p_course_id) >= (SELECT max_count FROM as_courses c3 WHERE c3.course_id = $p_course_id))
											   		, 4, 1),
											   (SELECT max(t1.rank) + 1 as next_rank FROM
											   		(SELECT rank FROM as_registrations as r WHERE r.course_id = $p_course_id
	                                          		UNION
	                                          		SELECT 0 as rank FROM dual) as t1),
	                                           '$p_ip_address',
	                                           '$p_kid_name'
										   );";
        } else {
            $statement = "
				
				UPDATE as_registrations
				   SET status = if(
									((SELECT max(t1.rank) as actual_max_rank FROM
								   		(SELECT rank FROM as_registrations as r WHERE r.course_id = $p_course_id
                                  		UNION
                                  		SELECT 0 as rank FROM dual) as t1) < (SELECT max_count FROM as_courses c WHERE c.course_id = $p_course_id))
							   		, 1, 4),
					   kid_name = '$p_kid_name',
					   rank = (SELECT max(t1.rank) + 1 as next_rank FROM
											   		(SELECT rank FROM as_registrations as r WHERE r.course_id = $p_course_id
	                                          		UNION
	                                          		SELECT 0 as rank FROM dual) as t1)								  
				 WHERE registration_id = $registration_id";
        }
        $result = $db->query($statement);
        if (!$result) {
            $msg = $db->error . "<br>" . $statement;
            $db->close();
            return array('error' => $msg);
        } else {
            if ($storno == false) $registration_id = $db->insert_id;


            $statement2 = "
				SELECT status,
					   registration_code
				  FROM as_registrations
				 WHERE course_id = $p_course_id
				   AND student_id = $p_student_id
				   AND kid_name = '$p_kid_name'";

            $result2 = $db->query($statement2);
            $line2 = $result2->fetch_array();
            $status = $line2["status"];
            $registration_code = $line2["registration_code"];
            $db->close();
            $this->db_update_actual_count($p_course_id);
            return array('registration_id' => $registration_id,
                'registration_code' => $registration_code,
                'status' => $status,
                'error' => false);
        }


    }


    public function db_update_actual_count($p_course_id)
    {

        $db = $this->db_connect();

        $c_statement = "
			UPDATE as_courses
			   SET	   registration_count = (SELECT count(1) from as_registrations r
					   							WHERE r.course_id = $p_course_id
												  AND r.status IN (2,3,22,23)),
			   		   pre_reg_count	  = (SELECT count(1) from as_registrations r
					   							WHERE r.course_id = $p_course_id
												  AND r.status IN (1,4,5))
			WHERE course_id= $p_course_id;";

        $result = $db->query($c_statement);

        if (!$result) echo $db->error;
        $db->close();
    }

    public function public_db_waitlist($p_course_id,
                                       $p_student_id,
                                       $p_confirm_code)
    {
        $db = $this->db_connect();

        $checkStatement = "SELECT r.status, mail_waitlist > (NOW() - INTERVAL 2 DAY) as valid, cl.voucher as voucher
			  FROM  as_registrations r	
			  		  left join as_courses c on c.course_id = r.course_id
			  		  left join as_course_levels cl on cl.id = c.course_level_id
			 WHERE r.course_id = $p_course_id
			   AND r.student_id = $p_student_id
			   AND r.registration_code = '$p_confirm_code'
               AND mail_waitlist IS NOT NULL";

        $result = $db->query($checkStatement);
        $line = $result->fetch_array();


        //Check registration
        if (isset($line)) {
            if ($line["valid"] == "0") {
                return array('msg' => "Der Bestätigungslink ist bereits abgelaufen",
                    'result' => false,);
            } else if ($line["status"] != "5") {
                return array('msg' => "Bestätigung bereits durchgeführt",
                    'result' => false,
                    'already_used' => true);
            }
        } else {
            return array('msg' => "Link ist nicht gültig",
                'result' => false);
        }

        $statementConfirm = "
			UPDATE as_registrations
			   SET status = 2
			 WHERE course_id = $p_course_id
			   AND student_id = $p_student_id
			   AND registration_code = '$p_confirm_code'
			   AND status = 5;";


        //If this course is a voucher course AND status is before submitted
//        if ($line["voucher"] == "1" && ($line["status"] == "1" || $line["status"] == "5")) {
//            $voucher = $this->db_get_student_vouchers($p_student_id);
//            if ($voucher["amount_student"] > $voucher["amount_used"]) {
//                $used = $voucher["amount_used"] + 1 . "/" . $voucher["amount_student"];
//                $statementConfirm = "
//			      UPDATE as_registrations
//			         SET status = 3,
//			         voucher = '$used'
//			      WHERE course_id = $p_course_id
//			        AND student_id = $p_student_id
//			        AND registration_code = '$p_confirm_code'
//			        AND status = 5;";
//            }
//        }


        $db->query($statementConfirm);

        $statement2 = "
			SELECT r.status,
                               r.registration_id,
                               IF(ct.payment_type = 2, true, false) as only_cash_allowed,
                               r.voucher as voucher
			
			  FROM 		as_registrations r
			  left join as_courses c	on r.course_id = c.course_id
                          left join as_course_levels cl on c.course_level_id = cl.id
                          left join as_course_types ct on cl.course_type_id = ct.id
			  
			 WHERE r.course_id = $p_course_id
			   AND r.student_id = $p_student_id
			   AND r.registration_code = '$p_confirm_code'";

        $result = $db->query($statement2);

        if ($result->num_rows == 0) {
            $msg = "Kein Wartelistenplatz mit diesem Bestätigungscode gefunden!";
            return array('msg' => $msg,
                'result' => false,
                'already_used' => false);
        }

        $line = $result->fetch_array();

        $this->db_update_actual_count($p_course_id);

        if ($line["voucher"] != null) {
            $msg = "Eintragung zu Kurs erfolgreich. Eine Bestätigungs-E-Mail wurde versandt. Dieser Kurs wird von deinem Open Silk Block abgezogen . " . $line["voucher"];
        } else if ($line["only_cash_allowed"]) {
            $msg = "Eintragung zu Kurs erfolgreich. Eine Bestätigungs-E-Mail wurde versandt. Bitte den Kursbeitrag bar vor Ort bezahlen.";
        } else {
            $msg = "Eintragung zu Kurs erfolgreich. Eine Bestätigungs-E-Mail wurde versandt. Bitte den Kursbeitrag gemäß den Angaben in der Bestätigungs-E-Mail überweisen.";
        }

        return array('msg' => $msg,
            'result' => true,
            'registration_id' => $line["registration_id"],
            'status' => $line["status"],
            'already_used' => false);
    }

    private function db_get_student_vouchers($p_student_id)
    {
        if (!isset($p_student_id)) {
            return false;
        }

        $db = $this->db_connect();
        if ($pre_stmt = $db->prepare("SELECT sum(amount) AS amount FROM voucher WHERE student = ? AND payed = 1 ;")
        ) {
            $pre_stmt->bind_param("i", $p_student_id);
            $pre_stmt->execute();
            $pre_stmt->bind_result($amountStudent);
            $pre_stmt->fetch();
            if ($amountStudent == null) {
                $amountStudent = 0;
            }
            $pre_stmt->close();
        } else {
            echo "E-2033: Fehler: OS Block konnte nicht ermittelt werden.";
            $db->close();
            return false;
        }
        if ($pre_stmt = $db->prepare("SELECT count(registration_id) AS amountUsed FROM as_registrations WHERE voucher IS NOT NULL AND student_id = ?;")
        ) {
            $pre_stmt->bind_param("i", $p_student_id);
            $pre_stmt->execute();
            $pre_stmt->bind_result($amountUsed);
            $pre_stmt->fetch();
            $db->close();
            if ($amountUsed == null) {
                $amountUsed = 0;
            }
            $amount = ["amount_student" => $amountStudent, "amount_used" => $amountUsed];
            return $amount;
        } else {
            echo "E-2033: Fehler: OS Block konnte nicht ermittelt werden.";
            $db->close();
            return false;
        }

    }

    public function public_db_change_course($p_code, $p_newstudent, $p_course)
    {
        global $db_functions;

        if (!isset($p_newstudent) || !isset($p_code) || !isset($p_course)) {
            return ["error" => "Link ungültig"];
        }
        $db = $this->db_connect();
        if ($pre_stmt = $db->prepare("SELECT r.registration_id, r.course_id, r.student_id, r.registration_code, r.status, c.begin FROM as_registrations r
                                                INNER JOIN as_courses c ON r.course_id = c.course_id
                                                INNER JOIN as_students s ON r.student_id = s.student_id
                                                WHERE r.registration_code = ?  AND c.begin > (now() + INTERVAL 30 MINUTE)")
        ) {
            $pre_stmt->bind_param("s", $p_code);
            $pre_stmt->execute();
            $pre_stmt->bind_result($registrationId, $courseId, $studentId, $registrationCode, $status, $begin);
            $pre_stmt->fetch();
            $pre_stmt->close();
            if ($registrationId == null) {
                $db->close();
                return ["error" => "Link ungültig oder Kursbeginn in weniger als 30 Minuten"];
            }
            if (!($status == 2)) {
                $db->close();
                return ["error" => "Vorheriger Teilnehmer war nicht angemeldet"];
            }

            $p_status = 2;  // manual registration
            $save_voucher = null;

//            $voucher = $this->db_get_student_vouchers($p_newstudent);
//            if ($voucher["amount_student"] > $voucher["amount_used"]) {
//                $save_voucher = $voucher["amount_used"] + 1 . "/" . $voucher["amount_student"];
//                $p_status = "3";
//            }
            $newRegistrationId = $this->db_insert_new_registration_with_id($courseId,
                $p_newstudent,
                $p_status,
                $save_voucher);

            $pre_stmt = $db->prepare("UPDATE as_registrations SET 
                                                  status = 21, 
                                                  voucher = NULL, 
                                                  public_remark = if(public_remark = '', 'Tausch', Concat(public_remark, '\r\nTausch')) 
                                                  WHERE registration_code = ?");
            $pre_stmt->bind_param("s", $p_code);
            $pre_stmt->execute();
            $pre_stmt->close();
            return ["old_registration_id" => $registrationId, "new_registration_id" => $newRegistrationId];
        } else {
            echo "E-2033: Fehler: OS Block konnte nicht ermittelt werden.";
            $db->close();
            return false;
        }


    }

    public function db_insert_new_registration_with_id($p_course_id,
                                                       $p_student_id,
                                                       $p_status,
                                                       $voucher)
    {
        $db = $this->db_connect();

        $next_rank_position = $this->db_get_next_rank_position($p_course_id);

        if ($pre_stmt = $db->prepare("
		
			INSERT INTO as_registrations (course_id,
										  student_id,
										  status,
										  rank,
										  voucher)
										  
			VALUES (?, ?, ?, ?, ?)")
        ) {
            $pre_stmt->bind_param("iiiis", $p_course_id,
                $p_student_id,
                $p_status,
                $next_rank_position,
                $voucher);
            $pre_stmt->execute();
            $returnId = $pre_stmt->insert_id;
            if ($pre_stmt->affected_rows == 1) {

                global $db_functions;
                $this->db_update_actual_count($p_course_id);
            }

            $db->close();
            return $returnId;

        } else {
            echo "E-2033: Fehler: insert failed";
            $db->close();
            return null;
        }


        $db->close();
    }


    public function public_db_confirm_registration($p_course_id,
                                                   $p_student_id,
                                                   $p_confirm_code)
    {

        $db = $this->db_connect();


        $statement0 = "
			SELECT r.status, cl.voucher
			
			  FROM 		as_registrations r
			  left join as_courses c	on r.course_id = c.course_id
			  left join as_categories ca	on c.cat_id = ca.cat_id
			  left join as_course_levels cl on c.course_level_id = cl.id
			  
			 WHERE r.course_id = $p_course_id
			   AND r.student_id = $p_student_id
			   AND r.registration_code = '$p_confirm_code'";

        $result = $db->query($statement0);
        $line = $result->fetch_array();

        if ($line["status"] == "2" || $line["status"] == "5" || $line["status"] == "3") {
            $msg = "Bestätigung bereits durchgeführt!";
            return array('msg' => $msg,
                'result' => false,
                'already_used' => true);
        }
        $statement = "
			UPDATE as_registrations
			   SET status = 2
			 WHERE course_id = $p_course_id
			   AND student_id = $p_student_id
			   AND registration_code = '$p_confirm_code'
			   AND status = 1;";

        //If this course is a voucher course AND status is before submitted
//        if ($line["voucher"] == "1" && $line["status"] == "1") {
//            $voucher = $this->db_get_student_vouchers($p_student_id);
//            if ($voucher["amount_student"] > $voucher["amount_used"]) {
//                $used = $voucher["amount_used"] + 1 . "/" . $voucher["amount_student"];
//                $statement = "
//			      UPDATE as_registrations
//			         SET status = 3,
//			         voucher = '$used'
//			      WHERE course_id = $p_course_id
//			        AND student_id = $p_student_id
//			        AND registration_code = '$p_confirm_code'
//			        AND status = 1;";
//            }
//        }


        $result = $db->query($statement);
        $statement = "
			UPDATE as_registrations
			   SET status = 5
			 WHERE course_id = $p_course_id
			   AND student_id = $p_student_id
			   AND registration_code = '$p_confirm_code'
			   AND status = 4;";

        $result = $db->query($statement);
        $db->close();
        $db = $this->db_connect();


        $statement2 = "
			SELECT r.status,
                               r.registration_id,
                               IF(ct.payment_type = 2, true, false) as only_cash_allowed,
                               r.voucher as voucher
			
			  FROM 		as_registrations r
			  left join as_courses c	on r.course_id = c.course_id
                          left join as_course_levels cl on c.course_level_id = cl.id
                          left join as_course_types ct on cl.course_type_id = ct.id
			  
			 WHERE r.course_id = $p_course_id
			   AND r.student_id = $p_student_id
			   AND r.registration_code = '$p_confirm_code'";

        $result = $db->query($statement2);


        if (!($line = $result->fetch_array())) {
            $msg = "Keine Vormerkung mit diesem Bestätigungscode gefunden!";
            return array('msg' => $msg,
                'result' => false,
                'already_used' => false);
        }
        $new_status = $line["status"];

        if ($result->num_rows == 0) {
            $msg = "Keine Vormerkung mit diesem Bestätigungscode gefunden!";
            return array('msg' => $msg,
                'result' => false,
                'already_used' => false);
        } else {
            $statement3 = "
			UPDATE as_students
			   SET status = 1
			 WHERE student_id = $p_student_id
			   AND status = 5;";
            $db->query($statement3);


            $statement4 = "
				UPDATE as_courses
				   SET	   registration_count = (SELECT count(1) from as_registrations r1
						   							WHERE r1.course_id = $p_course_id
													  AND r1.status IN (2,3,6,7)),
				   		   pre_reg_count	  = (SELECT count(1) from as_registrations r2
						   							WHERE r2.course_id = $p_course_id
													  AND r2.status IN (1,4,5))
				WHERE course_id= $p_course_id;";

            $db->query($statement4);  // update voranmeldungen


            if ($new_status == 2 || $new_status == 3) {


                if ($line["voucher"] != null) {
                    $msg = "Bestätigung erfolgreich. Eine Bestätigungs-E-Mail wurde versandt. Dieser Kurs wird von deinem Open Silk Block abgezogen . " . $line["voucher"];
                } else if ($line["only_cash_allowed"]) {
                    $msg = "Bestätigung erfolgreich. Eine Bestätigungs-E-Mail wurde versandt. Bitte den Kursbeitrag bar vor Ort bezahlen.";
                } else {
                    $msg = "Bestätigung erfolgreich. Eine Bestätigungs-E-Mail wurde versandt. Bitte den Kursbeitrag gemäß den Angaben in der Bestätigungs-E-Mail überweisen.";
                }


                return array('msg' => $msg,
                    'result' => true,
                    'registration_id' => $line["registration_id"],
                    'status' => $line["status"],
                    'only_cash_allowed' => $line["only_cash_allowed"],
                    'already_used' => false);

            } elseif ($new_status == 5) {
                return array('msg' => "Bestätigung für Eintragung in die Warteliste erfolgreich. Eine Bestätigungs-E-Mail wurde versandt.",
                    'result' => true,
                    'registration_id' => $line["registration_id"],
                    'status' => $line["status"],
                    'already_used' => false);

            } else {
                $msg = "Vormerkung erloschen. Bitte zunächst neu anmelden.";
                return array('msg' => $msg,
                    'result' => false,
                    'already_used' => false);
            }
        }
    }


    public function public_db_unsubscribe_registration($p_course_id,
                                                       $p_student_id,
                                                       $p_unsubscribe_code)
    {

        $db = $this->db_connect();


        $statement0 = "
			SELECT r.status,
				   if(now() > c.date1 - INTERVAL 2 DAY, 1, 0 ) as frist_abgelaufen,
				   COALESCE(cl.auto_unsubscribe, 0) as auto_unsubscribe
			
			  FROM 		as_registrations r
			  left join as_courses c			on r.course_id = c.course_id
			  left join as_categories ca		on c.cat_id = ca.cat_id
			  left join as_subcategories sca	on c.subcat_id = sca.subcat_id
			  left join as_course_levels  cl      on c.course_level_id = cl.id
			  
			 WHERE r.course_id = $p_course_id
			   AND r.student_id = $p_student_id
			   AND r.registration_code = '$p_unsubscribe_code'";

        $result = $db->query($statement0);
        $line = $result->fetch_array();

        if ($line["status"] == "20" || $line["status"] == "21" || $line["status"] == "22") {
            return array('msg' => "Bereits abgemeldet!",
                'result' => false,
                'title' => "Abmeldung");
        }

        if ($line["frist_abgelaufen"] == "1") {
            return array('msg' => "Abmeldefrist ist bereits abgelaufen.",
                'result' => false,
                'title' => "Abmeldung fehlgeschlagen");
        }
        if ($line["auto_unsubscribe"] != "1") {
            return array('msg' => "Selbstabmeldung für diesen Kurs nicht vorgesehen.",
                'result' => false,
                'title' => "Abmeldung fehlgeschlagen");
        }


        $statement = "
			UPDATE as_registrations
			   SET status = 21,
			   	   public_remark = if(public_remark = '', 'Selbstabmeldung', Concat(public_remark, '\r\nSelbstabmeldung')),
			   	   voucher = null
			 WHERE course_id = $p_course_id
			   AND student_id = $p_student_id
			   AND registration_code = '$p_unsubscribe_code'";

        $result = $db->query($statement);
        $db->close();
        $db = $this->db_connect();


        $statement2 = "
			SELECT r.status, r.registration_id, ca.only_cash_allowed
			
			  FROM 		as_registrations r
			  left join as_courses c	on r.course_id = c.course_id
			  left join as_categories ca	on c.cat_id = ca.cat_id
			  
			 WHERE r.course_id = $p_course_id
			   AND r.student_id = $p_student_id
			   AND r.registration_code = '$p_unsubscribe_code'";

        $result = $db->query($statement2);


        if (!($line = $result->fetch_array())) {
            return array('msg' => "Keine Anmeldung mit diesem Bestätigungscode gefunden!",
                'result' => false,
                'title' => "Abmeldung fehlgeschlagen");
        }
        $new_status = $line["status"];

        if ($result->num_rows == 0) {
            return array('msg' => "Keine Anmeldung mit diesem Bestätigungscode gefunden!",
                'result' => false,
                'title' => "Abmeldung fehlgeschlagen");
        } else {
            $statement4 = "
				UPDATE as_courses
				   SET	   registration_count = (SELECT count(1) from as_registrations r1
						   							WHERE r1.course_id = $p_course_id
													  AND r1.status IN (2,3,6,7)),
				   		   pre_reg_count	  = (SELECT count(1) from as_registrations r2
						   							WHERE r2.course_id = $p_course_id
													  AND r2.status IN (1,4,5))
				WHERE course_id= $p_course_id;";

            $db->query($statement4);  // update voranmeldungen


            if ($new_status == 21) {

                $msg = "Abmeldung erfolgreich.";


                return array('msg' => $msg,
                    'title' => "Abmeldung erfolgreich",
                    'result' => true,
                    'registration_id' => $line["registration_id"],
                    'status' => $line["status"],
                    'only_cash_allowed' => $line["only_cash_allowed"],
                    'already_used' => false);

            } else {
                $msg = "Abmeldefrist abgelaufen.";
                return array('msg' => $msg,
                    'title' => "Abmeldefrist abgelaufen",
                    'result' => false,
                    'already_used' => false);
            }
        }
    }

    public function db_get_registrations($course_id, $count)
    {
        $db = $this->db_connect();
        $result = $db->query(
            "SELECT registration_id, registration_code FROM as_registrations 
              LEFT JOIN as_students ON as_registrations.student_id = as_students.student_id 
              WHERE course_id = $course_id AND as_registrations.status = 5 AND as_registrations.mail_waitlist IS NULL ORDER BY as_registrations.rank LIMIT $count;");
        if (!$result) echo $db->error;

        $lines = array();
        while ($line = $result->fetch_array()) {
            array_push($lines, $line);
        }
        $db->close();
        return $lines;
    }

    public function db_get_next_rank_position($p_course_id)
    {

        $db = $this->db_connect();

        if ($pre_stmt = $db->prepare("
		
			SELECT max(rank)+1 AS next_position
			  FROM (SELECT 0 AS rank
			  		  FROM dual
			  		UNION
					SELECT rank
			  		  FROM as_registrations
			 		 WHERE course_id = ?) AS r;")
        ) {
            $pre_stmt->bind_param("i", $p_course_id);
            $pre_stmt->execute();
            $pre_stmt->bind_result($next_position);
            $pre_stmt->fetch();
            $db->close();
            return $next_position;

        } else {
            echo "E-2033: Fehler: Positionierung konnte nicht ermittelt werden.";
            return false;
        }
        $db->close();
    }

}

class DB_Functions_Public_Voucher_Request extends DB_Public_Connect
{
    public function requestVoucher($title, $student_id, $amount)
    {
        $db = $this->db_connect();

        $statement = "INSERT INTO voucher(title,
								   amount,
								   student,
								   added,
								   requested,
								   payed)
			                VALUES ('$title',
								   $amount,
								   $student_id,
								   NOW(),
								   1,
								   0);";
        $result = $db->query($statement);
        if (!$result) {
            $ret = $db->error;
            $db->close();
            return $ret;
        } else {
            $db->close();
            return true;
        }
    }
}

$db_public_functions = new DB_Public_Functions();

?>