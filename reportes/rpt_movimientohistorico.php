<?php
session_start();
require('../comunes/php/fpdf.php');
require_once "../modelo/clBienesModelo.php";
require_once '../modelo/clRutabienesModelo.php';
require('../comunes/php/utilidades.php');  


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
		$contacto=  new clBienesModelo();		
		$data=$contacto->selectBienesById($_REQUEST['id']);
		if($data){
			$tipo=$data[0]['stritemb'];
		}
		//Logo
		$this->Image('../comunes/images/cab_reportes.jpg',20,3,30,15);
		$this->SetX(12);
		$this->SetY(25);
		$this->SetFont('Arial','B',10);
		$this->Cell(0,10,'MOVIMIENTO HISTÓRICO',0,1,'C');
		$this->Cell(0,10,'Equipo: '.$data[0]['tipo'].' | Marca: '.$data[0]['marca'].' | Modelo: '.$data[0]['modelo'].' | Serial: '.$data[0]['strserial'],0,0,'L');
			
		$this->Ln(10);
		$this->SetFillColor(255,0,0);
		$this->SetTextColor(255,255,255);
		$this->SetDrawColor(0);
	    $this->SetWidths(array(20,45,30,45,25,70,25));
		$this->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C','C'));
		$this->Row(array('FECHA','USUARIO','ESTATUS','ASIGNADO A','GERENCIA','OBSERVACIÓN','UBICACIÓN'));
			
	}

	function Footer()
	{
		//Go to 1.5 cm from bottom
		$this->SetY(-15);		
		$this->SetFont('Arial','',6);
		//Print centered page number
		
		$this->Ln(5);
		$this->Cell(232,10,utf8_decode('Generado a travÃ©s del Sistema SICET en Fecha '.date('d/m/Y').' por el Usuario '.rtrim($_SESSION["strnombrefenix"]).' '.rtrim($_SESSION["strapellidofenix"]).'  |  Fuente: OSTI - '.date('Y').'  |  Licencia: GPL/GNU'),0,0,'L');
		$this->Cell(20,10,utf8_decode('PÃ¡gina '.$this->PageNo().'/{nb}'),0,0,'R');
	}


}
ob_end_clean();
$pdf=new PDF("L","mm","Letter");
$pdf->Open();
$pdf->AliasNbPages();
$pdf->SetMargins(10,10,10);
$pdf->SetFont('Arial','',8);
$pdf->AddPage();
$contacto=  new clRutabienesModelo();		
$data1=$contacto->selectAllRutabienesbyId_bienes($_REQUEST['id']);
if ($data1){
	$pdf->SetAligns(array('C', 'C', 'C', 'C', 'C', 'L','C'));
	for ($i= 0; $i < count($data1); $i++){
	   	$pdf->SetFillColor(255,255,255);
		$pdf->SetTextColor(0);
		$pdf->SetDrawColor(0); 
		
		$pdf->Row(array($data1[$i]['dtmfecha1'],utf8_decode($data1[$i]['usuario']),utf8_decode($data1[$i]['estatus']),utf8_decode($data1[$i]['responsable']),siglasGerencia($data1[$i]['id_gerencia_maestro']),utf8_decode($data1[$i]['memobservacion']),utf8_decode($data1[$i]['ubicacion'])));
	}
	
   

}


$pdf->Output();		

	
		
?>

