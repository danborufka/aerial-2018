<?php
/* Coypright(2015) by Ing. Roman Breitschopf, BA 							 */
/* office@breitschopf.wien			www.breitschopf.wien 					 */
/* License for Jasmin Liska. Do not distribute to others without permission. */
defined("main-call") or die();


function get_db_functions_ext() {
	global $db_functions_ext;
	if(!isset($db_functions_ext)) {
		require_once "controller/db_functions_ext.php";		
		$db_functions_ext = new DB_Functions_Ext();
	}
	return $db_functions_ext;
}

?>