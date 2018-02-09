<?php

echo "test123ssss"; exit();

define('main-call', 'true');

require_once "../inc/new_conf.php";
require_once "../inc/dbcourses.php";
require_once "../inc/result.php";
require_once "../inc/utilities.php";
require_once "../inc/rb_functions.php";

$cmd = $_GET['cmd'];


$allowed_commands = array('Courses.GetSearchResult');

if( in_array($cmd, $allowed_commands)) {
	call_user_func(str_replace('.', '', $cmd));
}

function CoursesGetSearchResult() {
	$result = new Result();
	$parameterNames = array('filter_course_format',
													'filter_course_type',
													'filter_weekday',
													'filter_trainer',
													'filter_location');
							
	$parameter = utilities::initParameter($parameterNames);
	try {
		echo "CoursesGetSearchResult executed";
		// $dbCourseFormat = new DbCourseFormat();
		// $new_result = $dbCourseFormat->getSearchResult($parameter);
		// $result = $new_result;
	}catch (Exception $e) {
	}
	utilities::output($result);
}


function CourseFormatsSave() {
	$result = new Result();
	$parameterNames = array('name',
							'id',
							'sort_no',
							'status');
							
	$parameter = utilities::initParameter($parameterNames);
	
	try {
		$dbCourseFormat = new DbCourseFormat();
		
		$new_result = $dbCourseFormat->save($parameter);
		
		$result = $new_result;
		
	}catch (Exception $e) {
	}
	utilities::output($result);
}

function CourseFormatsGetDetails() {
	$result = new Result();
	$parameterNames = array('id');
							
	$parameter = utilities::initParameter($parameterNames);
	try {
			
		$dbCourseFormat = new DbCourseFormat();
		$new_result = $dbCourseFormat->getDetails($parameter);
		$result = $new_result;
	}catch (Exception $e) {
	}
	utilities::output($result);
}

function CourseTypesGetSearchResult() {
	$result = new Result();
	$parameterNames = array('filter_status',
							'filter_name');
							
	$parameter = utilities::initParameter($parameterNames);
	try {
			
		$dbCourseType = new DbCourseType();
		$new_result = $dbCourseType->getSearchResult($parameter);
		$result = $new_result;
	}catch (Exception $e) {
	}
	utilities::output($result);
}


function CourseTypesSave() {
	$result = new Result();
	$parameterNames = array('name',
													'id',
													'course_format_id',
													'sort_no',
													'is_kid_course',
													'payment_type',
													'status');
							
	$parameter = utilities::initParameter($parameterNames);
	
	try {
		$dbCourseType = new DbCourseType();
		
		$new_result = $dbCourseType->save($parameter);
		
		$result = $new_result;
		
	}catch (Exception $e) {
	}
	utilities::output($result);
}

function CourseTypesGetDetails() {
	$result = new Result();
	$parameterNames = array('id');
							
	$parameter = utilities::initParameter($parameterNames);
	try {
			
		$dbCourseType = new DbCourseType();
		$new_result = $dbCourseType->getDetails($parameter);
		$result = $new_result;
	}catch (Exception $e) {
	}
	utilities::output($result);
}

function CourseLevelsGetSearchResult() {
	$result = new Result();
	$parameterNames = array('filter_status',
													'filter_name');
							
	$parameter = utilities::initParameter($parameterNames);
	try {
			
		$dbCourseLevel = new DbCourseLevel();
		$new_result = $dbCourseLevel->getSearchResult($parameter);
		$result = $new_result;
	}catch (Exception $e) {
	}
	utilities::output($result);
}


function CourseLevelsSave() {
	$result = new Result();
	$parameterNames = array('name',
													'id',
													'course_type_id',
													'sort_no',
													'status');
	$parameter = utilities::initParameter($parameterNames);
	
	try {
		
		$dbCourseLevel = new DbCourseLevel();
		$new_result = $dbCourseLevel->save($parameter);
		$result = $new_result;
		
	}catch (Exception $e) {
	}
	utilities::output($result);
}

function CourseLevelsGetDetails() {
	$result = new Result();
	$parameterNames = array('id');
							
	$parameter = utilities::initParameter($parameterNames);
	try {
		$dbCourseLevel = new DbCourseLevel();
		$new_result = $dbCourseLevel->getDetails($parameter);
		$result = $new_result;
	}catch (Exception $e) {
	}
	utilities::output($result);
}

$result = new Result();
$result->errtxt = "Die Anfrage >$cmd< konnte nicht identifiziert werden.";
utilities::output($result);
?>
