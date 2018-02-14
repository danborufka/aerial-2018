<?php
session_start();
define('main-call', 'true');

require_once "../inc/new_conf.php";
require_once "../inc/dbcourseformat.php";
require_once "../inc/dbcoursetype.php";
require_once "../inc/dbcourselevel.php";
require_once "../inc/dbstudent.php";
require_once "../inc/dbmembership.php";
require_once "../inc/dbvoucherrequest.php";
require_once "../inc/result.php";
require_once "../inc/utilities.php";
require_once "../inc/rb_functions.php";


if (!isset($_SESSION["login"]) || $_SESSION["login"] != "ok") {
    echo "not logged in";
    die;
}

$cmd = $_GET['cmd'];


$allowed_commands = array('CourseFormats.GetSearchResult',
    'CourseFormats.GetDetails',
    'CourseFormats.Save',
    'CourseTypes.GetSearchResult',
    'CourseTypes.GetDetails',
    'CourseTypes.Save',
    'CourseLevels.GetSearchResult',
    'CourseLevels.GetDetails',
    'CourseLevels.Save',
    'Students.GetSearchResult',
    'Students.GetDetails',
    'Students.Save',
    'Students.CourseList',
    'Students.VoucherList',
    'Students.Voucher',
    'Students.VoucherListUsed',
    'Students.SaveVoucher',
    'Memberships.GetSearchResult',
    'Memberships.GetDetails',
    'Memberships.ConvertToMember',
    'Memberships.Save',
    'VoucherRequests.GetSearchResult',
    'VoucherRequests.ChangeState');

if (in_array($cmd, $allowed_commands)) {
    call_user_func(str_replace('.', '', $cmd));
}

function CourseFormatsGetSearchResult()
{
    $result = new Result();
    $parameterNames = array('filter_status',
        'filter_name');

    $parameter = utilities::initParameter($parameterNames);
    try {

        $dbCourseFormat = new DbCourseFormat();
        $new_result = $dbCourseFormat->getSearchResult($parameter);
        $result = $new_result;
    } catch (Exception $e) {
    }
    utilities::output($result);
}


function CourseFormatsSave()
{
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

    } catch (Exception $e) {
    }
    utilities::output($result);
}

function CourseFormatsGetDetails()
{
    $result = new Result();
    $parameterNames = array('id');

    $parameter = utilities::initParameter($parameterNames);
    try {

        $dbCourseFormat = new DbCourseFormat();
        $new_result = $dbCourseFormat->getDetails($parameter);
        $result = $new_result;
    } catch (Exception $e) {
    }
    utilities::output($result);
}

function CourseTypesGetSearchResult()
{
    $result = new Result();
    $parameterNames = array('filter_status',
        'filter_name');

    $parameter = utilities::initParameter($parameterNames);
    try {

        $dbCourseType = new DbCourseType();
        $new_result = $dbCourseType->getSearchResult($parameter);
        $result = $new_result;
    } catch (Exception $e) {
    }
    utilities::output($result);
}


function CourseTypesSave()
{
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

    } catch (Exception $e) {
    }
    utilities::output($result);
}

function CourseTypesGetDetails()
{
    $result = new Result();
    $parameterNames = array('id');

    $parameter = utilities::initParameter($parameterNames);
    try {

        $dbCourseType = new DbCourseType();
        $new_result = $dbCourseType->getDetails($parameter);
        $result = $new_result;
    } catch (Exception $e) {
    }
    utilities::output($result);
}

function CourseLevelsGetSearchResult()
{
    $result = new Result();
    $parameterNames = array('filter_status',
        'filter_name');

    $parameter = utilities::initParameter($parameterNames);
    try {

        $dbCourseLevel = new DbCourseLevel();
        $new_result = $dbCourseLevel->getSearchResult($parameter);
        $result = $new_result;
    } catch (Exception $e) {
    }
    utilities::output($result);
}


