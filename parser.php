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
    array_push($list,$arr["stroke"]);
   print_r($list);
   return $list;
}

function parser_text($arr)
{
    $list=array();
    array_push($list,normalize($arr["left"]),normalize($arr["top"]),normalize($arr["width"]),normalize($arr["height"]));
    array_push($list,$arr["text"],$arr["fill"]);
    return $list;
}

function parser_rectangle($arr)
{
    $list=array();
    $width=(normalize($arr["width"]))*($arr["scaleX"]);
    $height=(normalize($arr["height"]))*($arr["scaleY"]);
    array_push($list,normalize($arr["left"]),normalize($arr["top"]),$width,$height);
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
        $val =array();
        list($r, $g, $b) = sscanf($str, "#%02x%02x%02x");
        $val=[$r, $g, $b];
        // print_r($val);
        
        
    if (preg_match('/rgb/', $str)) 
        {
            $str = substr($str, 5,-1);
            print_r($str);
            $val = explode(",", $str); 
        }
    }
    // print_r($val);
    return $val;
}
?>