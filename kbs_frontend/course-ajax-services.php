<?php

define('main-call', 'true');

require_once __DIR__."/inc/new_conf.php";
require_once __DIR__."/inc/dbcourse.php";
require_once __DIR__."/inc/result.php";
require_once __DIR__."/inc/utilities.php";
require_once __DIR__."/inc/rb_functions.php";

require_once "../kbs/booking/controller/mail/mail_configuration.php";
require_once "../kbs/booking/controller/mail/mail_functions.php";

$cmd = $_GET['cmd'];

$allowed_commands = array('Courses.GetSearchResult',
						  'Courses.GetDetails',
					   // 'Courses.SubmitRegistration',
						  'Membership.Registration');

if( in_array($cmd, $allowed_commands)) {
	call_user_func(str_replace('.', '', $cmd));
}

function CoursesGetSearchResult() {
	$result = new Result();
	// $parameterNames = array('filter_course_format',
													// 'filter_course_type',
													// 'filter_weekday',
													// 'filter_trainer',
													// 'filter_location');
// 							
	// $parameter = utilities::initParameter($parameterNames);
	try {
		$dbCourse = new DbCourse();
		$new_result = $dbCourse->getFormats();
		if($new_result->error != 0) utilities::output($new_result);
		$course_format_array = $new_result->data;
		foreach($course_format_array as $k => $v) {
			$level_result = $dbCourse->getLevels($v->course_format_id);
                        
			if($level_result->error != 0) utilities::output($level_result);
                            
			$course_level_array = $level_result->data;
                        $l = count($course_level_array);
                        for($i = 0; $i < $l; $i++) {
                           // if($level_result->data[$i]->description == null) continue;
                            $course_level_array[$i]->description =
				iconv('UTF-8', 'UTF-8//IGNORE', $course_level_array[$i]->description);
                        }
			foreach($course_level_array as $k2 => $v2) {
				$course_result = $dbCourse->getCourses($v2->course_level_id);
				if($course_result->error != 0) utilities::output($course_result);
				$course_level_array[$k2]->courses = $course_result->data;
			}
			$course_format_array[$k]->course_levels = $course_level_array;
		}
		$new_result->data = $course_format_array;
		$result = $new_result;
	}catch (Exception $e) {
	}
	utilities::output($result);
}


function CoursesGetDetails() {
	$result = new Result();
	$parameterNames = array('course_id',
													'registration_code');
	$parameter = utilities::initParameter($parameterNames);
	try {
		$dbCourse = new DbCourse();
		$new_result = $dbCourse->getDetails($parameter);
		$result = $new_result;
	}catch (Exception $e) {
	}
	utilities::output($result);
}

function MembershipRegistration() {
	$result = new Result();
	$parameterNames = array('prename',
							'surname',
							'email',
							'phone',
							'birthday',
							'street',
							'zip',
							'city',
							'terms_accepted');
	$parameter = utilities::initParameter($parameterNames);
	try {
		$dbCourse = new DbCourse();
		$new_result = $dbCourse->registrateMembership($parameter);
		$result = $new_result;

        global $mail_functions;
        $mail_functions->send_mail_membership_registration($parameter['email'], $parameter['prename'], $parameter['surname']);
	}catch (Exception $e) {
	}
	utilities::output($result);
}


$result = new Result();
$result->errtxt = "Die Anfrage >$cmd< konnte nicht identifiziert werden.";
utilities::output($result);
?>
