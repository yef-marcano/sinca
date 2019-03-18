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
$pdf->Ln(10);

$pdf->Cell(18, 10, '', 0);
$pdf->Cell(27, 40, '', 0);
$pdf->SetFont('Arial', '', 15);
$pdf->Cell(100,25,'ENTRADA DE INSUMOS ALMACEN',0,100,'L');

$pdf->Ln(1);
$pdf->SetFont('Arial', 'B', 11);
//$pdf->Image('../recursos/cintillo.jpg' , 10 ,7, 190 , 16,'JPG');	
//$pdf->Cell(18, 10, '', 0);
//$pdf->Cell(150, 40, '', 0);
$pdf->SetFillColor(255,0,0);
$pdf->SetTextColor(255);
$pdf->SetDrawColor(129,0,0);
$pdf->SetLineWidth(.1);

//Table header

$pdf->Cell(28);
$pdf->Cell(45,10,'Entrada Almacen',1,0,'L',1);
$pdf->Cell(30,10,'INSUMO',1,0,'L',1);
$pdf->Cell(55,10,'CANTIDAD',1,1,'L',1);
//Restore font and colors
$pdf->SetFont('Arial','',10);
$pdf->SetFillColor(224,235,255);
$pdf->SetTextColor(0);
//Connection and query

$str_conexion='dbname=bd_local port=5432 user=postgres password=';
$conexao=pg_connect($str_conexion) or die('La conexion a la base de datos fallo!');
$consulta=pg_exec($conexao,'select * from tbldetalle_entrada_almacen where clvestatus=0');

$numregs=pg_numrows($consulta);

//Build table

$fill=false;
$i=0;
while($i<$numregs)
{   
$pdf->Cell(28);
    $siape=pg_result($consulta,$i,'clventrada_almacen');
    $nome=pg_result($consulta,$i,'clvinsumo');
    $name=pg_result($consulta,$i,'intcantidad');
    $pdf->Cell(45,10,$siape,1,0,'m',$fill);
    $pdf->Cell(30,10,$nome,1,0,'L',$fill);
    $pdf->Cell(55,10,$name,1,1,'M',$fill);
    $fill=!$fill;
    $i++;
}

//Add a rectangle, a line, a logo and some text
$pdf->SetFillColor(224,235);
$pdf->SetFont('Arial','B',8);
$pdf->Cell(170,5,'PDF generado via PHP acceso a base de datos - Por Yeferson Marcano FS',1,1,'L',1,'mailto:ribafs@dnocs.gov.br');



function Footer()
    {
$pdf->SetFillColor(224,235);
$pdf->SetFont('Arial','B',8);
$pdf->Cell(170,5,'PDF generado via PHP acceso a base de datos - Por Yeferson Marcano FS',1,1,'L',1,'mailto:ribafs@dnocs.gov.br');
$pdf->SetY(-15);       
        $pdf->SetFont('Arial','',6);
        //Print centered page number
        
        $pdf->Ln(5);
        $pdf->Cell(150,10,utf8_decode('Generado a través del Sistema SICET en Fecha '.date('d/m/Y').' por el Usuario '.rtrim($_SESSION["strnombrefenix"]).' '.rtrim($_SESSION["strapellidofenix"]).'  |  Fuente: OT - '.date('Y').'  |  Licencia: GPL/GNU'),0,0,'L');
        $pdf->Cell(0,10,utf8_decode('Página '.$this->PageNo().'/{nb}'),0,0,'R');
        }

$pdf->Output();
?>