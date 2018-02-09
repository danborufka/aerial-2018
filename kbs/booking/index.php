<?php
/* Coypright(2015) by Ing. Roman Breitschopf, BA 									*/
/* office@breitschopf.wien			www.breitschopf.wien 							*/
/* License for Jasmin Liska. Do not distribute to others without permission. */
session_start();
$rb_path_self = str_replace("index.php", "", $_SERVER['PHP_SELF']);
define('main-call','true');
require_once "controller/get_handler.php";
require_once "controller/configuration.php";

require_once "controller/db_functions.php";
require_once "controller/rb_functions.php";
require_once "controller/autoload/autoload_db_functions_ext.php";

$rb_functions->check_login_processing();
$rb_functions->check_session_duration(45);
$rb_functions->check_login_is_ok_or_die();

require_once "controller/action_handler.php";

require_once "controller/view_handler.php";


?>