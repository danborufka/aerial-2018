<?php
/* Coypright(2015) by JK Informatik 									*/
/* License for Jasmin Liska. Do not distribute to others without permission. */
defined("main-call") or die();


class DB_Functions_Cronjobs extends DB_Connect
{


    public $backoffice = null;

    public function __construct()
    {
        $this->backoffice = new DB_Functions_Cronjobs_Backoffice();
    }


}

class DB_Functions_Cronjobs_Backoffice extends DB_Connect
{
    public function get_open_payments()
    {
        $db = $this->db_connect();
        $result = $db->query(
            "SELECT  reg.course_id AS course_id, as_courses.name AS course_name, 
                     reg.registration_id AS reg_id, reg.student_id AS student_id,
                     as_students.surname AS surname, as_students.prename AS prename, as_students.email AS email , date_format(reg.cre_dat, '%d.%m.%Y') AS date
            FROM as_registrations AS reg
            LEFT JOIN as_courses ON reg.course_id = as_courses.course_id
            LEFT JOIN as_students ON reg.student_id = as_students.student_id
            WHERE reg.status = 2
            AND reg.cre_dat < (NOW() - INTERVAL 14 DAY) 
	        AND reg.cre_dat > '2017-05-01 00:00:00'
            AND reg.mail_pay IS NULL
            AND reg.payment_reminder IS NULL
            AND reg.dunning IS NULL;");
        if (!$result) echo $db->error;

        $lines = array();
        while ($line = $result->fetch_array()) {
            array_push($lines, $line);
        }
        $db->close();
        return $lines;
    }

    public function set_mail_pay($ids)
    {
        $db = $this->db_connect();
        $result = $db->query("	UPDATE as_registrations
									SET mail_pay = now()
									WHERE as_registrations.registration_id IN (" . $ids . ")");
        if (!$result) echo $db->error;
        $db->close();
    }

    public function update_student_statii()
    {
        $db = $this->db_connect();
        $result = $db->query("UPDATE as_students 
                                SET membership=0,
                                    mb_end=NOW() 
                                WHERE (`membership` != 2) 
                                AND (mb_paid_date < NOW() - INTERVAL 5 HOUR)");

        if(!$result) echo $db->error;
        $db->close();
        return $result;
    }

    public function get_open_payments_reminder()
    {
        $db = $this->db_connect();
        $result = $db->query(
            "SELECT  reg.course_id AS course_id, as_courses.name AS course_name, 
                     reg.registration_id AS reg_id, reg.student_id AS student_id,
                     as_students.surname AS surname, as_students.prename AS prename, as_students.email AS email , date_format(reg.cre_dat, '%d.%m.%Y') AS date
            FROM as_registrations AS reg
            LEFT JOIN as_courses ON reg.course_id = as_courses.course_id
            LEFT JOIN as_students ON reg.student_id = as_students.student_id
            WHERE reg.status = 2
	        AND reg.payment_reminder IS NOT NULL
            AND reg.dunning IS NULL
            AND reg.mail_payment_reminder IS NULL
            AND reg.payment_reminder < (NOW() - INTERVAL 5 DAY)
	        AND reg.cre_dat > '2017-05-01 00:00:00'");
        if (!$result) echo $db->error;

        $lines = array();
        while ($line = $result->fetch_array()) {
            array_push($lines, $line);
        }
        $db->close();
        return $lines;
    }

    public function set_mail_payment_reminder($ids)
    {
        $db = $this->db_connect();
        $result = $db->query("	UPDATE as_registrations
									SET mail_payment_reminder = now()
									WHERE as_registrations.registration_id IN (" . $ids . ")");
        if (!$result) echo $db->error;
        $db->close();
    }

    public function get_open_dunnings()
    {
        $db = $this->db_connect();
        $result = $db->query(
            "SELECT  reg.course_id AS course_id, as_courses.name AS course_name, 
                     reg.registration_id AS reg_id, reg.student_id AS student_id,
                     as_students.surname AS surname, as_students.prename AS prename, as_students.email AS email , date_format(reg.cre_dat, '%d.%m.%Y') AS date
            FROM as_registrations AS reg
            LEFT JOIN as_courses ON reg.course_id = as_courses.course_id
            LEFT JOIN as_students ON reg.student_id = as_students.student_id
            WHERE reg.status = 2
	        AND reg.dunning IS NOT NULL
            AND reg.mail_dunning IS NULL
            AND reg.mail_dunning < (NOW() - INTERVAL 3 DAY)
	        AND reg.cre_dat > '2017-05-01 00:00:00'");
        if (!$result) echo $db->error;

        $lines = array();
        while ($line = $result->fetch_array()) {
            array_push($lines, $line);
        }
        $db->close();
        return $lines;
    }

    public function set_mail_dunning($ids)
    {
        $db = $this->db_connect();
        $result = $db->query("	UPDATE as_registrations
									SET mail_dunning = now()
									WHERE as_registrations.registration_id IN (" . $ids . ")");
        if (!$result) echo $db->error;
        $db->close();
    }

    public function set_storno_old_waitlist($registration_id)
    {
        $db = $this->db_connect();
        $result = $db->query("	UPDATE as_registrations SET status = 21 WHERE registration_id = " . $registration_id);
        if (!$result) {
            echo $db->error;
            return false;
        }
        $this->db_update_pre_reg_counts();
        $db->close();
        return true;
    }

    public function get_storno_old_waitlist()
    {
        $db = $this->db_connect();
        $result = $db->query(
            "SELECT as_registrations.registration_id AS registration_id, as_registrations.course_id AS course_id FROM as_registrations
              INNER JOIN as_courses ON as_registrations.course_id = as_courses.course_id
              WHERE as_registrations.status = 5 AND mail_waitlist IS NOT NULL AND mail_waitlist < (now() - INTERVAL 2 DAY) AND as_courses.begin > now()");
        if (!$result) echo $db->error;

        $lines = array();
        while ($line = $result->fetch_array()) {
            array_push($lines, $line);
        }
        $db->close();
        return $lines;
    }

    public function db_get_old_preregistrations()
    {
        $db = $this->db_connect();
        $result = $db->query(
            "SELECT as_registrations.registration_id AS registration_id, as_registrations.course_id AS course_id FROM as_registrations
              INNER JOIN as_courses ON as_registrations.course_id = as_courses.course_id
              WHERE as_registrations.status = 1 AND (as_registrations.mod_dat + INTERVAL 5 HOUR) < now() AND as_courses.begin > now()");
        if (!$result) echo $db->error;

        $lines = array();
        while ($line = $result->fetch_array()) {
            array_push($lines, $line);
        }
        $db->close();
        return $lines;
    }

    public function db_update_preregistration($registration_id)  // Status vorgemerkt updaten
    {

        $db = $this->db_connect();
        $statement = "
			UPDATE as_registrations
			   SET status = 20
			 WHERE registration_id = " . $registration_id;
        $result = $db->query($statement);
        if (!$result) {
            echo $db->error . "\r\n";
            return false;
        }
        $this->db_update_pre_reg_counts();
        $db->close();
        return true;
    }

    public function db_update_preregistrations_waitlist()
    {
        $db = $this->db_connect();
        $statement = "
			UPDATE as_registrations
			   SET status = 20,
			   	   re_calc_course = 1
			 WHERE status = 4
			   AND mod_dat + INTERVAL 5 HOUR < now();";
        $result = $db->query($statement);
        if (!$result) echo $db->error . "\r\n";

        $this->db_update_pre_reg_counts();

        $statement = "
			UPDATE as_registrations
			   SET re_calc_course = 0
			 WHERE re_calc_course = 1;";
        $result = $db->query($statement);
        if (!$result) echo $db->error . "\r\n";

        $db->close();
    }

    public function db_update_pre_reg_counts()
    {

        $db = $this->db_connect();
        $statement = "
			UPDATE as_courses c
			 INNER JOIN as_registrations r ON c.course_id = r.course_id
			   SET c.pre_reg_count	  = (SELECT count(1) FROM as_registrations r2
					   							WHERE r2.course_id = c.course_id
												  AND r2.status IN (1,4,5))
			 WHERE r.re_calc_course = 1;";
        $result = $db->query($statement);
        if (!$result) echo $db->error . "\r\n";
        $db->close();
    }

    public function db_get_open_course_spaces()
    {
        $db = $this->db_connect();
        $result = $db->query(
            "SELECT as_courses.course_id AS course_id, registration_count AS reg_count, max_count AS places, pre_reg_count AS pre_reg_count, (registration_count + SUM(if(as_registrations.status = 5 AND as_registrations.mail_waitlist IS NOT NULL, 1, 0)) + SUM(if(as_registrations.status = 1 , 1, 0))) - max_count AS open_for_waitlist
            FROM as_courses
            LEFT JOIN as_registrations ON as_courses.course_id = as_registrations.course_id
            WHERE registration_count < max_count AND pre_reg_count > 0 AND date1 > now()
            GROUP BY course_id");
        if (!$result) echo $db->error;

        $lines = array();
        while ($line = $result->fetch_array()) {
            array_push($lines, $line);
        }
        $db->close();
        return $lines;
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

    public function db_get_open_voucher_registrations()
    {
        $db = $this->db_connect();
        $result = $db->query(
            "SELECT registration_id, student_id FROM as_registrations
                INNER JOIN as_courses ON as_registrations.course_id = as_courses.course_id
                INNER JOIN as_course_levels ON as_course_levels.id = as_courses.course_level_id
                WHERE as_course_levels.voucher IS TRUE AND as_registrations.status = 2
                AND as_courses.begin > now() AND as_courses.begin <= (now() + INTERVAL 30 MINUTE)");
        if (!$result) echo $db->error;

        $lines = array();
        while ($line = $result->fetch_array()) {
            array_push($lines, $line);
        }
        $db->close();
        return $lines;
    }

    public function db_set_voucher_payment($registration_id, $voucher)
    {
        $db = $this->db_connect();
        $result = $db->query("	UPDATE as_registrations SET status = 3, voucher = '$voucher', mod_dat = now() WHERE registration_id = $registration_id");
        if (!$result) echo $db->error;
        $this->db_update_preregistrations_waitlist();
        $db->close();
    }


}


$db_functions_cronjobs = new DB_Functions_Cronjobs();

?>