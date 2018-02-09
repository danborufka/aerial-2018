<?php

class DB
{
	protected $db;
	
	public function __construct($db = null)
	{
		if ($db != null && $db instanceof mysqli)
		{
			$this->db = $db;
		}
		else
		{
			global $CONF;
			
			/*if($CONF["db_conn"] != null && $CONF["db_conn"] instanceof mysqli)
			{
				$this->db = $CONF["db_conn"];
			}
			else */
			{
				$this->db = new mysqli($CONF["db_url"], $CONF["db_user"], $CONF["db_pw"], $CONF["db_name"]);
                                $this->db->set_charset("utf8");
                               
              	$CONF["db_conn"] = $this->db;
			}
		}
	}
	
	public function getValue($sql, $default_value = "") 
	{
		try
		{
			if($res = $this->db->query($sql)) {
				if($row = $res->fetch_array()) {
					$default_value = $row[0];
				}
				$res->free();
			}
		}
		catch(Exception $e) 
		{
		}
		return $default_value;
	}
	
	function getResults($sql, $indexColumn = "")
	{
		$r = [];
		$q1 = $this->db->query($sql);
		
		if( ! $q1 ) throw new Exception($this->db->error, $this->db->errno);
		
		while($res = $q1->fetch_object())
		{
			if ($indexColumn != "") {
				$result_arr = get_object_vars ($res);
				$r[$result_arr[$indexColumn]] = $res;
			}
			else {
				$r[] = $res;
			}
		}
		return $r;
	}

    function getSingleResult($sql)
    {
        $q1 = $this->db->query($sql);

        if( ! $q1 ) throw new Exception($this->db->error, $this->db->errno);

        return $q1->fetch_object();
    }
	
	function getSqlStringValue($string)
	{
		if ((string)$string == "0")
			return "'0'";
		if (strlen($string) == 0 || $string == "NULL" || $string === null)
			return "''";
		else
			return "'" . $this->db->real_escape_string($string) . "'";
	}
	
	function processParameter($parameter) // escape, quote and return as object
	{
		$result = new stdClass();
		foreach ($parameter as $parameterName => $parameterValue) {
			if(! is_array($parameterValue)) $$parameterName = $this->getSqlStringValue($parameterValue);
			else {
				$$parameterName = $parameterValue;
				foreach($$parameterName as $k => $v) {
					$$parameterName[$k] = $this->getSqlStringValue($$parameterName[$k]);
				}
			}
			$result->$parameterName = $$parameterName;
			
		}
		return $result;
	}
	
}
?>