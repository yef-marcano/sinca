<?php
session_start();
require('../common/php/fpdf.php');
require('../model/md_tblsolicitud.php');



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
		$this->Image('../common/img/banner_censo.jpg',10,3,198,20);
		$this->SetFont('Arial','B',12);
                $this->Ln(20);
                $objTable = new md_tblurbanismo();
                $resultSet = null;
                $condition = "";
                $resultSet = $objTable->findUrbanismo("id_urbanismo=".$_REQUEST["id"]);
                $this->Cell(0,10,  utf8_decode('FICHA DE URBANISMO'),0,1,'C');              
                if ($resultSet){
                    $data = pg_fetch_all($resultSet);
                    $this->Cell(0,10,  utf8_decode($data[0]['descripcion']),0,1,'C');              
                            
                }
		//Line break
		$this->Ln(15);	
                
	}

	function Footer()
	{
		//Go to 1.5 cm from bottom
		$this->SetY(-15);
		$this->SetFont('Arial','',6);  
                $this->SetTextColor(0);
                $this->SetX(5);
		$this->Cell(190,10,utf8_decode('Generado a través del Sistema Viviendo Venezolanos en Fecha '.date('d/m/Y').' por el Usuario '.$_SESSION['nombreUsuario'].'  |  Fuente: OSTI - '.date('Y').'  |  Licencia: GPL/GNU'),0,0,'L');
                $this->Cell(20,10,utf8_decode('Página '.$this->PageNo().'/{nb}'),0,0,'R');
		
	}


}
ob_end_clean();
$pdf=new PDF("p","mm","Letter");

$pdf->Open();
$pdf->AliasNbPages();
$pdf->SetMargins(10,10,10);
$pdf->SetFont('Arial','B',10);
$pdf->AddPage();
$objTable = new md_tblurbanismo();
$resultSet = null;
$condition = "";
$resultSet = $objTable->findUrbanismo("id_urbanismo=".$_REQUEST["id"]);
     
