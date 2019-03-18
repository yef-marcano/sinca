<?php
session_start();
require('../comunes/php/fpdf.php');
require_once "../modelo/clBienesModelo.php";
require_once "../modelo/clUsuarioModelo.php";


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
		
		//Logo
		$this->Image('../comunes/images/cintillo.jpg',10,3,190,15);
		$this->SetX(12);
		$this->SetY(25);
		$this->SetFont('Arial','B',10);
		$this->Cell(0,10,utf8_decode('CONSOLIDADO DE BIENES TELEMÁTICOS POR ESTATUS'),0,0,'C');
		$this->Ln(10);
		
		$this->SetFont('Arial','B',8);		
		$this->SetFillColor(255,0,0);		
		$this->SetTextColor(255,255,255);
		$this->SetDrawColor(0);
		$this->SetLineWidth(.3);       
		$this->SetWidths(array(45,15,15,16,20,20,15,15,15,15));
        $this->SetAligns(array('C','C','C','C','C','C','C','C','C','C'));
		$this->Row(array('Equipo','Asignado',utf8_decode('Dañado'),'Desincor-porado','Disponible','Extraviado',utf8_decode('Préstamo'),'Robado','Reempla-zado','Total'));
		$this->SetAligns(array('L','R','R','R','R','R','R','R','R','R'));
	}

	function Footer()
	{
		//Go to 1.5 cm from bottom
		$this->SetY(-15);		
		$this->SetFont('Arial','',6);
		//Print centered page number
		
		$this->Ln(5);
		$this->Cell(150,10,utf8_decode('Generado a través del Sistema SICET en Fecha '.date('d/m/Y').' por el Usuario '.rtrim($_SESSION["strnombrefenix"]).' '.rtrim($_SESSION["strapellidofenix"]).'  |  Fuente: OT - '.date('Y').'  |  Licencia: GPL/GNU'),0,0,'L');
		$this->Cell(0,10,utf8_decode('Página '.$this->PageNo().'/{nb}'),0,0,'R');
	}


}
ob_end_clean();
$pdf=new PDF("p","mm","Letter");
$pdf->Open();
$pdf->AliasNbPages();
$pdf->SetMargins(10,10,10);
$pdf->SetFont('Arial','',8);
$pdf->AddPage();
$sql="select count(b.*), t.stritema as equipo, e.stritema, b.id_tipo_maestro, b.id_estatus_maestro 
from tblbienes b, tblmaestros t, tblmaestros e 
where b.id_tipo_maestro=t.id_maestro and b.id_estatus_maestro=e.id_maestro 
and b.bolborrado=0 and t.bolborrado=0 and e.bolborrado=0
group by t.stritema, e.stritema, b.id_tipo_maestro, b.id_estatus_maestro
order by t.stritema, e.stritema";
$conn= new Conexion();
$conn->abrirConexion();
$conn->sql=$sql;
$data=$conn->ejecutarSentencia(2);
$pdf->SetFillColor(255,255,255);		
$pdf->SetTextColor(0);
$pdf->SetDrawColor(0);
$pdf->SetLineWidth(.3);   
    
$pdf->SetWidths(array(45,15,15,16,20,20,15,15,15,15));
$pdf->SetAligns(array('L','R','R','R','R','R','R','R','R','R'));
if ($data){
	$disponible=0;
	$prestamo=0;
	$asignado=0;
	$danado=0;
	$extraviado=0;
	$desincorporado=0;
	$robado=0;
	$reemplazado=0;
	$total=0;
	$equipo=$data[0]['equipo'];
	$tdis=0;
	$tpres=0;
	$tasig=0;
	$tdan=0;
	$text=0;
	$tdes=0;
	$trob=0;
	$tree=0;
	$ttotal=0;
	for ($i= 0; $i < count($data); $i++){
		if ($equipo==$data[$i]['equipo']){			
			if ($data[$i]['id_estatus_maestro']=='86'){
				$disponible=$data[$i]['count'];
				$tdis+=$disponible;				
			}
			if ($data[$i]['id_estatus_maestro']=='87'){
				$prestamo=$data[$i]['count'];
				$tpres+=$data[$i]['count'];				
			}
			if ($data[$i]['id_estatus_maestro']=='88'){
				$asignado=$data[$i]['count'];
				$tasig+=$data[$i]['count'];				
			}
			if ($data[$i]['id_estatus_maestro']=='89'){
				$danado=$data[$i]['count'];
				$tdan+=$data[$i]['count'];				
			}
			if ($data[$i]['id_estatus_maestro']=='90'){
				$extraviado=$data[$i]['count'];
				$text+=$data[$i]['count'];				
			}
			if ($data[$i]['id_estatus_maestro']=='91'){
				$desincorporado=$data[$i]['count'];
				$tdes+=$data[$i]['count'];				
			}
			if ($data[$i]['id_estatus_maestro']=='618'){
				$robado=$data[$i]['count'];
				$trob+=$data[$i]['count'];				
			}
			if ($data[$i]['id_estatus_maestro']=='619'){
				$reemplazado=$data[$i]['count'];
				$tree+=$data[$i]['count'];				
			}
			
		}else{
			$total=$disponible + $prestamo + $asignado + $danado + $extraviado + $desincorporado + $robado + $reemplazado;
			$ttotal+=$total;
			$pdf->Row(array($equipo,$asignado,$danado,$desincorporado,$disponible,$extraviado,$prestamo,$robado,$reemplazado,$total));	
			
			$disponible=0;
			$prestamo=0;
			$asignado=0;
			$danado=0;
			$extraviado=0;
			$desincorporado=0;
			$robado=0;
			$reemplazado=0;
			$total=0;
			$equipo=$data[$i]['equipo'];
			if ($data[$i]['id_estatus_maestro']=='86'){
				$disponible=$data[$i]['count'];
				$tdis+=$data[$i]['count'];				
			}
			if ($data[$i]['id_estatus_maestro']=='87'){
				$prestamo=$data[$i]['count'];
				$tpres+=$data[$i]['count'];				
			}
			if ($data[$i]['id_estatus_maestro']=='88'){
				$asignado=$data[$i]['count'];
				$tasig+=$data[$i]['count'];				
			}
			if ($data[$i]['id_estatus_maestro']=='89'){
				$danado=$data[$i]['count'];
				$tdan+=$data[$i]['count'];				
			}
			if ($data[$i]['id_estatus_maestro']=='90'){
				$extraviado=$data[$i]['count'];
				$text+=$data[$i]['count'];				
			}
			if ($data[$i]['id_estatus_maestro']=='91'){
				$desincorporado=$data[$i]['count'];
				$tdes+=$data[$i]['count'];				
			}
			if ($data[$i]['id_estatus_maestro']=='618'){
				$robado=$data[$i]['count'];
				$trob+=$data[$i]['count'];				
			}
			if ($data[$i]['id_estatus_maestro']=='619'){
				$reemplazado=$data[$i]['count'];
				$tree+=$data[$i]['count'];				
			}
		}
		
	}
	$total=$disponible + $prestamo + $asignado + $danado + $extraviado + $desincorporado + $robado + $reemplazado;
	$ttotal+=$total;
	$pdf->Row(array($equipo,$asignado,$danado,$desincorporado,$disponible,$extraviado,$prestamo,$robado,$reemplazado,$total));	
	$pdf->Row(array('TOTAL',$tasig,$tdan,$tdes,$tdis,$text,$tpres,$trob,$tree,$ttotal));
}



$pdf->Output();		

	
		
?>

