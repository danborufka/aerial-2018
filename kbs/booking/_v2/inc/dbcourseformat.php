<?php

require_once(__DIR__.'/db.php');

class DbCourseFormat extends DB
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
		
		if(empty($parameter['name'])) return $this->getErrorResultObject('Bitte Kursformatnamen angeben.');
		if(empty($parameter['sort_no'])) $parameter['sort_no'] = 50;
		
		if(empty($parameter['status']) && $parameter['status'] !== '0') $parameter['status'] = 1;
		
		$p = $this->processParameter($parameter);
		
		if(empty($parameter['id'])) {
			$sql = "
				INSERT INTO as_course_formats
				   SET name = $p->name,
				   	   sort_no = $p->sort_no,
				   	   status = $p->status;
			";
		}else {
			$sql = "
				UPDATE as_course_formats
				   SET name = $p->name,
				   	   sort_no = $p->sort_no,
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
			SELECT *
			  FROM as_course_formats
			 WHERE 1 = 1 ";
			 
		if( ! empty($parameter['filter_name'])) {
			$sql .= "
				   AND name LIKE '%$p->filter_name%' ";
		}
		if( $parameter['filter_status'] != -2) {
			$sql .= "
				   AND status = $p->filter_status ";
		}
		$sql .= "	 
			 ORDER BY sort_no
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
			  FROM as_course_formats
			 WHERE id = $p->id ";
			
		// echo $sql;
		return $this->getReadResultObject($sql);
	}
	

}
?>