<?php

require_once(__DIR__.'/db.php');

class DbCourseLevel extends DB
{
	public function __construct()
	{
		parent::__construct();
	}

	public function getReadResultObject($sql) {
		$result = new Result();
		try
		{	
			$res = $this->getResults($sql);
			if (! empty($this->db->error)) {
				$result->errtxt = "Fehler:" . $this->db->error;
			}else {
				$result->error = 0;
				$result->data = $res;
			}
		}
		catch(exception $e) {
				$result->error = 1;
				$result->errtxt = $e->getMessage();
		}
		return $result;
	}

	public function getWriteResultObject($sql) {
		$result = new Result();
		try
		{
			$res = $this->db->query($sql);
			if (! empty($this->db->error)) {
				$result->errtxt = "Fehler:" . $this->db->error;
			}else {
				$result->error = 0;
			}
		}
		catch(exception $e) {
				$result->error = 1;
				$result->errtxt = $e->getMessage();
		}
		return $result;
	}

	public function getErrorResultObject($errtxt) {
		$result = new Result();
		$result->errtxt = $errtxt;
		return $result;
	}
	
	public function save($parameter)
	{
		if(rb_empty($parameter['name'])) return $this->getErrorResultObject('Bitte Namen angeben.');
		if(rb_empty($parameter['course_type_id'])) return $this->getErrorResultObject('Bitte Kursart angeben.');
		if(rb_empty($parameter['sort_no'])) return $this->getErrorResultObject('Bitte Sortierung angeben.');
		if(rb_empty($parameter['units'])) return $this->getErrorResultObject('Bitte Einheiten angeben.');
		if(rb_empty($parameter['price']) || $parameter['price'] <= 0) return $this->getErrorResultObject('Bitte Preis angeben.');
		if(rb_empty($parameter['member_price']) || $parameter['member_price'] <= 0) return $this->getErrorResultObject('Bitte Mitgliedschafts-Preis angeben.');
		if(rb_empty($parameter['status'])) return $this->getErrorResultObject('Bitte Status angeben.');
		if(rb_empty($parameter['voucher'])) return $this->getErrorResultObject('Bitte angeben ob dieser Kurs von einem OS Block abgezogen wird.');
		if(rb_empty($parameter['mail_reminder'])) return $this->getErrorResultObject('Bitte Anmeldungs Erinnerungs Typ angeben.');
		if(rb_empty($parameter['mail_reminder_hours']) && (!rb_empty($parameter['mail_reminder'] && $parameter['mail_reminder'] != 0))) return $this->getErrorResultObject('Bitte angeben wielange vor einem Kurs erinnert werden soll.');
		if(rb_empty($parameter['mail_reminder_hours'])) $parameter['mail_reminder_hours'] = 0;
		$p = $this->processParameter($parameter);
		if(empty($parameter['id'])) {
			$sql = "
				INSERT INTO as_course_levels
				   SET name = $p->name,
				   		 course_type_id = $p->course_type_id,
				   		 units = $p->units,
				   		 price = $p->price,
				   		 member_price = $p->member_price,
				   		 description = $p->description,
				   	   sort_no = $p->sort_no,
				   	   status = $p->status,
				   	   voucher = $p->voucher,
				   	   mail_reminder = $p->mail_reminder,
				   	   mail_reminder_hours = $p->mail_reminder_hours,
				   	   security_training = $p->security_training
			";
		}else {
			$sql = "
				UPDATE as_course_levels
				   SET name = $p->name,
				   		 course_type_id = $p->course_type_id,
				   		 units = $p->units,
				   		 price = $p->price,
				   		 member_price = $p->member_price,
				   		 description = $p->description,
				   	   sort_no = $p->sort_no,
				   	   status = $p->status,
				   	    voucher = $p->voucher,
				   	   mail_reminder = $p->mail_reminder,
				   	   mail_reminder_hours = $p->mail_reminder_hours,
				   	   security_training = $p->security_training
				 WHERE id = $p->id;
			";
		}
		
		//echo $sql;
		return $this->getWriteResultObject($sql);
	}
	
	public function getSearchResult($parameter)
	{
		$p = $this->processParameter($parameter);
		$p->filter_name = $this->db->real_escape_string($parameter['filter_name']);
		
		$sql = "
			SELECT cl.*,
						 IFNULL(ct.name, '') as course_type_name,
						 IFNULL(cf.name, '') as course_format_name
			  FROM as_course_levels cl
	 LEFT JOIN as_course_types ct ON ct.id = cl.course_type_id
	 LEFT JOIN as_course_formats cf ON cf.id = ct.course_format_id
			 WHERE 1 = 1 ";
			 
		if( ! empty($parameter['filter_name'])) {
			$sql .= "
				   AND cl.name LIKE '%$p->filter_name%' ";
		}
		if( $parameter['filter_status'] != -2) {
			$sql .= "
				   AND cl.status = $p->filter_status ";
		}
		$sql .= "	 
			 ORDER BY IFNULL(cf.sort_no, 1000), IFNULL(ct.sort_no, 1000), cl.sort_no, cl.name
		";
		// echo $sql;
		return $this->getReadResultObject($sql);
	}
	
	public function getDetails($parameter)
	{
		$p = $this->processParameter($parameter);
		if(empty($parameter['id'])) return $this->getErrorResultObject('Fehler: ID nicht gefunden.');
		
		$sql = "
			SELECT cl.*,
						 ct.course_format_id
			  FROM as_course_levels cl
			 INNER JOIN as_course_types ct ON cl.course_type_id = ct.id
			 WHERE cl.id = $p->id ";
			
		// echo $sql;
		return $this->getReadResultObject($sql);
	}
	

}
?>