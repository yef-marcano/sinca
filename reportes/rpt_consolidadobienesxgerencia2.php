<?php
session_start();
require('../comunes/php/fpdf.php');
require_once "../modelo/clBienesModelo.php";
require_once "../modelo/clUsuarioModelo.php";
require_once "../modelo/clMaestroModelo.php";


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
		$this->Image('../comunes/images/cintillo.jpg',20,3,180,15);
		$this->SetX(12);
		$this->SetY(25);
		$this->SetFont('Arial','B',10);
		$this->Cell(0,10,utf8_decode('CONSOLIDADO DE BIENES TELEMÁTICOS POR GERENCIA'),0,0,'C');
		$this->Ln(10);
		
		$this->SetFont('Arial','B',8);		
		$this->SetFillColor(255,0,0);		
		$this->SetTextColor(255,255,255);
		$this->SetDrawColor(0);
		$this->SetLineWidth(.3);       
		$this->SetWidths(array(20,15,15,15,15,15,15,15,15,15,15,15,15,15,15,15,15));
        $this->SetAligns(array('C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C'));
		$this->Row(array('Modelo','PRE','AI','OPP','CJ','OAF','ORRHH','OSTI','OGC','OAC','GG','GFPC','GEPAS','GIS','GC','CN','Total'));
	}

	function Footer()
	{
		//Go to 1.5 cm from bottom
		$this->SetY(-15);		
		$this->SetFont('Arial','',6);
		//Print centered page number
		
		$this->Ln(5);
		$this->Cell(0,10,utf8_decode('Generado a través del Sistema SICET en Fecha '.date('d/m/Y').' por el Usuario '.rtrim($_SESSION["strnombrefenix"]).' '.rtrim($_SESSION["strapellidofenix"]).'  |  Fuente: OSTI - '.date('Y').'  |  Licencia: GPL/GNU'),0,0,'L');
		$this->Cell(10,10,utf8_decode('Página '.$this->PageNo().'/{nb}'),0,0,'R');
	}


}
ob_end_clean();
$pdf=new PDF("L","mm","Letter");
$pdf->Open();
$pdf->AliasNbPages();
$pdf->SetMargins(10,10,10);
$pdf->SetFont('Arial','',8);
$pdf->AddPage();
$sql="select count(b.*), t.stritema as equipo, b.id_tipo_maestro, b.id_gerencia_maestro, g.stritemc as gerencia 
from tblbienes b, tblmaestros t,tblmaestros g 
where b.id_tipo_maestro=t.id_maestro and b.id_gerencia_maestro=g.id_maestro 
and b.bolborrado=0 and t.bolborrado=0 and g.stritemb='GERENCIA'
and g.bolborrado=0
group by t.stritema, b.id_tipo_maestro, b.id_gerencia_maestro, g.stritemc
order by t.stritema,b.id_gerencia_maestro";
$conn= new Conexion();
$conn->abrirConexion();
$conn->sql=$sql;
$data=$conn->ejecutarSentencia(2);
$pdf->SetFillColor(255,255,255);		
$pdf->SetTextColor(0);
$pdf->SetDrawColor(0);
$pdf->SetLineWidth(.3);   
    
    
$pdf->SetWidths(array(20,15,15,15,15,15,15,15,15,15,15,15,15,15,15,15,15));
$pdf->SetAligns(array('L','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R'));
if ($data){
	$pre=0;
	$ai=0;
	$opp=0;
	$cj=0;
	$oaf=0;
	$orrhh=0;
	$osti=0;
	$ogc=0;
	$oac=0;
	$gg=0;
	$gfpc=0;
	$gepas=0;
	$gis=0;
	$gc=0;
	$cn=0;	
	$total=0;
	$equipo=$data[0]['equipo'];
	$tpre=0;
	$tai=0;
	$topp=0;
	$tcj=0;
	$toaf=0;
	$torrhh=0;
	$tosti=0;
	$togc=0;
	$toac=0;
	$tgg=0;
	$tgfpc=0;
	$tgepas=0;
	$tgis=0;
	$tgc=0;
	$tcn=0;		
	$ttotal=0;
	for ($i= 0; $i < count($data); $i++){
		if ($equipo==$data[$i]['equipo']){			
			if ($data[$i]['id_gerencia_maestro']=='17'){
				$pre=$data[$i]['count'];
				$tpre+=$pre;				
			}
			if ($data[$i]['id_gerencia_maestro']=='18'){
				$ai=$data[$i]['count'];
				$tai+=$ai;				
			}
			if ($data[$i]['id_gerencia_maestro']=='19'){
				$opp=$data[$i]['count'];
				$topp+=$opp;				
			}
			if ($data[$i]['id_gerencia_maestro']=='20'){
				$cj=$data[$i]['count'];
				$tcj+=$cj;				
			}
			if ($data[$i]['id_gerencia_maestro']=='21'){
				$oaf=$data[$i]['count'];
				$toaf+=$oaf;				
			}
			if ($data[$i]['id_gerencia_maestro']=='22'){
				$orrhh=$data[$i]['count'];
				$trrhh+=$orrhh;				
			}
			if ($data[$i]['id_gerencia_maestro']=='23'){
				$osti=$data[$i]['count'];
				$tosti+=$osti;				
			}
			if ($data[$i]['id_gerencia_maestro']=='24'){
				$ogc=$data[$i]['count'];
				$togc+=$ogc;				
			}
			if ($data[$i]['id_gerencia_maestro']=='25'){
				$oac=$data[$i]['count'];
				$toac+=$oac;				
			}
			if ($data[$i]['id_gerencia_maestro']=='26'){
				$gg=$data[$i]['count'];
				$tgg+=$gg;				
			}
			if ($data[$i]['id_gerencia_maestro']=='27'){
				$gfpc=$data[$i]['count'];
				$tgfpc+=$gfpc;				
			}
			if ($data[$i]['id_gerencia_maestro']=='28'){
				$gepas=$data[$i]['count'];
				$tgepas+=$gepas;				
			}
			if ($data[$i]['id_gerencia_maestro']=='29'){
				$gis=$data[$i]['count'];
				$tgis+=$gis;				
			}
			if ($data[$i]['id_gerencia_maestro']=='30'){
				$gc=$data[$i]['count'];
				$tgc+=$gc;				
			}
			if ($data[$i]['id_gerencia_maestro']=='31'){
				$cn=$data[$i]['count'];
				$tcn+=$cn;				
			}		
			
		}else{
			$total=$pre+$ai+$opp+$cj+$oaf+$orrhh+$osti+$ogc+$oac+$gg+$gfpc+$gepas+$gis+$gc+$cn;	
			$ttotal+=$total;
			$pdf->Row(array($equipo,$pre,$ai,$opp,$cj,$oaf,$orrhh,$osti,$ogc,$oac,$gg,$gfpc,$gepas,$gis,$gc,$cn,$total));	
			
			$pre=0;
			$ai=0;
			$opp=0;
			$cj=0;
			$oaf=0;
			$orrhh=0;
			$osti=0;
			$ogc=0;
			$oac=0;
			$gg=0;
			$gfpc=0;
			$gepas=0;
			$gis=0;
			$gc=0;
			$cn=0;	
			$total=0;
			$equipo=$data[$i]['equipo'];
		if ($data[$i]['id_gerencia_maestro']=='17'){
				$pre=$data[$i]['count'];
				$tpre+=$pre;				
			}
			if ($data[$i]['id_gerencia_maestro']=='18'){
				$ai=$data[$i]['count'];
				$tai+=$ai;				
			}
			if ($data[$i]['id_gerencia_maestro']=='19'){
				$opp=$data[$i]['count'];
				$topp+=$opp;				
			}
			if ($data[$i]['id_gerencia_maestro']=='20'){
				$cj=$data[$i]['count'];
				$tcj+=$cj;				
			}
			if ($data[$i]['id_gerencia_maestro']=='21'){
				$oaf=$data[$i]['count'];
				$toaf+=$oaf;				
			}
			if ($data[$i]['id_gerencia_maestro']=='22'){
				$orrhh=$data[$i]['count'];
				$trrhh+=$orrhh;				
			}
			if ($data[$i]['id_gerencia_maestro']=='23'){
				$osti=$data[$i]['count'];
				$tosti+=$osti;				
			}
			if ($data[$i]['id_gerencia_maestro']=='24'){
				$ogc=$data[$i]['count'];
				$togc+=$ogc;				
			}
			if ($data[$i]['id_gerencia_maestro']=='25'){
				$oac=$data[$i]['count'];
				$toac+=$oac;				
			}
			if ($data[$i]['id_gerencia_maestro']=='26'){
				$gg=$data[$i]['count'];
				$tgg+=$gg;				
			}
			if ($data[$i]['id_gerencia_maestro']=='27'){
				$gfpc=$data[$i]['count'];
				$tgfpc+=$gfpc;				
			}
			if ($data[$i]['id_gerencia_maestro']=='28'){
				$gepas=$data[$i]['count'];
				$tgepas+=$gepas;				
			}
			if ($data[$i]['id_gerencia_maestro']=='29'){
				$gis=$data[$i]['count'];
				$tgis+=$gis;				
			}
			if ($data[$i]['id_gerencia_maestro']=='30'){
				$gc=$data[$i]['count'];
				$tgc+=$gc;				
			}
			if ($data[$i]['id_gerencia_maestro']=='31'){
				$cn=$data[$i]['count'];
				$tcn+=$cn;				
			}
		}
		
	}
	$total=$pre+$ai+$opp+$cj+$oaf+$orrhh+$osti+$ogc+$oac+$gg+$gfpc+$gepas+$gis+$gc+$cn;	
	$ttotal+=$total;
	$pdf->Row(array($equipo,$pre,$ai,$opp,$cj,$oaf,$orrhh,$osti,$ogc,$oac,$gg,$gfpc,$gepas,$gis,$gc,$cn,$total));		
	$pdf->Row(array('TOTAL',$tpre,$tai,$topp,$tcj,$toaf,$torrhh,$tosti,$togc,$toac,$tgg,$tgfpc,$tgepas,$tgis,$tgc,$tcn,$ttotal));
	
	
    
	
}else{
	echo "<script>alert('No existen registros para mostrar');window.close(this);</script>";
}



$pdf->Output();		

	

	
		
?>

