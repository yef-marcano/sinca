<?
set_time_limit(300);
require_once "../modelo/clBienesModelo.php";
require_once '../comunes/php/phpexcel/Classes/PHPExcel.php';
$objPHPExcel = new PHPExcel();

if ($_REQUEST['titulo']!=""){
    $titulo=$_REQUEST['titulo'];
}else{
    $titulo = 'PLAN DE INTERNET';}



$objPHPExcel->setActiveSheetIndex(0);

$sharedStyle1 = new PHPExcel_Style();
$sharedStyle1->applyFromArray(
        array(
            'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('argb' => '#CC0000')),
            'font' => array('bold' => true, 'color' => array('argb' => '#000000')),
            'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,),
            'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN,),
            )
));

$sharedStyle2 = new PHPExcel_Style();
$sharedStyle2->applyFromArray(
        array('fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,                
            ),
            'font' => array(
                'bold' => FALSE,
                
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            )
));
$sharedStyle3 = new PHPExcel_Style();
$sharedStyle3->applyFromArray(
        array('fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,                
            ),
            'font' => array(
                'bold' => true,
                'size' => '14',
                
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            )
));

$tituloStyle1 = new PHPExcel_Style();
$tituloStyle1->applyFromArray(
        array(
            'font' => array(
                'bold' => true,
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
));

$celdaUnionStyle1 = new PHPExcel_Style();
$celdaUnionStyle1->applyFromArray(
        array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_DOUBLE,
                ),
            )
));


$objPHPExcel->getActiveSheet()->setTitle('Plan de Internet');
$objPHPExcel->getActiveSheet()->SetCellValue("A1", $titulo);
$objPHPExcel->getActiveSheet()->mergeCells('A1:K1'); //union de colunmas
$objPHPExcel->getActiveSheet()->setSharedStyle($tituloStyle1, "A2");
$objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A2:K2");



$objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(40);
$objPHPExcel->getActiveSheet()->SetCellValue("A2",'N°'); //
$objPHPExcel->getActiveSheet()->SetCellValue("B2", 'EQUIPO'); //
$objPHPExcel->getActiveSheet()->SetCellValue("C2", 'SERIAL'); //
$objPHPExcel->getActiveSheet()->SetCellValue("D2", 'GERENCIA'); //
$objPHPExcel->getActiveSheet()->SetCellValue("E2", 'RESPONSABLE'); //
$objPHPExcel->getActiveSheet()->SetCellValue("F2", 'ESTATUS'); //
$objPHPExcel->getActiveSheet()->SetCellValue("G2", 'SISTEMA OPERATIVO'); //
$objPHPExcel->getActiveSheet()->SetCellValue("H2", 'PLAN-INTERNET'); //
$objPHPExcel->getActiveSheet()->SetCellValue("I2", 'IP'); //
$objPHPExcel->getActiveSheet()->SetCellValue("J2", 'MAC-LAN'); //
$objPHPExcel->getActiveSheet()->SetCellValue("K2", 'MAC-WIFI'); //


$sql="select e.stritema as equipo, b.strserial, g.stritema as gerencia,
case when b.id_responsable >0 and b.id_estatus_maestro in (86,87,88)  then
(select  r.strnombre || ' ' || r.strapellido from tblusuario r where b.id_responsable=r.id_usuario and r.bolborrado=0)
else case when b.id_responsable=0 and b.id_estatus_maestro in (87,88) and b.strnombres<>'' then
b.strnombres
else case  when b.id_responsable=0 and b.id_estatus_maestro in (86) then
'DISPONIBLE'
else case  when b.id_estatus_maestro in (89) then
es.stritemb
else case  when b.id_estatus_maestro in (90) then
'EXTRAVIADO'
else case  when b.id_estatus_maestro in (91) then
'DESINCORPORADO'
else case  when b.id_estatus_maestro in (618) then
'ROBADO'
else case  when b.id_estatus_maestro in (619) then
'REEMPLAZADO'
else
'SIN USUARIO'
end end end end end end end end as responsable,
es.stritema as estatus,
pi.strdescripcion as plan_internet,
ip.strdescripcion as ip,
mac.strdescripcion as macaddress,
wi.strdescripcion as wifi,
so.strdescripcion as sistema
from tblbienes b
inner join tblmaestros e on b.id_tipo_maestro=e.id_maestro and e.bolborrado=0
inner join tblmaestros g on b.id_gerencia_maestro=g.id_maestro and g.bolborrado=0
inner join tblmaestros es on b.id_estatus_maestro=es.id_maestro and es.bolborrado=0  
left join tbldetallebienes pi on pi.id_bienes=b.id_bienes and pi.id_caracteristica_maestro in (409,418,3094) and pi.bolborrado=0
left join tbldetallebienes ip on b.id_bienes=ip.id_bienes and ip.id_caracteristica_maestro in (410,419,3095) and ip.bolborrado=0
left join tbldetallebienes mac on b.id_bienes=mac.id_bienes and mac.id_caracteristica_maestro in (900,899,3096) and mac.bolborrado=0
left join tbldetallebienes wi on b.id_bienes=wi.id_bienes and wi.id_caracteristica_maestro in (948,2656,3098)  and wi.bolborrado=0
left join tbldetallebienes so on b.id_bienes=so.id_bienes and so.id_caracteristica_maestro in (3066,3067,3068)  and so.bolborrado=0 
where  b.bolborrado=0 and b.id_tipo_maestro in (106,107,3046) ";

