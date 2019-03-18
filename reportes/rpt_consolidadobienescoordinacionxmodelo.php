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
		$contacto=  new clMaestroModelo();		
		$data=$contacto->selectMaestroPadreById($_REQUEST['id']);
		if($data){
			$tipo=$data[0]['stritemb'];
		}
		//Logo
		$this->Image('../comunes/images/carta.png',20,3,180,15);
		$this->SetX(12);
		$this->SetY(25);
		$this->SetFont('Arial','B',10);
		$this->Cell(0,10,'CONSOLIDADO DE '.utf8_decode($tipo).' DE LAS COORDINACIONES POR MODELOS',0,0,'C');
		$this->Ln(10);
		
		$this->SetFont('Arial','B',8);		
		$this->SetFillColor(255,0,0);		
		$this->SetTextColor(255,255,255);
		$this->SetDrawColor(0);
		$this->SetLineWidth(.3);       
		$this->SetWidths(array(20,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10));
		$this->SetAligns(array('C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C','C'));
		$this->Row(array('Modelo','AMA','ANZ','APU','ARA','BAR','BOL','CAR','COJ','DELT','FAL','GUA','LAR','MER','MIR','MON','NVA','POR','SUC','TAC','TRU','VAR','YAR','ZUL','DTTO','Total'));
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
$pdf->SetMargins(5,5,5);
$pdf->SetFont('Arial','',7);
$pdf->AddPage();
$sql="select count(b.*), m.stritema as modelo, g.stritemc as gerencia,  b.id_modelo_maestro, b.id_gerencia_maestro, t.stritema as equipo
from tblbienes b, tblmaestros m,  tblmaestros g, tblmaestros t
where b.id_gerencia_maestro=g.id_maestro and b.id_modelo_maestro=m.id_maestro and b.id_tipo_maestro=t.id_maestro
and b.id_tipo_maestro=".$_REQUEST['id']." and b.bolborrado=0 and t.bolborrado=0 and m.bolborrado=0 and g.bolborrado=0 and g.stritemb='COORDINACION' and b.id_estatus_maestro in (86,87,88)
group by m.stritema, g.stritemc,  b.id_modelo_maestro, b.id_gerencia_maestro, t.stritema
order by b.id_modelo_maestro, b.id_gerencia_maestro";
$conn= new Conexion();
$conn->abrirConexion();
$conn->sql=$sql;
$data=$conn->ejecutarSentencia(2);
$pdf->SetFillColor(255,255,255);		
$pdf->SetTextColor(0);
$pdf->SetDrawColor(0);
$pdf->SetLineWidth(.3);   
    
