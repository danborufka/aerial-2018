<?php
/* Coypright(2016) by Ing. Roman Breitschopf, BA 									*/
/* office@breitschopf.wien			www.breitschopf.wien 							*/
/* License for Jasmin Liska. Do not distribute to others without permission. */
/* Calendar */
session_start();
$rb_path_self = str_replace("index.php", "", $_SERVER['PHP_SELF']);
define('main-call','true');
require_once "controller/cal_get_handler.php";
require_once "controller/cal_configuration.php";

require_once "controller/cal_db_functions.php";
require_once "controller/cal_rb_functions.php";

$cal_rb_functions->check_login_processing();
$cal_rb_functions->check_session_duration(45);
$cal_rb_functions->check_login_is_ok_or_die();

require_once "controller/cal_action_handler.php";

require_once "controller/cal_view_handler.php";

?>