<?php

require_once(__DIR__.'/db.php');

class DbCourseType extends DB
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
		if(rb_empty($parameter['course_format_id'])) return $this->getErrorResultObject('Bitte Kursformat angeben.');
		if(rb_empty($parameter['sort_no'])) return $this->getErrorResultObject('Bitte Sortierung angeben.');
		if(rb_empty($parameter['is_kid_course'])) return $this->getErrorResultObject('Bitte Kinderkursoption wählen.');
		if(rb_empty($parameter['status'])) return $this->getErrorResultObject('Bitte Status angeben.');
		
		
		
		$p = $this->processParameter($parameter);
		
		if(empty($parameter['id'])) {
			$sql = "
				INSERT INTO as_course_types
				   SET name = $p->name,
				   		 course_format_id = $p->course_format_id,
				   	   sort_no = $p->sort_no,
				   	   is_kid_course = $p->is_kid_course,
				   	   payment_type = $p->payment_type,
				   	   status = $p->status;
			";
		}else {
			$sql = "
				UPDATE as_course_types
				   SET name = $p->name,
				   		 course_format_id = $p->course_format_id,
				   	   sort_no = $p->sort_no,
				   	   is_kid_course = $p->is_kid_course,
				   	   payment_type = $p->payment_type,
				   	   status = $p->status
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
			SELECT ct.*,
						 IFNULL(cf.name, '') as course_format_name
			  FROM as_course_types ct
	 LEFT JOIN as_course_formats cf ON cf.id = ct.course_format_id
			 WHERE 1 = 1 ";
			 
		if( ! empty($parameter['filter_name'])) {
			$sql .= "
				   AND ct.name LIKE '%$p->filter_name%' ";
		}
		if( $parameter['filter_status'] != -2) {
			$sql .= "
				   AND ct.status = $p->filter_status ";
		}
		$sql .= "	 
			 ORDER BY IFNULL(cf.sort_no, 1000), ct.sort_no
		";
		// echo $sql;
		return $this->getReadResultObject($sql);
	}
	
	public function getDetails($parameter)
	{
		$p = $this->processParameter($parameter);
		if(empty($parameter['id'])) return $this->getErrorResultObject('Fehler: ID nicht gefunden.');
		
		$sql = "
			SELECT *
			  FROM as_course_types
			 WHERE id = $p->id ";
			
		// echo $sql;
		return $this->getReadResultObject($sql);
	}
	

}
?>