<?php

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/dbstudent.php';
require_once __DIR__ . '/../../controller/mail/mail_configuration.php';
require_once __DIR__ . '/../../controller/mail/mail_functions.php';

class DbMembership extends DB
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

    public function getSingleReadResult($sql)
    {
        $result = new Result();
        try {
            $res = $this->getSingleResult($sql);
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
        if (rb_empty($parameter['prename'])) return $this->getErrorResultObject('Bitte Vornamen angeben.');
        if (rb_empty($parameter['surname'])) return $this->getErrorResultObject('Bitte Nachnamen angeben.');
        if (rb_empty($parameter['phone'])) return $this->getErrorResultObject('Bitte Telefonnummer angeben.');
        if (rb_empty($parameter['street'])) return $this->getErrorResultObject('Bitte Straße und Hausnummer angeben.');
        if (rb_empty($parameter['zip'])) return $this->getErrorResultObject('Bitte PLZ angeben.');
        if (rb_empty($parameter['city'])) return $this->getErrorResultObject('Bitte Stadt angeben.');

        $p = $this->processParameter($parameter);
        if (empty($parameter['id'])) {
            $sql = "
		  INSERT INTO as_membership_registrations
		  	 SET prename = $p->prename,
	  	 		 surname = $p->surname,
	  	 		 email = $p->email,
	  	 		 phone = $p->phone,
	  	 		 street = $p->street,
	  	 		 zip = $p->zip,
	  	 		 city = $p->city,
	  	 		 status = $p->status;";
        } else {
            $sql = "
				UPDATE as_membership_registrations
				   SET prename = $p->prename,
				   		 surname = $p->surname,
				   		 email = $p->email,
				   		 phone = $p->phone,
				   		 street = $p->street,
				   		 zip = $p->zip,
				   	    city = $p->city,
				   	    status = $p->status
				 WHERE id = $p->id;
			";
        }

        //echo $sql;
        return $this->getWriteResultObject($sql);
    }

    public function getSearchResult($parameter)
    {
//		$p = $this->processParameter($parameter);
//		$p->filter_name = $this->db->real_escape_string($parameter['filter_name']);

        $sql = "SELECT  id,
                        CONCAT(prename, ' ', surname) AS name,
		                email,
		                phone,
		                DATE_FORMAT(cre_dat, '%d.%m.%Y um %H:%i') AS registered,
		                status
		  FROM as_membership_registrations
		  ORDER BY id DESC
		  ";

//		if( ! empty($parameter['filter_name'])) {
//			$sql .= "
//				   AND cl.name LIKE '%$p->filter_name%' ";
//		}
//		if( $parameter['filter_status'] != -2) {
//			$sql .= "
//				   AND cl.status = $p->filter_status ";
//		}

        return $this->getReadResultObject($sql);
    }

    public function getDetails($parameter)
    {
        $p = $this->processParameter($parameter);
        if (empty($parameter['id'])) return $this->getErrorResultObject('Fehler: ID nicht gefunden.');

        $sql = "
		SELECT mr.*, m.student_id as mid
		FROM as_membership_registrations mr
		LEFT JOIN as_students as m ON mr.email = m.email
		WHERE id = $p->id";

        return $this->getReadResultObject($sql);
    }

    public function convertToMember($parameter)
    {
        $p = $this->processParameter($parameter);
        if (empty($parameter['id'])) return $this->getErrorResultObject('Fehler: ID nicht gefunden.');

        $sqlUpdateApplication = "UPDATE as_membership_registrations SET status = 3 WHERE id = $p->id";

        $result = $this->getWriteResultObject($sqlUpdateApplication);

        if ($result->error == 0) {
            $sqlMember = "SELECT student_id from as_students where email = (select email from as_membership_registrations where id = $p->id)";
            $hasMember = $this->getReadResultObject($sqlMember);
            if (isset($hasMember)) {
                $sql = "UPDATE as_students as s 
                        LEFT JOIN as_membership_registrations as m ON s.email = m.email
                        SET
                         s.prename = m.prename,
                         s.surname = m.surname,
                         s.phone = m.phone,
                         s.birthday = m.birthday,
                         s.street = m.street,
                         s.zip = m.zip,
                         s.city = m.city,
                         s.membership = 1,
                         s.mb_application = m.cre_dat,
                         s.mb_begin = now(),
                         s.mb_paid_date = CONCAT(DATE_FORMAT(m.cre_dat, '%Y'),'-12-31')
                         WHERE m.id = $p->id";
            } else {
                $sql = "INSERT INTO as_students(prename,surname,email,phone,birthday,street,zip,city,newsletter,status,membership,mb_application,mb_begin,mb_paid_date, student_remark)
                    SELECT prename, surname, email, phone, birthday, street, zip, city, 1, 1, 1, cre_dat, now(), CONCAT(DATE_FORMAT(cre_dat, '%Y'),'-12-31'), ''
                    FROM as_membership_registrations 
                    WHERE id = $p->id ";
            }
            $result = $this->getWriteResultObject($sql);
            if ($result->error == 0) {
                $studentId = $this->getSingleReadResult("SELECT student_id from as_students where email = (select email from as_membership_registrations where id = $p->id)");
                if ($studentId->error == 0) {
                    $dbStudent = new DbStudent();
                    $voucherResult = $dbStudent->saveVoucher("'Open Training 10er Block'", 10, $studentId->data->student_id);

                    $studentData = $this->getSingleReadResult("SELECT email, prename, surname from as_membership_registrations where id = $p->id");
                    
                    global $mail_functions;
                    $mail_functions->send_membership_activation_mail($studentData->data->email, $studentData->data->prename, $studentData->data->surname);
                }

            }


            return $result;
        } else {
            return $result;
        }
    }


}

?>