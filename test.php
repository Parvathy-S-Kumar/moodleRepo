<?php
// require 'newparser.php';
function draw_path($arr, $pdf) 
{
    $list=parser_path($arr);
    // print_r ($list);
    // $stroke = process_color(end($list));
    // print_r ($stroke);
    // $pdf->SetDrawColor($stroke[0], $stroke[1], $stroke[2]);
    for($k = 0; $k < sizeof($list) - 2; $k++) {
        $pdf->Line($list[$k][0], 
        $list[$k][1], 
        $list[$k + 1][0], 
        $list[$k + 1][1]);
    }
}

function insert_text($arr,$pdf)
{
    $list=parser_text($arr);
    $color = process_color($list[5]);
    print_r($color);
    $pdf->SetTextColor(0,0,0);
    $pdf->SetFont('Times');
    $pdf->SetFontSize(16);
    print_r($list);
    $pdf->text($list[0],
    $list[1] + $list[3],
    $list[4]);
}
?>