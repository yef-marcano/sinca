<?php
//Example FPDF script with PostgreSQL
//Ribamar FS - ribafs@dnocs.gov.br

require('../fpdf/fpdf.php');

$pdf=new FPDF();
$pdf->AddPage();
$pdf->SetTitle('ejemplo de reporte en PDF via PHP');
//Set font and colors
$pdf->Image('../recursos/cintillo.jpg' , 10 ,7, 190 , 16,'JPG');
$pdf->Cell(18, 10, '', 0);
$pdf->Cell(140, 40, '', 0);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(20, 40, 'Fecha: '.date('d-m-Y').'', 0);
$pdf->Ln(30);
$pdf->SetFont('Arial', 'B', 11);
//$pdf->Image('../recursos/cintillo.jpg' , 10 ,7, 190 , 16,'JPG');	
//$pdf->Cell(18, 10, '', 0);
//$pdf->Cell(150, 40, '', 0);
$pdf->SetFillColor(255,0,0);
$pdf->SetTextColor(255);
$pdf->SetDrawColor(129,0,0);
$pdf->SetLineWidth(.3);

//Table header
$pdf->Cell(45,10,'Toma de Inventario',1,0,'L',1);
$pdf->Cell(30,10,'INSUMO',1,0,'L',1);
$pdf->Cell(55,10,'CANTIDAD EN EL SISTEMA',1,0,'L',1);
$pdf->Cell(50,10,'CANTIDAD EN EL FISICA',1,1,'L',1);
//Restore font and colors
$pdf->SetFont('Arial','',10);
$pdf->SetFillColor(224,235,255);
$pdf->SetTextColor(0);

//Connection and query

$str_conexion='dbname=bd_local port=5432 user=postgres password=';
$conexao=pg_connect($str_conexion) or die('La conexion a la case de datos fallo!');
$consulta=pg_exec($conexao,'select * from tbldetalle_toma_inventario where clvestatus=0');
$consulta2=pg_exec($conexao,'select b.clvcodigo,a.strdescripcion from tblinsumo  as a, tbldetalle_entrada_almacen as b where a.clvcodigo=b.clvinsumo');
$numregs=pg_numrows($consulta);

//Build table

$fill=false;
$i=0;
while($i<$numregs)
{
    $siape=pg_result($consulta,$i,'clvtoma_inventario');
    $nome=pg_result($consulta2,$i,'clvinsumo');
    $name=pg_result($consulta,$i,'intcantidad_sistema');
    $name=pg_result($consulta,$i,'intcantidad_fisica');
    $pdf->Cell(45,10,$siape,1,0,'m',$fill);
    $pdf->Cell(30,10,$nome,1,0,'L',$fill);
    $pdf->Cell(55,10,$name,1,0,'M',$fill);
    $pdf->Cell(50,10,$name,1,1,'M',$fill);
    $fill=!$fill;
    $i++;
}

//Add a rectangle, a line, a logo and some text

$pdf->Output();
?>