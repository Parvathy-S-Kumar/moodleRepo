<?php

/**
 * @author Tausif Iqbal, Vishal Rao
 * This page saves annotated pdf to database.
 * 
 * It gets the file data from JavaScript through POST request.
 * Then save it temporarily in this directory.
 * Then create new file in databse using this temporary file.
 */

require_once('../../config.php');
require_once('locallib.php');
require __DIR__ . '/test.php';
require __DIR__ . '/parser.php';
// require_once('parser.php');
// require_once('test.php')
include 'fpdi-fpdf/vendor/autoload.php';
// include 'fpdi-fpdf/vendor/alphapdf.php';
use setasign\Fpdi\Fpdi;
// include 

define("RATIO", 0.238);

//Getting all the data from mypdfannotate.js
$value = $_POST['id'];
$contextid = $_POST['contextid'];
$attemptid = $_POST['attemptid'];
$filename = $_POST['filename'];
$component = 'question';
$filearea = 'response_attachments';
$filepath = '/';
$itemid = $attemptid;

// $fs = get_file_storage();
// // Prepare file record object
// $fileinfo = array(
//     'contextid' => $contextid,
//     'component' => $component,
//     'filearea' => $filearea,
//     'itemid' => $itemid,
//     'filepath' => $filepath,
//     'filename' => $filename);

echo $contextid;

//Created a new file with the annotation data
$fn = "values.txt"; // name the file
$fi = fopen("./" .$fn, 'w'); // open the file path
fwrite($fi, $value); //save data
fclose($fi);

// for($i=1;$i<=$pagecount;$i++) { 
//     $tpl = $pdf->importPage($i); 
//     $size = $pdf->getTemplateSize($tpl); 
//     $pdf->addPage(); 
//     $pdf->useTemplate($tpl, 1, 1, $size['width'], $size['height'], FALSE); 
//     draw($obj_array, $pdf);
// } 


function debug_to_console($data) {
    $output = $data;
    if (is_array($output))
        $output = implode(',', $output);

    echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
}
$myfile=fopen("values.txt", "r");

// // $myfile = getting_json();
// // parser_rect($myfile);
$final_array = parser();
$obj_array = $final_array[1];
$info_array = $final_array[0];
$orientation = $info_array[0];
// $orientation = $final_array[0];

$file =  'dummy.pdf'; 
$pdf = new fpdi('l'); 

if(file_exists("./".$file))
    $pagecount = $pdf->setSourceFile($file); 
else
    die('\nSource PDF not found!'); 
 
for($i=1;$i<=$pagecount;$i++) { 
    $tpl = $pdf->importPage($i); 
    $size = $pdf->getTemplateSize($tpl); 
    $pdf->addPage(); 
    $pdf->useTemplate($tpl, 1, 1, $size['width'], $size['height'], FALSE); 
    draw($obj_array, $pdf);
} 

// create_pdf();
$pdf->Output('F','outputmoodle.pdf');

//Using FPDF-FPDI functions to annotate the PDF
// $file = $filename;
// $pdf = new Fpdi('l'); 
// $pagecount = $pdf->setSourceFile($file);
// draw($pdf,$pagecount);

// // for($i=1;$i<=$pagecount;$i++) { 
// //     $tpl = $pdf->importPage($i); 
// //     $size = $pdf->getTemplateSize($tpl); 
// //     $pdf->addPage(); 
// //     $pdf->useTemplate($tpl, 1, 1, $size['width'], $size['height'], FALSE); 
// //     $pdf->rect(50, 
// //     50, 
// //     100, 
// //     100);
// // } 
// $pdf->Output('F','outputmoodle.pdf');

$fname='outputmoodle.pdf';
$temppath = './' . $fname;
// echo $temppath;

$fs = get_file_storage();
// Prepare file record object
$fileinfo = array(
    'contextid' => $contextid,
    'component' => $component,
    'filearea' => $filearea,
    'itemid' => $itemid,
    'filepath' => $filepath,
    'filename' => $filename);

//check if file already exists, then first delete it.
$doesExists = $fs->file_exists($contextid, $component, $filearea, $itemid, $filepath, $filename);
if($doesExists === true)
{
    $storedfile = $fs->get_file($contextid, $component, $filearea, $itemid, $filepath, $filename);
    $storedfile->delete();
}
// finally save the file (creating a new file)
$fs->create_file_from_pathname($fileinfo, $temppath);ile_from_pathname($fileinfo, $temppath);
?>

