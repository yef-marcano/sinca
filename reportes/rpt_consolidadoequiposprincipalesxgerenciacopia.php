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
		$this->SetY(25);		
		$this->Cell(0,10,utf8_decode('CONSOLIDADO DE EQUIPOS TELEMÁTICOS OPERATIVOS  SEDE ALTAMIRA Y SUDAMERIS POR GERENCIAS AL ').date('d/m/Y'),0,0,'C');
		$this->Ln(15);
		$this->SetFont('Arial','B',8);
		$this->SetFillColor(255,0,0);		
		$this->SetTextColor(255,255,255);
		$this->SetDrawColor(0);
		$this->SetLineWidth(.3);    
		$this->Cell(60,20,'OFICINAS Y/O GERENCIAS', 1,0,'C',1);
		$this->Cell(34,10,'IMPRESORA','1',0,'C',1);
		$this->Cell(34,10,'LAPTOP','1',0,'C',1);
		$this->Cell(34,10,'DESKTOP','1',0,'C',1);
		$this->Cell(34,10,'MONITOR','1',1,'C',1);
		$this->SetX(70);
		$this->SetFont('Arial','B',6);
		$this->Cell(17,10,'ALTAMIRA','1',0,'C',1);
		$this->Cell(17,10,'SUDAMERIS','1',0,'C',1);
		$this->Cell(17,10,'ALTAMIRA','1',0,'C',1);
		$this->Cell(17,10,'SUDAMERIS','1',0,'C',1);
		$this->Cell(17,10,'ALTAMIRA','1',0,'C',1);
		$this->Cell(17,10,'SUDAMERIS','1',0,'C',1);
		$this->Cell(17,10,'ALTAMIRA','1',0,'C',1);
		$this->Cell(17,10,'SUDAMERIS','1',0,'C',1);
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
$pdf=new PDF("p","mm","Letter");
$pdf->Open();
$pdf->AliasNbPages();
$pdf->SetMargins(10,10,10);
$pdf->SetFont('Arial','',8);
$pdf->AddPage();
$sql="select count(b.*),g.stritema as gerencia, t.stritema as equipo,b.id_tipo_maestro, b.id_ubicacion
from tblbienes b, tblmaestros t, tblmaestros g
where b.id_tipo_maestro=t.id_maestro and b.id_gerencia_maestro=g.id_maestro
and b.bolborrado=0 and t.bolborrado=0 and g.bolborrado=0
and b.id_tipo_maestro in (106,107,117,108) and g.stritemb='GERENCIA' 
and b.id_estatus_maestro in (86,88) and b.id_ubicacion in (3,4)
group by g.stritema, t.stritema, b.id_tipo_maestro, b.id_ubicacion
order by g.stritema,t.stritema,b.id_ubicacion";
$conn= new Conexion();
$conn->abrirConexion();
$conn->sql=$sql;
$data=$conn->ejecutarSentencia(2);
$pdf->SetFillColor(255,255,255);		
$pdf->SetTextColor(0);
$pdf->SetDrawColor(0);
$pdf->SetLineWidth(.3);   
    
