<?php

require_once(__DIR__ . '/db.php');
require_once(__DIR__ . '/df.php');

class DbStudent extends DB
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getReadResultObject($sql)
    {
        $result = new Result();
        try {
            $res = $this->getResults($sql);
            if (!empty($this->db->error)) {
                $result->errtxt = "Fehler:" . $this->db->error;
            } else {
                $result->error = 0;
                $result->data = $res;
            }
        } catch (exception $e) {
            $result->error = 1;
            $result->errtxt = $e->getMessage();
        }
        return $result;
    }

    public function getWriteResultObject($sql)
    {
        $result = new Result();
        try {
            $res = $this->db->query($sql);
            if (!empty($this->db->error)) {
                $result->errtxt = "Fehler:" . $this->db->error;
            } else {
                $result->error = 0;
            }
        } catch (exception $e) {
            $result->error = 1;
            $result->errtxt = $e->getMessage();
        }
        return $result;
    }

    public function getErrorResultObject($errtxt)
    {
        $result = new Result();
        $result->errtxt = $errtxt;
        return $result;
    }

    public function save($parameter)
    {

        if (rb_empty($parameter['surname'])) return $this->getErrorResultObject('Bitte Nachnamen angeben.');
        if (rb_empty($parameter['prename'])) return $this->getErrorResultObject('Bitte Vornamen angeben.');
        if (rb_empty($parameter['email'])) return $this->getErrorResultObject('Bitte Email angeben.');
//		if(rb_empty($parameter['phone'])) return $this->getErrorResultObject('Bitte Telefonnummer angeben.');
//		if(rb_empty($parameter['birthday'])) return $this->getErrorResultObject('Bitte Geburtsdatum angeben.');
//		if(rb_empty($parameter['street'])) return $this->getErrorResultObject('Bitte Straße und Hausnummer angeben.');
//		if(rb_empty($parameter['zip'])) return $this->getErrorResultObject('Bitte PLZ angeben.');
//		if(rb_empty($parameter['city'])) return $this->getErrorResultObject('Bitte Stadt angeben.');
        if (rb_empty($parameter['newsletter'])) return $this->getErrorResultObject('Bitte Newsletter angeben.');
        if (rb_empty($parameter['status'])) return $this->getErrorResultObject('Bitte Status angeben.');
        if (rb_empty($parameter['membership'])) return $this->getErrorResultObject('Bitte Mitgliedschaft angeben.');


        $p = $this->processParameter($parameter);


        // if(rb_empty($parameter['student_remark'])) 	$p->student_remark = "''";
        // if(rb_empty($parameter['phone'])) 			$p->phone = "''";
        if (rb_empty($parameter['birthday'])) $p->birthday = 'NULL';
        // if(rb_empty($parameter['street'])) 			$p->street = '';
        // if(rb_empty($parameter['zip'])) 			$p->zip = 'NULL';
        // if(rb_empty($parameter['city'])) 			$p->city = 'NULL';


        $birthday = df::formatDateFromPattern($parameter['birthday'], 'd.m.Y', 'Y-m-d');
        if (empty($birthday)) {
            $p->birthday = 'NULL';
        } else {
            $p->birthday = "'" . $birthday . "'";
        }


        $mb_application = df::formatDateFromPattern($parameter['mb_application'], 'd.m.Y', 'Y-m-d');
        if (empty($mb_application)) {
            $p->mb_application = 'NULL';
        } else {
            $p->mb_application = "'" . $mb_application . "'";
        }


        $mb_begin = df::formatDateFromPattern($parameter['mb_begin'], 'd.m.Y', 'Y-m-d');
        if (empty($mb_begin)) {
            $p->mb_begin = 'NULL';
        } else {
            $p->mb_begin = "'" . $mb_begin . "'";
        }

        $mb_paid_date = df::formatDateFromPattern($parameter['mb_paid_date'], 'd.m.Y', 'Y-m-d');
        if (empty($mb_paid_date)) {
            $p->mb_paid_date = 'NULL';
        } else {
            $p->mb_paid_date = "'" . $mb_paid_date . "'";
        }

        $mb_end = df::formatDateFromPattern($parameter['mb_end'], 'd.m.Y', 'Y-m-d');
        if (empty($mb_end)) {
            $p->mb_end = 'NULL';
        } else {
            $p->mb_end = "'" . $mb_end . "'";
        }


        if (empty($parameter['student_id'])) {
            $sql = "
				INSERT INTO as_students
				   SET prename = $p->prename,
			   		   surname = $p->surname,
				   	   email = $p->email,
					   phone = $p->phone,
					   birthday = $p->birthday,
					   street = $p->street,
					   zip = $p->zip,
					   city = $p->city,
				   	   student_remark = $p->student_remark,
				   	   newsletter = $p->newsletter,
				   	   status = $p->status,
				   	   membership = $p->membership,
				   	   mb_application = $p->mb_application,
				   	   mb_begin = $p->mb_begin,
				   	   mb_paid_date = $p->mb_paid_date,
				   	   mb_end = $p->mb_end,
				   	   security_training = $p->security_training;
			";
        } else {
            $sql = "
				UPDATE as_students
				   SET prename = $p->prename,
			   		   surname = $p->surname,
				   	   email = $p->email,
					   phone = $p->phone,
					   birthday = $p->birthday,
					   street = $p->street,
					   zip = $p->zip,
					   city = $p->city,
				   	   student_remark = $p->student_remark,
				   	   newsletter = $p->newsletter,
				   	   status = $p->status,
				   	   membership = $p->membership,
				   	   mb_application = $p->mb_application,
				   	   mb_begin = $p->mb_begin,
				   	   mb_paid_date = $p->mb_paid_date,
				   	   mb_end = $p->mb_end,
				   	   security_training = $p->security_training
				 WHERE student_id = $p->student_id;
			";
        }

//		 echo $sql;
        return $this->getWriteResultObject($sql);
    }

    public function getSearchResult($parameter)
    {
        $p = $this->processParameter($parameter);
        $p->filter_prename = $this->db->real_escape_string($parameter['filter_prename']);
        $p->filter_surname = $this->db->real_escape_string($parameter['filter_surname']);
        $p->filter_email = $this->db->real_escape_string($parameter['filter_email']);
        $p->filter_mb_paid_date = $this->db->real_escape_string($parameter['filter_mb_paid_date']);


        $mb_paid_date = df::formatDateFromPattern($parameter['filter_mb_paid_date'], 'd.m.Y', 'Y-m-d');
        if (rb_empty($mb_paid_date)) {
            $p->filter_mb_paid_date = null;
        } else {
            $p->filter_mb_paid_date = $mb_paid_date;
        }


        $sql = "
			SELECT s.student_id,
				   s.prename,
				   s.surname,
				   s.email,
				   s.newsletter,
				   s.status,
				   s.student_remark,
				   s.membership,
				   s.security_training,
				   date_format(s.mb_paid_date, '%d.%m.%Y') AS mb_paid_date_formatted,  
				   date_format(s.mb_application, '%d.%m.%Y') AS mb_application_date_formatted
			  FROM as_students s
			 WHERE 1 = 1 ";

        if (!empty($parameter['filter_prename'])) {
            $sql .= "
				   AND prename LIKE '%$p->filter_prename%' ";
        }

        if (!empty($parameter['filter_surname'])) {
            $sql .= "
				   AND surname LIKE '%$p->filter_surname%' ";
        }

        if (!empty($parameter['filter_email'])) {
            $sql .= "
				   AND email LIKE '%$p->filter_email%' ";
        }

        if ($parameter['filter_newsletter'] != -2) {

            $sql .= "
				   AND newsletter = $p->filter_newsletter ";
        }

        if ($parameter['filter_status'] != -2) {
            $sql .= "
				   AND status = $p->filter_status ";
        }

        if ($parameter['filter_membership'] != -2 && $parameter['filter_membership'] != -3) {

            $sql .= "
				   AND membership = $p->filter_membership ";
        }
        if ($parameter['filter_membership'] == -3) {

            $sql .= "
				   AND mb_application IS NOT NULL ";
        }

        if (!empty($parameter['filter_mb_paid_date'])) {
            $sql .= "
				   AND mb_paid_date >= '$p->filter_mb_paid_date' ";
        }
        // echo $sql;
        return $this->getReadResultObject($sql);
    }

    public function getDetails($parameter)
    {
        $p = $this->processParameter($parameter);
        if (empty($parameter['student_id'])) return $this->getErrorResultObject('Fehler: ID nicht gefunden.');

        $sql = "
			SELECT s.*,
				   date_format(s.birthday, '%d.%m.%Y') as birthday_formatted,
				   date_format(s.mb_application, '%d.%m.%Y') as mb_application_formatted,
				   date_format(s.mb_begin, '%d.%m.%Y') as mb_begin_formatted,
				   date_format(s.mb_paid_date, '%d.%m.%Y') as mb_paid_date_formatted,
				   date_format(s.mb_end, '%d.%m.%Y') as mb_end_formatted
			  FROM as_students s
			 WHERE student_id = $p->student_id ";

        // echo $sql;
        return $this->getReadResultObject($sql);
    }

    public function getCourseList($parameter)
    {
        $p = $this->processParameter($parameter);
        if (empty($parameter['student_id'])) return $this->getErrorResultObject('Fehler: ID nicht gefunden.');

        $sql = "
			SELECT as_courses.course_id AS course_id,
			 as_course_levels.name AS level_name,
			 as_course_types.name AS type_name,
			 as_course_formats.name AS format_name,
			 as_registrations.status AS status,
			 as_registrations.public_remark AS public_remark,
			 as_registrations.private_remark AS private_remark
            FROM as_students
            LEFT JOIN as_registrations ON as_students.student_id = as_registrations.student_id
            LEFT JOIN as_courses ON as_registrations.course_id = as_courses.course_id
            LEFT JOIN as_course_levels ON as_courses.course_level_id = as_course_levels.id
            LEFT JOIN as_course_types ON as_course_levels.course_type_id = as_course_types.id
            LEFT JOIN as_course_formats ON as_course_types.course_format_id = as_course_formats.id
            WHERE (as_registrations.present1 = 1 OR
            as_registrations.present2 = 1 OR
            as_registrations.present3 = 1 OR
            as_registrations.present4 = 1 OR
            as_registrations.present5 = 1 OR
            as_registrations.present6 = 1 OR
            as_registrations.present7 = 1 OR
            as_registrations.present8 = 1 OR
            as_registrations.present9 = 1 OR
            as_registrations.present10 = 1 OR
            as_registrations.present11 = 1 OR
            as_registrations.present12 = 1)
            AND as_students.student_id = $p->student_id
            ORDER BY as_courses.begin desc;";

        // echo $sql;
        return $this->getReadResultObject($sql);
    }

    public function getVoucherList($parameter)
    {
        $p = $this->processParameter($parameter);
        if (empty($parameter['student_id'])) return $this->getErrorResultObject('Fehler: ID nicht gefunden.');

        $sql = "SELECT id, title, date_format(added, '%d.%m.%Y') AS added , amount
        FROM voucher
        WHERE student = $p->student_id AND payed = 1 
        ORDER BY added desc
        ";
        return $this->getReadResultObject($sql);
    }

    public function getVoucher($parameter)
    {
        $p = $this->processParameter($parameter);
        if (empty($parameter['voucher_id'])) return $this->getErrorResultObject('Fehler: ID nicht gefunden.');

        $sql = "SELECT id, title, amount, student, added, expires
            FROM voucher
            WHERE id = $p->voucher_id AND payed = 1 ";
        return $this->getReadResultObject($sql);
    }

    public function getVoucherListUsed($parameter)
    {
        $p = $this->processParameter($parameter);
        if (empty($parameter['student_id'])) return $this->getErrorResultObject('Fehler: ID nicht gefunden.');

        $sql = "SELECT as_courses.course_id AS course_id,
			 as_course_levels.name AS level_name,
			 as_course_types.name AS type_name,
			 as_course_formats.name AS format_name,
			 as_registrations.status AS status,
			 as_registrations.public_remark AS public_remark,
			 as_registrations.private_remark AS private_remark,
			 as_registrations.voucher as voucher
            FROM as_students
            LEFT JOIN as_registrations ON as_students.student_id = as_registrations.student_id
            LEFT JOIN as_courses ON as_registrations.course_id = as_courses.course_id
            LEFT JOIN as_course_levels ON as_courses.course_level_id = as_course_levels.id
            LEFT JOIN as_course_types ON as_course_levels.course_type_id = as_course_types.id
            LEFT JOIN as_course_formats ON as_course_types.course_format_id = as_course_formats.id
            WHERE as_students.student_id = $p->student_id AND as_registrations.voucher IS NOT NULL
            ORDER BY as_courses.begin DESC
        ";
        return $this->getReadResultObject($sql);
    }

    public function saveVoucherParameter($parameter)
    {
        if (rb_empty($parameter['v_title'])) return $this->getErrorResultObject('Bitte Titel angeben.');
        if (rb_empty($parameter['v_amount'])) return $this->getErrorResultObject('Bitte Menge angeben.');

        $p = $this->processParameter($parameter);
        if (isset($parameter['v_id']) && '' != $parameter['v_id']) {
            return $this->updateVoucher($p->v_id, $p->v_title, $p->v_amount);
        } else {
            return $this->saveVoucher($p->v_title, $p->v_amount, $p->student_id);
        }

    }

    public function saveVoucher($title, $amount, $student_id)
    {
        $sql = "
				INSERT INTO voucher
				   SET title = $title ,
			   		   amount = $amount ,
				   	   added = now() ,
					   student = $student_id;
			";
        return $this->getWriteResultObject($sql);
    }

    public function updateVoucher($id, $title, $amount)
    {
        $sql = "
				UPDATE voucher
				   SET title = $title ,
			   		   amount = $amount
			   		   WHERE id = $id;
			";
        return $this->getWriteResultObject($sql);
    }


}

?>