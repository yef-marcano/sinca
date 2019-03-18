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
		$this->Image('../comunes/images/carta.png',20,3,180,15);
		$this->SetX(12);
		$this->SetY(25);
		$this->SetFont('Arial','B',10);
		$this->Cell(0,10,  utf8_decode('CONSOLIDADO DETALLADO DE EQUIPOS TELEMÁTICOS PRINCIPALES'),0,0,'C');
		$this->Ln(10);
		
		$this->SetFont('Arial','B',7);
		$this->SetFillColor(255,0,0);		
		$this->SetTextColor(255,255,255);
		$this->SetDrawColor(0);
		$this->SetLineWidth(.3);    
		$this->Cell(30,10,'EQUIPO', '1',0,'C',1);
		
		$this->SetX(40);		
		$this->Cell(26,5,'ASIGNADO','1',0,'C',1);
		$this->Cell(26,5,utf8_decode('DAÑADO'),'1',0,'C',1);
		$this->Cell(26,5,'DESINCORP.','1',0,'C',1);
		$this->Cell(26,5,'DISPONIBLE','1',0,'C',1);
		$this->Cell(26,5,'EXTRAVIADO','1',0,'C',1);
		$this->Cell(26,5,utf8_decode('PRÉSTAMO'),'1',0,'C',1);
		$this->Cell(26,5,'ROBADO','1',0,'C',1);
		
		$this->Cell(26,5,'SUB TOTAL','1',0,'C',1);
		$this->Cell(26,10,'TOTAL','1',0,'C',1);
		$this->Ln(5);
		$this->SetX(40);
		$this->SetFont('Arial','B',6);
		$this->Cell(13,5,'NAC.','1',0,'C',1);
		$this->Cell(13,5,'COORD.','1',0,'C',1);
		$this->Cell(13,5,'NAC.','1',0,'C',1);
		$this->Cell(13,5,'COORD.','1',0,'C',1);
		$this->Cell(13,5,'NAC.','1',0,'C',1);
		$this->Cell(13,5,'COORD.','1',0,'C',1);
		$this->Cell(13,5,'NAC.','1',0,'C',1);
		$this->Cell(13,5,'COORD.','1',0,'C',1);
		$this->Cell(13,5,'NAC.','1',0,'C',1);
		$this->Cell(13,5,'COORD.','1',0,'C',1);
		$this->Cell(13,5,'NAC.','1',0,'C',1);
		$this->Cell(13,5,'COORD.','1',0,'C',1);
		$this->Cell(13,5,'NAC.','1',0,'C',1);
		$this->Cell(13,5,'COORD.','1',0,'C',1);
		$this->Cell(13,5,'NAC.','1',0,'C',1);
		$this->Cell(13,5,'COORD.','1',0,'C',1);
		
		
		$this->Ln(5);
		
		
		
	}

	function Footer()
	{
		//Go to 1.5 cm from bottom
		$this->SetY(-15);		
		$this->SetFont('Arial','',6);
		//Print centered page number
		
		$this->Ln(5);
		$this->Cell(150,10,utf8_decode('Generado a través del Sistema SICET en Fecha '.date('d/m/Y').' por el Usuario '.rtrim($_SESSION["strnombrefenix"]).' '.rtrim($_SESSION["strapellidofenix"]).'  |  Fuente: OSTI - '.date('Y').'  |  Licencia: GPL/GNU'),0,0,'L');
		$this->Cell(0,10,utf8_decode('Página '.$this->PageNo().'/{nb}'),0,0,'R');
	}


}
ob_end_clean();
$pdf=new PDF("L","mm","Letter");
$pdf->Open();
$pdf->AliasNbPages();
$pdf->SetMargins(10,10,10);
$pdf->SetFont('Arial','',8);
$pdf->AddPage();
$sql="select count(b.*), t.stritema as equipo, e.stritema, b.id_tipo_maestro, b.id_estatus_maestro ,  g.stritemb as gerencia
from tblbienes b, tblmaestros t, tblmaestros e, tblmaestros g 
where b.id_tipo_maestro=t.id_maestro and b.id_estatus_maestro=e.id_maestro 
and b.id_tipo_maestro in (106,107,117,125,118,108,133) and g.id_maestro=b.id_gerencia_maestro
and b.bolborrado=0 and t.bolborrado=0 and e.bolborrado=0 and g.bolborrado=0
group by t.stritema, e.stritema, b.id_tipo_maestro, b.id_estatus_maestro, g.stritemb
order by t.stritema, e.stritema, g.stritemb ";
$conn= new Conexion();
$conn->abrirConexion();
$conn->sql=$sql;
$data=$conn->ejecutarSentencia(2);
$pdf->SetFillColor(255,255,255);		
$pdf->SetTextColor(0);
$pdf->SetDrawColor(0);
$pdf->SetLineWidth(.3);   
    
