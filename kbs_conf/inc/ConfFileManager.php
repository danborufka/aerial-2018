<?php

class ConfFileManager {
	
	public static function getConfData($confPath)
	{
		$conf_json = file_get_contents($confPath);
		$conf_parsed = json_decode($conf_json);
		
		return $conf_parsed;
	}
}

?>