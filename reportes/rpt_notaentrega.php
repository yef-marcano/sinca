<?php
session_start();
require('../comunes/php/fpdf.php');
require_once "../modelo/clNotaentregaModelo.php";
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
		$contacto=  new clNotaentregaModelo();		
		$data= $contacto->selectNotaentregaById($_REQUEST['id_notaentrega']);
		if($data){
			$tipo=strtoupper($data[0]['tipo']);
		}
		//Logo
		$this->Image('../comunes/images/cab_reportes.jpg',20,3,30,15);
		$this->SetX(12);
		$this->SetY(18);
		$this->SetFont('Arial','B',7);
		$this->Cell(0,10,'OFICINA DE SISTEMAS Y TECNOLOGÍA DE LA INFORMACIÓN',0,0,'L');
		$this->Ln(10);
		//Select Arial bold 15
		$this->SetFont('Arial','B',12);
		//Move to the right		
		//Framed title			
		$this->Cell(0,10,'NOTA DE ENTREGA EQUIPOS DE COMPUTACIÓN ('.$tipo.')',0,0,'C');
		//Line break
		$this->Ln(10);
		$this->SetFont('Arial','B',8);
		$this->Cell(0,10,utf8_decode('DATOS DE LOS EQUIPOS ASIGNADOS:'),0,0,'L');		
		$this->Ln(10);
		$this->SetFont('Arial','B',8);		
		$this->SetFillColor(207,207,207);
		$this->SetTextColor(0);
		$this->SetDrawColor(0);
		$this->SetLineWidth(.3);       
		$this->SetWidths(array(20,106,50));
        $this->SetAligns(array('C', 'C', 'C'));
		$this->Row(array('CANT.','DESCRIPCIÓN','SERIAL'));
	}

	function Footer()
	{
		//Go to 1.5 cm from bottom
		$this->SetY(-15);		
		$this->SetFont('Arial','',6);
		//Print centered page number
		$this->SetX(5);	
		$this->Cell(0,10,'NOTA: Los equipos asignados a través de este documento, quedan bajo la responsabilidad de la persona que los recibe y firma conforme ',0,0,'L');
		$this->Ln(5);
		$this->SetX(5);		
		$this->Cell(190,10,utf8_decode('Generado a travÃ©s del Sistema SICET en Fecha '.date('d/m/Y').' por el Usuario '.rtrim($_SESSION["strnombrefenix"]).' '.rtrim($_SESSION["strapellidofenix"]).'  |  Fuente: OSTI - '.date('Y').'  |  Licencia: GPL/GNU'),0,0,'L');
		$this->Cell(20,10,utf8_decode('PÃ¡gina '.$this->PageNo().'/{nb}'),0,0,'R');
	}


}
ob_end_clean();
$pdf=new PDF("p","mm","Letter");
$pdf->Open();
$pdf->AliasNbPages();
$pdf->SetMargins(20,10,20);
$pdf->SetFont('Arial','',8);
$pdf->AddPage();
$contacto=  new clNotaentregaModelo();
$detalle= new clBienesModelo();
$usuario= new clUsuarioModelo();
$data= $contacto->selectNotaentregaById($_REQUEST['id_notaentrega']);
if($data){
	$serial=split(",",$data[0]['strequipos']);
	$entrega=$usuario->selectUsuarioById($data[0]['id_entrega']);
	$recibe=$usuario->selectUsuarioById($data[0]['id_recibe']);
	foreach($serial as $strserial){
		$data1=$detalle->selectBienesById($strserial);
        if ($data1){ 	
        	$pdf->SetFillColor(255,255,255);
        	$pdf->SetLineWidth(.3);       
            $pdf->SetTextColor(0);
            $pdf->SetFont('Arial','',8);
            $pdf->SetAligns(array('C', 'C', 'C'));
            $pdf->Row(array(1,utf8_decode($data1[0]['tipo'].' '.$data1[0]['marca'].', Modelo '.$data1[0]['modelo']), $data1[0]["strserial"]));
        }
	}
	
	$pdf->SetFont('Arial','B',7);
	$pdf->MultiCell(0,5,'OBSERVACIÓN: '.utf8_decode($data[0]['memobservacion']),0,'J',false); 
	$pdf->Ln(20);
	$pdf->SetFont('Arial','B',7);
	$pdf->Cell(88,5,'ENTREGA CONFORME',0,0,'C');
	$pdf->Cell(88,5,'RECIBE CONFORME',0,0,'C');
	$pdf->Ln(5);
	$pdf->SetFont('Arial','',7);
	$pdf->Cell(18,5,'NOMBRE:',0,0,'L');
	$pdf->SetFont('Arial','',7);
	$pdf->Cell(70,5,utf8_decode($data[0]['entrega']),0,0,'L');
	$pdf->SetFont('Arial','',7);
	$pdf->Cell(18,5,'NOMBRE:',0,0,'L');
	$pdf->SetFont('Arial','',7);
	$pdf->Cell(70,5,utf8_decode($data[0]['recibe']),0,0,'L');
	$pdf->Ln(5);
	$pdf->SetFont('Arial','',7);
	$pdf->Cell(18,5,'C.I.:',0,0,'L');
	$pdf->SetFont('Arial','',7);
	$pdf->Cell(70,5,number_format($entrega[0]['strcedula'],0,",","."),0,0,'L');
	$pdf->SetFont('Arial','',7);
	$pdf->Cell(18,5,'C.I.:',0,0,'L');
	$pdf->SetFont('Arial','',7);
	if ($data[0]['id_recibe']>0){
		$pdf->Cell(70,5,number_format($recibe[0]['strcedula'],0,",","."),0,0,'L');	
	}else{
		$pdf->Cell(70,5,number_format($data[0]['strcedula'],0,",","."),0,0,'L');
	}
	
	$pdf->Ln(5);
	$pdf->SetFont('Arial','',7);	
	$pdf->SetWidths(array(18,70,18,70));
    $pdf->SetAligns(array('L','L','L','L'));
    $pdf->SetDrawColor(255,255,255);  
    if ($data[0]['id_recibe']>0){	
    	$pdf->Row(array('CARGO:',utf8_decode($entrega[0]['cargo']),'CARGO:',utf8_decode($recibe[0]['cargo'])));
    }else{
    	$pdf->Row(array('CARGO:',utf8_decode($entrega[0]['cargo']),'CARGO:',utf8_decode($data[0]['strcargo'])));
    }
	$pdf->SetFont('Arial','',7);
	$pdf->Cell(18,5,'GERENCIA:',0,0,'L');
	$pdf->SetFont('Arial','',7);
	$pdf->Cell(70,5,utf8_decode($entrega[0]['gerencia1']),0,0,'L');
	$pdf->SetFont('Arial','',7);
	$pdf->Cell(18,5,'GERENCIA:',0,0,'L');
	$pdf->SetFont('Arial','',7);
	$pdf->Cell(70,5,utf8_decode($recibe[0]['gerencia1']),0,0,'L');
	$pdf->Ln(5);
	$pdf->SetFont('Arial','',7);
	$pdf->Cell(18,5,'FECHA:',0,0,'L');
	$pdf->SetFont('Arial','',7);
	$pdf->Cell(70,5,utf8_decode($data[0]['fecha']),0,0,'L');
	$pdf->SetFont('Arial','',7);
	$pdf->Cell(18,5,'FECHA:',0,0,'L');
	$pdf->SetFont('Arial','',7);
	$pdf->Cell(70,5,utf8_decode($data[0]['fecha']),0,0,'L');
	$pdf->Ln(5);
	$pdf->SetFont('Arial','',7);
	$pdf->Cell(18,5,'HORA:',0,0,'L');
	$pdf->SetFont('Arial','',7);
	$pdf->Cell(70,5,'__________________',0,0,'L');
	$pdf->SetFont('Arial','',7);
	$pdf->Cell(18,5,'HORA:',0,0,'L');
	$pdf->SetFont('Arial','',7);
	$pdf->Cell(70,5,'__________________',0,0,'L');
	$pdf->Ln(20);
	$pdf->SetFont('Arial','',7);
	$pdf->SetDrawColor(0,0,0); 
	$pdf->SetLineWidth(0.1);
	$pdf->Line(30,$pdf->GetY(),98,$pdf->GetY());
	$pdf->Line(118,$pdf->GetY(),178,$pdf->GetY());
	$pdf->Cell(88,5,'FIRMA Y SELLO',0,0,'C');
	$pdf->Cell(88,5,'FIRMA Y SELLO',0,0,'C');
	
	
       
}
  	
$pdf->Output();		

	
		
?>

