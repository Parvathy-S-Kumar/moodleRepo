<?php 
function create_pdf() {
    $final_array = parser();
    $obj_array = $final_array[1];
    $orientation = $final_array[0];

    $file = 'input.pdf'; 
    // $pdf = new Fpdi($orientation); 
    $pdf = new AlphaPDF($orientation);
    $pdf->SetFont('Times', '', 16);

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
    echo "helloooooooo"; 
    $pdf->Output('F', 'output.pdf');
}

function draw_path($list, $pdf) {
    // print_r ($list);
    $stroke = process_color(end($list));
    // print_r ($stroke);
    $pdf->SetDrawColor($stroke[0], $stroke[1], $stroke[2]);
    for($k = 0; $k < sizeof($list) - 2; $k++) {
        $pdf->Line($list[$k][0], 
        $list[$k][1], 
        $list[$k + 1][0], 
        $list[$k + 1][1]);
    }
}

function set_color($list, $pdf) {
    $stroke = process_color($list[5]);
    $pdf->SetDrawColor($stroke[0], $stroke[1], $stroke[2]);
    // print_r($stroke);
    $fill = process_color($list[4]);
    if (sizeof($fill) == 1)
        return 'D';
    $pdf->SetFillColor($fill[0], $fill[1], $fill[2]);
    return 'F';
}

function draw_rect($list, $pdf) {
    // print_r($list);
    $type = set_color($list, $pdf);
    $pdf->rect($list[0], 
    $list[1], 
    $list[2], 
    $list[3], $type);
}

function write_text($list, $pdf) {
    print_r($list);
    // $color = process_color($list[2]);
    $color = process_color($list[4]);

    // print_r($color);
    $pdf->SetTextColor($color[0], $color[1], $color[2]);
    // $pdf->SetFontSize($list[4]);
    // $pdf->SetFontSize($list[6]);

    $pdf->SetFontSize(12);
    // $pdf->rect($list[0], $list[1], $list[2], $list[3]);
    // $pdf->text($list[0],
    // $list[1],
    // $list[3]);

    $pdf->text($list[0],
    $list[1] + $list[3],
    $list[5]);
}

function draw($obj_array, $pdf) {
    for($j = 0; $j < sizeof($obj_array['path']); $j++) {
        $list = $obj_array['path'][$j];
        draw_path($list, $pdf);
    } 

    for($j = 0; $j < sizeof($obj_array['rect']); $j++) {
        $list = $obj_array['rect'][$j];
        draw_rect($list, $pdf);
    } 

    for($j = 0; $j < sizeof($obj_array['text']); $j++) {
        $list = $obj_array['text'][$j];
        write_text($list, $pdf);
    }
}
?>