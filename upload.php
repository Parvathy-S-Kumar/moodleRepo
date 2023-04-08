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

$fs = get_file_storage();
// Prepare file record object
$fileinfo = array(
    'contextid' => $contextid,
    'component' => $component,
    'filearea' => $filearea,
    'itemid' => $itemid,
    'filepath' => $filepath,
    'filename' => $filename);

//Created a new file with the annotation data
$fn = "values.txt"; // name the file
$fi = fopen("./" .$fn, 'w'); // open the file path
fwrite($fi, $value); //save data
fclose($fi);

$values = file_get_contents("values.txt");

$json = json_decode($values,true);

$fn = "json.txt"; // name the file
$fi = fopen("./" .$fn, 'w'); // open the file path
fwrite($fi, $json); //save data
fclose($fi);
echo $json;

$orientation=$json["page_setup"]['orientation'];
$orientation=($orientation=="portrait")? 'p' : 'l';


$file = 'dummy.pdf'; 
$filepdf = fopen($file,"r");
if ($filepdf) 
{
    $line_first = fgets($filepdf);
    preg_match_all('!\d+!', $line_first, $matches);	
    // save that number in a variable
    $pdfversion = implode('.', $matches[0]);
    if($pdfversion > "1.4")
    {
        $srcfile_new="newdummy.pdf";
        $srcfile=$file;
        shell_exec('gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dNOPAUSE \
        -dBATCH -sOutputFile="'.$srcfile_new.'" "'.$srcfile.'"'); 
        $file=$srcfile_new;
    }
fclose($filepdf);
}
$pdf = new Fpdi($orientation); 
if(file_exists("./".$file))
    $pagecount = $pdf->setSourceFile($file); 
else
    die('\nSource PDF not found!'); 

for($i=1 ; $i <= $pagecount; $i++)
{
    $tpl = $pdf->importPage($i); 
    $size = $pdf->getTemplateSize($tpl); 
    $pdf->addPage(); 
    $pdf->useTemplate($tpl, 1, 1, $size['width'], $size['height'], FALSE); 
    // echo (count($json["pages"][$i-1]));
    if(count($json["pages"][$i-1]) ==0)
        continue;
    $objnum=count($json["pages"][$i-1][0]["objects"]);
    for($j=0;$j<$objnum;$j++)
    {
        $arr = $json["pages"][$i-1][0]["objects"][$j];
        if($arr["type"]=="path")
        {
           draw_path($arr,$pdf);
        }
        else if($arr["type"]=="i-text")
        {
            insert_text($arr,$pdf);
        }
    }
}


$pdf->Output('F','outputmoodle.pdf');

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

