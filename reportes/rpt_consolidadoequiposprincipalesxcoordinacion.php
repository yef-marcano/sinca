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
		$this->SetFont('Arial','B',10);   
		$this->SetX(12);
		$this->SetY(15);		
		$this->Cell(0,10,utf8_decode('CONSOLIDADO DETALLADO DE EQUIPOS TELEMÁTICOS DE LAS COORDINACIONES Y ESCUELAS DE FORMACI�N AL ').date('d/m/Y'),0,0,'C');
		$this->Ln(10);
		$this->SetFont('Arial','B',8);
		$this->SetFillColor(255,0,0);		
		$this->SetTextColor(255,255,255);
		$this->SetDrawColor(0);
		$this->SetLineWidth(.3);    
		$this->Cell(30,15,'COORDINACION', '1',0,'C',1);
		$this->Cell(60,5,'IMPRESORA','1',0,'C',1);
		$this->Cell(60,5,'LAPTOP','1',0,'C',1);
		$this->Cell(60,5,'DESKTOP','1',0,'C',1);
		$this->Cell(60,5,'MONITOR','1',0,'C',1);
		$this->Cell(60,5,'VIDEO BEAM','1',1,'C',1);
		$this->SetX(40);
		$this->SetFont('Arial','B',6);
		$this->Cell(30,5,'COORD.','1',0,'C',1);
		$this->Cell(30,5,'ESCUELA','1',0,'C',1);
		$this->Cell(30,5,'COORD.','1',0,'C',1);
		$this->Cell(30,5,'ESCUELA','1',0,'C',1);
		$this->Cell(30,5,'COORD.','1',0,'C',1);
		$this->Cell(30,5,'ESCUELA','1',0,'C',1);
		$this->Cell(30,5,'COORD.','1',0,'C',1);
		$this->Cell(30,5,'ESCUELA','1',0,'C',1);
		$this->Cell(30,5,'COORD.','1',0,'C',1);
		$this->Cell(30,5,'ESCUELA','1',0,'C',1);
		$this->Ln(5);
		
		$this->SetX(40);
		
		
		$this->Cell(6,5,'AS','1',0,'C',1);
		$this->Cell(6,5,'PR','1',0,'C',1);		
		$this->Cell(6,5,'DA','1',0,'C',1);
		$this->Cell(6,5,'EX','1',0,'C',1);		
		$this->Cell(6,5,'RO','1',0,'C',1);
		
		$this->Cell(6,5,'AS','1',0,'C',1);
		$this->Cell(6,5,'PR','1',0,'C',1);		
		$this->Cell(6,5,'DA','1',0,'C',1);
		$this->Cell(6,5,'EX','1',0,'C',1);		
		$this->Cell(6,5,'RO','1',0,'C',1);
		$this->Cell(6,5,'AS','1',0,'C',1);
		$this->Cell(6,5,'PR','1',0,'C',1);		
		$this->Cell(6,5,'DA','1',0,'C',1);
		$this->Cell(6,5,'EX','1',0,'C',1);		
		$this->Cell(6,5,'RO','1',0,'C',1);
		$this->Cell(6,5,'AS','1',0,'C',1);
		$this->Cell(6,5,'PR','1',0,'C',1);		
		$this->Cell(6,5,'DA','1',0,'C',1);
		$this->Cell(6,5,'EX','1',0,'C',1);		
		$this->Cell(6,5,'RO','1',0,'C',1);
		$this->Cell(6,5,'AS','1',0,'C',1);
		$this->Cell(6,5,'PR','1',0,'C',1);		
		$this->Cell(6,5,'DA','1',0,'C',1);
		$this->Cell(6,5,'EX','1',0,'C',1);		
		$this->Cell(6,5,'RO','1',0,'C',1);
		$this->Cell(6,5,'AS','1',0,'C',1);
		$this->Cell(6,5,'PR','1',0,'C',1);		
		$this->Cell(6,5,'DA','1',0,'C',1);
		$this->Cell(6,5,'EX','1',0,'C',1);		
		$this->Cell(6,5,'RO','1',0,'C',1);
		$this->Cell(6,5,'AS','1',0,'C',1);
		$this->Cell(6,5,'PR','1',0,'C',1);		
		$this->Cell(6,5,'DA','1',0,'C',1);
		$this->Cell(6,5,'EX','1',0,'C',1);		
		$this->Cell(6,5,'RO','1',0,'C',1);
		$this->Cell(6,5,'AS','1',0,'C',1);
		$this->Cell(6,5,'PR','1',0,'C',1);		
		$this->Cell(6,5,'DA','1',0,'C',1);
		$this->Cell(6,5,'EX','1',0,'C',1);		
		$this->Cell(6,5,'RO','1',0,'C',1);
		$this->Cell(6,5,'AS','1',0,'C',1);
		$this->Cell(6,5,'PR','1',0,'C',1);		
		$this->Cell(6,5,'DA','1',0,'C',1);
		$this->Cell(6,5,'EX','1',0,'C',1);		
		$this->Cell(6,5,'RO','1',0,'C',1);
		$this->Cell(6,5,'AS','1',0,'C',1);
		$this->Cell(6,5,'PR','1',0,'C',1);		
		$this->Cell(6,5,'DA','1',0,'C',1);
		$this->Cell(6,5,'EX','1',0,'C',1);		
		$this->Cell(6,5,'RO','1',0,'C',1);
		
		
				
		$this->Ln();
		
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
$pdf=new PDF("L","mm","Legal");
$pdf->Open();
$pdf->AliasNbPages();
$pdf->SetMargins(10,5,5);
$pdf->SetFont('Arial','',6);
$pdf->AddPage();
$sql="select count(b.*),g.stritema as gerencia, t.stritema as equipo,b.id_tipo_maestro, b.id_ubicacion, b.id_estatus_maestro, e.stritema as estatus
from tblbienes b, tblmaestros t, tblmaestros g, tblmaestros e
where b.id_tipo_maestro=t.id_maestro and b.id_gerencia_maestro=g.id_maestro
and b.bolborrado=0 and t.bolborrado=0 and g.bolborrado=0 and b.id_estatus_maestro=e.id_maestro and e.bolborrado=0
and b.id_tipo_maestro in (106,107,117,108,118) and g.stritemb='COORDINACION' 
and b.id_ubicacion in (680,681)
group by g.stritema, t.stritema, b.id_tipo_maestro,b.id_ubicacion, b.id_estatus_maestro, e.stritema 
order by g.stritema,t.stritema,b.id_ubicacion, b.id_estatus_maestro";
$conn= new Conexion();
$conn->abrirConexion();
$conn->sql=$sql;
$data=$conn->ejecutarSentencia(2);
$pdf->SetFillColor(255,255,255);		
$pdf->SetTextColor(0);
$pdf->SetDrawColor(0);
$pdf->SetLineWidth(.3);   
    
