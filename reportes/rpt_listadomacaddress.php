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
		$this->Image('../comunes/images/carta.png',10,3,250,0);
		$this->SetX(12);
		$this->SetY(25);
		$this->SetFont('Arial','B',10);
		$this->Cell(0,10,utf8_decode('LISTADO DE MACADDRESS'),0,0,'C');
		$this->Ln(10);
		
	}

	function Footer()
	{
		//Go to 1.5 cm from bottom
		$this->SetY(-20);		
		$this->SetFont('Arial','',6);
		//Print centered page number
		
		
		if ($_REQUEST['filtro']!=""){
			$this->MultiCell(0,5,'Filtro Aplicado:'.utf8_decode($_REQUEST['filtro']),0,'J');
		}
		$this->Cell(232,10,utf8_decode('Generado a través del Sistema SICET en Fecha '.date('d/m/Y').' por el Usuario '.rtrim($_SESSION["strnombrefenix"]).' '.rtrim($_SESSION["strapellidofenix"]).'  |  Fuente: OSTI - '.date('Y').'  |  Licencia: GPL/GNU'),0,0,'L');
		$this->Cell(20,10,utf8_decode('Página '.$this->PageNo().'/{nb}'),0,0,'R');
	}


}
function formato ($palabra){
    
    if (strlen($palabra)>1){
        for ($i=0;$i<=strlen($palabra)-2;$i+=2){
            $arreglo[]=substr($palabra, $i, 2);
        }
        foreach($arreglo as $arre){
            $palabranew.=$arre.":";
        }
        $largo=strlen($palabranew)-1;
        $palabranew2=substr($palabranew, 0,$largo );
        return $palabranew2;
    }else{
        return $palabra;
    }
}
ob_end_clean();
$pdf=new PDF("L","mm","Letter");
$pdf->Open();
$pdf->AliasNbPages();
$pdf->SetMargins(10,10,10);
$pdf->SetFont('Arial','B',8);
$pdf->AddPage();
$bienes= new clBienesModelo();
$usuario= new clUsuarioModelo();

$sql="select b.strserial,b.strnombre, b.id_tipo_maestro,b.id_responsable,ti.stritemb as tipo,
case when b.id_unidad_maestro=814 then
(select  u.stritema || ' ' || a.stritemb  from tblusuario r, tblmaestros a, tblmaestros u where b.id_unidad_maestro=u.id_maestro and u.bolborrado=0 and b.id_responsable=r.id_usuario and r.id_cargo_maestro=a.id_maestro and a.bolborrado=0 and r.bolborrado=0)
else case when b.id_unidad_maestro>0 then
(select stritema from tblmaestros where id_maestro=b.id_unidad_maestro and bolborrado=0)
else
(select stritema from tblmaestros where id_maestro=b.id_gerencia_maestro and bolborrado=0)
end end as gerencia,
t.stritema as estatus, m.stritema as marca,
case when b.id_responsable >0 and b.id_estatus_maestro in (88)  then
(select  r.strnombre || ' ' || r.strapellido from tblusuario r where b.id_responsable=r.id_usuario and r.bolborrado=0)
else case when b.id_responsable=0 and b.id_estatus_maestro in (88) and b.strnombres<>'' then
b.strnombres
end end as responsable,
mo.stritema as modelo,
case when (select d.id_detallebienes from tbldetallebienes d where d.bolborrado=0 and d.id_bienes=b.id_bienes and d.id_caracteristica_maestro in (900,899))>0 then
	(select d.strdescripcion from tbldetallebienes d
	where d.bolborrado=0 and d.id_bienes=b.id_bienes and d.id_caracteristica_maestro in (899,900))
else

	'-'
end as macaddres,

case when (select d.id_detallebienes from tbldetallebienes d where d.bolborrado=0 and d.id_bienes=b.id_bienes and d.id_caracteristica_maestro in (948))>0 then
	(select d.strdescripcion from tbldetallebienes d
	where d.bolborrado=0 and d.id_bienes=b.id_bienes and d.id_caracteristica_maestro in (948))
else

	'-'
end as wifi,
case when (select d.id_detallebienes from tbldetallebienes d where d.bolborrado=0 and d.id_bienes=b.id_bienes and d.id_caracteristica_maestro in (949))>0 then
	(select d.strdescripcion from tbldetallebienes d
	where d.bolborrado=0 and d.id_bienes=b.id_bienes and d.id_caracteristica_maestro in (949))
else

	'-'
end as serinalambrica,


