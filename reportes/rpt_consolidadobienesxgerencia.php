<?php
session_start();
require('../common/php/fpdf.php');
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
        if($this->GetY()+$h+8>$this->PageBreakTrigger){
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
		$this->Image('../comunes/images/carta_horizontal.jpg',10,3,260,20);
		$this->SetX(12);
		$this->SetY(25);
		$this->SetFont('Arial','B',10);
		$this->Cell(0,10,utf8_decode('CONSOLIDADO DE BIENES TELEMÁTICOS POR DIRECCION GENERAL'),0,0,'C');
		$this->Ln(10);
		
		$this->SetFont('Arial','B',8);
		$this->SetFillColor(255,0,0);		
		$this->SetTextColor(255,255,255);
		$this->SetDrawColor(0);
		$this->SetLineWidth(.3);       
		$this->SetWidths(array(30,11,11,15,13,11,11,11,11,15,11,11,11,11,11,11,11,11,11,11,11));
                $this->SetAligns(array('C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C'));
		$this->Row(array('Equipo','DMIN','DGD','OESEPP','OENCC','AI','OGA','OSTI','CJ','ORRHH','OCRI','OCI','OME','OAC','OSCE','OPPO','VFC','VPC','VEC','VPS','Total'));
	}

	function Footer()
	{
		//Go to 1.5 cm from bottom
		$this->SetY(-30);
		$this->SetFont('Arial','',6);
		//Print centered page number
                $texto='LEYENDA: DMIN=DESPACHO DEL MINISTERIO | DGD=DIRECCIÓN GENERAL DEL DESPACHO | OESEPP=OFICINA ESTRATÉGICA DE SEGUIMIENTO Y EVALUACIÓN DE POLÍTICAS PÚBLICAS | ';
                $texto.='OENCC=OFICINA ESTRATÉGICA NACIONAL DE LOS CONSEJOS COMUNALES | AI=AUDITORÍA INTERNA | OGA=OFICINA DE GESTIÓN ADMINISTRATIVA | OSTI= OFICINA DE SISTEMAS Y TECNOLOGÍA DE LA INFORMACIÓN | ';
                $texto.='CJ=CONSULTORÍA JURÍDICA | ORRHH=OFICINA DE RECURSOS HUMANOS | OCRI=OFICINA DE COMUNICACIÓN Y RELACIONES INSTITUCIONALES | OCI=OFICINA DE COOPERACION INSTERNACIONAL | ';
                $texto.='OME=OFICINA DE MISIONES ESPECIALES | OAC=OFICINA DE ATENCIÓN AL CIUDADANO | OSCE=OFICINA DE SEGUIMIENTO Y CONTROL DE LOS ESTADOS | OPPO=OFICINA DE PLANIFICACIÓN PRESUPUESTO Y ORGANIZACIÓN | ';
                $texto.='VFC=VICEMINISTERIO DE FORMACIÓN COMUNAL | VPC=VICEMINISTERIO DE PARTICIPACIÓN COMUNAL | VEC=VICEMINISTERIO DE ECONOMÍA COMUNAL | VPS=VICEMINISTERIO DE PROTECCIÓN SOCIAL';
		$this->MultiCell(0,4, utf8_decode($texto), 0, 'J');
		$this->Ln(1);
		$this->Cell(0,5,utf8_decode('Generado a través del Sistema SICET en Fecha '.date('d/m/Y').' por el Usuario '.rtrim($_SESSION["strnombrefenix"]).' '.rtrim($_SESSION["strapellidofenix"]).'  |  Fuente: OSTI - '.date('Y').'  |  Licencia: GPL/GNU'),0,0,'L');
		$this->Cell(10,5,utf8_decode('Página '.$this->PageNo().'/{nb}'),0,0,'R');
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
and b.bolborrado=0 and t.bolborrado=0 and g.stritemb='DIRECCION GENERAL'
and g.bolborrado=0 and b.id_estatus_maestro in (86,87,88)
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
    
    
$pdf->SetWidths(array(30,11,11,15,13,11,11,11,11,15,11,11,11,11,11,11,11,11,11,11,11));
$pdf->SetAligns(array('L','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R'));

if ($data){
        $dmin=0;
        $dgd=0;
        $oesepp=0;
        $oencc=0;
        $ai=0;
        $oga=0;
        $osti=0;
        $cj=0;
        $orrhh=0;
        $ocri=0;
        $oci=0;
        $ome=0;
        $oac=0;
        $osce=0;
        $oppo=0;
        $vfc=0;
        $vpc=0;
        $vec=0;
        $vps=0;
	$total=0;
	$equipo=$data[0]['equipo'];
	$tdmin=0;
        $tdgd=0;
        $toesepp=0;
        $toencc=0;
        $tai=0;
        $toga=0;
        $tosti=0;
        $tcj=0;
        $torrhh=0;
        $tocri=0;
        $toci=0;
        $tome=0;
        $toac=0;
        $tosce=0;
        $toppo=0;
        $tvfc=0;
        $tvpc=0;
        $tvec=0;
        $tvps=0;
	$ttotal=0;
	for ($i= 0; $i < count($data); $i++){
		if ($equipo==$data[$i]['equipo']){			
			if ($data[$i]['id_gerencia_maestro']=='17'){
				$dmin=$data[$i]['count'];
				$tdmin+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='18'){
				$ai=$data[$i]['count'];
				$tai+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='19'){
				$oppo=$data[$i]['count'];
				$toppo+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='20'){
				$cj=$data[$i]['count'];
				$tcj+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='21'){
				$oga=$data[$i]['count'];
				$toga+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='22'){
				$orrhh=$data[$i]['count'];
				$trrhh+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='23'){
				$osti=$data[$i]['count'];
				$tosti+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='24'){
				$ocri=$data[$i]['count'];
				$tocri+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='25'){
				$oac=$data[$i]['count'];
				$toac+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='27'){
				$oencc=$data[$i]['count'];
				$toencc+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='28'){
				$oesepp=$data[$i]['count'];
				$toesepp+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='29'){
				$oci=$data[$i]['count'];
				$toci+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='30'){
				$ome=$data[$i]['count'];
				$tome+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='31'){
				$osce=$data[$i]['count'];
				$tosce+=$data[$i]['count'];
			}
                        if ($data[$i]['id_gerencia_maestro']=='682'){
				$dgd=$data[$i]['count'];
				$tdgd+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='931'){
				$vec=$data[$i]['count'];
				$tvec+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='1110'){
				$vps=$data[$i]['count'];
				$tvps+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='1115'){
				$vfc=$data[$i]['count'];
				$tvfc+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='1120'){
				$vpc=$data[$i]['count'];
				$tvpc+=$data[$i]['count'];
			}
			
		}else{
			$total=$dmin+$dgd+$oesepp+$oencc+$ai+$oga+$osti+$cj+$orrhh+$ocri+$oci+$ome+$oac+$osce+$oppo+$vfc+$vpc+$vec+$vps;
			$ttotal+=$total;
			$pdf->Row(array($equipo,$dmin,$dgd,$oesepp,$oencc,$ai,$oga,$osti,$cj,$orrhh,$ocri,$oci,$ome,$oac,$osce,$oppo,$vfc,$vpc,$vec,$vps,$total));
			
			$dmin=0;
                        $dgd=0;
                        $oesepp=0;
                        $oencc=0;
                        $ai=0;
                        $oga=0;
                        $osti=0;
                        $cj=0;
                        $orrhh=0;
                        $ocri=0;
                        $oci=0;
                        $ome=0;
                        $oac=0;
                        $osce=0;
                        $oppo=0;
                        $vfc=0;
                        $vpc=0;
                        $vec=0;
                        $vps=0;
                        $total=0;
			$equipo=$data[$i]['equipo'];
                        if ($data[$i]['id_gerencia_maestro']=='17'){
				$dmin=$data[$i]['count'];
				$tdmin+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='18'){
				$ai=$data[$i]['count'];
				$tai+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='19'){
				$oppo=$data[$i]['count'];
				$toppo+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='20'){
				$cj=$data[$i]['count'];
				$tcj+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='21'){
				$oga=$data[$i]['count'];
				$toga+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='22'){
				$orrhh=$data[$i]['count'];
				$trrhh+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='23'){
				$osti=$data[$i]['count'];
				$tosti+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='24'){
				$ocri=$data[$i]['count'];
				$tocri+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='25'){
				$oac=$data[$i]['count'];
				$toac+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='27'){
				$oencc=$data[$i]['count'];
				$toencc+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='28'){
				$oesepp=$data[$i]['count'];
				$toesepp+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='29'){
				$oci=$data[$i]['count'];
				$toci+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='30'){
				$ome=$data[$i]['count'];
				$tome+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='31'){
				$osce=$data[$i]['count'];
				$tosce+=$data[$i]['count'];
			}
                        if ($data[$i]['id_gerencia_maestro']=='682'){
				$dgd=$data[$i]['count'];
				$tdgd+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='931'){
				$vec=$data[$i]['count'];
				$tvec+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='1110'){
				$vps=$data[$i]['count'];
				$tvps+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='1115'){
				$vfc=$data[$i]['count'];
				$tvfc+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='1120'){
				$vpc=$data[$i]['count'];
				$tvpc+=$data[$i]['count'];
			}
		}
		
	}
	$total=$dmin+$dgd+$oesepp+$oencc+$ai+$oga+$osti+$cj+$orrhh+$ocri+$oci+$ome+$oac+$osce+$oppo+$vfc+$vpc+$vec+$vps;
        $ttotal+=$total;
	$pdf->Row(array($equipo,$dmin,$dgd,$oesepp,$oencc,$ai,$oga,$osti,$cj,$orrhh,$ocri,$oci,$ome,$oac,$osce,$oppo,$vfc,$vpc,$vec,$vps,$total));
	$pdf->Row(array('TOTAL',$tdmin,$tdgd,$toesepp,$toencc,$tai,$toga,$tosti,$tcj,$torrhh,$tocri,$toci,$tome,$toac,$tosce,$toppo,$tvfc,$tvpc,$tvec,$tvps,$ttotal));
	
	
    
	
}else{
	echo "<script>alert('No existen registros para mostrar');window.close(this);</script>";
}



$pdf->Output();		

	

	
		
?>

