<?php
session_start();
define('main-call', 'true');

require_once "../_v2/inc/new_conf.php";
require_once "../_v2/inc/rb_functions.php";
require_once "../_v2/inc/result.php";
require_once('../_v2/inc/df.php');
/**
 * PHPExcel
 *
 * Copyright (C) 2006 - 2011 PHPExcel
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PHPExcel
 * @package    PHPExcel
 * @copyright  Copyright (c) 2006 - 2011 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    ##VERSION##, ##DATE##
 */

/** Error reporting */
error_reporting(E_ALL);

date_default_timezone_set('Europe/Vienna');

/** PHPExcel */
require_once '../../../PHPExcel/PHPExcel.php';

/** DB */
require_once '../_v2/inc/dbcourse.php';
require_once '../_v2/inc/utilities.php';

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set properties
$objPHPExcel->getProperties()->setCreator("Aerialsilk Vienna")
    ->setLastModifiedBy("Aerialsilk Vienna")
    ->setTitle("Office 2007 XLSX Document")
    ->setSubject("Office 2007 XLSX Document")
    ->setDescription("Document for Office 2007 XLSX, generated using PHP classes.")
    ->setKeywords("office 2007 openxml php")
    ->setCategory("Kursliste");

// Add some data
$objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('A1', 'Kurs Nr.')
    ->setCellValue('B1', 'Kurslevel')
    ->setCellValue('C1', 'Kurstyp')
    ->setCellValue('D1', 'Kursformat')
    ->setCellValue('E1', 'Kursname')
    ->setCellValue('F1', 'Kursbegin')
    ->setCellValue('G1', 'Kursende')
    ->setCellValue('H1', 'Vorname')
    ->setCellValue('I1', 'Nachname')
    ->setCellValue('J1', 'E-Mail')
    ->setCellValue('K1', 'Anmeldungsnotizen')
    ->setCellValue('L1', 'Anmeldungsnotizen (verborgen)')
    ->setCellValue('M1', 'Preis ')
    ->setCellValue('N1', 'Preis Mitglieder')
    ->setCellValue('O1', 'Preis bezahlt')
    ->setCellValue('P1', 'Status');


$dbCourses = new DbCourses();
$parameterNames = array('filter_trainer',
    'filter_zeitraum',
    'filter_from_date',
    'filter_to_date',
    'filter_location',
    'filter_categories',
    'filter_subcategories',
    'filter_status',
    'filter_publishing',
    'filter_course_number',
    'filter_student_email',
    'filter_course_format',
    'filter_course_type',
    'filter_course_level',
    'filter_student_vorname',
    'filter_student_nachname');
$parameter = utilities::initParameter($parameterNames);
$new_result = $dbCourses->loadCourseExport($parameter['filter_trainer'],
    $parameter['filter_zeitraum'],
    $parameter['filter_from_date'],
    $parameter['filter_to_date'],
    $parameter['filter_location'],
    $parameter['filter_categories'],
    $parameter['filter_subcategories'],
    $parameter['filter_status'],
    $parameter['filter_publishing'],
    $parameter['filter_course_number'],
    $parameter['filter_student_email'],
    $parameter['filter_course_format'],
    $parameter['filter_course_type'],
    $parameter['filter_course_level'],
    $parameter['filter_student_vorname'],
    $parameter['filter_student_nachname']);

$length = count($new_result->data);


for ($i = 0; $i < $length; $i++) {
    switch ($new_result->data[$i]->status) {
        case 1:
            $new_result->data[$i]->status = "Vorgemerkt";
            break;
        case 2:
            $new_result->data[$i]->status = "Angemeldet";
            break;
        case 3:
            $new_result->data[$i]->status = "Bezahlt";
            break;
        case 4:
            $new_result->data[$i]->status = "Vorgemerkt Warteliste";
            break;
        case 5:
            $new_result->data[$i]->status = "Warteliste";
            break;
        case 6:
            $new_result->data[$i]->status = "Nachholer";
            break;
        case 7:
            $new_result->data[$i]->status = "Sonstiges";
            break;
        case 20:
            $new_result->data[$i]->status = "Storno (abgelaufen)";
            break;
        case 21:
            $new_result->data[$i]->status = "Abgemeldet";
            break;
        case 22:
            $new_result->data[$i]->status = "Drop-In";
            break;
        case 23:
            $new_result->data[$i]->status = "Stundenübernahme";
            break;
        default:
            $new_result->data[$i]->status = "?";
            break;
    }


    $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A' . ($i + 2), $new_result->data[$i]->course_id)
        ->setCellValue('B' . ($i + 2), $new_result->data[$i]->course_level_name)
        ->setCellValue('C' . ($i + 2), $new_result->data[$i]->course_type_name)
        ->setCellValue('D' . ($i + 2), $new_result->data[$i]->course_format_name)
        ->setCellValue('E' . ($i + 2), $new_result->data[$i]->kursname)
        ->setCellValue('F' . ($i + 2), $new_result->data[$i]->begin)
        ->setCellValue('G' . ($i + 2), $new_result->data[$i]->end)
        ->setCellValue('H' . ($i + 2), $new_result->data[$i]->prename)
        ->setCellValue('I' . ($i + 2), $new_result->data[$i]->surname)
        ->setCellValue('J' . ($i + 2), $new_result->data[$i]->email)
        ->setCellValue('K' . ($i + 2), $new_result->data[$i]->public_remark)
        ->setCellValue('L' . ($i + 2), $new_result->data[$i]->private_remark)
        ->setCellValue('M' . ($i + 2), $new_result->data[$i]->price)
        ->setCellValue('N' . ($i + 2), $new_result->data[$i]->price_member)
        ->setCellValue('O' . ($i + 2), $new_result->data[$i]->price_payed)
        ->setCellValue('P' . ($i + 2), $new_result->data[$i]->status);
}


// Rename sheet
$objPHPExcel->getActiveSheet()->setTitle('Kurse');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Kursliste_' . date('d-m-Y') . '.xlsx"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