g.stritemb as gerencia1, u.stritema as ubicacion
from tblbienes b, tblmaestros g,tblmaestros t, tblmaestros u,
tblmaestros mo,  tblmaestros m, tblmaestros ti
where b.id_gerencia_maestro=g.id_maestro
and b.id_estatus_maestro=t.id_maestro
and b.id_marca_maestro=m.id_maestro
and b.id_modelo_maestro=mo.id_maestro and b.bolborrado=0 and g.bolborrado=0
and t.bolborrado=0 and mo.bolborrado=0  and m.bolborrado=0
and b.id_tipo_maestro=ti.id_maestro and ti.bolborrado=0
and b.id_tipo_maestro in (106,107) and b.id_estatus_maestro=88
and u.id_maestro=b.id_ubicacion and u.bolborrado=0  ";
 
 if ($_REQUEST['ubicacion']!=""){
	$sql.="AND b.id_ubicacion in (".$_REQUEST['ubicacion'].") ";
 } 
 if ($_REQUEST['gerencia']!=""){
			$sql.="AND b.id_gerencia_maestro in (".$_REQUEST['gerencia'].") ";
		}

$sql.=" order by gerencia, responsable, tipo";
$conn= new Conexion();
$conn->abrirConexion();
$conn->sql=$sql;
$data=$conn->ejecutarSentencia(2);       
$total=0;
$pdf->Cell(0, 10,utf8_decode($data[0]["gerencia"]), 0, 1, 'L');
$proveedor=$data[0]["gerencia"];
$responsable="";
if ($data){
	$pdf->SetFillColor(255,0,0);
	$pdf->SetTextColor(255,255,255);
	$pdf->SetDrawColor(0);
	$pdf->SetFont('Arial','B',6);
        $pdf->SetWidths(array(10,40,15,25,30,30,30,30,35));
	$pdf->SetAligns(array('C','C','C','C','C','C','C','C','C'));
	$pdf->Row(array(utf8_decode('N°'),'RESPONSABLE',utf8_decode('UBICACIÓN'),'EQUIPO','SERIAL','MACADDRESS-LOCAL','WI-FI','SERIAL TARJETA INALAMBRICA','MACADDRESS-INALAMBRICA'));
	
	$pdf->SetFont('Arial','',6);       
	$pdf->SetFillColor(255,255,255);
	$pdf->SetTextColor(0);
	$pdf->SetDrawColor(0);
	$pdf->SetLineWidth(0.3);
    
	for ($i= 0; $i < count($data); $i++){
		if ($data[$i]['responsable']=='DANADO') {
			$responsable="DAÑADO";
		}else if ($data[$i]['responsable']==" "){
			$responsable="SIN USUARIO";
		}else{			
			$responsable=utf8_decode($data[$i]['responsable']);
		}

                if ($data[$i]['id_tipo_maestro']=='107'){ //******PARA BUSCAR LAS TARJETA INALAMBRICA ***//
                    $sql="select b.strserial,b.strnombre, b.id_tipo_maestro,b.id_responsable,
                        case when b.id_unidad_maestro=814 then
                        (select  u.stritema || ' ' || a.stritemb  from tblusuario r, tblmaestros a, tblmaestros u where b.id_unidad_maestro=u.id_maestro and u.bolborrado=0 and b.id_responsable=r.id_usuario and r.id_cargo_maestro=a.id_maestro and a.bolborrado=0 and r.bolborrado=0)
                        else case when b.id_unidad_maestro>0 then
                        (select stritema from tblmaestros where id_maestro=b.id_unidad_maestro and bolborrado=0)
                        else
                        (select stritema from tblmaestros where id_maestro=b.id_gerencia_maestro and bolborrado=0)
                        end end as gerencia,
                        t.stritema as estatus, m.stritema as marca,
                        case when b.id_responsable >0   then
                        (select  r.strnombre || ' ' || r.strapellido from tblusuario r where b.id_responsable=r.id_usuario and r.bolborrado=0)
                        else case when b.id_responsable=0  and b.strnombres<>'' then
                        b.strnombres
                        end end as responsable,
                        mo.stritema as modelo,
                        case when (select d.id_detallebienes from tbldetallebienes d where d.bolborrado=0 and d.id_bienes=b.id_bienes and d.id_caracteristica_maestro in (924))>0 then
                                (select d.strdescripcion from tbldetallebienes d
                                where d.bolborrado=0 and d.id_bienes=b.id_bienes and d.id_caracteristica_maestro in (924))
                        else

                                '-'
                        end as macaddres,

                        g.stritemb as gerencia1, u.stritema as ubicacion
                        from tblbienes b, tblmaestros g,tblmaestros t, tblmaestros u,
                        tblmaestros mo,  tblmaestros m
                        where b.id_gerencia_maestro=g.id_maestro
                        and b.id_estatus_maestro=t.id_maestro
                        and b.id_marca_maestro=m.id_maestro
                        and b.id_modelo_maestro=mo.id_maestro and b.bolborrado=0 and g.bolborrado=0
                        and t.bolborrado=0 and mo.bolborrado=0  and m.bolborrado=0
                        and b.id_tipo_maestro in (142) 
                        and u.id_maestro=b.id_ubicacion and u.bolborrado=0 and b.strserial='".$data[$i]['serinalambrica']."' ";
                        /*if ($_REQUEST['ubicacion']!=""){
                            $sql.="AND b.id_ubicacion in (".$_REQUEST['ubicacion'].") ";
                        }
                        if ($_REQUEST['gerencia']!=""){
                            $sql.="AND b.id_gerencia_maestro in (".$_REQUEST['gerencia'].") ";
                        }*/
                        $conn1= new Conexion();
                        $conn1->abrirConexion();
                        $conn1->sql=$sql;
                        $data3=$conn1->ejecutarSentencia(2);
                        if ($data3){
                            $macaddress=$data3[0]['macaddres'];
                            //$macaddress="PASE X AQUI";
                            
                        }else{
                            $macaddress="-";
                            
                        }
                }else{
                    $macaddress="-";
                    
                }
			
		if ($proveedor!=$data[$i]["gerencia"]){
			$total=0;
                        $pdf->Ln();
			//$pdf->AddPage();
			$pdf->SetFont('Arial','B',8);
			$pdf->SetFillColor(255,255,255);
			$pdf->SetTextColor(0);
			$pdf->SetDrawColor(0);
			$pdf->SetLineWidth(0.3);
			$pdf->Cell(0, 10,utf8_decode($data[$i]["gerencia"]), 0, 1, 'L');
                        //$pdf->Ln();
			$pdf->SetFillColor(255,0,0);
			$pdf->SetTextColor(255,255,255);
			$pdf->SetDrawColor(0);
			$pdf->SetFont('Arial','B',6);
                        $pdf->SetWidths(array(10,40,15,25,30,30,30,30,35));
                        $pdf->SetAligns(array('C','C','C','C','C','C','C','C','C'));

                        $pdf->Row(array(utf8_decode('N°'),'RESPONSABLE',utf8_decode('UBICACIÓN'),'EQUIPO','SERIAL','MACADDRESS-LOCAL','WI-FI','SERIAL TARJETA INALAMBRICA','MACADDRESS-INALAMBRICA'));

			$pdf->SetFont('Arial','',6);       
			$pdf->SetFillColor(255,255,255);
			$pdf->SetTextColor(0);
			$pdf->SetDrawColor(0);
			$pdf->SetLineWidth(0.3);
                        $total++;
    			$pdf->Row(array($total,utf8_decode($data[$i]['responsable']),utf8_decode($data[$i]['ubicacion']),  utf8_decode($data[$i]['tipo']),utf8_decode($data[$i]['strserial']),formato(utf8_decode($data[$i]['macaddres'])),formato(utf8_decode($data[$i]['wifi'])),utf8_decode($data[$i]['serinalambrica']),formato($macaddress)));
			
		}
		else
		{
			$total++;
			$pdf->SetWidths(array(10,40,15,25,30,30,30,30,35));
			$pdf->SetFont('Arial','',6);       
			$pdf->SetFillColor(255,255,255);
			$pdf->SetTextColor(0);
			$pdf->SetDrawColor(0);
			$pdf->SetLineWidth(0.3);
			$pdf->Row(array($total,utf8_decode($data[$i]['responsable']),utf8_decode($data[$i]['ubicacion']),  utf8_decode($data[$i]['tipo']),utf8_decode($data[$i]['strserial']),formato(utf8_decode($data[$i]['macaddres'])),formato(utf8_decode($data[$i]['wifi'])),utf8_decode($data[$i]['serinalambrica']),formato($macaddress)));
		}
		$proveedor=$data[$i]["gerencia"];
	}
    
}else{
	echo "<script>alert('No existen registros para mostrar');window.close(this);</script>";
}


$pdf->Output();		

	
		
?>

