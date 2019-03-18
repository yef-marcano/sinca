<?php
session_start();
require('../comunes/php/fpdf.php');
require_once "../modelo/clNotaentregaModelo.php";
require_once "../modelo/clBienesModelo.php";
require_once "../modelo/clUsuarioModelo.php";
require_once "../modelo/clRutausuarioModelo.php";



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
                        $id_tipo=($data[0]['id_tipo_maestro']);
		}
		//Logo
		$this->Image('../comunes/images/cintillo.jpg',20,3,180,15);
		$this->SetFont('Arial','B',12);
                $this->Ln(10);
		$this->Cell(215,10,$data[0]['strcodigo'],0,0,'R');
		$this->SetX(12);
		$this->SetY(18);	
		$this->Ln(10);
                if ($id_tipo=='599'){
                    $this->Cell(0,10,'ACTA DE ENTREGA DE BIEN NACIONAL',0,0,'C');
                }else{
                    $this->Cell(0,10,utf8_decode('ACTA DE PRÉSTAMO DE BIEN NACIONAL'),0,0,'C');
                }
		//Line break
		$this->Ln(15);		
	}

	function Footer()
	{
		//Go to 1.5 cm from bottom
		$this->SetY(-25);
		$this->SetFont('Arial','I',10);
		//Print centered page number
		$this->SetX(5);	
		$this->Ln();
		$this->SetX(5);
		$this->SetFont('Arial','',6);                
		$this->Cell(190,10,utf8_decode('Generado a través del Sistema SICET en Fecha '.date('d/m/Y').' por el Usuario '.rtrim($_SESSION["strnombrefenix"]).' '.rtrim($_SESSION["strapellidofenix"]).'  |  Fuente: OSTI - '.date('Y').'  |  Licencia: GPL/GNU'),0,0,'L');
                $this->Cell(20,10,utf8_decode('Página '.$this->PageNo().'/{nb}'),0,0,'R');
		
	}


}
ob_end_clean();
$pdf=new PDF("p","mm","Letter");
$pdf->Open();
$pdf->AliasNbPages();
$pdf->SetMargins(20,10,20);
$pdf->SetFont('Arial','',10);
$pdf->AddPage();
$contacto=  new clNotaentregaModelo();
$detalle= new clBienesModelo();
$ruta= new clRutaUsuarioModelo();
$usuario= new clUsuarioModelo();

