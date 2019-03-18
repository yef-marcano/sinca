<?php
session_start();
require('../comunes/php/fpdf.php');
require_once "../modelo/clOrdensalidaModelo.php";
require_once "../modelo/clBienesModelo.php";
require_once "../modelo/clUsuarioModelo.php";
require_once "../modelo/clRutausuarioModelo.php";
include('../comunes/php/utilidades.php');


class PDF_MC_Table extends FPDF
{
    var $widths;
    var $aligns;

    function SetWidths($w){
        //Set the array of column widths
        $this->widths=$w;
    }

    function SetAligns($a){
        //Set the array of column alignments
        $this->aligns=$a;
    }

    function Row($data){
        //Calculate the height of the row
        $nb=0;
        for($i=0;$i<count($data);$i++){
            $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
        }
        $h=5*$nb;
        //Issue a page break first if needed
        $this->CheckPageBreak($h);
        //Draw the cells of the row
        for($i=0;$i<count($data);$i++){
            $w=$this->widths[$i];
            $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            //Save the current position
            $x=$this->GetX();
            $y=$this->GetY();
            //Draw the border
            $this->Rect($x,$y,$w,$h,'DF');
            //Print the text
            $this->MultiCell($w,5,$data[$i],0,$a,0);
            //Put the position to the right of the cell
            $this->SetXY($x+$w,$y);
        }
        //Go to the next line
        $this->Ln($h);
    }

    function CheckPageBreak($h){
        //If the height h would cause an overflow, add a new page immediately
        if($this->GetY()+$h>$this->PageBreakTrigger){
            $this->AddPage($this->CurOrientation);
        }
    }

    function NbLines($w,$txt){
        //Computes the number of lines a MultiCell of width w will take
        $cw=&$this->CurrentFont['cw'];
        if($w==0){
            $w=$this->w-$this->rMargin-$this->x;
        }
        $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
        $s=str_replace("\r",'',$txt);
        $nb=strlen($s);
        if($nb>0 and $s[$nb-1]=="\n"){
            $nb--;
        }
        $sep=-1;
        $i=0;
        $j=0;
        $l=0;
        $nl=1;
        while($i<$nb){
            $c=$s[$i];
            if($c=="\n"){
                $i++;
                $sep=-1;
                $j=$i;
                $l=0;
                $nl++;
                continue;
            }
            if($c==' '){
                $sep=$i;
            }
            $l+=$cw[$c];
            if($l>$wmax){
                if($sep==-1){
                    if($i==$j){
                        $i++;
                    }
                }else{
                    $i=$sep+1;
                }
                $sep=-1;
                $j=$i;
                $l=0;
                $nl++;
            }else{
                $i++;
            }
        }
        return $nl;
    }
}
class PDF  extends PDF_MC_Table
{
	function Header()
	{
		$contacto=  new clOrdensalidaModelo();		
		$data= $contacto->selectOrdensalidaById($_REQUEST['id_ordensalida']);
		if($data){
			$fecha=strtoupper($data[0]['fecha']);
			$fechaletra=fechaCompleta($fecha);
		}
		//Logo
		$this->Image('../comunes/images/cintillo.jpg',20,3,180,15);
		$this->SetX(12);
		$this->SetY(28);
		$this->SetFont('Arial','',11);
		$this->Cell(0,10,'Caracas, '.$fechaletra,0,0,'R');
		$this->Ln(10);
		//Select Arial bold 15
		$this->SetFont('Arial','B',13);
		//Move to the right		
		//Framed title			
		$this->Cell(0,10,'ORDEN DE SALIDA',0,0,'C');
		//Line break
		$this->Ln(10);
		
	}