function CourseLevelsSave()
{
    $result = new Result();
    $parameterNames = array('name',
        'id',
        'course_type_id',
        'units',
        'price',
        'member_price',
        'description',
        'sort_no',
        'status',
        'voucher',
        'mail_reminder',
        'mail_reminder_hours',
        'security_training');
    $parameter = utilities::initParameter($parameterNames);

    try {

        $dbCourseLevel = new DbCourseLevel();
        $new_result = $dbCourseLevel->save($parameter);
        $result = $new_result;

    } catch (Exception $e) {
    }
    utilities::output($result);
}

function CourseLevelsGetDetails()
{
    $result = new Result();
    $parameterNames = array('id');

    $parameter = utilities::initParameter($parameterNames);
    try {
        $dbCourseLevel = new DbCourseLevel();
        $new_result = $dbCourseLevel->getDetails($parameter);
        $result = $new_result;
    } catch (Exception $e) {
    }
    utilities::output($result);
}

function StudentsGetSearchResult()
{
    $result = new Result();
    $parameterNames = array('filter_status',
        'filter_prename',
        'filter_surname',
        'filter_email',
        'filter_newsletter',
        'filter_membership',
        'filter_mb_paid_date');

    $parameter = utilities::initParameter($parameterNames);
    try {

        $dbStudent = new DbStudent();
        $new_result = $dbStudent->getSearchResult($parameter);


        $length = count($new_result->data);
        for ($i = 0; $i < $length; $i++) {
            // $new_result->data[$i]->student_remark = iconv('CP819', 'UTF-8', $new_result->data[$i]->student_remark);
            //$new_result->data[$i]->student_remark = iconv('UTF-8', 'UTF-8//IGNORE', $new_result->data[$i]->student_remark);
            //$new_result->data[$i]->email = iconv('UTF-8', 'UTF-8//IGNORE', $new_result->data[$i]->email);
            $prename_old = $new_result->data[$i]->prename;
            $surname_old = $new_result->data[$i]->surname;
            $new_result->data[$i]->prename = iconv('UTF-8', 'UTF-8//IGNORE', $new_result->data[$i]->prename);
            $new_result->data[$i]->surname = iconv('UTF-8', 'UTF-8//IGNORE', $new_result->data[$i]->surname);

            if ($surname_old != $new_result->data[$i]->surname) {
                $new_result->data[$i]->student_remark = '!!surname ' . $new_result->data[$i]->student_remark;
            }
            if ($prename_old != $new_result->data[$i]->prename) {
                $new_result->data[$i]->student_remark = '!!prename ' . $new_result->data[$i]->student_remark;
            }
            // $new_result->data[$i]->email = iconv('CP819', 'UTF-8', $new_result->data[$i]->email);
            // $new_result->data[$i]->prename = iconv('CP819', 'UTF-8', $new_result->data[$i]->prename);
            // $new_result->data[$i]->surname = iconv('CP819', 'UTF-8', $new_result->data[$i]->surname);
        }
        $result = $new_result;
    } catch (Exception $e) {
    }
    utilities::output($result);
}


function StudentsSave()
{
    $result = new Result();
    $parameterNames = array('student_id',
        'surname',
        'prename',
        'email',
        'phone',
        'birthday',
        'street',
        'zip',
        'city',
        'newsletter',
        'student_remark',
        'status',
        'membership',
        'mb_application',
        'mb_begin',
        'mb_paid_date',
        'mb_end',
        'security_training');

    $parameter = utilities::initParameter($parameterNames);

    try {
        $dbStudent = new DbStudent();

        $new_result = $dbStudent->save($parameter);

        $result = $new_result;

    } catch (Exception $e) {
    }
    utilities::output($result);
}

function StudentsGetDetails()
{
    $result = new Result();
    $parameterNames = array('student_id');

    $parameter = utilities::initParameter($parameterNames);
    try {

        $dbStudent = new DbStudent();
        $new_result = $dbStudent->getDetails($parameter);
        $result = $new_result;
    } catch (Exception $e) {
    }
    utilities::output($result);
}

function StudentsCourseList()
{
    $result = new Result();
    $parameterNames = array('student_id');
    $parameter = utilities::initParameter($parameterNames);
    try {

        $dbStudent = new DbStudent();
        $new_result = $dbStudent->getCourseList($parameter);
        $result = $new_result;
    } catch (Exception $e) {
    }
    utilities::output($result);
}

