<?php
/* Coypright(2015) by Ing. Roman Breitschopf, BA 									*/
/* office@breitschopf.wien			www.breitschopf.wien 							*/
/* License for Jasmin Liska. Do not distribute to others without permission. */
//
// handle $_GET messages
//
defined("main-call") or die();

$redirect = false;

if (isset($_GET["action"]) && $_GET["action"] != "") {
	$_SESSION["action"] = htmlspecialchars($_GET["action"]);
	$redirect = true;
}
if (isset($_GET["view"]) && $_GET["view"] != "")
{
	$_SESSION["view"] = $_GET["view"];

	if (isset($_GET["course"])){
		$_SESSION["course_id"] = htmlspecialchars($_GET["course"]);
	}
	if (isset($_GET["r_course"])){
		$_SESSION["r_course_id"] = htmlspecialchars($_GET["r_course"]);
	}
	if (isset($_GET["student"])){
		$_SESSION["student_id"] = htmlspecialchars($_GET["student"]);
	}
	if (isset($_GET["user"])){
		$_SESSION["user_id"] = htmlspecialchars($_GET["user"]);
	}
	if (isset($_GET["cat"])){
		$_SESSION["cat_id"] = htmlspecialchars($_GET["cat"]);
	}
	if (isset($_GET["subcat"])){
		$_SESSION["subcat_id"] = htmlspecialchars($_GET["subcat"]);
	}
	if (isset($_GET["reg"])){
		$_SESSION["r_edit_id"] = htmlspecialchars($_GET["reg"]);
	}
	if (isset($_GET["edit"])){
		$_SESSION["n_edit"] = htmlspecialchars($_GET["edit"]);
	}
	if (isset($_GET["location"])){
		$_SESSION["location_id"] = htmlspecialchars($_GET["location"]);
	}
	if (isset($_GET["note"])){
		$_SESSION["note_id"] = htmlspecialchars($_GET["note"]);
	}
	if (isset($_GET["pay_id"])){
		$_SESSION["r_pay_id"] = htmlspecialchars($_GET["pay_id"]);
	}
	if (isset($_GET["premode"])){
		$_SESSION["premode"] = htmlspecialchars($_GET["premode"]);
	}
	if (isset($_GET["block"])){
		$_SESSION["block_id"] = htmlspecialchars($_GET["block"]);
	}
	if (isset($_GET["view_mode"])){
		$_SESSION["view_mode"] = htmlspecialchars($_GET["view_mode"]);
	}
	
		
	$redirect = true;
}

	if (!isset($_GET["focus"])) {
		$_GET["focus"] = "";
	}else {
		$_GET["focus"] = "#" . $_GET["focus"];
	}

	if ($redirect) {
		if (isset($_GET["w1"]) && $_GET["w1"] == "small"){
			if (isset($_GET["c1"]) && is_numeric($_GET["c1"])) {
				$_SESSION["sr_course_id"] = htmlspecialchars($_GET["c1"]);
				header('Location: ' . $rb_path_self . '?w1=' . $_GET["w1"] . '&c1=' . $_GET["c1"]);
			}else{	
				header('Location: ' . $rb_path_self . '?w1=' . $_GET["w1"]);
			}
		}else{
			header('Location: ' . $rb_path_self . $_GET["focus"]);
		}
		exit;
		
	};
?>