$pdf->SetWidths(array(30,13,13,13,13,13,13,13,13,13,13,13,13,13,13,13,13,26));
$pdf->SetAligns(array('L','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R'));
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
	$idequipo=$data[0]['id_tipo_maestro'];
	$tdis=0;
	$tpres=0;
	$tasig=0;
	$tdan=0;
	$text=0;
	$tdes=0;
	$trob=0;
	$tree=0;
	$ttotal=0;
	
	$disponiblec=0;
	$prestamoc=0;
	$asignadoc=0;
	$danadoc=0;
	$extraviadoc=0;
	$desincorporadoc=0;
	$robadoc=0;
	$reemplazadoc=0;
	$totalc=0;
	
	$tdisc=0;
	$tpresc=0;
	$tasigc=0;
	$tdanc=0;
	$textc=0;
	$tdesc=0;
	$trobc=0;
	$treec=0;
	$ttotalc=0;
	
	$sql="select count(b.*), t.stritema as equipo
			from tblbienes b, tblmaestros t
			where b.id_tipo_maestro=t.id_maestro and b.id_tipo_maestro=".$idequipo." 
			and b.id_estatus_maestro=86 and b.id_unidad_maestro=690
			and b.bolborrado=0 and t.bolborrado=0
			group by t.stritema";
	$conn= new Conexion();
	$conn->abrirConexion();
	$conn->sql=$sql;
	$data1=$conn->ejecutarSentencia(2);
	if ($data1[0]['count']!=""){
		$disponible=$data1[0]['count'];
		$tdis+=$data1[0]['count'];
	}else{
		$disponible=0;
		$tdis+=0;
	}		
	
	for ($i= 0; $i < count($data); $i++){
		if ($equipo==$data[$i]['equipo']){			
			if ($data[$i]['id_estatus_maestro']=='86' && $data[$i]['gerencia']=='GERENCIA' ){
				$asignado+=$data[$i]['count']-$disponible;
				$tasignado+=$asignado;				
			}
			if ($data[$i]['id_estatus_maestro']=='87' && $data[$i]['gerencia']=='GERENCIA'){
				$prestamo=$data[$i]['count'];
				$tpres+=$data[$i]['count'];				
			}
			if ($data[$i]['id_estatus_maestro']=='88' && $data[$i]['gerencia']=='GERENCIA'){
				$asignado=$data[$i]['count'];
				$tasig+=$data[$i]['count'];				
			}
			
			if ($data[$i]['id_estatus_maestro']=='90' && $data[$i]['gerencia']=='GERENCIA'){
				$extraviado=$data[$i]['count'];
				$text+=$data[$i]['count'];				
			}
			if ($data[$i]['id_estatus_maestro']=='91' && $data[$i]['gerencia']=='GERENCIA'){
				$desincorporado=$data[$i]['count'];
				$tdes+=$data[$i]['count'];				
			}
			if ($data[$i]['id_estatus_maestro']=='618' && $data[$i]['gerencia']=='GERENCIA'){
				$robado=$data[$i]['count'];
				$trob+=$data[$i]['count'];				
			}
			if ($data[$i]['id_estatus_maestro']=='619' && $data[$i]['gerencia']=='GERENCIA'){
				$reemplazado=$data[$i]['count'];
				$tree+=$data[$i]['count'];				
			}
			
			if ($data[$i]['id_estatus_maestro']=='89' && $data[$i]['gerencia']=='GERENCIA'){
				$danado=$data[$i]['count'];
				$tdan+=$data[$i]['count'];				
			}
			
			
			
			
			if ($data[$i]['id_estatus_maestro']=='86' && $data[$i]['gerencia']=='COORDINACION' ){
				$disponiblec=$data[$i]['count'];
				$tdisc+=$disponiblec;				
			}
			if ($data[$i]['id_estatus_maestro']=='87' && $data[$i]['gerencia']=='COORDINACION'){
				$prestamoc=$data[$i]['count'];
				$tpresc+=$data[$i]['count'];				
			}
			if ($data[$i]['id_estatus_maestro']=='88' && $data[$i]['gerencia']=='COORDINACION'){
				$asignadoc=$data[$i]['count'];
				$tasigc+=$data[$i]['count'];				
			}
			
			if ($data[$i]['id_estatus_maestro']=='90' && $data[$i]['gerencia']=='COORDINACION'){
				$extraviadoc=$data[$i]['count'];
				$textc+=$data[$i]['count'];				
			}
			if ($data[$i]['id_estatus_maestro']=='91' && $data[$i]['gerencia']=='COORDINACION'){
				$desincorporadoc=$data[$i]['count'];
				$tdesc+=$data[$i]['count'];				
			}
			if ($data[$i]['id_estatus_maestro']=='618' && $data[$i]['gerencia']=='COORDINACION'){
				$robadoc=$data[$i]['count'];
				$trobc+=$data[$i]['count'];				
			}
			if ($data[$i]['id_estatus_maestro']=='619' && $data[$i]['gerencia']=='COORDINACION'){
				$reemplazadoc=$data[$i]['count'];
				$treec+=$data[$i]['count'];				
			}
			if ($data[$i]['id_estatus_maestro']=='89' && $data[$i]['gerencia']=='COORDINACION'){
				$danadoc=$data[$i]['count'];
				$tdanc+=$data[$i]['count'];				
			}
			
		}else{
			$total=$disponible + $prestamo + $asignado +  $extraviado + $desincorporado + $robado + $danado;
			$ttotal+=$total;
			$totalc=$disponiblec + $prestamoc + $asignadoc + $extraviadoc + $desincorporadoc + $robadoc + $danadoc;
			$ttotalc+=$totalc;
			$ttotalt=$total+$totalc;
			$pdf->Row(array(utf8_decode($equipo),$asignado,$asignadoc,$danado,$danadoc,$desincorporado,$desincorporadoc,$disponible,$disponiblec,$extraviado,$extraviadoc,$prestamo,$prestamoc,$robado,$robadoc,$total,$totalc,$ttotalt));	
			
			$disponible=0;
			$prestamo=0;
			$asignado=0;
			$danado=0;
			$extraviado=0;
			$desincorporado=0;
			$robado=0;
			$reemplazado=0;
			$total=0;
			$disponiblec=0;
			$prestamoc=0;
			$asignadoc=0;
			$danadoc=0;
			$extraviadoc=0;
			$desincorporadoc=0;
			$robadoc=0;
			$reemplazadoc=0;
			$totalc=0;
			$ttotalt=0;
			$equipo=$data[$i]['equipo'];
			$idequipo=$data[$i]['id_tipo_maestro'];
			
			$sql="select count(b.*), t.stritema as equipo
			from tblbienes b, tblmaestros t
			where b.id_tipo_maestro=t.id_maestro and b.id_tipo_maestro=".$idequipo." 
			and b.id_estatus_maestro=86 and b.id_unidad_maestro=690
			and b.bolborrado=0 and t.bolborrado=0
			group by t.stritema";
			$conn= new Conexion();
			$conn->abrirConexion();
			$conn->sql=$sql;
			$data1=$conn->ejecutarSentencia(2);
			if ($data1[0]['count']!=""){
				$disponible=$data1[0]['count'];
				$tdis+=$data1[0]['count'];
			}else{
				$disponible=0;
				$tdis+=0;
			}
						
			
			
			
			
			if ($data[$i]['id_estatus_maestro']=='86' && $data[$i]['gerencia']=='GERENCIA' ){
				$asignado+=$data[$i]['count']-$disponible;
				$tasignado+=$asignado;				
			}
			if ($data[$i]['id_estatus_maestro']=='87' && $data[$i]['gerencia']=='GERENCIA'){
				$prestamo=$data[$i]['count'];
				$tpres+=$data[$i]['count'];				
			}
			if ($data[$i]['id_estatus_maestro']=='88' && $data[$i]['gerencia']=='GERENCIA'){
				$asignado=$data[$i]['count']+$disponible;
				$tasig+=$data[$i]['count'];				
			}
			
			if ($data[$i]['id_estatus_maestro']=='90' && $data[$i]['gerencia']=='GERENCIA'){
				$extraviado=$data[$i]['count'];
				$text+=$data[$i]['count'];				
			}
			if ($data[$i]['id_estatus_maestro']=='91' && $data[$i]['gerencia']=='GERENCIA'){
				$desincorporado=$data[$i]['count'];
				$tdes+=$data[$i]['count'];				
			}
			if ($data[$i]['id_estatus_maestro']=='618' && $data[$i]['gerencia']=='GERENCIA'){
				$robado=$data[$i]['count'];
				$trob+=$data[$i]['count'];				
			}			
			if ($data[$i]['id_estatus_maestro']=='89' && $data[$i]['gerencia']=='GERENCIA'){
				$danado=$data[$i]['count'];
				$tdan+=$data[$i]['count'];				
			}
			
			if ($data[$i]['id_estatus_maestro']=='86' && $data[$i]['gerencia']=='COORDINACION' ){
				$disponiblec=$data[$i]['count'];
				$tdisc+=$disponiblec;				
			}
			if ($data[$i]['id_estatus_maestro']=='87' && $data[$i]['gerencia']=='COORDINACION'){
				$prestamoc=$data[$i]['count'];
				$tpresc+=$data[$i]['count'];				
			}
			if ($data[$i]['id_estatus_maestro']=='88' && $data[$i]['gerencia']=='COORDINACION'){
				$asignadoc=$data[$i]['count'];
				$tasigc+=$data[$i]['count'];				
			}
			
			if ($data[$i]['id_estatus_maestro']=='90' && $data[$i]['gerencia']=='COORDINACION'){
				$extraviadoc=$data[$i]['count'];
				$textc+=$data[$i]['count'];				
			}
			if ($data[$i]['id_estatus_maestro']=='91' && $data[$i]['gerencia']=='COORDINACION'){
				$desincorporadoc=$data[$i]['count'];
				$tdesc+=$data[$i]['count'];				
			}
			if ($data[$i]['id_estatus_maestro']=='618' && $data[$i]['gerencia']=='COORDINACION'){
				$robadoc=$data[$i]['count'];
				$trobc+=$data[$i]['count'];				
			}
			if ($data[$i]['id_estatus_maestro']=='89' && $data[$i]['gerencia']=='COORDINACION'){
				$danadoc=$data[$i]['count'];
				$tdanc+=$data[$i]['count'];				
			}
		}
		
	}
	$total=$disponible + $prestamo + $asignado + $danado + $extraviado + $desincorporado + $robado;
	$ttotal+=$total;
	$totalc=$disponiblec + $prestamoc + $asignadoc + $danadoc + $desincorporadoc + $robadoc;
	$ttotalc+=$totalc;
	$ttotalt=$total+$totalc;
	$pdf->Row(array(utf8_decode($equipo),$asignado,$asignadoc,$danado,$danadoc,$desincorporado,$desincorporadoc,$disponible,$disponiblec,$extraviado,$extraviadoc,$prestamo,$prestamoc,$robado,$robadoc,$total,$totalc,$ttotalt));	
	
}


