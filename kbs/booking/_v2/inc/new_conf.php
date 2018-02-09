<?php
/* Coypright(2016) by Ing. Roman Breitschopf, BA */

	require_once dirname(dirname(dirname(dirname(__DIR__)))) . '/kbs_conf/inc/ConfFileManager.php';

	defined("main-call") or die("error-conf");
	
	$confPath = dirname(dirname(dirname(dirname(__DIR__)))) . '/kbs_conf/kbs_configuration.json';
	$conf = ConfFileManager::getConfData($confPath);
	
	$CONF['production_mode'] = "auto";
										/*		auto = automatic detection
										 * 		1 = production_mode
										 * 		2 = development_mode
										 */
	
		if($CONF['production_mode'] == "auto") {
			if($_SERVER['SERVER_NAME'] == "localhost") {
				$CONF['production_mode'] = 2;
			} else {
				$CONF['production_mode'] = 1;
			}
		}
		$_SESSION["production_mode"] = $CONF['production_mode'];
		if ($CONF['production_mode'] == 1) {
			// PRODUCTION MODE
			if ($_SERVER["HTTPS"] != "on") {
				header('Location: https://' . $_SERVER["SERVER_NAME"] . $_SERVER["PHP_SELF"]);
			}

			$_conf = $conf->prod;
			
			$CONF['db_url']	 =	$_conf->db_url;
			$CONF['db_name'] =	$_conf->db_name;
			$CONF['db_user'] =	$_conf->db_user;
			$CONF['db_pw']	 =	$_conf->db_pwd;	
			$CONF['link_for_registration']="https://www.aerialsports.at";	
		}else{
			// DEVELOPMENT MODE
			
			$_conf = $conf->dev;
			
			$CONF['db_url']	 =	$_conf->db_url;
			$CONF['db_name'] =	$_conf->db_name;
			$CONF['db_user'] =	$_conf->db_user;
			$CONF['db_pw']	 =	$_conf->db_pwd;
			$CONF['link_for_registration']="xxx";
		}
?>