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
		$this->Image('../comunes/images/cintillo.jpg',10,3,255,15);
		$this->SetX(12);
		$this->SetY(25);
		$this->SetFont('Arial','B',10);
		$this->Cell(0,10,utf8_decode('PLAN DE INTERNET'),0,0,'C');
		$this->Ln(10);
		
		$this->SetFont('Arial','B',8);		
		$this->SetFillColor(255,0,0);		
		$this->SetTextColor(255,255,255);
		$this->SetDrawColor(0);
		$this->SetLineWidth(.3);       
		$this->SetWidths(array(17,20,60,35,22,20,20,20,20,20));
        $this->SetAligns(array('C','C','C','C','C','C','C','C','C','C'));
		$this->Row(array('Equipo','Serial',utf8_decode('Gerencia'),'Responsable','Estatus','S.O','Plan-Internet',utf8_decode('IP'),'Mac-Lan','Mac-Wifi'));
		$this->SetAligns(array('L','R','C','R','R','C','R','R','R','R','R'));
	}

	function Footer()
	{
		//Go to 1.5 cm from bottom
		$this->SetY(-15);		
		$this->SetFont('Arial','',6);
		//Print centered page number
		
		$this->Ln(5);
		$this->Cell(150,10,utf8_decode('Generado a través del Sistema SICET en Fecha '.date('d/m/Y').' por el Usuario '.rtrim($_SESSION["strnombrefenix"]).' '.rtrim($_SESSION["strapellidofenix"]).'  |  Fuente: OT - '.date('Y').'  |  Licencia: GPL/GNU'),0,0,'L');
		$this->Cell(0,10,utf8_decode('Página '.$this->PageNo().'/{nb}'),0,0,'R');
	}


}
ob_end_clean();
$pdf=new PDF("l","mm","Letter");
$pdf->Open();
$pdf->AliasNbPages();
$pdf->SetMargins(10,10,10);
$pdf->SetFont('Arial','',7);
$pdf->AddPage();
$sql="select e.stritema as equipo, b.strserial, g.stritema as gerencia,
case when b.id_responsable >0 and b.id_estatus_maestro in (86,87,88)  then
(select  r.strnombre || ' ' || r.strapellido from tblusuario r where b.id_responsable=r.id_usuario and r.bolborrado=0)
else case when b.id_responsable=0 and b.id_estatus_maestro in (87,88) and b.strnombres<>'' then
b.strnombres
else case  when b.id_responsable=0 and b.id_estatus_maestro in (86) then
'DISPONIBLE'
else case  when b.id_estatus_maestro in (89) then
es.stritemb
else case  when b.id_estatus_maestro in (90) then
'EXTRAVIADO'
else case  when b.id_estatus_maestro in (91) then
'DESINCORPORADO'
else case  when b.id_estatus_maestro in (618) then
'ROBADO'
else case  when b.id_estatus_maestro in (619) then
'REEMPLAZADO'
else
'SIN USUARIO'
end end end end end end end end as responsable,
es.stritema as estatus,
pi.strdescripcion as plan_internet,
ip.strdescripcion as ip,
mac.strdescripcion as macaddress,
wi.strdescripcion as wifi,
so.strdescripcion as sistema
from tblbienes b
inner join tblmaestros e on b.id_tipo_maestro=e.id_maestro and e.bolborrado=0
inner join tblmaestros g on b.id_gerencia_maestro=g.id_maestro and g.bolborrado=0
inner join tblmaestros es on b.id_estatus_maestro=es.id_maestro and es.bolborrado=0  
left join tbldetallebienes pi on pi.id_bienes=b.id_bienes and pi.id_caracteristica_maestro in (409,418,3094) and pi.bolborrado=0
left join tbldetallebienes ip on b.id_bienes=ip.id_bienes and ip.id_caracteristica_maestro in (410,419,3095) and ip.bolborrado=0
left join tbldetallebienes mac on b.id_bienes=mac.id_bienes and mac.id_caracteristica_maestro in (900,899,3096) and mac.bolborrado=0
left join tbldetallebienes wi on b.id_bienes=wi.id_bienes and wi.id_caracteristica_maestro in (948,2656,3098)  and wi.bolborrado=0
left join tbldetallebienes so on b.id_bienes=so.id_bienes and so.id_caracteristica_maestro in (3066,3067,3068)  and so.bolborrado=0 
where  b.bolborrado=0 and b.id_tipo_maestro in (106,107,3046) ";

if ($_REQUEST["gerencia"]!=""){
	$sql.=" and b.id_gerencia_maestro=".$_REQUEST["gerencia"];
}

if ($_REQUEST["estatus"]!=""){
    $sql.=" and b.id_estatus_maestro=".$_REQUEST["estatus"];
}



if ($_REQUEST["ip"]!=""){
	$sql.= " and ip.strdescripcion ilike '%".utf8_decode($_REQUEST["ip"])."%'";
}


if ($_REQUEST["macinterna"]!=""){
	$sql.=" and mac.strdescripcion ilike '%".utf8_decode($_REQUEST["macinterna"])."%'";
}


if ($_REQUEST["macexterna"]!=""){
	$sql.=" and wi.strdescripcion ilike '%".utf8_decode($_REQUEST["macexterna"])."%'";
}

if ($_REQUEST["internet"]!=""){
	$sql.=" and pi.strdescripcion ilike '%".utf8_decode($_REQUEST["internet"])."%'";
}
if ($_REQUEST["sistema"]!=""){
	$sql.=" and so.strdescripcion ilike '%".utf8_decode($_REQUEST["sistema"])."%'";
}

//echo $sql;die;
$conn= new Conexion();
$conn->abrirConexion();
$conn->sql=$sql;
$data=$conn->ejecutarSentencia(2);
$pdf->SetFillColor(255,255,255);		
$pdf->SetTextColor(0);
$pdf->SetDrawColor(0);
$pdf->SetLineWidth(.3);   
    
$pdf->SetWidths(array(17,20,60,35,22,20,20,20,20,20));
$pdf->SetAligns(array('L','R','C','R','R','C','R','R','R','R','R'));


if ($data){

	for ($i= 0; $i < count($data); $i++){
		

            $pdf->Row(array($data[$i]['equipo'],$data[$i]['strserial'],$data[$i]['gerencia'],$data[$i]['responsable'],$data[$i]['estatus'],$data[$i]['sistema'],$data[$i]['plan_internet'],$data[$i]['ip'],$data[$i]['macaddress'],$data[$i]['wifi']));	
			
	
		
		
	}

	

}else{

echo "<script>alert('No existen registros para mostrar');window.close(this);</script>";

}
$pdf->Output();		

	
		
?>