function StudentsVoucherList()
{
    $result = new Result();
    $parameterNames = array('student_id');
    $parameter = utilities::initParameter($parameterNames);
    try {

        $dbStudent = new DbStudent();
        $new_result = $dbStudent->getVoucherList($parameter);
        $result = $new_result;
    } catch (Exception $e) {
    }
    utilities::output($result);
}

function StudentsVoucher()
{
    $result = new Result();
    $parameterNames = array('voucher_id');
    $parameter = utilities::initParameter($parameterNames);
    try {

        $dbStudent = new DbStudent();
        $new_result = $dbStudent->getVoucher($parameter);
        $result = $new_result;
    } catch (Exception $e) {
    }
    utilities::output($result);
}

function StudentsVoucherListUsed()
{
    $result = new Result();
    $parameterNames = array('student_id');
    $parameter = utilities::initParameter($parameterNames);
    try {

        $dbStudent = new DbStudent();
        $new_result = $dbStudent->getVoucherListUsed($parameter);
        $result = $new_result;
    } catch (Exception $e) {
    }
    utilities::output($result);
}

function StudentsSaveVoucher()
{
    $result = new Result();
    $parameterNames = array('student_id',
        'v_title',
        'v_amount',
        'v_id');

    $parameter = utilities::initParameter($parameterNames);

    try {
        $dbStudent = new DbStudent();

        $new_result = $dbStudent->saveVoucherParameter($parameter);

        $result = $new_result;

    } catch (Exception $e) {
    }
    utilities::output($result);
}

function MembershipsGetSearchResult()
{
    $result = new Result();
    $parameterNames = array('filter_status',
        'filter_name');

    $parameter = utilities::initParameter($parameterNames);
    try {

        $dbMembership = new DbMembership();
        $new_result = $dbMembership->getSearchResult($parameter);
        $result = $new_result;
    } catch (Exception $e) {
    }
    utilities::output($result);
}

function MembershipsGetDetails()
{
    $result = new Result();
    $parameterNames = array('id');

    $parameter = utilities::initParameter($parameterNames);
    try {
        $dbMembership = new DbMembership();
        $new_result = $dbMembership->getDetails($parameter);
        $result = $new_result;
    } catch (Exception $e) {
    }
    utilities::output($result);
}

function MembershipsConvertToMember()
{
    $result = new Result();
    $parameterNames = array('id');

    $parameter = utilities::initParameter($parameterNames);
    try {
        $dbMembership = new DbMembership();
        $new_result = $dbMembership->convertToMember($parameter);
        $result = $new_result;
    } catch (Exception $e) {
    }
    utilities::output($result);
}

function MembershipsSave()
{
    $result = new Result();
    $parameterNames = array('id',
        'prename',
        'surname',
        'email',
        'phone',
        'street',
        'zip',
        'city',
        'status');
    $parameter = utilities::initParameter($parameterNames);

    try {

        $dbMembership = new DbMembership();
        $new_result = $dbMembership->save($parameter);
        $result = $new_result;
    } catch (Exception $e) {
    }
    utilities::output($result);
}

function VoucherRequestsGetSearchResult()
{
    try {
        $dbVoucherRequest = new DbVoucherRequest();
        $new_result = $dbVoucherRequest->getSearchResult();
        $result = $new_result;
    } catch (Exception $e) {
    }
    utilities::output($result);
}

function VoucherRequestsChangeState(){
    $result = new Result();
    $parameterNames = array('id');
    $parameter = utilities::initParameter($parameterNames);

    try {

        $dbVoucherRequest = new DbVoucherRequest();
        $new_result = $dbVoucherRequest->changeState($parameter);
        $result = $new_result;

    } catch (Exception $e) {
    }
    utilities::output($result);
}

$result = new Result();
$result->errtxt = "Die Anfrage >$cmd< konnte nicht identifiziert werden.";
utilities::output($result);
?>
