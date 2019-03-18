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
		$this->Image('../comunes/images/cab_reportes.jpg',20,3,30,15);
		$this->SetX(12);
		$this->SetY(25);
		$this->SetFont('Arial','B',10);
		$this->Cell(0,10,'LISTADO TARJETAS DE CONEXION A INTERNET OPERATIVAS',0,0,'C');
		$this->Ln(10);
		
	}

	function Footer()
	{
		//Go to 1.5 cm from bottom
		$this->SetY(-15);		
		$this->SetFont('Arial','',6);
		//Print centered page number
		
		$this->Ln(5);
		$this->Cell(232,10,utf8_decode('Generado a travÃ©s del Sistema SICET en Fecha '.date('d/m/Y').' por el Usuario '.rtrim($_SESSION["strnombrefenix"]).' '.rtrim($_SESSION["strapellidofenix"]).'  |  Fuente: OSTI - '.date('Y').'  |  Licencia: GPL/GNU'),0,0,'L');
		$this->Cell(20,10,utf8_decode('PÃ¡gina '.$this->PageNo().'/{nb}'),0,0,'R');
	}


}
ob_end_clean();
$pdf=new PDF("L","mm","Letter");
$pdf->Open();
$pdf->AliasNbPages();
$pdf->SetMargins(10,10,10);
$pdf->SetFont('Arial','',8);
$pdf->AddPage();
$bienes= new clBienesModelo();
$usuario= new clUsuarioModelo();

$sql="select b.*, g.stritema as gerencia,
case when b.id_unidad_maestro>0 then
(select stritema from tblmaestros where id_maestro=b.id_unidad_maestro and bolborrado=0)
else
'N/A'
end as unidad,t.stritema as tipo, m.stritema as marca,
case when b.id_responsable >0 then
(select  r.strnombre || ' ' || r.strapellido from tblusuario r where b.id_responsable=r.id_usuario and r.bolborrado=0)

else
'SIN USUARIO'
end as responsable, mo.stritema as modelo,
case when (select d.id_detallebienes from tbldetallebienes d where d.bolborrado=0 and d.id_bienes=b.id_bienes and d.id_caracteristica_maestro=445)>0 then
	(select d.strdescripcion from tbldetallebienes d
	where d.bolborrado=0 and d.id_bienes=b.id_bienes and d.id_caracteristica_maestro=445)
else

	'SE DESCONOCE'
end as proveedor,
case when (select d.id_detallebienes from tbldetallebienes d where d.bolborrado=0 and d.id_bienes=b.id_bienes and d.id_caracteristica_maestro=446)>0 then
	(select d.strdescripcion from tbldetallebienes d
	where d.bolborrado=0 and d.id_bienes=b.id_bienes and d.id_caracteristica_maestro=446)
else

	'N/A'
end as plan,
case when (select d.id_detallebienes from tbldetallebienes d where d.bolborrado=0 and d.id_bienes=b.id_bienes and d.id_caracteristica_maestro=782)>0 then
	(select d.strdescripcion from tbldetallebienes d
	where d.bolborrado=0 and d.id_bienes=b.id_bienes and d.id_caracteristica_maestro=782)
else

	'N/A'
end as telefono
from tblbienes b, tblmaestros g,tblmaestros t, 
tblmaestros mo,  tblmaestros m
where b.id_gerencia_maestro=g.id_maestro 
and b.id_tipo_maestro=t.id_maestro
and b.id_marca_maestro=m.id_maestro
and b.id_modelo_maestro=mo.id_maestro and b.bolborrado=0 and g.bolborrado=0 
and t.bolborrado=0 and mo.bolborrado=0  and m.bolborrado=0  and b.id_tipo_maestro=133 and b.id_estatus_maestro not in(89,90,91,618,619) order by proveedor, gerencia";
echo $sql;
$conn= new Conexion();
$conn->abrirConexion();
$conn->sql=$sql;
$data=$conn->ejecutarSentencia(2);       
$total=0;
$pdf->Cell(0, 10,'Proveedor: '.utf8_decode($data[0]["proveedor"]), 0, 1, 'L');     
$proveedor=$data[0]["proveedor"];   


    

