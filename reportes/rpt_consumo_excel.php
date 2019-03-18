<?
set_time_limit(300);
require_once "../modelo/clConsumoModelo.php";
require_once "../modelo/clDetalleconsumoModelo.php";
require_once "../comunes/writeexcel/class.writeexcel_workbookbig.inc.php";
require_once "../comunes/writeexcel/class.writeexcel_worksheet.inc.php";
$fname = tempnam("/tmp", "consumo.xls");
$libro = &new writeexcel_workbookbig($fname);
$hoja = &$libro->addworksheet('CONSUMO TELEFONIA');
$lo_encabezado= &$libro->addformat();
$lo_encabezado->set_bold();
$lo_encabezado->set_font("Arial");
$lo_encabezado->set_align('center');
$lo_encabezado->set_size('11');
$lo_titulo= &$libro->addformat();
$lo_titulo->set_text_wrap();
$lo_titulo->set_bold();
$lo_titulo->set_font("Arial");
$lo_titulo->set_align('center');
$lo_titulo->set_size('9');
$lo_titulo->set_fg_color('10');
$lo_titulo->set_color('9');
$lo_titulo->set_border('1');
$lo_titulo->set_valign('middle');
$lo_datacenter= &$libro->addformat();
$lo_datacenter->set_font("Arial");
$lo_datacenter->set_align('center');
$lo_datacenter->set_size('9');
$lo_datacenter->set_border('1');
$lo_datacenter->set_text_wrap();
$lo_datacenter->set_num_format('1');
$lo_dataleft= &$libro->addformat();
$lo_dataleft->set_font("Arial");
$lo_dataleft->set_align('left');
$lo_dataleft->set_size('9');
$lo_dataleft->set_border('1');
$lo_dataleft->set_text_wrap('2');
$lo_dataleft->set_num_format('0');
$lo_dataleft->set_text_justlast('1');

$lo_dataright= &$libro->addformat();
$lo_dataright->set_font("Arial");
$lo_dataright->set_bold();
$lo_dataright->set_align('right');
$lo_dataright->set_size('10');
$lo_dataright->set_border('1');
$lo_dataright->set_text_wrap();
$lo_dataright->set_num_format('1');


$hoja->set_column(0, 0, 7);
$hoja->set_column(1, 1, 20);
$hoja->set_column(2, 2, 30);
$hoja->set_column(3, 3, 30);
$hoja->set_column(4, 4, 30);
$hoja->set_column(5, 5, 30);
$hoja->set_column(6, 6, 30);
$hoja->set_column(7, 7, 20);
$hoja->set_column(8, 8, 20);
$hoja->set_column(9, 9, 20);


$consumo= new clConsumoModelo();
$detalle= new clDetalleconsumoModelo();

$data_consumo=$consumo->selectConsumoById($_REQUEST['id_consumo']);
$data=$detalle->selectAllDetalleconsumo($_REQUEST['id_consumo'], 'proveedor');

$hoja->write(0,4,utf8_decode('CONSUMO TELEFONÍA AL '.$data_consumo[0]['fecha']),$lo_encabezado);


$hoja->write(2,0,utf8_decode('N°'),$lo_titulo);
$hoja->write(2,1,utf8_decode('PROVEEDOR'),$lo_titulo);
$hoja->write(2,2,utf8_decode('CUENTA'),$lo_titulo);
$hoja->write(2,3,utf8_decode('LÍNEA'),$lo_titulo);
$hoja->write(2,4,utf8_decode('DIRECCIÓN GENERAL'),$lo_titulo);
$hoja->write(2,5,utf8_decode('DIRECCIÓN DE LÍNEA'),$lo_titulo);
$hoja->write(2,6,'RESPONSABLE',$lo_titulo);
$hoja->write(2,7,utf8_decode('CARGO'),$lo_titulo);
$hoja->write(2,8,utf8_decode('ESTATUS'),$lo_titulo);
$hoja->write(2,9,utf8_decode('CONSUMO BS.'),$lo_titulo);

$total=0;
$total_consumo=0;
if ($data){
    for ($i= 0; $i < count($data); $i++){
        $total++;
        $largo=strlen($data[$i]['strserial']);
        if ($largo>=18 && is_numeric($data[$i]['strserial']) ){
            $serial='#'.$data[$i]['strserial'];
        }else{
            $serial=$data[$i]['strserial'];
        }
        $hoja->write($i+3, 0,$total,$lo_dataleft);
        $hoja->write($i+3, 1,$data[$i]["proveedor"],$lo_dataleft);
        $hoja->write($i+3, 2,$data[$i]["cuenta"],$lo_dataleft);
        $hoja->write($i+3, 3,$serial,$lo_dataleft);
        $hoja->write($i+3, 4,$data[$i]["gerencia"],$lo_dataleft);
        $hoja->write($i+3, 5,$data[$i]["unidad"],$lo_dataleft);
        $hoja->write($i+3, 6,$data[$i]["responsable"],$lo_dataleft);
        $hoja->write($i+3, 7,$data[$i]["cargo"],$lo_dataleft);
        $hoja->write($i+3, 8,$data[$i]["estatus"],$lo_dataleft);
        $hoja->write($i+3, 9,number_format($data[$i]["sngconsumo"],"2",".",","),$lo_dataleft);
        $total_consumo+=$data[$i]["sngconsumo"];
    }
    
    $libro->close();

    header("Content-Type: application/x-msexcel; name=\"consumo.xls\"");
    header("Content-Disposition: inline; filename=\"consumo.xls\"");
    $fh=fopen($fname, "rb");
    fpassthru($fh);
    unlink($fname);
}else{
    echo "<script>alert('No existen registros para mostrar');window.close(this);</script>";
}



?>