$data= $contacto->selectNotaentregaById($_REQUEST['id_notaentrega']);
if($data){	
	if ($data[0]['strequiposasignados']!=""){
            $data1=$detalle->selectAllBienesvarios($data[0]['strequiposasignados']);
        }else{
            $data1=$detalle->selectAllBienesvarios($data[0]['strequipos']);
        }
	$serialfinalizado=split(",",$data[0]['strequiposfinalizados']);
        $entrega=$ruta->selectAllRutaUsuarioByIdUsuario($data[0]['id_entrega'], $data[0]['fecha']);
        if (!$entrega){
            $entrega=$usuario->selectUsuarioById($data[0]['id_entrega']);
        }
        $recibe=$ruta->selectAllRutaUsuarioByIdUsuario($data[0]['id_recibe'], $data[0]['fecha']);
	if (!$recibe){
            $recibe=$usuario->selectUsuarioById($data[0]['id_recibe']);
        }
	
	$pdf->MultiCell(0,5,utf8_decode('En la Ciudad de Caracas a los ').utf8_decode(fechaLetra($data[0]['fecha'])).utf8_decode(', en la Oficina de Despacho de la FUNDACIÓN GRAN MISIÓN SABER Y TRABAJO, ubicada en la Avenida Principal Boleíta Norte, Edificio GRAN MISIÓN SABER Y TRABAJO, Calle Miraima, Boleíta Norte, Municipio Sucre, Parroquia Leoncio Martínez, el(la) Ciudadano(a) ').$data[0]['entrega'].utf8_decode(', Titular de la Cédula de Identidad N° ').number_format($entrega[0]['strcedula'],0,",",".").utf8_decode(', en su carácter de ').$entrega[0]['cargo'].utf8_decode(', certifica por medio de la presente, la entrega de los siguientes equipos que se detallan a continuación:'),0,'J',0);
	$pdf->Ln(5);
	$pdf->SetX(25);		
	$pdf->SetFillColor(207,207,207);
	$pdf->SetTextColor(0);
	$pdf->SetDrawColor(0);
	$pdf->SetLineWidth(.1);     
	$pdf->SetFont('Arial','B',10);  
	$pdf->SetWidths(array(18,40,30,30,50));
        $pdf->SetAligns(array('C', 'C', 'C','C','C'));
	$pdf->Row(array('Cantidad',utf8_decode('Descripción'),'Marca','Modelo','Serial'));
	$total=0;
        $sw=false;
        $max='25';
        $j='0';
        if ($data1){
            for ($i=0; $i<count($data1);$i++){
                $total++;
                $color=false;
                if ($serialfinalizado){
                        foreach($serialfinalizado as $strserialfinalizado){
                                if ($data1[$i]['id_bienes']==$strserialfinalizado){
                                        $color=true;
                                        break;
                                }
                        }
                }
                $pdf->SetX(25);
                $pdf->SetFillColor(255,255,255);
                $pdf->SetLineWidth(.1);
                if ($color==false){
                    $pdf->SetTextColor(0);
                }else{
                    $pdf->SetTextColor(255,0,0);
                }
                $pdf->SetFont('Arial','',10);
                $pdf->SetAligns(array('C','C','C','C','C'));
                $j++;
                if ($j == $max)
                {
                    $j= 0;
					$max=25;
					$sw=true;
                    $pdf->AddPage();
                    $pdf->SetFont('Arial','B',10);
                    $pdf->SetFillColor(207,207,207);
                    $pdf->SetTextColor(0);
                    $pdf->SetDrawColor(0);
                    $pdf->SetLineWidth(.1);
                    $pdf->SetWidths(array(18,40,30,30,50));
                    $pdf->SetAligns(array('C', 'C', 'C','C','C'));
                    $pdf->SetX(25);
                    $pdf->Row(array('Cantidad',utf8_decode('Descripción'),'Marca','Modelo','Serial'));
                    $pdf->SetFillColor(255,255,255);
                    $pdf->SetLineWidth(.1);
                    $pdf->SetFont('Arial','',10);
                    $pdf->SetAligns(array('C','C','C','C','C'));
                }
                    $pdf->SetX(25);
                    $pdf->Row(array('01',$data1[$i]['tipo'],$data1[$i]['marca'],$data1[$i]['modelo'], trim($data1[$i]["strserial"])));
			}      
            
        }
	
	$pdf->SetTextColor(0);
	$pdf->Ln(5);
        if ($pdf->GetY()>200){
            $pdf->AddPage();
        }
	
	$pdf->MultiCell(0,5,utf8_decode('OBSERVACIÓN: ').$data[0]['memobservacion'],0,'J',false);
	$pdf->Ln(5);	
	if ($data[0]['id_recibe']>0){
            if (substr($recibe[0]['strcedula'],0,1)!="E"){
                //if ($data[0]['id_ubicacion']>='1915' and $data[0]['id_ubicacion']<='1938' ){
                //    $pdf->MultiCell(0,5,utf8_decode('A el(la) Ciudadano(a) ').trim($data[0]['recibe']).utf8_decode(', Titular de la Cédula de Identidad N° ').number_format($recibe[0]['strcedula'],0,",",".").utf8_decode(', en su carácter de ').$recibe[0]['cargo'].' DEL ESTADO '.$data[0]['ubicacion'].'.',0,'J',0);
                //}else{        
                    $pdf->MultiCell(0,5,utf8_decode('A el(la) Ciudadano(a) ').trim($data[0]['recibe']).utf8_decode(', Titular de la Cédula de Identidad N° ').number_format($recibe[0]['strcedula'],0,",",".").utf8_decode(', en su carácter de ').$recibe[0]['cargo'].'.',0,'J',0);
                //}        
            }else{
                //if ($data[0]['id_ubicacion']>='1915' and $data[0]['id_ubicacion']<='1938' ){
                //    $pdf->MultiCell(0,5,utf8_decode('A el(la) Ciudadano(a) ').trim($data[0]['recibe']).utf8_decode(', Titular del Pasaporte N° ').$recibe[0]['strcedula'].utf8_decode(', en su carácter de ').$recibe[0]['cargo'].' DEL ESTADO '.$data[0]['ubicacion'].'.',0,'J',0);
                //}else{    
                    $pdf->MultiCell(0,5,utf8_decode('A el(la) Ciudadano(a) ').trim($data[0]['recibe']).utf8_decode(', Titular del Pasaporte N° ').$recibe[0]['strcedula'].utf8_decode(', en su carácter de ').$recibe[0]['cargo'].'.',0,'J',0);
                //}    
            }
	}else{
            if (substr($data[0]['strcedula'],0,1)!="E"){
                //if ($data[0]['id_ubicacion']>='1915' and $data[0]['id_ubicacion']<='1938' ){
                //    $pdf->MultiCell(0,5,utf8_decode('A el(la) Ciudadano(a) ').trim($data[0]['recibe']).utf8_decode(', Titular de la Cédula de Identidad N° ').number_format($data[0]['strcedula'],0,",",".").utf8_decode(', en su carácter de ').$data[0]['strcargo'].' DEL ESTADO '.$data[0]['ubicacion'].'.',0,'J',0);
                //}else{
                    $pdf->MultiCell(0,5,utf8_decode('A el(la) Ciudadano(a) ').trim($data[0]['recibe']).utf8_decode(', Titular de la Cédula de Identidad N° ').number_format($data[0]['strcedula'],0,",",".").utf8_decode(', en su carácter de ').$data[0]['strcargo'].'.',0,'J',0);
                //}    
            }else{
                //if ($data[0]['id_ubicacion']>='1915' and $data[0]['id_ubicacion']<='1938' ){
                //    $pdf->MultiCell(0,5,utf8_decode('A el(la) Ciudadano(a) ').trim($data[0]['recibe']).utf8_decode(', Titular del Pasaporte N° ').$data[0]['strcedula'].utf8_decode(', en su carácter de ').$data[0]['strcargo'].' DEL ESTADO '.$data[0]['ubicacion'].'.',0,'J',0);
                //}else{
                    $pdf->MultiCell(0,5,utf8_decode('A el(la) Ciudadano(a) ').trim($data[0]['recibe']).utf8_decode(', Titular del Pasaporte N° ').$data[0]['strcedula'].utf8_decode(', en su carácter de ').$data[0]['strcargo'].'.',0,'J',0);
                //}
            }
	}
	$pdf->Ln(5);
        if ($pdf->GetY()>200){
            $pdf->AddPage();
        }
	
	$pdf->MultiCell(0,5,utf8_decode('Los equipos descritos ut supra; son propiedad de la Gran Misión Saber y Trabajo por órgano de la Fundación Gran Misión Saber y Trabajo, en tal sentido la disposición de los mismos estará sujeta a las regulaciones de Ley, toda vez que el  uso y conservación de ellos quedará bajo la responsabilidad del ciudadano identificado anteriormente, quien fungirá  como Coadministrador de tales recursos.'),0,'J',0);
	$pdf->Ln(5);
	if ($pdf->GetY()>200){
            $pdf->AddPage();
        }
	$pdf->MultiCell(0,5,utf8_decode('En tal sentido  el mencionado ciudadano estará sujeto a control fiscal de acuerdo a lo contemplado en el artículo 9 numeral 12 de la Ley Orgánica de la Contraloría General de la República Bolivariana de Venezuela y del Sistema Nacional de Control Fiscal, en concordancia con lo establecido en el artículo 4, último aparte de la Ley Contra la Corrupción, se consideran Patrimonio Público hasta que se demuestre el logro de las actividades envueltas en el mismo. '),0,'J',0);
	$pdf->Ln(5);
	if ($pdf->GetY()>200){
            $pdf->AddPage();
        }
	$pdf->MultiCell(0,5,utf8_decode('Quedando el referido bien nacional bajo su responsabilidad y custodia, de lo contrario será objeto de sanción prevista en los artículos N° 13, 21 y 54 de la "Ley Contra la Corrupción", se levanta la presente Acta, la cual leída y encontrada conforme firman y sellan.'),0,'J',0);
	
	$pdf->Ln(15);
	
	if ($pdf->GetY()>260){
            $pdf->AddPage();
            $pdf->SetX(60);
        }
	$pdf->SetFont('Arial','B',10);
	$pdf->SetDrawColor(0,0,0); 
	$pdf->SetLineWidth(0.1);
	$pdf->Line(30,$pdf->GetY(),98,$pdf->GetY());
	$pdf->Line(118,$pdf->GetY(),186,$pdf->GetY());
	$pdf->SetX(35);
	$pdf->Cell(58,5,$data[0]['entrega'],0,0,'C');        
	$pdf->SetX(123);
	$pdf->Cell(58,5,$data[0]['recibe'],0,0,'C',0);
        
	$pdf->Ln(5);
	$pdf->SetX(25);
	if ($data[0]['id_recibe']>0){
		$pdf->SetLineWidth(.3);     
		$pdf->SetWidths(array(68,25,68));
	    $pdf->SetAligns(array('C','C','C'));
	    $pdf->SetDrawColor(255,255,255);   
//	    if ($recibe[0]["id_gerencia_maestro"]>=32 && $recibe[0]["id_gerencia_maestro"]<=616) {
            //if ($data[0]['id_ubicacion']>='1915' and $data[0]['id_ubicacion']<='1938' ){
	    	//$pdf->Row(array($entrega[0]['cargo'],'',$recibe[0]['cargo'].' DEL ESTADO '.$data[0]['ubicacion']));
            //}else{
                $pdf->Row(array($entrega[0]['cargo'],'',$recibe[0]['cargo']));
            //}
//	    }else if (($recibe[0]['id_cargo_maestro']>=491 && $recibe[0]['id_cargo_maestro']<=499)  || $recibe[0]['id_cargo_maestro']==501 || $recibe[0]['id_cargo_maestro']==505 || $recibe[0]['id_cargo_maestro']==713 || $recibe[0]['id_cargo_maestro']==763 || $recibe[0]['id_cargo_maestro']==772 || $recibe[0]['id_cargo_maestro']==875){
//	    	$pdf->Row(array(utf8_decode($entrega[0]['cargo']),'',utf8_decode($recibe[0]['cargo'])));
//	    }else{
//	    	$pdf->Row(array(utf8_decode($entrega[0]['cargo']),'',utf8_decode($recibe[0]['cargo']).' de la '.utf8_decode(trim($recibe[0]['gerencia1']))));
//	    }
	    
	    
	}else{
            $pdf->SetLineWidth(.3);
            $pdf->SetWidths(array(68,25,68));
	    $pdf->SetAligns(array('C','C','C'));
	    $pdf->SetDrawColor(255,255,255); 
            //if ($data[0]['id_ubicacion']>='1915' and $data[0]['id_ubicacion']<='1938' ){
            //    $pdf->Row(array($entrega[0]['cargo'],'',$data[0]['strcargo'].' DEL ESTADO '.$data[0]['ubicacion']));	
            //}else{
                $pdf->Row(array($entrega[0]['cargo'],'',$data[0]['strcargo']));	
            //}
	}
	
	
       
}
  	
$pdf->Output();		

	
		
?>