$pdf->SetWidths(array(60,17,17,17,17,17,17,17,17));
$pdf->SetAligns(array('L','R','R','R','R','R','R','R','R'));
if ($data){	
	$aimp=0;
	$simp=0;
	$alap=0;
	$slap=0;
	$ades=0;
	$sdes=0;
	$amon=0;
	$smon=0;
	$taimp=0;
	$tsimp=0;
	$talap=0;
	$tslap=0;
	$tades=0;
	$tsdes=0;
	$tamon=0;
	$tsmon=0;		
	
	$gerencia=$data[0]['gerencia'];
	$equipo=$data[0]['equipo'];
	$ubicacion=$data[0]['id_ubicacion'];	
	for ($i= 0; $i < count($data); $i++){		
		if ($gerencia==$data[$i]['gerencia']){								
			if ($data[$i]['id_tipo_maestro']=='108' && $data[$i]['id_ubicacion']=='3'){
				$aimp=$data[$i]['count'];
				$taimp+=$data[$i]['count'];				
			}
			if ($data[$i]['id_tipo_maestro']=='108' && $data[$i]['id_ubicacion']=='4'){
				$simp=$data[$i]['count'];
				$tsimp+=$data[$i]['count'];				
			}
			if ($data[$i]['id_tipo_maestro']=='106' && $data[$i]['id_ubicacion']=='3'){
				$alap=$data[$i]['count'];
				$talap+=$data[$i]['count'];				
			}
			if ($data[$i]['id_tipo_maestro']=='106' && $data[$i]['id_ubicacion']=='4'){
				$slap=$data[$i]['count'];
				$tslap+=$data[$i]['count'];				
			}
			if ($data[$i]['id_tipo_maestro']=='107' && $data[$i]['id_ubicacion']=='3'){
				$ades=$data[$i]['count'];
				$tades+=$data[$i]['count'];				
			}
			if ($data[$i]['id_tipo_maestro']=='107' && $data[$i]['id_ubicacion']=='4'){
				$sdes=$data[$i]['count'];
				$tsdes+=$data[$i]['count'];				
			}
			if ($data[$i]['id_tipo_maestro']=='117' && $data[$i]['id_ubicacion']=='3'){
				$amon=$data[$i]['count'];
				$tamon+=$data[$i]['count'];				
			}
			if ($data[$i]['id_tipo_maestro']=='117' && $data[$i]['id_ubicacion']=='4'){
				$smon=$data[$i]['count'];
				$tsmon+=$data[$i]['count'];				
			}				
		}else{					
			$pdf->Row(array(utf8_decode($gerencia),$aimp,$simp,$alap,$slap,$ades,$sdes,$amon,$smon));	
			$aimp=0;
			$simp=0;
			$alap=0;
			$slap=0;
			$ades=0;
			$sdes=0;
			$amon=0;
			$smon=0;	
			$gerencia=$data[$i]['gerencia'];
			$equipo=$data[$i]['equipo'];
			$ubicacion=$data[$i]['id_ubicacion'];	
			if ($data[$i]['id_tipo_maestro']=='108' && $data[$i]['id_ubicacion']=='3'){
				$aimp=$data[$i]['count'];
				$taimp+=$data[$i]['count'];				
			}
			if ($data[$i]['id_tipo_maestro']=='108' && $data[$i]['id_ubicacion']=='4'){
				$simp=$data[$i]['count'];
				$tsimp+=$data[$i]['count'];				
			}
			if ($data[$i]['id_tipo_maestro']=='106' && $data[$i]['id_ubicacion']=='3'){
				$alap=$data[$i]['count'];
				$talap+=$data[$i]['count'];				
			}
			if ($data[$i]['id_tipo_maestro']=='106' && $data[$i]['id_ubicacion']=='4'){
				$slap=$data[$i]['count'];
				$tslap+=$data[$i]['count'];				
			}
			if ($data[$i]['id_tipo_maestro']=='107' && $data[$i]['id_ubicacion']=='3'){
				$ades=$data[$i]['count'];
				$tades+=$data[$i]['count'];				
			}
			if ($data[$i]['id_tipo_maestro']=='107' && $data[$i]['id_ubicacion']=='4'){
				$sdes=$data[$i]['count'];
				$tsdes+=$data[$i]['count'];				
			}
			if ($data[$i]['id_tipo_maestro']=='117' && $data[$i]['id_ubicacion']=='3'){
				$amon=$data[$i]['count'];
				$tamon+=$data[$i]['count'];				
			}
			if ($data[$i]['id_tipo_maestro']=='117' && $data[$i]['id_ubicacion']=='4'){
				$smon=$data[$i]['count'];
				$tsmon+=$data[$i]['count'];				
			}				
		}
			
		
	}
	$pdf->Row(array(utf8_decode($gerencia),$aimp,$simp,$alap,$slap,$ades,$sdes,$amon,$smon));
	$pdf->Row(array('TOTAL',$taimp,$tsimp,$talap,$tslap,$tades,$tsdes,$tamon,$tsmon));
	$pdf->SetFont('Arial','B',8);
	$pdf->SetWidths(array(60,34,34,34,34));
	$pdf->SetAligns(array('R','R','R','R','R'));
	$pdf->Row(array('TOTAL GENERAL',$taimp+$tsimp,$talap+$tslap,$tades+$tsdes,$tamon+$tsmon));
	$pdf->AddPage();
	// PORCENTAJE DE UBICACION DE LOS EQUIPOS
	$pdf->SetFont('Arial','',8);
	$pdf->SetWidths(array(60,17,17,17,17,17,17,17,17));
	$pdf->SetAligns(array('L','R','R','R','R','R','R','R','R'));
	$sql="select count(b.*), b.id_tipo_maestro
		from tblbienes b, tblmaestros t, tblmaestros g
		where b.id_tipo_maestro=t.id_maestro and b.id_gerencia_maestro=g.id_maestro
		and b.bolborrado=0 and t.bolborrado=0 and g.bolborrado=0
		and b.id_tipo_maestro in (106,107,117,108)
		group by b.id_tipo_maestro";
	$conn= new Conexion();
	$conn->abrirConexion();
	$conn->sql=$sql;
	$data1=$conn->ejecutarSentencia(2);
	for ($i= 0; $i < count($data1); $i++){	
		if ($data1[$i]['id_tipo_maestro']=='108'){
			$pimp=$data1[$i]['count'];							
		}
		
		if ($data1[$i]['id_tipo_maestro']=='106'){
			$plap=$data1[$i]['count'];
						
		}
		
		if ($data1[$i]['id_tipo_maestro']=='107'){
			$pdes=$data1[$i]['count'];
					
		}
		
		if ($data1[$i]['id_tipo_maestro']=='117'){
			$pmon=$data1[$i]['count'];								
		}				
	}
	
	
	
	/*
	$pimp=$taimp+$tsimp;
	$plap=$talap+$tslap;
	$pdes=$tades+$tsdes;
	$pmon=$tamon+$tsmon;
	*/
	$aimp=0;
	$simp=0;
	$alap=0;
	$slap=0;
	$ades=0;
	$sdes=0;
	$amon=0;
	$smon=0;
	$taimp=0;
	$tsimp=0;
	$talap=0;
	$tslap=0;
	$tades=0;
	$tsdes=0;
	$tamon=0;
	$tsmon=0;		
	
	$gerencia=$data[0]['gerencia'];
	$equipo=$data[0]['equipo'];
	$ubicacion=$data[0]['id_ubicacion'];	
	for ($i= 0; $i < count($data); $i++){		
		if ($gerencia==$data[$i]['gerencia']){								
			if ($data[$i]['id_tipo_maestro']=='108' && $data[$i]['id_ubicacion']=='3'){
				$aimp=($data[$i]['count']*100)/$pimp;
				$taimp+=$aimp;				
			}
			if ($data[$i]['id_tipo_maestro']=='108' && $data[$i]['id_ubicacion']=='4'){
				$simp=($data[$i]['count']*100)/$pimp;
				$tsimp+=$simp;				
			}
			if ($data[$i]['id_tipo_maestro']=='106' && $data[$i]['id_ubicacion']=='3'){
				$alap=($data[$i]['count']*100)/$plap;
				$talap+=$alap;				
			}
			if ($data[$i]['id_tipo_maestro']=='106' && $data[$i]['id_ubicacion']=='4'){
				$slap=($data[$i]['count']*100)/$plap;
				$tslap+=$slap;				
			}
			if ($data[$i]['id_tipo_maestro']=='107' && $data[$i]['id_ubicacion']=='3'){
				$ades=($data[$i]['count']*100)/$pdes;
				$tades+=$ades;				
			}
			if ($data[$i]['id_tipo_maestro']=='107' && $data[$i]['id_ubicacion']=='4'){
				$sdes=($data[$i]['count']*100)/$pdes;
				$tsdes+=$sdes;				
			}
			if ($data[$i]['id_tipo_maestro']=='117' && $data[$i]['id_ubicacion']=='3'){
				$amon=($data[$i]['count']*100)/$pmon;
				$tamon+=$amon;				
			}
			if ($data[$i]['id_tipo_maestro']=='117' && $data[$i]['id_ubicacion']=='4'){
				$smon=($data[$i]['count']*100)/$pmon;
				$tsmon+=$smon;				
			}				
		}else{					
			$pdf->Row(array(utf8_decode($gerencia),number_format($aimp,2).' %',number_format($simp,2).' %',number_format($alap,2).' %',number_format($slap,2).' %',number_format($ades,2).' %',number_format($sdes,2).' %',number_format($amon,2).' %',number_format($smon,2).' %'));	
			$aimp=0;
			$simp=0;
			$alap=0;
			$slap=0;
			$ades=0;
			$sdes=0;
			$amon=0;
			$smon=0;	
			$gerencia=$data[$i]['gerencia'];
			$equipo=$data[$i]['equipo'];
			$ubicacion=$data[$i]['id_ubicacion'];	
			if ($data[$i]['id_tipo_maestro']=='108' && $data[$i]['id_ubicacion']=='3'){
				$aimp=($data[$i]['count']*100)/$pimp;
				$taimp+=$aimp;				
			}
			if ($data[$i]['id_tipo_maestro']=='108' && $data[$i]['id_ubicacion']=='4'){
				$simp=($data[$i]['count']*100)/$pimp;
				$tsimp+=$simp;				
			}
			if ($data[$i]['id_tipo_maestro']=='106' && $data[$i]['id_ubicacion']=='3'){
				$alap=($data[$i]['count']*100)/$plap;
				$talap+=$alap;				
			}
			if ($data[$i]['id_tipo_maestro']=='106' && $data[$i]['id_ubicacion']=='4'){
				$slap=($data[$i]['count']*100)/$plap;
				$tslap+=$slap;				
			}
			if ($data[$i]['id_tipo_maestro']=='107' && $data[$i]['id_ubicacion']=='3'){
				$ades=($data[$i]['count']*100)/$pdes;
				$tades+=$ades;				
			}
			if ($data[$i]['id_tipo_maestro']=='107' && $data[$i]['id_ubicacion']=='4'){
				$sdes=($data[$i]['count']*100)/$pdes;
				$tsdes+=$sdes;				
			}
			if ($data[$i]['id_tipo_maestro']=='117' && $data[$i]['id_ubicacion']=='3'){
				$amon=($data[$i]['count']*100)/$pmon;
				$tamon+=$amon;				
			}
			if ($data[$i]['id_tipo_maestro']=='117' && $data[$i]['id_ubicacion']=='4'){
				$smon=($data[$i]['count']*100)/$pmon;
				$tsmon+=$smon;				
			}				
		}
			
		
	}
	$pdf->Row(array(utf8_decode($gerencia),number_format($aimp,2).' %',number_format($simp,2).' %',number_format($alap,2).' %',number_format($slap,2).' %',number_format($ades,2).' %',number_format($sdes,2).' %',number_format($amon,2).' %',number_format($smon,2).' %'));
	$pdf->Row(array('TOTAL',number_format($taimp,2).' %',number_format($tsimp,2).' %',number_format($talap,2).' %',number_format($tslap,2).' %',number_format($tades,2).' %',number_format($tsdes,2).' %',number_format($tamon,2).' %',number_format($tsmon,2).' %'));
	$pdf->SetFont('Arial','B',8);
	$pdf->SetWidths(array(60,34,34,34,34));
	$pdf->SetAligns(array('R','R','R','R','R'));
	$pdf->Row(array('TOTAL GENERAL',number_format($taimp+$tsimp,2).' %',number_format($talap+$tslap,2).' %',number_format($tades+$tsdes,2).' %',number_format($tamon+$tsmon,2).' %'));
	
	
	
	
	
}



$pdf->Output();		

	
		
?>

