<?php
// require_once('test.php')
define("RATIO", 0.238);

function parser() {
    $obj_array = ['rect'=> array(), 'path' => array()];
    // $info_array = array();
    $orientation = "";
    $myfile = getting_json();

    if (!$myfile) {
        echo "file does not exist\n";
    }
    
    while(!feof($myfile)) {
        $line = fgets($myfile);
        // echo $line;
        // determining landscape or portrait
        if (strpos($line, "\"orientation\":")!== false ) {
            if (strpos($line, "landscape") !== false)
                // array_push($info_array, 'L');
                $orientation = 'L';
        }
        if (strpos($line, "\"type\":") !== false) {
            // echo "hello";
            if (strpos($line, "rect") !== false)
                array_push($obj_array['rect'], parser_rect($myfile));
            else if (strpos($line, "path") !== false)
                array_push($obj_array['path'], parser_path($myfile));
        }
    }
    
    fclose($myfile);
    // return [$info_array, $obj_array];
    return [$orientation, $obj_array];
}

// $myfile = getting_json();
// parser_rect($myfile);
// print_r (parser_path($myfile));
// print_r (parser());
// parser();

function getting_json() {  
    return fopen("values.txt", "r");
}

function string_to_int($str, $start) {
    if (strpos($str, ",") !== false)
        return RATIO * (double)substr($str, $start, -1);
    return RATIO * (double)substr($str, $start);
}
// "fill": "blue",
//                     "stroke": "blue",
// $st = "\"fill\": \"blue\",";
// echo (substr($st, 9, -2));

function parser_rect($myfile) {
    $list = array();

    while(!feof($myfile)) {
        $line = trim(fgets($myfile), ' ');
        $val = 0;
        
        if (strpos($line, "strokeWidth") !== false)
            break;
        else if (strpos($line, "fill") !== false) {
            $val = substr($line, 8, -2); 
            if ($val != "null")
            $val = substr($val, 1, -1);
        }
        else if (strpos($line, "stroke") !== false)
            $val = substr($line, 11, -3);
        else if(strpos($line, "\"left\":") !== false) 
            $val = string_to_int($line, 8);
        else  if (strpos($line, "\"top\":") !== false)
            $val = string_to_int($line, 7);
        else  if (strpos($line, "\"width\":") !== false)
            $val = string_to_int($line, 9);
        else  if (strpos($line, "\"height\":") !== false)
            $val = string_to_int($line, 10);
        else continue;

        array_push($list, $val);
    }

    return $list;
}


function parser_path($myfile) {
    $list = array();
    $temp = array();
    $flag = 0;
    $color = "";
    $endpoint = 0;

    while(!feof($myfile)) {
        $line = fgets($myfile);
        // echo $line;
        if ($endpoint >= 1) {
            $num = string_to_int($line, 0);
            array_push($temp, $num);
            if ($endpoint == 2) { 
                array_push($list, $temp);
                $temp = array();
                $endpoint = -1;
                if ($flag == -1)
                    break;
            }
            $endpoint++;
        }

        if (strpos($line, "\"path\": [") !== false)
            $flag = 1;
        else if (strpos($line, "\"M\",") !== false)
            $endpoint++;
        else if (strpos($line, "\"L\",") !== false) {
            $endpoint++;
            $flag = -1;
        }
        else if (strpos($line, "\"stroke\":") !== false) {
            $line = trim($line, ' ');
            $color = substr($line, 11, -3);
        }

        if ($flag >= 9 && $flag <= 12) {
            $num = string_to_int($line, 0);
            $flag++;
            array_push($temp, $num);
            if (sizeof($temp) == 2) {
                array_push($list, $temp);
                $temp = array();
            }
        }
        else if ($flag == 14)
            $flag = 8;
        else if ($flag > 0)
            $flag++;
    } 
    
    array_push($list, $color);
    return $list;
}

function process_color($str) {
    if ($str == "null")
        $val = [null];
    if ($str == "red")
        $val = [255, 0, 0];
    else if ($str == "green")
        $val = [0, 255, 0];
    else if($str == "blue")
        $val = [0, 0, 255];
    else if($str == "black")
        $val = [0, 0, 0];
        else if($str == "yellow")
        $val = [255, 255, 0];
    else {
        $str = substr($str, 4, -1);
        if ($str[0] == '(')
            $str = substr($str, 1);
        $val = explode(",", $str); 
    }
    return $val;
}

?>