$pdf->SetWidths(array(20,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10,10));
$pdf->SetAligns(array('L','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R'));
if ($data){
	$ama=0;
	$anz=0;
	$apu=0;
	$ara=0;
	$bar=0;
	$bol=0;
	$car=0;
	$coj=0;
	$delta=0;
	$fal=0;
	$gua=0;
	$lar=0;
	$mer=0;
	$mir=0;
	$mon=0;	
	$nva=0;
	$por=0;
	$suc=0;
	$tac=0;
	$tru=0;
	$var=0;
	$yar=0;
	$zul=0;
	$dtto=0;	
	$total=0;
	$equipo=$data[0]['modelo'];
	$tama=0;
	$tanz=0;
	$tapu=0;
	$tara=0;
	$tbar=0;
	$tbol=0;
	$tcar=0;
	$tcoj=0;
	$tdelta=0;
	$tfal=0;
	$tgua=0;
	$tlar=0;
	$tmer=0;
	$tmir=0;
	$tmon=0;	
	$tnva=0;
	$tpor=0;
	$tsuc=0;
	$ttac=0;
	$ttru=0;
	$tvar=0;
	$tyar=0;
	$tzul=0;
	$tdtto=0;			
	$ttotal=0;
	for ($i= 0; $i < count($data); $i++){
		if ($equipo==$data[$i]['modelo']){			
			if ($data[$i]['id_gerencia_maestro']=='32'){
				$ama=$data[$i]['count'];
				$tama+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='33'){
				$anz=$data[$i]['count'];
				$tanz+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='34'){
				$apu=$data[$i]['count'];
				$tapu+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='35'){
				$ara=$data[$i]['count'];
				$tara+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='36'){
				$bar=$data[$i]['count'];
				$tbar+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='37'){
				$bol=$data[$i]['count'];
				$tbol+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='38'){
				$car=$data[$i]['count'];
				$tcar+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='39'){
				$coj=$data[$i]['count'];
				$tcoj+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='40'){
				$delta=$data[$i]['count'];
				$tdelta+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='41'){
				$fal=$data[$i]['count'];
				$tfal+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='42'){
				$gua=$data[$i]['count'];
				$tgua+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='43'){
				$lar=$data[$i]['count'];
				$tlar+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='44'){
				$mer=$data[$i]['count'];
				$tmer+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='45'){
				$mir=$data[$i]['count'];
				$tmir+=$mir;				
			}
			if ($data[$i]['id_gerencia_maestro']=='46'){
				$mon=$data[$i]['count'];
				$tmon+=$data[$i]['count'];
			}		
			if ($data[$i]['id_gerencia_maestro']=='47'){
				$nva=$data[$i]['count'];
				$tnva+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='48'){
				$por=$data[$i]['count'];
				$tpor+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='49'){
				$suc=$data[$i]['count'];
				$tsuc+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='50'){
				$tac=$data[$i]['count'];
				$ttac+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='51'){
				$tru=$data[$i]['count'];
				$ttru+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='52'){
				$var=$data[$i]['count'];
				$tvar+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='53'){
				$yar=$data[$i]['count'];
				$tyar+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='54'){
				$zul=$data[$i]['count'];
				$tzul+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='616'){
				$dtto=$data[$i]['count'];
				$tdtto+=$data[$i]['count'];
			}	
		}else{
			$total=$ama+$anz+$apu+$ara+$bar+$bol+$car+$coj+$delta+$fal+$gua+$lar+$mer+$mir+$mon+$nva+$por+$suc+$tac+$tru+$var+$yar+$zul+$dtto;	
			$ttotal+=$total;
			$pdf->Row(array(utf8_decode($equipo),$ama,$anz,$apu,$ara,$bar,$bol,$car,$coj,$delta,$fal,$gua,$lar,$mer,$mir,$mon,$nva,$por,$suc,$tac,$tru,$var,$yar,$zul,$dtto,$total));	
			
			$ama=0;
			$anz=0;
			$apu=0;
			$ara=0;
			$bar=0;
			$bol=0;
			$car=0;
			$coj=0;
			$delta=0;
			$fal=0;
			$gua=0;
			$lar=0;
			$mer=0;
			$mir=0;
			$mon=0;	
			$nva=0;
			$por=0;
			$suc=0;
			$tac=0;
			$tru=0;
			$var=0;
			$yar=0;
			$zul=0;
			$dtto=0;	
			$total=0;
			$equipo=$data[$i]['modelo'];
			if ($data[$i]['id_gerencia_maestro']=='32'){
				$ama=$data[$i]['count'];
				$tama+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='33'){
				$anz=$data[$i]['count'];
				$tanz+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='34'){
				$apu=$data[$i]['count'];
				$tapu+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='35'){
				$ara=$data[$i]['count'];
				$tara+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='36'){
				$bar=$data[$i]['count'];
				$tbar+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='37'){
				$bol=$data[$i]['count'];
				$tbol+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='38'){
				$car=$data[$i]['count'];
				$tcar+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='39'){
				$coj=$data[$i]['count'];
				$tcoj+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='40'){
				$delta=$data[$i]['count'];
				$tdelta+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='41'){
				$fal=$data[$i]['count'];
				$tfal+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='42'){
				$gua=$data[$i]['count'];
				$tgua+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='43'){
				$lar=$data[$i]['count'];
				$tlar+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='44'){
				$mer=$data[$i]['count'];
				$tmer+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='45'){
				$mir=$data[$i]['count'];
				$tmir+=$mir;
			}
			if ($data[$i]['id_gerencia_maestro']=='46'){
				$mon=$data[$i]['count'];
				$tmon+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='47'){
				$nva=$data[$i]['count'];
				$tnva+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='48'){
				$por=$data[$i]['count'];
				$tpor+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='49'){
				$suc=$data[$i]['count'];
				$tsuc+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='50'){
				$tac=$data[$i]['count'];
				$ttac+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='51'){
				$tru=$data[$i]['count'];
				$ttru+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='52'){
				$var=$data[$i]['count'];
				$tvar+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='53'){
				$yar=$data[$i]['count'];
				$tyar+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='54'){
				$zul=$data[$i]['count'];
				$tzul+=$data[$i]['count'];
			}
			if ($data[$i]['id_gerencia_maestro']=='616'){
				$dtto=$data[$i]['count'];
				$tdtto+=$data[$i]['count'];
			}	
		}
		
	}
	$total=$ama+$anz+$apu+$ara+$bar+$bol+$car+$coj+$delta+$fal+$gua+$lar+$mer+$mir+$mon+$nva+$por+$suc+$tac+$tru+$var+$yar+$zul+$dtto;	
	$ttotal+=$total;
	$pdf->Row(array(utf8_decode($equipo),$ama,$anz,$apu,$ara,$bar,$bol,$car,$coj,$delta,$fal,$gua,$lar,$mer,$mir,$mon,$nva,$por,$suc,$tac,$tru,$var,$yar,$zul,$dtto,$total));			
	$pdf->Row(array('TOTAL',$tama,$tanz,$tapu,$tara,$tbar,$tbol,$tcar,$tcoj,$tdelta,$tfal,$tgua,$tlar,$tmer,$tmir,$tmon,$tnva,$tpor,$tsuc,$ttac,$ttru,$tvar,$tyar,$tzul,$tdtto,$ttotal));
	
	
    
	
}else{
	echo "<script>alert('No existen registros para mostrar');window.close(this);</script>";
}



$pdf->Output();		

	
		
?>

