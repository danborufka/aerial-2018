<?php
/* Coypright(2015) by Ing. Roman Breitschopf, BA 									*/
/* office@breitschopf.wien			www.breitschopf.wien 							*/
/* License for Jasmin Liska. Do not distribute to others without permission. */
session_start();
$rb_path_self = str_replace("controller/ajax_functions/publish_now.php", "", $_SERVER['PHP_SELF']);
define('main-call','true');

require_once "../configuration.php";

require_once "../db_functions.php";
require_once "../rb_functions.php";

$rb_functions->check_login_processing();
$rb_functions->check_login_is_ok_or_die_for_ajax_function();

$db_functions->courses->db_publish_now();
?>