	function Footer()
	{
		//Go to 1.5 cm from bottom
		$this->SetY(-25);
		$this->SetFont('Arial','I',10);
		//Print centered page number
		$this->SetX(5);
		$this->Cell(0,10,utf8_decode('"200 años después: Independencia y Revolución"'),0,0,'C');
		$this->Ln();
		$this->SetX(5);
		$this->SetFont('Arial','',6);
		$this->Cell(190,10,utf8_decode('Generado a través del Sistema SICET en Fecha '.date('d/m/Y').' por el Usuario '.rtrim($_SESSION["strnombrefenix"]).' '.rtrim($_SESSION["strapellidofenix"]).'  |  Fuente: OSTI - '.date('Y').'  |  Licencia: GPL/GNU'),0,0,'L');
                $this->Cell(20,10,utf8_decode('Página '.$this->PageNo().'/{nb}'),0,0,'R');
	}


}
ob_end_clean();
$pdf=new PDF("p","mm","Letter");
$pdf->Open();
$pdf->AliasNbPages();
$pdf->SetMargins(20,10,20);
$pdf->SetFont('Arial','',11);
$pdf->AddPage();
$contacto=  new clOrdensalidaModelo();
$detalle= new clBienesModelo();
$usuario= new clUsuarioModelo();
$ruta= new clRutaUsuarioModelo();
$data= $contacto->selectOrdensalidaById($_REQUEST['id_ordensalida']);
if($data){
	$texto=utf8_decode("     Por medio de la presente, se autoriza al funcionario (a) ").$data[0]['usuario'].
	utf8_decode(", titular de la Cédula de Identidad: ").number_format($data[0]['cedula_usuario'],0,",",".").utf8_decode(", a retirar de la Sede de la Fundación Gran Misión Saber y Trabajo ").$data[0]['sede'].
	utf8_decode(" los siguientes equipos que se describen a continuación:");
	$pdf->MultiCell(0,8,$texto,0,'J');
	$pdf->Ln(10);	
	$pdf->SetFont('Arial','B',11);		
	$pdf->SetFillColor(255,0,0);
	$pdf->SetTextColor(0);
	$pdf->SetDrawColor(0);
	$pdf->SetLineWidth(.3);       
	$pdf->SetWidths(array(20,36,36,34,50));
        $pdf->SetAligns(array('C', 'C', 'C','C','C'));
        $pdf->SetTextColor(255,255,255);
	$pdf->Row(array('CANT.',utf8_decode('DESCRIPCIÓN'),'MARCA','MODELO','SERIAL'));
	$pdf->SetTextColor(0);
	//$serial=split(",",$data[0]['strequipos']);
        $data1=$detalle->selectAllBienesvarios($data[0]['strequipos']);
        $entrega=$ruta->selectAllRutaUsuarioByIdUsuario($data[0]['id_usuario'], $data[0]['fecha']);
        if (!$entrega){
            $entrega=$usuario->selectUsuarioById($data[0]['id_usuario']);
        }
        $recibe=$ruta->selectAllRutaUsuarioByIdUsuario($data[0]['id_autorizado'], $data[0]['fecha']);
	if (!$recibe){
            $recibe=$usuario->selectUsuarioById($data[0]['id_autorizado']);
        }
        $max='20';
        $j='0';
        if ($data1){
            for ($i=0; $i<count($data1); $i++){	
                $j++;
                if ($j == $max)
                {
                    $pdf->AddPage();
                    $pdf->SetFont('Arial','B',11);
                    $pdf->SetFillColor(255,0,0);
                    $pdf->SetTextColor(0);
                    $pdf->SetDrawColor(0);
                    $pdf->SetLineWidth(.3);
                    $pdf->SetWidths(array(20,36,36,34,50));
                    $pdf->SetAligns(array('C', 'C', 'C','C','C'));
                    $pdf->SetTextColor(255,255,255);
                    $pdf->Row(array('CANT.',utf8_decode('DESCRIPCIÓN'),'MARCA','MODELO','SERIAL'));
                    $pdf->SetTextColor(0);
                    $pdf->SetFont('Arial','',8);
                    $pdf->SetAligns(array('C', 'L','L','L', 'C'));
                    $j='0';
                }


                $pdf->SetFillColor(255,255,255);
                $pdf->SetLineWidth(.3);
                $pdf->SetTextColor(0);
                $pdf->SetFont('Arial','',8);
                $pdf->SetAligns(array('C', 'L','L','L', 'C'));
                $pdf->Row(array(1,$data1[$i]['tipo'],$data1[$i]['marca'],$data1[$i]['modelo'], $data1[$i]["strserial"]));
            }
	}
       
	$pdf->Ln(5);
	$pdf->SetFont('Arial','',9);
	$pdf->MultiCell(0,8,utf8_decode('OBSERVACIÓN: ').$data[0]['memobservacion'],0,'J',false);
	$pdf->Ln(30);
	$pdf->SetFont('Arial','B',11);
	$pdf->SetX(70);
	$pdf->SetLineWidth(0.1);
	$pdf->Line(70,$pdf->GetY(),155,$pdf->GetY());
	$pdf->Cell(85,10,$data[0]['autorizado'],0,0,'C');
	$pdf->Ln(8);	
	$pdf->SetX(70);
	$pdf->MultiCell(85,5,  $recibe[0]['cargo'],0,'C');
	
	
	
	
	
       
}
  	
$pdf->Output();		

	
		
?>