$pdf->SetWidths(array(30,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6));
$pdf->SetAligns(array('L','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R'));
if ($data){	
	$aimp=0;
	$disaimp=0;
	$preaimp=0;
	$asiaimp=0;
	$danaimp=0;
	$extaimp=0;
	$desaimp=0;
	$robaimp=0;
	$reeaimp=0;
	$tdisaimp=0;
	$tpreaimp=0;
	$tasiaimp=0;
	$tdanaimp=0;
	$textaimp=0;
	$tdesaimp=0;
	$trobaimp=0;
	$treeaimp=0;
	
	$dissimp=0;
	$presimp=0;
	$asisimp=0;
	$dansimp=0;
	$extsimp=0;
	$dessimp=0;
	$robsimp=0;
	$reesimp=0;
	$tdissimp=0;
	$tpresimp=0;
	$tasisimp=0;
	$tdansimp=0;
	$textsimp=0;
	$tdessimp=0;
	$trobsimp=0;
	$treesimp=0;	
	
	$disalap=0;
	$prealap=0;
	$asialap=0;
	$danalap=0;
	$extalap=0;
	$desalap=0;
	$robalap=0;
	$reealap=0;
	$tdisalap=0;
	$tprealap=0;
	$tasialap=0;
	$tdanalap=0;
	$textalap=0;
	$tdesalap=0;
	$trobalap=0;
	$treealap=0;
	
	$disslap=0;
	$preslap=0;
	$asislap=0;
	$danslap=0;
	$extslap=0;
	$desslap=0;
	$robslap=0;
	$reeslap=0;
	$tdisslap=0;
	$tpreslap=0;
	$tasislap=0;
	$tdanslap=0;
	$textslap=0;
	$tdesslap=0;
	$trobslap=0;
	$treeslap=0;	
	
	$disades=0;
	$preades=0;
	$asiades=0;
	$danades=0;
	$extades=0;
	$desades=0;
	$robades=0;
	$reeades=0;
	$tdisades=0;
	$tpreades=0;
	$tasiades=0;
	$tdanades=0;
	$textades=0;
	$tdesades=0;
	$trobades=0;
	$treeades=0;
	
	$dissdes=0;
	$presdes=0;
	$asisdes=0;
	$dansdes=0;
	$extsdes=0;
	$dessdes=0;
	$robsdes=0;
	$reesdes=0;
	$tdissdes=0;
	$tpresdes=0;
	$tasisdes=0;
	$tdansdes=0;
	$textsdes=0;
	$tdessdes=0;
	$trobsdes=0;
	$treesdes=0;	
	
	$disamon=0;
	$preamon=0;
	$asiamon=0;
	$danamon=0;
	$extamon=0;
	$desamon=0;
	$robamon=0;
	$reeamon=0;
	$tdisamon=0;
	$tpreamon=0;
	$tasiamon=0;
	$tdanamon=0;
	$textamon=0;
	$tdesamon=0;
	$trobamon=0;
	$treeamon=0;
	
	$dissmon=0;
	$presmon=0;
	$asismon=0;
	$dansmon=0;
	$extsmon=0;
	$dessmon=0;
	$robsmon=0;
	$reesmon=0;
	$tdissmon=0;
	$tpresmon=0;
	$tasismon=0;
	$tdansmon=0;
	$textsmon=0;
	$tdessmon=0;
	$trobsmon=0;
	$treesmon=0;	
	
	$disavid=0;
	$preavid=0;
	$asiavid=0;
	$danavid=0;
	$extavid=0;
	$desavid=0;
	$robavid=0;
	$reeavid=0;
	$tdisavid=0;
	$tpreavid=0;
	$tasiavid=0;
	$tdanavid=0;
	$textavid=0;
	$tdesavid=0;
	$trobavid=0;
	$treeavid=0;
	
	$dissvid=0;
	$presvid=0;
	$asisvid=0;
	$dansvid=0;
	$extsvid=0;
	$dessvid=0;
	$robsvid=0;
	$reesvid=0;
	$tdissvid=0;
	$tpresvid=0;
	$tasisvid=0;
	$tdansvid=0;
	$textsvid=0;
	$tdessvid=0;
	$trobsvid=0;
	$treesvid=0;	
	
	
	
	$gerencia=substr($data[0]['gerencia'],16,200);
	$equipo=$data[0]['equipo'];
	$ubicacion=$data[0]['id_ubicacion'];	
	for ($i= 0; $i < count($data); $i++){		
		if ($gerencia==substr($data[$i]['gerencia'],16,200)){								
			if ($data[$i]['id_tipo_maestro']=='108' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='86' ){
				$disaimp=$data[$i]['count'];
				$tdisaimp+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='108' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='87' ){
				$preaimp=$data[$i]['count'];
				$tpreaimp+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='108' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='88' ){
				$asiaimp=$data[$i]['count'];
				$tasiaimp+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='108' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='89' ){
				$danaimp=$data[$i]['count'];
				$tdanaimp+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='108' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='90' ){
				$extaimp=$data[$i]['count'];
				$textaimp+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='108' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='91' ){
				$desaimp=$data[$i]['count'];
				$tdesaimp+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='108' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='618' ){
				$robaimp=$data[$i]['count'];
				$trobaimp+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='108' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='619' ){
				$reeaimp=$data[$i]['count'];
				$treeaimp+=$data[$i]['count'];		
			}
			
			if ($data[$i]['id_tipo_maestro']=='108' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='86' ){
				$dissimp=$data[$i]['count'];
				$tdissimp+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='108' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='87' ){
				$presimp=$data[$i]['count'];
				$tpresimp+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='108' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='88' ){
				$asisimp=$data[$i]['count'];
				$tasisimp+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='108' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='89' ){
				$dansimp=$data[$i]['count'];
				$tdansimp+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='108' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='90' ){
				$extsimp=$data[$i]['count'];
				$textsimp+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='108' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='91' ){
				$dessimp=$data[$i]['count'];
				$tdessimp+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='108' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='618' ){
				$robsimp=$data[$i]['count'];
				$trobsimp+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='108' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='619' ){
				$reesimp=$data[$i]['count'];
				$treesimp+=$data[$i]['count'];		
			}
			
			
			if ($data[$i]['id_tipo_maestro']=='106' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='86' ){
				$disalap=$data[$i]['count'];
				$tdisalap+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='106' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='87' ){
				$prealap=$data[$i]['count'];
				$tprealap+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='106' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='88' ){
				$asialap=$data[$i]['count'];
				$tasialap+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='106' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='89' ){
				$danalap=$data[$i]['count'];
				$tdanalap+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='106' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='90' ){
				$extalap=$data[$i]['count'];
				$textalap+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='106' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='91' ){
				$desalap=$data[$i]['count'];
				$tdesalap+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='106' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='618' ){
				$robalap=$data[$i]['count'];
				$trobalap+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='106' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='619' ){
				$reealap=$data[$i]['count'];
				$treealap+=$data[$i]['count'];		
			}
			
			if ($data[$i]['id_tipo_maestro']=='106' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='86' ){
				$disslap=$data[$i]['count'];
				$tdisslap+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='106' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='87' ){
				$preslap=$data[$i]['count'];
				$tpreslap+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='106' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='88' ){
				$asislap=$data[$i]['count'];
				$tasislap+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='106' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='89' ){
				$danslap=$data[$i]['count'];
				$tdanslap+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='106' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='90' ){
				$extslap=$data[$i]['count'];
				$textslap+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='106' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='91' ){
				$desslap=$data[$i]['count'];
				$tdesslap+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='106' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='618' ){
				$robslap=$data[$i]['count'];
				$trobslap+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='106' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='619' ){
				$reeslap=$data[$i]['count'];
				$treeslap+=$data[$i]['count'];		
			}
			
			
			if ($data[$i]['id_tipo_maestro']=='107' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='86' ){
				$disades=$data[$i]['count'];
				$tdisades+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='107' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='87' ){
				$preades=$data[$i]['count'];
				$tpreades+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='107' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='88' ){
				$asiades=$data[$i]['count'];
				$tasiades+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='107' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='89' ){
				$danades=$data[$i]['count'];
				$tdanades+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='107' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='90' ){
				$extades=$data[$i]['count'];
				$textades+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='107' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='91' ){
				$desades=$data[$i]['count'];
				$tdesades+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='107' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='618' ){
				$robades=$data[$i]['count'];
				$trobades+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='107' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='619' ){
				$reeades=$data[$i]['count'];
				$treeades+=$data[$i]['count'];		
			}
			
			if ($data[$i]['id_tipo_maestro']=='107' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='86' ){
				$dissdes=$data[$i]['count'];
				$tdissdes+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='107' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='87' ){
				$presdes=$data[$i]['count'];
				$tpresdes+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='107' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='88' ){
				$asisdes=$data[$i]['count'];
				$tasisdes+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='107' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='89' ){
				$dansdes=$data[$i]['count'];
				$tdansdes+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='107' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='90' ){
				$extsdes=$data[$i]['count'];
				$textsdes+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='107' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='91' ){
				$dessdes=$data[$i]['count'];
				$tdessdes+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='107' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='618' ){
				$robsdes=$data[$i]['count'];
				$trobsdes+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='107' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='619' ){
				$reesdes=$data[$i]['count'];
				$treesdes+=$data[$i]['count'];		
			}
			
			
			
			if ($data[$i]['id_tipo_maestro']=='117' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='86' ){
				$disamon=$data[$i]['count'];
				$tdisamon+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='117' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='87' ){
				$preamon=$data[$i]['count'];
				$tpreamon+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='117' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='88' ){
				$asiamon=$data[$i]['count'];
				$tasiamon+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='117' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='89' ){
				$danamon=$data[$i]['count'];
				$tdanamon+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='117' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='90' ){
				$extamon=$data[$i]['count'];
				$textamon+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='117' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='91' ){
				$desamon=$data[$i]['count'];
				$tdesamon+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='117' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='618' ){
				$robamon=$data[$i]['count'];
				$trobamon+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='117' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='619' ){
				$reeamon=$data[$i]['count'];
				$treeamon+=$data[$i]['count'];		
			}
			
			if ($data[$i]['id_tipo_maestro']=='117' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='86' ){
				$dissmon=$data[$i]['count'];
				$tdissmon+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='117' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='87' ){
				$presmon=$data[$i]['count'];
				$tpresmon+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='117' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='88' ){
				$asismon=$data[$i]['count'];
				$tasismon+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='117' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='89' ){
				$dansmon=$data[$i]['count'];
				$tdansmon+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='117' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='90' ){
				$extsmon=$data[$i]['count'];
				$textsmon+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='117' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='91' ){
				$dessmon=$data[$i]['count'];
				$tdessmon+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='117' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='618' ){
				$robsmon=$data[$i]['count'];
				$trobsmon+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='117' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='619' ){
				$reesmon=$data[$i]['count'];
				$treesmon+=$data[$i]['count'];		
			}
			
			
			
			
			if ($data[$i]['id_tipo_maestro']=='118' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='86' ){
				$disavid=$data[$i]['count'];
				$tdisavid+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='118' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='87' ){
				$preavid=$data[$i]['count'];
				$tpreavid+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='118' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='88' ){
				$asiavid=$data[$i]['count'];
				$tasiavid+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='118' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='89' ){
				$danavid=$data[$i]['count'];
				$tdanavid+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='118' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='90' ){
				$extavid=$data[$i]['count'];
				$textavid+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='118' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='91' ){
				$desavid=$data[$i]['count'];
				$tdesavid+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='118' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='618' ){
				$robavid=$data[$i]['count'];
				$trobavid+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='118' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='619' ){
				$reeavid=$data[$i]['count'];
				$treeavid+=$data[$i]['count'];		
			}
			
			if ($data[$i]['id_tipo_maestro']=='118' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='86' ){
				$dissvid=$data[$i]['count'];
				$tdissvid+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='118' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='87' ){
				$presvid=$data[$i]['count'];
				$tpresvid+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='118' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='88' ){
				$asisvid=$data[$i]['count'];
				$tasisvid+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='118' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='89' ){
				$dansvid=$data[$i]['count'];
				$tdansvid+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='118' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='90' ){
				$extsvid=$data[$i]['count'];
				$textsvid+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='118' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='91' ){
				$dessvid=$data[$i]['count'];
				$tdessvid+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='118' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='618' ){
				$robsvid=$data[$i]['count'];
				$trobsvid+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='118' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='619' ){
				$reesvid=$data[$i]['count'];
				$treesvid+=$data[$i]['count'];		
			}
			
			
			
			
			
			
			
					
		}else{					
			$pdf->Row(array(utf8_decode($gerencia),$asiaimp,$preaimp,$danaimp,$extaimp,$robaimp,$asisimp,$presimp,$dansimp,$extsimp,$robsimp,$asialap,$prealap,$danalap,$extalap,$robalap,$asislap,$preslap,$danslap,$extslap,$robslap,$asiades,$preades,$danades,$extades,$robades,$asisdes,$presdes,$dansdes,$extsdes,$robsdes,$asiamon,$preamon,$danamon,$extamon,$robamon,$asismon,$presmon,$dansmon,$extsmon,$robsmon,$asiavid,$preavid,$danavid,$extavid,$robavid,$asisvid,$presvid,$dansvid,$extsvid,$robsvid));	
			
			$disalap=0;
			$prealap=0;
			$asialap=0;
			$danalap=0;
			$extalap=0;
			$desalap=0;
			$robalap=0;
			$reealap=0;
			
			
			$disslap=0;
			$preslap=0;
			$asislap=0;
			$danslap=0;
			$extslap=0;
			$desslap=0;
			$robslap=0;
			$reeslap=0;
				
			
			$disalap=0;
			$prealap=0;
			$asialap=0;
			$danalap=0;
			$extalap=0;
			$desalap=0;
			$robalap=0;
			$reealap=0;
			
			$disslap=0;
			$preslap=0;
			$asislap=0;
			$danslap=0;
			$extslap=0;
			$desslap=0;
			$robslap=0;
			$reeslap=0;
			
			
			$disades=0;
			$preades=0;
			$asiades=0;
			$danades=0;
			$extades=0;
			$desades=0;
			$robades=0;
			$reeades=0;
			
			$dissdes=0;
			$presdes=0;
			$asisdes=0;
			$dansdes=0;
			$extsdes=0;
			$dessdes=0;
			$robsdes=0;
			$reesdes=0;
			
			$disamon=0;
			$preamon=0;
			$asiamon=0;
			$danamon=0;
			$extamon=0;
			$desamon=0;
			$robamon=0;
			$reeamon=0;
			
			$dissmon=0;
			$presmon=0;
			$asismon=0;
			$dansmon=0;
			$extsmon=0;
			$dessmon=0;
			$robsmon=0;
			$reesmon=0;
			
			
			$disavid=0;
			$preavid=0;
			$asiavid=0;
			$danavid=0;
			$extavid=0;
			$desavid=0;
			$robavid=0;
			$reeavid=0;
			
			
			$dissvid=0;
			$presvid=0;
			$asisvid=0;
			$dansvid=0;
			$extsvid=0;
			$dessvid=0;
			$robsvid=0;
			$reesvid=0;
	
			
			
			$gerencia=substr($data[$i]['gerencia'],16,200);
			$equipo=$data[$i]['equipo'];
			$ubicacion=$data[$i]['id_ubicacion'];	
		if ($data[$i]['id_tipo_maestro']=='108' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='86' ){
				$disaimp=$data[$i]['count'];
				$tdisaimp+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='108' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='87' ){
				$preaimp=$data[$i]['count'];
				$tpreaimp+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='108' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='88' ){
				$asiaimp=$data[$i]['count'];
				$tasiaimp+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='108' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='89' ){
				$danaimp=$data[$i]['count'];
				$tdanaimp+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='108' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='90' ){
				$extaimp=$data[$i]['count'];
				$textaimp+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='108' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='91' ){
				$desaimp=$data[$i]['count'];
				$tdesaimp+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='108' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='618' ){
				$robaimp=$data[$i]['count'];
				$trobaimp+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='108' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='619' ){
				$reeaimp=$data[$i]['count'];
				$treeaimp+=$data[$i]['count'];		
			}
			
			if ($data[$i]['id_tipo_maestro']=='108' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='86' ){
				$dissimp=$data[$i]['count'];
				$tdissimp+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='108' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='87' ){
				$presimp=$data[$i]['count'];
				$tpresimp+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='108' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='88' ){
				$asisimp=$data[$i]['count'];
				$tasisimp+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='108' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='89' ){
				$dansimp=$data[$i]['count'];
				$tdansimp+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='108' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='90' ){
				$extsimp=$data[$i]['count'];
				$textsimp+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='108' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='91' ){
				$dessimp=$data[$i]['count'];
				$tdessimp+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='108' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='618' ){
				$robsimp=$data[$i]['count'];
				$trobsimp+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='108' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='619' ){
				$reesimp=$data[$i]['count'];
				$treesimp+=$data[$i]['count'];		
			}
			
			
			if ($data[$i]['id_tipo_maestro']=='106' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='86' ){
				$disalap=$data[$i]['count'];
				$tdisalap+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='106' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='87' ){
				$prealap=$data[$i]['count'];
				$tprealap+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='106' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='88' ){
				$asialap=$data[$i]['count'];
				$tasialap+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='106' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='89' ){
				$danalap=$data[$i]['count'];
				$tdanalap+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='106' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='90' ){
				$extalap=$data[$i]['count'];
				$textalap+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='106' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='91' ){
				$desalap=$data[$i]['count'];
				$tdesalap+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='106' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='618' ){
				$robalap=$data[$i]['count'];
				$trobalap+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='106' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='619' ){
				$reealap=$data[$i]['count'];
				$treealap+=$data[$i]['count'];		
			}
			
			if ($data[$i]['id_tipo_maestro']=='106' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='86' ){
				$disslap=$data[$i]['count'];
				$tdisslap+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='106' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='87' ){
				$preslap=$data[$i]['count'];
				$tpreslap+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='106' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='88' ){
				$asislap=$data[$i]['count'];
				$tasislap+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='106' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='89' ){
				$danslap=$data[$i]['count'];
				$tdanslap+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='106' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='90' ){
				$extslap=$data[$i]['count'];
				$textslap+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='106' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='91' ){
				$desslap=$data[$i]['count'];
				$tdesslap+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='106' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='618' ){
				$robslap=$data[$i]['count'];
				$trobslap+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='106' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='619' ){
				$reeslap=$data[$i]['count'];
				$treeslap+=$data[$i]['count'];		
			}
			
			
			if ($data[$i]['id_tipo_maestro']=='107' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='86' ){
				$disades=$data[$i]['count'];
				$tdisades+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='107' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='87' ){
				$preades=$data[$i]['count'];
				$tpreades+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='107' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='88' ){
				$asiades=$data[$i]['count'];
				$tasiades+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='107' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='89' ){
				$danades=$data[$i]['count'];
				$tdanades+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='107' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='90' ){
				$extades=$data[$i]['count'];
				$textades+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='107' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='91' ){
				$desades=$data[$i]['count'];
				$tdesades+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='107' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='618' ){
				$robades=$data[$i]['count'];
				$trobades+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='107' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='619' ){
				$reeades=$data[$i]['count'];
				$treeades+=$data[$i]['count'];		
			}
			
			if ($data[$i]['id_tipo_maestro']=='107' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='86' ){
				$dissdes=$data[$i]['count'];
				$tdissdes+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='107' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='87' ){
				$presdes=$data[$i]['count'];
				$tpresdes+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='107' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='88' ){
				$asisdes=$data[$i]['count'];
				$tasisdes+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='107' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='89' ){
				$dansdes=$data[$i]['count'];
				$tdansdes+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='107' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='90' ){
				$extsdes=$data[$i]['count'];
				$textsdes+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='107' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='91' ){
				$dessdes=$data[$i]['count'];
				$tdessdes+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='107' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='618' ){
				$robsdes=$data[$i]['count'];
				$trobsdes+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='107' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='619' ){
				$reesdes=$data[$i]['count'];
				$treesdes+=$data[$i]['count'];		
			}
			
			
			
			if ($data[$i]['id_tipo_maestro']=='117' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='86' ){
				$disamon=$data[$i]['count'];
				$tdisamon+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='117' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='87' ){
				$preamon=$data[$i]['count'];
				$tpreamon+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='117' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='88' ){
				$asiamon=$data[$i]['count'];
				$tasiamon+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='117' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='89' ){
				$danamon=$data[$i]['count'];
				$tdanamon+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='117' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='90' ){
				$extamon=$data[$i]['count'];
				$textamon+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='117' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='91' ){
				$desamon=$data[$i]['count'];
				$tdesamon+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='117' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='618' ){
				$robamon=$data[$i]['count'];
				$trobamon+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='117' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='619' ){
				$reeamon=$data[$i]['count'];
				$treeamon+=$data[$i]['count'];		
			}
			
			if ($data[$i]['id_tipo_maestro']=='117' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='86' ){
				$dissmon=$data[$i]['count'];
				$tdissmon+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='117' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='87' ){
				$presmon=$data[$i]['count'];
				$tpresmon+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='117' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='88' ){
				$asismon=$data[$i]['count'];
				$tasismon+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='117' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='89' ){
				$dansmon=$data[$i]['count'];
				$tdansmon+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='117' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='90' ){
				$extsmon=$data[$i]['count'];
				$textsmon+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='117' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='91' ){
				$dessmon=$data[$i]['count'];
				$tdessmon+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='117' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='618' ){
				$robsmon=$data[$i]['count'];
				$trobsmon+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='117' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='619' ){
				$reesmon=$data[$i]['count'];
				$treesmon+=$data[$i]['count'];		
			}
			
			
			
			
			if ($data[$i]['id_tipo_maestro']=='118' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='86' ){
				$disavid=$data[$i]['count'];
				$tdisavid+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='118' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='87' ){
				$preavid=$data[$i]['count'];
				$tpreavid+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='118' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='88' ){
				$asiavid=$data[$i]['count'];
				$tasiavid+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='118' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='89' ){
				$danavid=$data[$i]['count'];
				$tdanavid+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='118' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='90' ){
				$extavid=$data[$i]['count'];
				$textavid+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='118' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='91' ){
				$desavid=$data[$i]['count'];
				$tdesavid+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='118' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='618' ){
				$robavid=$data[$i]['count'];
				$trobavid+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='118' && $data[$i]['id_ubicacion']=='680' && $data[$i]['id_estatus_maestro']=='619' ){
				$reeavid=$data[$i]['count'];
				$treeavid+=$data[$i]['count'];		
			}
			
			if ($data[$i]['id_tipo_maestro']=='118' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='86' ){
				$dissvid=$data[$i]['count'];
				$tdissvid+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='118' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='87' ){
				$presvid=$data[$i]['count'];
				$tpresvid+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='118' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='88' ){
				$asisvid=$data[$i]['count'];
				$tasisvid+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='118' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='89' ){
				$dansvid=$data[$i]['count'];
				$tdansvid+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='118' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='90' ){
				$extsvid=$data[$i]['count'];
				$textsvid+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='118' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='91' ){
				$dessvid=$data[$i]['count'];
				$tdessvid+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='118' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='618' ){
				$robsvid=$data[$i]['count'];
				$trobsvid+=$data[$i]['count'];		
			}
			if ($data[$i]['id_tipo_maestro']=='118' && $data[$i]['id_ubicacion']=='681' && $data[$i]['id_estatus_maestro']=='619' ){
				$reesvid=$data[$i]['count'];
				$treesvid+=$data[$i]['count'];		
			}
		}
			
		
	}
	$pdf->Row(array(utf8_decode($gerencia),$asiaimp,$preaimp,$danaimp,$extaimp,$robaimp,$asisimp,$presimp,$dansimp,$extsimp,$robsimp,$asialap,$prealap,$danalap,$extalap,$robalap,$asislap,$preslap,$danslap,$extslap,$robslap,$asiades,$preades,$danades,$extades,$robades,$asisdes,$presdes,$dansdes,$extsdes,$robsdes,$asiamon,$preamon,$danamon,$extamon,$robamon,$asismon,$presmon,$dansmon,$extsmon,$robsmon,$asiavid,$preavid,$danavid,$extavid,$robavid,$asisvid,$presvid,$dansvid,$extsvid,$robsvid));
	$pdf->Row(array('TOTAL',$tasiaimp,$tpreaimp,$tdanaimp,$textaimp,$trobaimp,$tasisimp,$tpresimp,$tdansimp,$textsimp,$trobsimp,$tasialap,$tprealap,$tdanalap,$textalap,$trobalap,$tasislap,$tpreslap,$tdanslap,$textslap,$trobslap,$tasiades,$tpreades,$tdanades,$textades,$trobades,$tasisdes,$tpresdes,$tdansdes,$textsdes,$trobsdes,$tasiamon,$tpreamon,$tdanamon,$textamon,$trobamon,$tasismon,$tpresmon,$tdansmon,$textsmon,$trobsmon,$tasiavid,$tpreavid,$tdanavid,$textavid,$trobavid,$tasisvid,$tpresvid,$tdansvid,$textsvid,$trobsvid));
	$pdf->SetFont('Arial','B',6);
	$pdf->SetWidths(array(30,60,60,60,60,60));
	$pdf->SetAligns(array('R','C','C','C','C','C'));
	$pdf->Row(array('TOTAL GENERAL',$tasiaimp+$tpreaimp+$tdanaimp+$textaimp+$trobaimp+$tasisimp+$tpresimp+$tdansimp+$textsimp+$trobsimp,$tasialap+$tprealap+$tdanalap+$textalap+$trobalap+$tasislap+$tpreslap+$tdanslap+$textslap+$trobslap,$tasiades+$tpreades+$tdanades+$textades+$trobades+$tasisdes+$tpresdes+$tdansdes+$textsdes+$trobsdes,$tasiamon+$tpreamon+$tdanamon+$textamon+$trobamon+$tasismon+$tpresmon+$tdansmon+$textsmon+$trobsmon,$tasiavid+$tpreavid+$tdanavid+$textavid+$trobavid+$tasisvid+$tpresvid+$tdansvid+$textsvid+$trobsvid));
	$pdf->Cell(0,10,'LEYENDA: AS=ASIGNADO; PR=PRESTAMO; DA=DA�ADO; EX=EXTRAVIADO; RO=ROBADO  ',0,0,'L');
	
	
	
	
}



$pdf->Output();		

	
		
?>

