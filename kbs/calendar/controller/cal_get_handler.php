<?php
/* Coypright(2015) by Ing. Roman Breitschopf, BA 									*/
/* office@breitschopf.wien			www.breitschopf.wien 							*/
/* License for Jasmin Liska. Do not distribute to others without permission. */
//
// handle $_GET messages
//
defined("main-call") or die();

$redirect = false;

if (isset($_GET["cal_action"]) && $_GET["cal_action"] != "") {
	$_SESSION["cal_action"] = htmlspecialchars($_GET["cal_action"]);
	$redirect = true;
}
if (isset($_GET["cal_view"]) && $_GET["cal_view"] != "")
{
	$_SESSION["cal_view"] = $_GET["cal_view"];

	if (isset($_GET["course"])){
		$_SESSION["course_id"] = htmlspecialchars($_GET["course"]);   
	}
	if (isset($_GET["event"])){
		$_SESSION["event_id"] = htmlspecialchars($_GET["event"]);   
	}

	$redirect = true;
}

if ($redirect) header('Location: ' . $rb_path_self);

?>