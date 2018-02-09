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
require_once '../_v2/inc/dbstudent.php';
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
    ->setCategory("Teilnehmerliste");


// Add some data
$objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('A1', 'Vorname')
    ->setCellValue('B1', 'Nachname')
    ->setCellValue('C1', 'Email')
    ->setCellValue('D1', 'Status')
    ->setCellValue('E1', 'Newsletter')
    ->setCellValue('F1', 'Mitgliedschaft')
    ->setCellValue('G1', 'Mitgliedschaft beantragt am')
    ->setCellValue('H1', 'Mitgliedschaft bezahlt bis')
    ->setCellValue('I1', 'Teilnehmer-Vermerk');


$dbStudent = new DbStudent();

$parameterNames = array('filter_status',
    'filter_prename',
    'filter_surname',
    'filter_email',
    'filter_newsletter',
    'filter_membership',
    'filter_mb_paid_date');
$parameter = utilities::initParameter($parameterNames);
$new_result = $dbStudent->getSearchResult($parameter);
$length = count($new_result->data);
for( $i = 0; $i < $length; $i++) {

    $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A' . ($i + 2), $new_result->data[$i]->prename)
        ->setCellValue('B' . ($i + 2), $new_result->data[$i]->surname)
        ->setCellValue('C' . ($i + 2), $new_result->data[$i]->email)
        ->setCellValue('D' . ($i + 2), ($new_result->data[$i]->status == 1 ? '✔' : '✖') )
        ->setCellValue('E' . ($i + 2), ($new_result->data[$i]->newsletter == 1 ? '✔' : '✖'))
        ->setCellValue('F' . ($i + 2), ($new_result->data[$i]->membership == 1 ? '✔' : '✖'))
        ->setCellValue('G' . ($i + 2), $new_result->data[$i]->mb_application_date_formatted)
        ->setCellValue('H' . ($i + 2), $new_result->data[$i]->mb_paid_date_formatted)
        ->setCellValue('I' . ($i + 2), $new_result->data[$i]->student_remark);
}


// Rename sheet
$objPHPExcel->getActiveSheet()->setTitle('Teilnehmer');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Teilnehmerliste_' . date('d-m-Y') . '.xlsx"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