if ($_REQUEST["gerencia"]!=""){
	$sql.=" and b.id_gerencia_maestro=".$_REQUEST["gerencia"];
}

if ($_REQUEST["estatus"]!=""){
    $sql.=" and b.id_estatus_maestro=".$_REQUEST["estatus"];
}



if ($_REQUEST["ip"]!=""){
	$sql.= " and ip.strdescripcion ilike '%".utf8_decode($_REQUEST["ip"])."%'";
}


if ($_REQUEST["macinterna"]!=""){
	$sql.=" and mac.strdescripcion ilike '%".utf8_decode($_REQUEST["macinterna"])."%'";
}


if ($_REQUEST["macexterna"]!=""){
	$sql.=" and wi.strdescripcion ilike '%".utf8_decode($_REQUEST["macexterna"])."%'";
}

if ($_REQUEST["internet"]!=""){
	$sql.=" and pi.strdescripcion ilike '%".utf8_decode($_REQUEST["internet"])."%'";
}
if ($_REQUEST["sistema"]!=""){
	$sql.=" and so.strdescripcion ilike '%".utf8_decode($_REQUEST["sistema"])."%'";
}

//$sql.=" order by proveedor,cargo, gerencia1, gerencia, estatus";
$conn= new Conexion();
$conn->abrirConexion();
$conn->sql=$sql;
$data=$conn->ejecutarSentencia(2);   
$total=0;
if ($data){
    $j = 3;    
    for ($i= 0; $i < count($data); $i++){
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "A$j:K$j");
        $total++;
        $largo=strlen($data[$i]['strserial']);
        if ($largo>=18 && is_numeric($data[$i]['strserial']) ){
            $serial='#'.$data[$i]['strserial'];
        }else{
            $serial=$data[$i]['strserial'];
        }
        
        $objPHPExcel->getActiveSheet()->SetCellValue("A$j", $total);    
        $objPHPExcel->getActiveSheet()->SetCellValue("B$j", utf8_encode($data[$i]["equipo"]));
        $objPHPExcel->getActiveSheet()->SetCellValue("C$j", utf8_encode($serial));    
        $objPHPExcel->getActiveSheet()->SetCellValue("D$j", utf8_encode($data[$i]["gerencia"]));
        $objPHPExcel->getActiveSheet()->SetCellValue("E$j", utf8_encode($data[$i]["responsable"]));
        $objPHPExcel->getActiveSheet()->SetCellValue("F$j", utf8_encode($data[$i]["estatus"]));
        $objPHPExcel->getActiveSheet()->SetCellValue("G$j", utf8_encode($data[$i]["sistema"]));
        $objPHPExcel->getActiveSheet()->SetCellValue("H$j", utf8_encode($data[$i]["plan_internet"]));
        $objPHPExcel->getActiveSheet()->SetCellValue("I$j", utf8_encode($data[$i]["ip"]));
        $objPHPExcel->getActiveSheet()->SetCellValue("J$j", utf8_encode($data[$i]["macaddress"]));
        $objPHPExcel->getActiveSheet()->SetCellValue("K$j", utf8_encode($data[$i]["wifi"]));        
        $j++;
    }
    $Fecha = date("d-m-Y h:i a");
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="internet'.date('dmy').'xls."');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');

    // Echo memory peak usage
    echo date('H:i:s') . " Peak memory usage: " . (memory_get_peak_usage(true) / 1024 / 1024) . " MB\r\n";

    // Echo done
    echo date('H:i:s') . " Done writing file.\r\n";
}else{
    echo "<script>alert('No existen registros para mostrar');window.close(this);</script>";
}



?>