if ($data){
	$pdf->SetFillColor(255,0,0);
	$pdf->SetTextColor(255,255,255);
	$pdf->SetDrawColor(0);
	$pdf->SetWidths(array(30,30,30,30,30,50,30,35));		
	$pdf->SetAligns(array('C','C','C','C','C','C','C','C'));
	$pdf->SetFont('Arial','B',8);
	$pdf->Row(array('N° LINEA','MARCA','MODELO','SERIAL','PLAN','GERENCIA','UNIDAD','RESPONSABLE'));     
	$pdf->SetWidths(array(30,30,30,30,30,50,30,35));		
	$pdf->SetAligns(array('L','L','L','L','L','L','L','L','L')); 
	$pdf->SetFont('Arial','',8);       
	$pdf->SetFillColor(255,255,255);
	$pdf->SetTextColor(0);
	$pdf->SetDrawColor(0);
	$pdf->SetLineWidth(0.3);
    
	for ($i= 0; $i < count($data); $i++){
		if ($data[$i]['id_ubicacion']<>689){				
			$responsable=$data[$i]['responsable'];
		}else if ($data[$i]['id_responsable']==0 && $data[$i]['id_ubicacion']==689){
			$sql="select * from tblnotaentrega ne where substring(ne.strequipos from '".$data[$i]['id_bienes']."')='".$data[$i]['id_bienes']."' order by ne.id_notaentrega DESC";
			$conn= new Conexion();
			$conn->abrirConexion();
			$conn->sql=$sql;
			$data2=$conn->ejecutarSentencia(2);
			if ($data2){
				$responsable=trim($data2[0]['strnombres']);					
			}else{
				$responsable="CONTACTO EXTERNO";
			}
		}else{
			$responsable=$data[$i]['responsable'];
		}
    	if ($proveedor!=$data[$i]["proveedor"]){
			$pdf->SetWidths(array(265));
            $pdf->SetAligns(array('R'));
			$pdf->Row(array('Total de Tarjetas '.$total));
            $pdf->Ln();
			$total=0;
			$pdf->AddPage();
			$pdf->SetFont('Arial','B',8);
			$pdf->SetFillColor(255,255,255);
			$pdf->SetTextColor(0);
			$pdf->SetDrawColor(0);
			$pdf->SetLineWidth(0.3);
			$pdf->Cell(0, 10,'Proveedor: '.utf8_decode($data[$i]["proveedor"]), 0, 1, 'L');     
            $pdf->Ln();
            $pdf->SetFillColor(255,0,0);
			$pdf->SetTextColor(255,255,255);
			$pdf->SetDrawColor(0);
			$pdf->SetWidths(array(30,30,30,30,30,50,30,35));		
			$pdf->SetAligns(array('C','C','C','C','C','C','C','C'));
			$pdf->SetFont('Arial','B',8);
			$pdf->Row(array('N° LINEA','MARCA','MODELO','SERIAL','PLAN','GERENCIA','UNIDAD','RESPONSABLE'));     
			$pdf->SetWidths(array(30,30,30,30,30,50,30,35));
			$pdf->SetAligns(array('L','L','L','L','L','L','L','L','L')); 
			$pdf->SetFont('Arial','',8);       
			$pdf->SetFillColor(255,255,255);
			$pdf->SetTextColor(0);
			$pdf->SetDrawColor(0);
			$pdf->SetLineWidth(0.3);
            $pdf->Row(array(utf8_decode($data[$i]["telefono"]),utf8_decode($data[$i]["marca"]), utf8_decode($data[$i]["modelo"]),utf8_decode($data[$i]["strserial"]), utf8_decode($data[$i]["plan"]),utf8_decode($data[$i]["gerencia"]),utf8_decode($data[$i]["unidad"]),utf8_decode($responsable)));
			$total++;
		}
		else
		{
			$total++;
			$pdf->SetWidths(array(30,30,30,30,30,50,30,35));
			$pdf->SetAligns(array('L','L','L','L','L','L','L','L','L')); 
			$pdf->SetFont('Arial','',8);       
			$pdf->SetFillColor(255,255,255);
			$pdf->SetTextColor(0);
			$pdf->SetDrawColor(0);
			$pdf->SetLineWidth(0.3);
            $pdf->Row(array(utf8_decode($data[$i]["telefono"]),utf8_decode($data[$i]["marca"]), utf8_decode($data[$i]["modelo"]),utf8_decode($data[$i]["strserial"]), utf8_decode($data[$i]["plan"]),utf8_decode($data[$i]["gerencia"]),utf8_decode($data[$i]["unidad"]),utf8_decode($responsable)));
		}
		$proveedor=$data[$i]["proveedor"];
	}
	$pdf->SetWidths(array(265));
    $pdf->SetAligns(array('R'));
    $pdf->Row(array('Total de Tarjetas '.$total));
    $pdf->Output();
}else{
	echo "<script>alert('No existen registros para mostrar');window.close(this);</script>";
}


$pdf->Output();		

	
		
?>