if ($resultSet){
    $data = pg_fetch_all($resultSet);
    $pdf->SetFillColor(255,0,0);		
    $pdf->SetTextColor(255,255,255);
    $pdf->SetDrawColor(0);
    $pdf->SetLineWidth(.3);
    $pdf->SetWidths(array(200));
    $pdf->SetAligns(array('C'));
    $pdf->Row(array(utf8_decode('DATOS DE UBICACIÓN')));
    $pdf->SetFillColor(210,210,210);		
    $pdf->SetTextColor(0);
    $pdf->SetDrawColor(0);
    $pdf->SetLineWidth(.3);
    $pdf->SetWidths(array(50,50,50,50));
    $pdf->SetAligns(array('C','C','C','C'));
    $pdf->Row(array('ESTADO','MUNICIPIO','PARROQUIA','SECTOR'));
    $pdf->SetFillColor(255,255,255);		
    $pdf->SetFont('Arial','',9);
    $pdf->SetAligns(array('L','L','L','L'));
    $pdf->Row(array(utf8_decode($data[0]['estado']),utf8_decode($data[0]['municipio']),utf8_decode($data[0]['parroquia']),utf8_decode($data[0]['sector'])));
    $pdf->SetFont('Arial','B',10);
    $pdf->SetWidths(array(133,67));
    $pdf->SetAligns(array('C','C'));
    $pdf->SetFillColor(210,210,210);		
    $pdf->Row(array(utf8_decode('DIRECCIÓN'),'ENTE EJECUTOR'));
    $pdf->SetFillColor(255,255,255);		
    $pdf->SetFont('Arial','',9);
    $pdf->SetAligns(array('L','L'));
    $pdf->Row(array(utf8_decode($data[0]['comunidad']),utf8_decode($data[0]['ente_ejecutor'])));
    $pdf->SetFont('Arial','B',10);
    $pdf->SetFillColor(255,0,0);		
    $pdf->SetTextColor(255,255,255);
    $pdf->SetDrawColor(0);
    $pdf->SetLineWidth(.3);
    $pdf->SetWidths(array(200));
    $pdf->SetAligns(array('C')); 
    $pdf->Ln(15);
    $pdf->Row(array(utf8_decode('CARACTERÍSTICAS DEL URBANISMO')));
    $pdf->SetFillColor(255,255,255);		
    $pdf->SetTextColor(0);
    $pdf->SetDrawColor(0);
    $pdf->SetLineWidth(.3);
    $pdf->SetFont('Arial','B',10);
    $pdf->SetWidths(array(33,33,34,33,33,34));
    $pdf->SetAligns(array('C','C','C','C','C','C'));
    $pdf->SetFillColor(210,210,210);	
    $pdf->Row(array('AGUA','CALIDAD AGUA','LUZ','CALIDAD LUZ','ASEO','CALIDAD ASEO'));
    $pdf->SetFillColor(255,255,255);	
    $pdf->SetFont('Arial','',9);
    $pdf->SetAligns(array('L','L','L','L','L','L'));
    $pdf->Row(array($data[0]['agua']=='1'?'SI':'NO',utf8_decode($data[0]['calidad_agua']),$data[0]['luz']=='1'?'SI':'NO',utf8_decode($data[0]['calidad_luz']),$data[0]['aseo']=='1'?'SI':'NO',utf8_decode($data[0]['calidad_aseo'])));
    $pdf->SetFont('Arial','B',10);
    $pdf->SetWidths(array(20,25,21,25,25,25,30,29));
    $pdf->SetAligns(array('C','C','C','C','C','C','C','C'));
    $pdf->SetFillColor(210,210,210);	
    $pdf->Row(array('FACHADA','CALIDAD FACHADA','PINTURA','CALIDAD PINTURA','ESCALERAS','CALIDAD ESCALERAS','ASCENSORES','CALIDAD ASCENSORES'));
    $pdf->SetFillColor(255,255,255);	
    $pdf->SetFont('Arial','',9);
    $pdf->SetAligns(array('L','L','L','L','L','L','L','L'));                    
    $pdf->Row(array($data[0]['fachada']=='1'?'SI':'NO',utf8_decode($data[0]['calidad_fachada']),$data[0]['pintura']=='1'?'SI':'NO',utf8_decode($data[0]['calidad_pintura']),$data[0]['escaleras']=='1'?'SI':'NO',utf8_decode($data[0]['calidad_escaleras']),$data[0]['asensores']=='1'?'SI':'NO',utf8_decode($data[0]['calidad_asensores'])));                
    $pdf->SetFont('Arial','B',10);
    $pdf->SetWidths(array(66,68,66));
    $pdf->SetAligns(array('C','C','C'));
    $pdf->SetFillColor(210,210,210);	
    $pdf->Row(array('LOCALES COMERCIALES','AREAS RECREATIVAS','AREAS GENERALES'));
    $pdf->SetFillColor(255,255,255);	
    $pdf->SetFont('Arial','',9);
    $pdf->SetAligns(array('L','L','L'));                    
    $pdf->Row(array(utf8_decode($data[0]['locales_comerciales']),utf8_decode($data[0]['areas_recreativas']), utf8_decode($data[0]['areas_generales'])));                
    $pdf->SetFont('Arial','B',10);
    $pdf->SetWidths(array(100,100));
    $pdf->SetAligns(array('C','C'));
    $pdf->SetFillColor(210,210,210);	
    $pdf->Row(array('CANTIDAD DE TORRES / MANZANAS','CANTIDAD DE APARTAMENTOS / CASAS'));
    $pdf->SetFillColor(255,255,255);	
    $pdf->SetFont('Arial','',9);
    $pdf->SetAligns(array('C','C'));                    
    $pdf->Row(array(utf8_decode($data[0]['cantidad_torres']),utf8_decode($data[0]['cantidad_aptos'])));                
    $pdf->SetFont('Arial','B',10);
    $pdf->SetFillColor(255,0,0);		
    $pdf->SetTextColor(255,255,255);
    $pdf->SetDrawColor(0);
    $pdf->SetLineWidth(.3);
    $pdf->SetWidths(array(200));
    $pdf->SetAligns(array('C'));
    $pdf->Ln(15);
    $pdf->Row(array(utf8_decode('CARACTERIZACIÓN DE FAMILIAS DEL URBANISMO')));
    $pdf->SetFillColor(210,210,210);		
    $pdf->SetTextColor(0,0,0);
    $pdf->SetDrawColor(0);
    $pdf->SetLineWidth(.3);
    $pdf->SetFont('Arial','B',10);
    $pdf->SetWidths(array(200));
    $pdf->SetAligns(array('C'));
    $pdf->Row(array(utf8_decode('SITUACIÓN DE REGISTRO DEL APARTAMENTO / CASA')));
     $pdf->SetFillColor(255,255,255);		
    $pdf->SetTextColor(0);
    $pdf->SetDrawColor(0);
    $pdf->SetLineWidth(.3);
    $pdf->SetFont('Arial','',9);
    $pdf->SetWidths(array(40,40,40,40,40));
    $pdf->SetAligns(array('C','C','C','C','C'));                    
    $pdf->SetFont('Arial','B',9);
    $pdf->Row(array(utf8_decode('POR REGISTRAR'),utf8_decode('REGISTRADO'),utf8_decode('DESOCUPADO'),utf8_decode('AUSENTE'),utf8_decode('NEGADO')));
        
    $pdf->SetFont('Arial','',9);
    $pdf->Row(array(utf8_decode($data[0]['por_registrar']),utf8_decode($data[0]['registrado']),utf8_decode($data[0]['desocupado']),utf8_decode($data[0]['ausente']),utf8_decode($data[0]['negado'])));
        
    $pdf->SetFillColor(210,210,210);		
    $pdf->SetTextColor(0,0,0);
    $pdf->SetDrawColor(0);
    $pdf->SetLineWidth(.3);
    $pdf->SetFont('Arial','B',10);
    $pdf->SetWidths(array(200));
    $pdf->SetAligns(array('C'));
    $pdf->Row(array(utf8_decode('SITUACIÓN LEGAL DEL APARTAMENTO / CASA')));
     $pdf->SetFillColor(255,255,255);		
    $pdf->SetTextColor(0);
    $pdf->SetDrawColor(0);
    $pdf->SetLineWidth(.3);
    $pdf->SetFont('Arial','',9);
    $pdf->SetWidths(array(25,20,20,25,25,35,25,25));
    $pdf->SetAligns(array('C','C','C','C','C','C','C','C'));                    
    $pdf->SetFont('Arial','B',9);    
    $pdf->Row(array(utf8_decode('NO ASIGNADO'),utf8_decode('ASIGNADO'),utf8_decode('VENDIDO'),utf8_decode('PERMUTADO'),utf8_decode('ALQUILADO'),utf8_decode('NO RESIDE EN APTO'),utf8_decode('CEDIDO'),utf8_decode('TRASPASADO')));
    $pdf->SetFont('Arial','',9);
    $pdf->Row(array(utf8_decode($data[0]['no_asignado']),utf8_decode($data[0]['asignado']),utf8_decode($data[0]['vendido']),utf8_decode($data[0]['permutado']),utf8_decode($data[0]['alquilado']),utf8_decode($data[0]['no_reside']),utf8_decode($data[0]['cedido']),utf8_decode($data[0]['traspasado'])));
    $pdf->SetFillColor(210,210,210);		
    $pdf->SetTextColor(0,0,0);
    $pdf->SetDrawColor(0);
    $pdf->SetLineWidth(.3);
    $pdf->SetFont('Arial','B',10);
    $pdf->SetWidths(array(200));
    $pdf->SetAligns(array('C'));
    $pdf->Row(array(utf8_decode('CANTIDAD DE FAMILIAS CARACTERIZADAS')));
     $pdf->SetFillColor(255,255,255);		
    $pdf->SetTextColor(0);
    $pdf->SetDrawColor(0);
    $pdf->SetLineWidth(.3);
    $pdf->SetFont('Arial','',9);
    $pdf->SetWidths(array(66,67,67));
    $pdf->SetAligns(array('C','C','C'));                    
    $pdf->SetFont('Arial','B',9);    
    $pdf->Row(array(utf8_decode('JEFE DE FAMILIA MASCULINO'),utf8_decode('JEFE DE FAMILIA FEMENINO'),utf8_decode('TOTAL JEFES DE FAMILIA')));
    $pdf->SetFont('Arial','',9);
    $pdf->Row(array(utf8_decode($data[0]['jefe_masculino']),utf8_decode($data[0]['jefe_femenino']),utf8_decode($data[0]['total_jefe'])));
    $pdf->SetFont('Arial','B',9);    
    $pdf->Row(array(utf8_decode('PERSONAS MASCULINO'),utf8_decode('PERSONAS FEMENINO'),utf8_decode('TOTAL PERSONAS')));
    $pdf->SetFont('Arial','',9);
    $pdf->Row(array(utf8_decode($data[0]['total_masculino']),utf8_decode($data[0]['total_femenino']),utf8_decode($data[0]['total_personas'])));
    $pdf->AddPage();
    $pdf->SetFont('Arial','B',10);
    $pdf->SetFillColor(255,0,0);		
    $pdf->SetTextColor(255,255,255);
    $pdf->SetDrawColor(0);
    $pdf->SetLineWidth(.3);
    $pdf->SetWidths(array(200));
    $pdf->SetAligns(array('C'));
    $pdf->Row(array(utf8_decode('MEMORIA FOTOGRÁFICA ')));
    $y=$pdf->GetY();
    if ($data[0]['foto1']!=''){
        $pdf->Image('../common/upload/'.$data[0]['foto1'],10,$y,100,70);
    }
    if ($data[0]['foto2']!=''){
        $pdf->Image('../common/upload/'.$data[0]['foto2'],110,$y,100,70);
    }
    if ($data[0]['foto3']!=''){    
        $pdf->Image('../common/upload/'.$data[0]['foto3'],10,$y+70,100,70);
    }
    if ($data[0]['foto4']!=''){
        $pdf->Image('../common/upload/'.$data[0]['foto4'],110,$y+70,100,70);
    }
    //$pdf->Image('../common/upload/'.$data[0]['foto1'],10,$y);
       
    
}    	
$pdf->Output();		

	
		
?>