//PORCENTAJE DE EQUIPOS

$pdf->AddPage();
$pdf->SetFillColor(255,255,255);		
$pdf->SetTextColor(0);
$pdf->SetDrawColor(0);
$pdf->SetLineWidth(.3);   
    
$pdf->SetWidths(array(30,13,13,13,13,13,13,13,13,13,13,13,13,13,13,13,13,26));
$pdf->SetAligns(array('L','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R'));



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
	$idequipo=$data[0]['id_tipo_maestro'];
	$tdis=0;
	$tpres=0;
	$tasig=0;
	$tdan=0;
	$text=0;
	$tdes=0;
	$trob=0;
	$tree=0;
	$ttotal=0;
	
	$disponiblec=0;
	$prestamoc=0;
	$asignadoc=0;
	$danadoc=0;
	$extraviadoc=0;
	$desincorporadoc=0;
	$robadoc=0;
	$reemplazadoc=0;
	$totalc=0;
	
	$tdisc=0;
	$tpresc=0;
	$tasigc=0;
	$tdanc=0;
	$textc=0;
	$tdesc=0;
	$trobc=0;
	$treec=0;
	$ttotalc=0;
	
	$sql="select count(b.*), t.stritema as equipo
			from tblbienes b, tblmaestros t
			where b.id_tipo_maestro=t.id_maestro and b.id_tipo_maestro=".$idequipo." 
			and b.id_estatus_maestro=86 and b.id_unidad_maestro=690
			and b.bolborrado=0 and t.bolborrado=0
			group by t.stritema";
	$conn= new Conexion();
	$conn->abrirConexion();
	$conn->sql=$sql;
	$data1=$conn->ejecutarSentencia(2);
	if ($data1[0]['count']!=""){
		$disponible=$data1[0]['count'];
		$tdis+=$data1[0]['count'];
	}else{
		$disponible=0;
		$tdis+=0;
	}		
	
	for ($i= 0; $i < count($data); $i++){
		if ($equipo==$data[$i]['equipo']){			
			if ($data[$i]['id_estatus_maestro']=='86' && $data[$i]['gerencia']=='GERENCIA' ){
				$asignado+=$data[$i]['count']-$disponible;
				$tasignado+=$asignado;				
			}
			if ($data[$i]['id_estatus_maestro']=='87' && $data[$i]['gerencia']=='GERENCIA'){
				$prestamo=$data[$i]['count'];
				$tpres+=$data[$i]['count'];				
			}
			if ($data[$i]['id_estatus_maestro']=='88' && $data[$i]['gerencia']=='GERENCIA'){
				$asignado=$data[$i]['count'];
				$tasig+=$data[$i]['count'];				
			}
			
			if ($data[$i]['id_estatus_maestro']=='90' && $data[$i]['gerencia']=='GERENCIA'){
				$extraviado=$data[$i]['count'];
				$text+=$data[$i]['count'];				
			}
			if ($data[$i]['id_estatus_maestro']=='91' && $data[$i]['gerencia']=='GERENCIA'){
				$desincorporado=$data[$i]['count'];
				$tdes+=$data[$i]['count'];				
			}
			if ($data[$i]['id_estatus_maestro']=='618' && $data[$i]['gerencia']=='GERENCIA'){
				$robado=$data[$i]['count'];
				$trob+=$data[$i]['count'];				
			}
			if ($data[$i]['id_estatus_maestro']=='619' && $data[$i]['gerencia']=='GERENCIA'){
				$reemplazado=$data[$i]['count'];
				$tree+=$data[$i]['count'];				
			}
			
			if ($data[$i]['id_estatus_maestro']=='89' && $data[$i]['gerencia']=='GERENCIA'){
				$danado=$data[$i]['count'];
				$tdan+=$data[$i]['count'];				
			}
			
			
			
			
			if ($data[$i]['id_estatus_maestro']=='86' && $data[$i]['gerencia']=='COORDINACION' ){
				$disponiblec=$data[$i]['count'];
				$tdisc+=$disponiblec;				
			}
			if ($data[$i]['id_estatus_maestro']=='87' && $data[$i]['gerencia']=='COORDINACION'){
				$prestamoc=$data[$i]['count'];
				$tpresc+=$data[$i]['count'];				
			}
			if ($data[$i]['id_estatus_maestro']=='88' && $data[$i]['gerencia']=='COORDINACION'){
				$asignadoc=$data[$i]['count'];
				$tasigc+=$data[$i]['count'];				
			}
			
			if ($data[$i]['id_estatus_maestro']=='90' && $data[$i]['gerencia']=='COORDINACION'){
				$extraviadoc=$data[$i]['count'];
				$textc+=$data[$i]['count'];				
			}
			if ($data[$i]['id_estatus_maestro']=='91' && $data[$i]['gerencia']=='COORDINACION'){
				$desincorporadoc=$data[$i]['count'];
				$tdesc+=$data[$i]['count'];				
			}
			if ($data[$i]['id_estatus_maestro']=='618' && $data[$i]['gerencia']=='COORDINACION'){
				$robadoc=$data[$i]['count'];
				$trobc+=$data[$i]['count'];				
			}
			if ($data[$i]['id_estatus_maestro']=='619' && $data[$i]['gerencia']=='COORDINACION'){
				$reemplazadoc=$data[$i]['count'];
				$treec+=$data[$i]['count'];				
			}
			if ($data[$i]['id_estatus_maestro']=='89' && $data[$i]['gerencia']=='COORDINACION'){
				$danadoc=$data[$i]['count'];
				$tdanc+=$data[$i]['count'];				
			}
			
		}else{
			$total=$disponible + $prestamo + $asignado +  $extraviado + $desincorporado + $robado + $danado;
			$ttotal+=$total;
			$totalc=$disponiblec + $prestamoc + $asignadoc + $extraviadoc + $desincorporadoc + $robadoc + $danadoc;
			$ttotalc+=$totalc;
			$ttotalt=$total+$totalc;
			$pdf->Row(array(utf8_decode($equipo),number_format((($asignado*100)/$ttotalt),2,",",".").'%',number_format((($asignadoc*100)/$ttotalt),2,",",".").'%',number_format((($danado*100)/$ttotalt),2,",",".").'%',number_format((($danadoc*100)/$ttotalt),2,",",".").'%',number_format((($desincorporado*100)/$ttotalt),2,",",".").'%',number_format((($desincorporadoc*100)/$ttotalt),2,",",".").'%',number_format((($disponible*100)/$ttotalt),2,",",".").'%',number_format((($disponiblec*100)/$ttotalt),2,",",".").'%',number_format((($extraviado*100)/$ttotalt),2,",",".").'%',number_format((($extraviadoc*100)/$ttotalt),2,",",".").'%',number_format((($prestamo*100)/$ttotalt),2,",",".").'%',number_format((($prestamoc*100)/$ttotalt),2,",",".").'%',number_format((($robado*100)/$ttotalt),2,",",".").'%',number_format((($robadoc*100)/$ttotalt),2,",",".").'%',number_format((($total*100)/$ttotalt),2,",",".").'%',number_format((($totalc*100)/$ttotalt),2,",",".").'%',number_format((($ttotalt*100)/$ttotalt),2,",",".").'%'));	
			
			$disponible=0;
			$prestamo=0;
			$asignado=0;
			$danado=0;
			$extraviado=0;
			$desincorporado=0;
			$robado=0;
			$reemplazado=0;
			$total=0;
			$disponiblec=0;
			$prestamoc=0;
			$asignadoc=0;
			$danadoc=0;
			$extraviadoc=0;
			$desincorporadoc=0;
			$robadoc=0;
			$reemplazadoc=0;
			$totalc=0;
			$ttotalt=0;
			$equipo=$data[$i]['equipo'];
			$idequipo=$data[$i]['id_tipo_maestro'];
			
			$sql="select count(b.*), t.stritema as equipo
			from tblbienes b, tblmaestros t
			where b.id_tipo_maestro=t.id_maestro and b.id_tipo_maestro=".$idequipo." 
			and b.id_estatus_maestro=86 and b.id_unidad_maestro=690
			and b.bolborrado=0 and t.bolborrado=0
			group by t.stritema";
			$conn= new Conexion();
			$conn->abrirConexion();
			$conn->sql=$sql;
			$data1=$conn->ejecutarSentencia(2);
			if ($data1[0]['count']!=""){
				$disponible=$data1[0]['count'];
				$tdis+=$data1[0]['count'];
			}else{
				$disponible=0;
				$tdis+=0;
			}
						
			
			
			
			
			if ($data[$i]['id_estatus_maestro']=='86' && $data[$i]['gerencia']=='GERENCIA' ){
				$asignado+=$data[$i]['count']-$disponible;
				$tasignado+=$asignado;				
			}
			if ($data[$i]['id_estatus_maestro']=='87' && $data[$i]['gerencia']=='GERENCIA'){
				$prestamo=$data[$i]['count'];
				$tpres+=$data[$i]['count'];				
			}
			if ($data[$i]['id_estatus_maestro']=='88' && $data[$i]['gerencia']=='GERENCIA'){
				$asignado=$data[$i]['count']+$disponible;
				$tasig+=$data[$i]['count'];				
			}
			
			if ($data[$i]['id_estatus_maestro']=='90' && $data[$i]['gerencia']=='GERENCIA'){
				$extraviado=$data[$i]['count'];
				$text+=$data[$i]['count'];				
			}
			if ($data[$i]['id_estatus_maestro']=='91' && $data[$i]['gerencia']=='GERENCIA'){
				$desincorporado=$data[$i]['count'];
				$tdes+=$data[$i]['count'];				
			}
			if ($data[$i]['id_estatus_maestro']=='618' && $data[$i]['gerencia']=='GERENCIA'){
				$robado=$data[$i]['count'];
				$trob+=$data[$i]['count'];				
			}			
			if ($data[$i]['id_estatus_maestro']=='89' && $data[$i]['gerencia']=='GERENCIA'){
				$danado=$data[$i]['count'];
				$tdan+=$data[$i]['count'];				
			}
			
			if ($data[$i]['id_estatus_maestro']=='86' && $data[$i]['gerencia']=='COORDINACION' ){
				$disponiblec=$data[$i]['count'];
				$tdisc+=$disponiblec;				
			}
			if ($data[$i]['id_estatus_maestro']=='87' && $data[$i]['gerencia']=='COORDINACION'){
				$prestamoc=$data[$i]['count'];
				$tpresc+=$data[$i]['count'];				
			}
			if ($data[$i]['id_estatus_maestro']=='88' && $data[$i]['gerencia']=='COORDINACION'){
				$asignadoc=$data[$i]['count'];
				$tasigc+=$data[$i]['count'];				
			}
			
			if ($data[$i]['id_estatus_maestro']=='90' && $data[$i]['gerencia']=='COORDINACION'){
				$extraviadoc=$data[$i]['count'];
				$textc+=$data[$i]['count'];				
			}
			if ($data[$i]['id_estatus_maestro']=='91' && $data[$i]['gerencia']=='COORDINACION'){
				$desincorporadoc=$data[$i]['count'];
				$tdesc+=$data[$i]['count'];				
			}
			if ($data[$i]['id_estatus_maestro']=='618' && $data[$i]['gerencia']=='COORDINACION'){
				$robadoc=$data[$i]['count'];
				$trobc+=$data[$i]['count'];				
			}
			if ($data[$i]['id_estatus_maestro']=='89' && $data[$i]['gerencia']=='COORDINACION'){
				$danadoc=$data[$i]['count'];
				$tdanc+=$data[$i]['count'];				
			}
		}
		
	}
	$total=$disponible + $prestamo + $asignado + $danado + $extraviado + $desincorporado + $robado;
	$ttotal+=$total;
	$totalc=$disponiblec + $prestamoc + $asignadoc + $danadoc + $desincorporadoc + $robadoc;
	$ttotalc+=$totalc;
	$ttotalt=$total+$totalc;
	$pdf->Row(array(utf8_decode($equipo),number_format((($asignado*100)/$ttotalt),2,",",".").'%',number_format((($asignadoc*100)/$ttotalt),2,",",".").'%',number_format((($danado*100)/$ttotalt),2,",",".").'%',number_format((($danadoc*100)/$ttotalt),2,",",".").'%',number_format((($desincorporado*100)/$ttotalt),2,",",".").'%',number_format((($desincorporadoc*100)/$ttotalt),2,",",".").'%',number_format((($disponible*100)/$ttotalt),2,",",".").'%',number_format((($disponiblec*100)/$ttotalt),2,",",".").'%',number_format((($extraviado*100)/$ttotalt),2,",",".").'%',number_format((($extraviadoc*100)/$ttotalt),2,",",".").'%',number_format((($prestamo*100)/$ttotalt),2,",",".").'%',number_format((($prestamoc*100)/$ttotalt),2,",",".").'%',number_format((($robado*100)/$ttotalt),2,",",".").'%',number_format((($robadoc*100)/$ttotalt),2,",",".").'%',number_format((($total*100)/$ttotalt),2,",",".").'%',number_format((($totalc*100)/$ttotalt),2,",",".").'%',number_format((($ttotalt*100)/$ttotalt),2,",",".").'%'));	
	
}





$pdf->Output();		

	
		
?>

