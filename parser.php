<?php
define("RATIO", 0.238);

function normalize($val)
{
    return RATIO * $val;
}

function parser_path($arr) 
{
    $list = array();
    $temp = array();
    $len = count($arr["path"]);
    for($i=0; $i< $len-1 ; $i++)
    {
        $temp = array();
        if($i==0 || $i== $len-2)
            continue;

        // echo gettype($arr["path"][$i][1]);
        array_push($temp,normalize($arr["path"][$i][1]));
        array_push($temp,normalize($arr["path"][$i][2]));
        array_push($list,$temp);
        $temp = array();
        array_push($temp,normalize($arr["path"][$i][3]));
        array_push($temp,normalize($arr["path"][$i][4]));
        array_push($list,$temp);
    }
   print_r($list);
   return $list;

    // $flag = 0;
    // $color = "";
    // $endpoint = 0;

    // while(!feof($myfile)) {
    //     $line = fgets($myfile);
    //     // echo $line;
    //     if ($endpoint >= 1) {
    //         $num = string_to_int($line, 0);
    //         array_push($temp, $num);
    //         if ($endpoint == 2) { 
    //             array_push($list, $temp);
    //             $temp = array();
    //             $endpoint = -1;
    //             if ($flag == -1)
    //                 break;
    //         }
    //         $endpoint++;
    //     }

    //     if (strpos($line, "\"path\": [") !== false)
    //         $flag = 1;
    //     else if (strpos($line, "\"M\",") !== false)
    //         $endpoint++;
    //     else if (strpos($line, "\"L\",") !== false) {
    //         $endpoint++;
    //         $flag = -1;
    //     }
    //     else if (strpos($line, "\"stroke\":") !== false) {
    //         $line = trim($line, ' ');
    //         $color = substr($line, 11, -3);
    //     }

    //     if ($flag >= 9 && $flag <= 12) {
    //         $num = string_to_int($line, 0);
    //         $flag++;
    //         array_push($temp, $num);
    //         if (sizeof($temp) == 2) {
    //             array_push($list, $temp);
    //             $temp = array();
    //         }
    //     }
    //     else if ($flag == 14)
    //         $flag = 8;
    //     else if ($flag > 0)
    //         $flag++;
    // } 
    
    // array_push($list, $color);
    // return $list;
}

function parser_text($arr)
{
    $list=array();
    array_push($list,normalize($arr["left"]));
    array_push($list,normalize($arr["top"]));
    array_push($list,normalize($arr["width"]));
    array_push($list,normalize($arr["height"]));
    array_push($list,$arr["text"]);
    array_push($list,$arr["fill"]);
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