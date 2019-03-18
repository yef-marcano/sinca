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
		$this->Image('../comunes/images/cintillo.jpg',10,3,260,15);
		$this->SetX(12);
		$this->SetY(25);
		$this->SetFont('Arial','B',10);
		if ($_REQUEST['titulo']!=""){
			$this->Cell(0,10,strtoupper(utf8_decode($_REQUEST['titulo'])),0,0,'C');
		}else{
			$this->Cell(0,10,utf8_decode('LISTADO TARJETAS DE CONEXIÓN A INTERNET'),0,0,'C');
		}
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
ob_end_clean();
$pdf=new PDF("L","mm","Letter");
$pdf->Open();
$pdf->AliasNbPages();
$pdf->SetMargins(10,10,10);
$pdf->SetFont('Arial','B',8);
$pdf->AddPage();
$bienes= new clBienesModelo();
$usuario= new clUsuarioModelo();
if ($_REQUEST['linea']!=""){
	$sql="select b.*, 
case when b.id_unidad_maestro=814 then
(select  u.stritema || ' ' || a.stritemb  from tblusuario r, tblmaestros a, tblmaestros u where b.id_unidad_maestro=u.id_maestro and u.bolborrado=0 and b.id_responsable=r.id_usuario and r.id_cargo_maestro=a.id_maestro and a.bolborrado=0 and r.bolborrado=0)
else case when b.id_unidad_maestro>0 then
(select stritema from tblmaestros where id_maestro=b.id_unidad_maestro and bolborrado=0)
else
(select stritema from tblmaestros where id_maestro=b.id_gerencia_maestro and bolborrado=0)
end end as gerencia,
t.stritema as estatus, m.stritema as marca,
case when b.id_responsable >0 and b.id_estatus_maestro in (86,87,88)  then
(select  r.strnombre || ' ' || r.strapellido from tblusuario r where b.id_responsable=r.id_usuario and r.bolborrado=0)
else case when b.id_responsable=0 and b.id_estatus_maestro in (87,88) and b.strnombres<>'' then
b.strnombres
else case  when b.id_responsable=0 and b.id_estatus_maestro in (86) then
'DISPONIBLE'
else case  when b.id_estatus_maestro in (89) then
t.stritemb
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
case when b.id_responsable >0 and b.id_estatus_maestro in (86,87,88)  then
(select  stritemc  from tblusuario r, tblmaestros a where b.id_responsable=r.id_usuario and r.id_cargo_maestro=a.id_maestro and a.bolborrado=0 and r.bolborrado=0)
else case when b.id_responsable=0 and b.id_estatus_maestro in (87) then
b.strcargo
else case  when b.id_responsable=0 and b.id_estatus_maestro in (86) then
'-'
else
'-'
end end end as cargo,
mo.stritema as modelo,
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
end as telefono,
case when (select d.id_detallebienes from tbldetallebienes d where d.bolborrado=0 and d.id_bienes=b.id_bienes and d.id_caracteristica_maestro=934)>0 then
	(select d.strdescripcion from tbldetallebienes d
	where d.bolborrado=0 and d.id_bienes=b.id_bienes and d.id_caracteristica_maestro=934)
else

	'N/A'
end as sim,
case when (select d.id_detallebienes from tbldetallebienes d where d.bolborrado=0 and d.id_bienes=b.id_bienes and d.id_caracteristica_maestro in (1839,1988))>0 then
	(select d.strdescripcion from tbldetallebienes d
	where d.bolborrado=0 and d.id_bienes=b.id_bienes and d.id_caracteristica_maestro in (1839,1988))
else

	'N/A'
end as cuenta,g.stritemb as gerencia1
from tblbienes b, tblmaestros g,tblmaestros t, tbldetallebienes tl,
tblmaestros mo,  tblmaestros m
where b.id_gerencia_maestro=g.id_maestro 
and b.id_estatus_maestro=t.id_maestro
and b.id_marca_maestro=m.id_maestro
and b.id_bienes=tl.id_bienes
and tl.id_caracteristica_maestro=782
and b.id_modelo_maestro=mo.id_maestro and b.bolborrado=0 and g.bolborrado=0 
and t.bolborrado=0 and mo.bolborrado=0  and m.bolborrado=0  and b.id_tipo_maestro=133
and tl.strdescripcion LIKE '%".$_REQUEST['linea']."%'";
}else if ($_REQUEST['proveedor']!=""){
    $sql="select b.*,
case when b.id_unidad_maestro=814 then
(select  u.stritema || ' ' || a.stritemb  from tblusuario r, tblmaestros a, tblmaestros u where b.id_unidad_maestro=u.id_maestro and u.bolborrado=0 and b.id_responsable=r.id_usuario and r.id_cargo_maestro=a.id_maestro and a.bolborrado=0 and r.bolborrado=0)
else case when b.id_unidad_maestro>0 then
(select stritema from tblmaestros where id_maestro=b.id_unidad_maestro and bolborrado=0)
else
(select stritema from tblmaestros where id_maestro=b.id_gerencia_maestro and bolborrado=0)
end end as gerencia,
t.stritema as estatus, m.stritema as marca,
case when b.id_responsable >0 and b.id_estatus_maestro in (86,87,88)  then
(select  r.strnombre || ' ' || r.strapellido from tblusuario r where b.id_responsable=r.id_usuario and r.bolborrado=0)
else case when b.id_responsable=0 and b.id_estatus_maestro in (87,88) and b.strnombres<>'' then
b.strnombres
else case  when b.id_responsable=0 and b.id_estatus_maestro in (86) then
'DISPONIBLE'
else case  when b.id_estatus_maestro in (89) then
t.stritemb
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
case when b.id_responsable >0 and b.id_estatus_maestro in (86,87,88)  then
(select  stritemc  from tblusuario r, tblmaestros a where b.id_responsable=r.id_usuario and r.id_cargo_maestro=a.id_maestro and a.bolborrado=0 and r.bolborrado=0)
else case when b.id_responsable=0 and b.id_estatus_maestro in (87) then
b.strcargo
else case  when b.id_responsable=0 and b.id_estatus_maestro in (86) then
'-'
else
'-'
end end end as cargo,
mo.stritema as modelo,
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
end as telefono,case when (select d.id_detallebienes from tbldetallebienes d where d.bolborrado=0 and d.id_bienes=b.id_bienes and d.id_caracteristica_maestro=934)>0 then
	(select d.strdescripcion from tbldetallebienes d
	where d.bolborrado=0 and d.id_bienes=b.id_bienes and d.id_caracteristica_maestro=934)
else

	'N/A'
end as sim,
case when (select d.id_detallebienes from tbldetallebienes d where d.bolborrado=0 and d.id_bienes=b.id_bienes and d.id_caracteristica_maestro in (1839,1988))>0 then
	(select d.strdescripcion from tbldetallebienes d
	where d.bolborrado=0 and d.id_bienes=b.id_bienes and d.id_caracteristica_maestro in (1839,1988))
else

	'N/A'
end as cuenta,g.stritemb as gerencia1
from tblbienes b, tblmaestros g,tblmaestros t, tbldetallebienes tl,
tblmaestros mo,  tblmaestros m
where b.id_gerencia_maestro=g.id_maestro
and b.id_estatus_maestro=t.id_maestro
and b.id_marca_maestro=m.id_maestro
and b.id_bienes=tl.id_bienes
and tl.id_caracteristica_maestro=445 and b.id_bienes=tl.id_bienes and tl.bolborrado=0
and b.id_modelo_maestro=mo.id_maestro and b.bolborrado=0 and g.bolborrado=0
and t.bolborrado=0 and mo.bolborrado=0  and m.bolborrado=0  and b.id_tipo_maestro=133
and tl.strdescripcion LIKE '%".strtoupper($_REQUEST['proveedor'])."%'";
}else if ($_REQUEST["sim"]!=""){
    $sql="select b.*,
case when b.id_unidad_maestro=814 then
(select  u.stritema || ' ' || a.stritemb  from tblusuario r, tblmaestros a, tblmaestros u where b.id_unidad_maestro=u.id_maestro and u.bolborrado=0 and b.id_responsable=r.id_usuario and r.id_cargo_maestro=a.id_maestro and a.bolborrado=0 and r.bolborrado=0)
else case when b.id_unidad_maestro>0 then
(select stritema from tblmaestros where id_maestro=b.id_unidad_maestro and bolborrado=0)
else
(select stritema from tblmaestros where id_maestro=b.id_gerencia_maestro and bolborrado=0)
end end as gerencia,
t.stritema as estatus, m.stritema as marca,
case when b.id_responsable >0 and b.id_estatus_maestro in (86,87,88)  then
(select  r.strnombre || ' ' || r.strapellido from tblusuario r where b.id_responsable=r.id_usuario and r.bolborrado=0)
else case when b.id_responsable=0 and b.id_estatus_maestro in (87,88) and b.strnombres<>'' then
b.strnombres
else case  when b.id_responsable=0 and b.id_estatus_maestro in (86) then
'DISPONIBLE'
else case  when b.id_estatus_maestro in (89) then
t.stritemb
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
case when b.id_responsable >0 and b.id_estatus_maestro in (86,87,88)  then
(select  stritemc  from tblusuario r, tblmaestros a where b.id_responsable=r.id_usuario and r.id_cargo_maestro=a.id_maestro and a.bolborrado=0 and r.bolborrado=0)
else case when b.id_responsable=0 and b.id_estatus_maestro in (87) then
b.strcargo
else case  when b.id_responsable=0 and b.id_estatus_maestro in (86) then
'-'
else
'-'
end end end as cargo,
mo.stritema as modelo,
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
end as telefono,
case when (select d.id_detallebienes from tbldetallebienes d where d.bolborrado=0 and d.id_bienes=b.id_bienes and d.id_caracteristica_maestro=934)>0 then
	(select d.strdescripcion from tbldetallebienes d
	where d.bolborrado=0 and d.id_bienes=b.id_bienes and d.id_caracteristica_maestro=934)
else

	'N/A'
end as sim, 
case when (select d.id_detallebienes from tbldetallebienes d where d.bolborrado=0 and d.id_bienes=b.id_bienes and d.id_caracteristica_maestro in (1839,1988))>0 then
	(select d.strdescripcion from tbldetallebienes d
	where d.bolborrado=0 and d.id_bienes=b.id_bienes and d.id_caracteristica_maestro in (1839,1988))
else

	'N/A'
end as cuenta,
g.stritemb as gerencia1
from tblbienes b, tblmaestros g,tblmaestros t, tbldetallebienes tl,
tblmaestros mo,  tblmaestros m
where b.id_gerencia_maestro=g.id_maestro
and b.id_estatus_maestro=t.id_maestro
and b.id_marca_maestro=m.id_maestro
and b.id_bienes=tl.id_bienes
and tl.id_caracteristica_maestro=934
and b.id_modelo_maestro=mo.id_maestro and b.bolborrado=0 and g.bolborrado=0
and t.bolborrado=0 and mo.bolborrado=0  and m.bolborrado=0  and b.id_tipo_maestro=133
and tl.strdescripcion LIKE '%".strtoupper($_REQUEST['sim'])."%'";
}else if ($_REQUEST["cuenta"]!=""){
    $sql="select b.*,
case when b.id_unidad_maestro=814 then
(select  u.stritema || ' ' || a.stritemb  from tblusuario r, tblmaestros a, tblmaestros u where b.id_unidad_maestro=u.id_maestro and u.bolborrado=0 and b.id_responsable=r.id_usuario and r.id_cargo_maestro=a.id_maestro and a.bolborrado=0 and r.bolborrado=0)
else case when b.id_unidad_maestro>0 then
(select stritema from tblmaestros where id_maestro=b.id_unidad_maestro and bolborrado=0)
else
(select stritema from tblmaestros where id_maestro=b.id_gerencia_maestro and bolborrado=0)
end end as gerencia,
t.stritema as estatus, m.stritema as marca,
case when b.id_responsable >0 and b.id_estatus_maestro in (86,87,88)  then
(select  r.strnombre || ' ' || r.strapellido from tblusuario r where b.id_responsable=r.id_usuario and r.bolborrado=0)
else case when b.id_responsable=0 and b.id_estatus_maestro in (87,88) and b.strnombres<>'' then
b.strnombres
else case  when b.id_responsable=0 and b.id_estatus_maestro in (86) then
'DISPONIBLE'
else case  when b.id_estatus_maestro in (89) then
t.stritemb
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
case when b.id_responsable >0 and b.id_estatus_maestro in (86,87,88)  then
(select  stritemc  from tblusuario r, tblmaestros a where b.id_responsable=r.id_usuario and r.id_cargo_maestro=a.id_maestro and a.bolborrado=0 and r.bolborrado=0)
else case when b.id_responsable=0 and b.id_estatus_maestro in (87) then
b.strcargo
else case  when b.id_responsable=0 and b.id_estatus_maestro in (86) then
'-'
else
'-'
end end end as cargo,
mo.stritema as modelo,
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
end as telefono,
case when (select d.id_detallebienes from tbldetallebienes d where d.bolborrado=0 and d.id_bienes=b.id_bienes and d.id_caracteristica_maestro=934)>0 then
	(select d.strdescripcion from tbldetallebienes d
	where d.bolborrado=0 and d.id_bienes=b.id_bienes and d.id_caracteristica_maestro=934)
else

	'N/A'
end as sim, 
case when (select d.id_detallebienes from tbldetallebienes d where d.bolborrado=0 and d.id_bienes=b.id_bienes and d.id_caracteristica_maestro in (1839,1988))>0 then
	(select d.strdescripcion from tbldetallebienes d
	where d.bolborrado=0 and d.id_bienes=b.id_bienes and d.id_caracteristica_maestro in (1839,1988))
else

	'N/A'
end as cuenta,
g.stritemb as gerencia1
from tblbienes b, tblmaestros g,tblmaestros t, tbldetallebienes tl,
tblmaestros mo,  tblmaestros m
where b.id_gerencia_maestro=g.id_maestro
and b.id_estatus_maestro=t.id_maestro
and b.id_marca_maestro=m.id_maestro
and b.id_bienes=tl.id_bienes
and tl.id_caracteristica_maestro in (1839,1988)
and b.id_modelo_maestro=mo.id_maestro and b.bolborrado=0 and g.bolborrado=0
and t.bolborrado=0 and mo.bolborrado=0  and m.bolborrado=0  and b.id_tipo_maestro=133
and tl.strdescripcion LIKE '%".strtoupper($_REQUEST['cuenta'])."%'";
    
}else{
	$sql="select b.*, 
case when b.id_unidad_maestro=814 then
(select  u.stritema || ' ' || a.stritemb  from tblusuario r, tblmaestros a, tblmaestros u where b.id_unidad_maestro=u.id_maestro and u.bolborrado=0 and b.id_responsable=r.id_usuario and r.id_cargo_maestro=a.id_maestro and a.bolborrado=0 and r.bolborrado=0)
else case when b.id_unidad_maestro>0 then
(select stritema from tblmaestros where id_maestro=b.id_unidad_maestro and bolborrado=0)
else
(select stritema from tblmaestros where id_maestro=b.id_gerencia_maestro and bolborrado=0)
end end as gerencia,
t.stritema as estatus, m.stritema as marca,
case when b.id_responsable >0 and b.id_estatus_maestro in (86,87,88)  then
(select  r.strnombre || ' ' || r.strapellido from tblusuario r where b.id_responsable=r.id_usuario and r.bolborrado=0)
else case when b.id_responsable=0 and b.id_estatus_maestro in (87,88) and b.strnombres<>'' then
b.strnombres
else case  when b.id_responsable=0 and b.id_estatus_maestro in (86) then
'DISPONIBLE'
else case  when b.id_estatus_maestro in (89) then
t.stritemb
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
case when b.id_responsable >0 and b.id_estatus_maestro in (86,87,88)  then
(select  stritemc  from tblusuario r, tblmaestros a where b.id_responsable=r.id_usuario and r.id_cargo_maestro=a.id_maestro and a.bolborrado=0 and r.bolborrado=0)
else case when b.id_responsable=0 and b.id_estatus_maestro in (87) then
b.strcargo
else case  when b.id_responsable=0 and b.id_estatus_maestro in (86) then
'-'
else
'-'
end end end as cargo,
mo.stritema as modelo,
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
end as telefono,
case when (select d.id_detallebienes from tbldetallebienes d where d.bolborrado=0 and d.id_bienes=b.id_bienes and d.id_caracteristica_maestro=934)>0 then
	(select d.strdescripcion from tbldetallebienes d
	where d.bolborrado=0 and d.id_bienes=b.id_bienes and d.id_caracteristica_maestro=934)
else

	'N/A'
end as sim,
case when (select d.id_detallebienes from tbldetallebienes d where d.bolborrado=0 and d.id_bienes=b.id_bienes and d.id_caracteristica_maestro in (1839,1988))>0 then
	(select d.strdescripcion from tbldetallebienes d
	where d.bolborrado=0 and d.id_bienes=b.id_bienes and d.id_caracteristica_maestro in (1839,1988))
else

	'N/A'
end as cuenta,
g.stritemb as gerencia1
from tblbienes b, tblmaestros g,tblmaestros t, 
tblmaestros mo,  tblmaestros m
where b.id_gerencia_maestro=g.id_maestro 
and b.id_estatus_maestro=t.id_maestro
and b.id_marca_maestro=m.id_maestro
and b.id_modelo_maestro=mo.id_maestro and b.bolborrado=0 and g.bolborrado=0 
and t.bolborrado=0 and mo.bolborrado=0  and m.bolborrado=0  and b.id_tipo_maestro=133 ";
	
}

 if ($_REQUEST['estatus']!=""){
	$sql.="AND b.id_estatus_maestro in (".$_REQUEST['estatus'].") ";			
 }
 if ($_REQUEST['ubicacion']!=""){
	$sql.="AND b.id_ubicacion in (".$_REQUEST['ubicacion'].") ";
 } 
 if ($_REQUEST['gerencia']!=""){
			$sql.="AND b.id_gerencia_maestro in (".$_REQUEST['gerencia'].") ";
		}
if ($_REQUEST['unidad']!=""){
			$sql.="AND b.id_unidad_maestro in (".$_REQUEST['unidad'].") ";
		}
$sql.=" order by proveedor,cargo, gerencia1, gerencia, estatus";
$conn= new Conexion();
$conn->abrirConexion();
$conn->sql=$sql;
$data=$conn->ejecutarSentencia(2);       
$total=0;
$pdf->Cell(0, 10,'Proveedor: '.utf8_decode($data[0]["proveedor"]), 0, 1, 'L');     
$proveedor=$data[0]["proveedor"];   
$responsable="";
$nroCampos=split(",",$_REQUEST['campo']);
$textColumnas=split(",",$_REQUEST['nombreCampo']);
$campos=count($nroCampos);
$ancho='260'/$campos;

for ($i= 0; $i < count($nroCampos); $i++){
	$anchocolumnas[]=$ancho;
	$alincolumnas[]="C";
}


    

if ($data){
	$pdf->SetFillColor(255,0,0);
	$pdf->SetTextColor(255,255,255);
	$pdf->SetDrawColor(0);
	$pdf->SetFont('Arial','B',6);
    $pdf->SetWidths($anchocolumnas);
	$pdf->SetAligns($alincolumnas);
	$pdf->Row($textColumnas);    
	
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
			
		if ($proveedor!=$data[$i]["proveedor"]){
			$pdf->SetWidths(array(260));
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
			$pdf->SetFont('Arial','B',6);
		    $pdf->SetWidths($anchocolumnas);
			$pdf->SetAligns($alincolumnas);
			$pdf->Row($textColumnas);
			$pdf->SetFont('Arial','',6);       
			$pdf->SetFillColor(255,255,255);
			$pdf->SetTextColor(0);
			$pdf->SetDrawColor(0);
			$pdf->SetLineWidth(0.3);
			switch ($campos) {
    			case 1:
    				$pdf->Row(array(utf8_decode($data[$i][$nroCampos[0]])));
    				break;
    			case 2:
    				$pdf->Row(array(utf8_decode($data[$i][$nroCampos[0]]),utf8_decode($data[$i][$nroCampos[1]])));
    				
    				break;
    			case 3:
    				$pdf->Row(array(utf8_decode($data[$i][$nroCampos[0]]),utf8_decode($data[$i][$nroCampos[1]]),utf8_decode($data[$i][$nroCampos[2]])));
    				break;
    			case 4:
    				$pdf->Row(array(utf8_decode($data[$i][$nroCampos[0]]),utf8_decode($data[$i][$nroCampos[1]]),utf8_decode($data[$i][$nroCampos[2]]),utf8_decode($data[$i][$nroCampos[3]])));
    				break;
    			case 5:
    				$pdf->Row(array(utf8_decode($data[$i][$nroCampos[0]]),utf8_decode($data[$i][$nroCampos[1]]),utf8_decode($data[$i][$nroCampos[2]]),utf8_decode($data[$i][$nroCampos[3]]),utf8_decode($data[$i][$nroCampos[4]])));
    				break;
    			case 6:
    				$pdf->Row(array(utf8_decode($data[$i][$nroCampos[0]]),utf8_decode($data[$i][$nroCampos[1]]),utf8_decode($data[$i][$nroCampos[2]]),utf8_decode($data[$i][$nroCampos[3]]),utf8_decode($data[$i][$nroCampos[4]]),utf8_decode($data[$i][$nroCampos[5]])));
    				break;
    			case 7:
    				$pdf->Row(array(utf8_decode($data[$i][$nroCampos[0]]),utf8_decode($data[$i][$nroCampos[1]]),utf8_decode($data[$i][$nroCampos[2]]),utf8_decode($data[$i][$nroCampos[3]]),utf8_decode($data[$i][$nroCampos[4]]),utf8_decode($data[$i][$nroCampos[5]]),utf8_decode($data[$i][$nroCampos[6]])));
    				break;
    			case 8:
    				$pdf->Row(array(utf8_decode($data[$i][$nroCampos[0]]),utf8_decode($data[$i][$nroCampos[1]]),utf8_decode($data[$i][$nroCampos[2]]),utf8_decode($data[$i][$nroCampos[3]]),utf8_decode($data[$i][$nroCampos[4]]),utf8_decode($data[$i][$nroCampos[5]]),utf8_decode($data[$i][$nroCampos[6]]),utf8_decode($data[$i][$nroCampos[7]])));
    				break;
    			case 9:
    				$pdf->Row(array(utf8_decode($data[$i][$nroCampos[0]]),utf8_decode($data[$i][$nroCampos[1]]),utf8_decode($data[$i][$nroCampos[2]]),utf8_decode($data[$i][$nroCampos[3]]),utf8_decode($data[$i][$nroCampos[4]]),utf8_decode($data[$i][$nroCampos[5]]),utf8_decode($data[$i][$nroCampos[6]]),utf8_decode($data[$i][$nroCampos[7]]),utf8_decode($data[$i][$nroCampos[8]])));
    				break;
    			case 10:
    				$pdf->Row(array(utf8_decode($data[$i][$nroCampos[0]]),utf8_decode($data[$i][$nroCampos[1]]),utf8_decode($data[$i][$nroCampos[2]]),utf8_decode($data[$i][$nroCampos[3]]),utf8_decode($data[$i][$nroCampos[4]]),utf8_decode($data[$i][$nroCampos[5]]),utf8_decode($data[$i][$nroCampos[6]]),utf8_decode($data[$i][$nroCampos[7]]),utf8_decode($data[$i][$nroCampos[8]]),utf8_decode($data[$i][$nroCampos[9]])));
    				break;
    		}
			$total++;
		}
		else
		{
			$total++;
			$pdf->SetWidths($anchocolumnas);		
			$pdf->SetFont('Arial','',6);       
			$pdf->SetFillColor(255,255,255);
			$pdf->SetTextColor(0);
			$pdf->SetDrawColor(0);
			$pdf->SetLineWidth(0.3);
			switch ($campos) {
    			case 1:
    				$pdf->Row(array(utf8_decode($data[$i][$nroCampos[0]])));
    				break;
    			case 2:
    				$pdf->Row(array(utf8_decode($data[$i][$nroCampos[0]]),utf8_decode($data[$i][$nroCampos[1]])));
    				
    				break;
    			case 3:
    				$pdf->Row(array(utf8_decode($data[$i][$nroCampos[0]]),utf8_decode($data[$i][$nroCampos[1]]),utf8_decode($data[$i][$nroCampos[2]])));
    				break;
    			case 4:
    				$pdf->Row(array(utf8_decode($data[$i][$nroCampos[0]]),utf8_decode($data[$i][$nroCampos[1]]),utf8_decode($data[$i][$nroCampos[2]]),utf8_decode($data[$i][$nroCampos[3]])));
    				break;
    			case 5:
    				$pdf->Row(array(utf8_decode($data[$i][$nroCampos[0]]),utf8_decode($data[$i][$nroCampos[1]]),utf8_decode($data[$i][$nroCampos[2]]),utf8_decode($data[$i][$nroCampos[3]]),utf8_decode($data[$i][$nroCampos[4]])));
    				break;
    			case 6:
    				$pdf->Row(array(utf8_decode($data[$i][$nroCampos[0]]),utf8_decode($data[$i][$nroCampos[1]]),utf8_decode($data[$i][$nroCampos[2]]),utf8_decode($data[$i][$nroCampos[3]]),utf8_decode($data[$i][$nroCampos[4]]),utf8_decode($data[$i][$nroCampos[5]])));
    				break;
    			case 7:
    				$pdf->Row(array(utf8_decode($data[$i][$nroCampos[0]]),utf8_decode($data[$i][$nroCampos[1]]),utf8_decode($data[$i][$nroCampos[2]]),utf8_decode($data[$i][$nroCampos[3]]),utf8_decode($data[$i][$nroCampos[4]]),utf8_decode($data[$i][$nroCampos[5]]),utf8_decode($data[$i][$nroCampos[6]])));
    				break;
    			case 8:
    				$pdf->Row(array(utf8_decode($data[$i][$nroCampos[0]]),utf8_decode($data[$i][$nroCampos[1]]),utf8_decode($data[$i][$nroCampos[2]]),utf8_decode($data[$i][$nroCampos[3]]),utf8_decode($data[$i][$nroCampos[4]]),utf8_decode($data[$i][$nroCampos[5]]),utf8_decode($data[$i][$nroCampos[6]]),utf8_decode($data[$i][$nroCampos[7]])));
    				break;
    			case 9:
    				$pdf->Row(array(utf8_decode($data[$i][$nroCampos[0]]),utf8_decode($data[$i][$nroCampos[1]]),utf8_decode($data[$i][$nroCampos[2]]),utf8_decode($data[$i][$nroCampos[3]]),utf8_decode($data[$i][$nroCampos[4]]),utf8_decode($data[$i][$nroCampos[5]]),utf8_decode($data[$i][$nroCampos[6]]),utf8_decode($data[$i][$nroCampos[7]]),utf8_decode($data[$i][$nroCampos[8]])));
    				break;
    			case 10:
    				$pdf->Row(array(utf8_decode($data[$i][$nroCampos[0]]),utf8_decode($data[$i][$nroCampos[1]]),utf8_decode($data[$i][$nroCampos[2]]),utf8_decode($data[$i][$nroCampos[3]]),utf8_decode($data[$i][$nroCampos[4]]),utf8_decode($data[$i][$nroCampos[5]]),utf8_decode($data[$i][$nroCampos[6]]),utf8_decode($data[$i][$nroCampos[7]]),utf8_decode($data[$i][$nroCampos[8]]),utf8_decode($data[$i][$nroCampos[9]])));
    				break;
	    	}
		}
		$proveedor=$data[$i]["proveedor"];
	}
	$pdf->SetWidths(array(260));
    $pdf->SetAligns(array('R'));
    $pdf->Row(array('Total de Tarjetas '.$total));
    
    if ($_REQUEST['linea']=="" && $_REQUEST['sim']=="" ){
        if ($_REQUEST['proveedor']==""){
                $sql="select count(b.*),
            t.stritema as estatus,b.id_estatus_maestro,
            case when (select d.id_detallebienes from tbldetallebienes d where d.bolborrado=0 and d.id_bienes=b.id_bienes and d.id_caracteristica_maestro=445)>0 then
                    (select d.strdescripcion from tbldetallebienes d
                    where d.bolborrado=0 and d.id_bienes=b.id_bienes and d.id_caracteristica_maestro=445)
            else

                    'SE DESCONOCE'
            end as proveedor


            from tblbienes b,tblmaestros t
            where b.id_estatus_maestro=t.id_maestro
            and b.bolborrado=0 and t.bolborrado=0
            and b.id_tipo_maestro=133 ";
        }else{
            $sql="select count(b.*),
            t.stritema as estatus,b.id_estatus_maestro,
            case when (select d.id_detallebienes from tbldetallebienes d where d.bolborrado=0 and d.id_bienes=b.id_bienes and d.id_caracteristica_maestro=445)>0 then
                    (select d.strdescripcion from tbldetallebienes d
                    where d.bolborrado=0 and d.id_bienes=b.id_bienes and d.id_caracteristica_maestro=445)
            else

                    'SE DESCONOCE'
            end as proveedor


            from tblbienes b,tblmaestros t, tbldetallebienes tl
            where b.id_estatus_maestro=t.id_maestro
            and b.bolborrado=0 and t.bolborrado=0 and b.id_bienes=tl.id_bienes and tl.bolborrado=0
            and b.id_tipo_maestro=133 and tl.id_caracteristica_maestro=445 and tl.strdescripcion LIKE '%".strtoupper($_REQUEST['proveedor'])."%' ";
        }
	if ($_REQUEST['estatus']!=""){
		$sql.="AND b.id_estatus_maestro in (".$_REQUEST['estatus'].") ";			
	 }
	 if ($_REQUEST['ubicacion']!=""){
		$sql.="AND b.id_ubicacion in (".$_REQUEST['ubicacion'].") ";
	 }   
	if ($_REQUEST['gerencia']!=""){
				$sql.="AND b.id_gerencia_maestro in (".$_REQUEST['gerencia'].") ";
			}
    if ($_REQUEST['unidad']!=""){
			$sql.="AND b.id_unidad_maestro in (".$_REQUEST['unidad'].") ";
		}
	$sql.=" group by proveedor, estatus, b.id_estatus_maestro order by proveedor, estatus";
		$conn->sql=$sql;		
				$data1=$conn->ejecutarSentencia(2); 
				$pdf->AddPage();
				$pdf->SetFont('Arial','B',8);	
				$pdf->Cell(0, 10,'CONSOLIDADO GENERAL', 0, 1, 'L'); 
				$pdf->SetFillColor(255,0,0);		
				$pdf->SetTextColor(255,255,255);
				$pdf->SetDrawColor(0);
				$pdf->SetLineWidth(.3);       
				$pdf->SetWidths(array(50,20,20,20,20,20,25,20,25,25));
		        $pdf->SetAligns(array('C','C','C','C','C','C','C','C','C','C'));
				$pdf->Row(array('Proveedor','Disponible','Asignado',utf8_decode('Préstamo'),  utf8_decode('Dañado'),'Extraviado','Desincorporado','Robado','Reemplazado','Total'));
				$pdf->SetFont('Arial','',8);	
				
				$pdf->SetFillColor(255,255,255);		
				$pdf->SetTextColor(0);
				$pdf->SetDrawColor(0);
				$pdf->SetLineWidth(.3);   
				    
				$pdf->SetWidths(array(50,20,20,20,20,20,25,20,25,25));
		        $pdf->SetAligns(array('L','R','R','R','R','R','R','R','R','R'));
				if ($data1){	
					$asignado=0;
					$danado=0;
					$extraviado=0;
					$disponible=0;
					$prestamo=0;
					$reemplazado=0;
					$robado=0;
					$desincorporado=0;				
					$totalc=0;
					$equipo=$data1[0]['proveedor'];
					$tdis=0;
					$tpres=0;
					$tasig=0;
					$tdan=0;
					$text=0;
					$tdes=0;
					$trob=0;
					$tree=0;
					$ttotalc=0;
					for ($j= 0; $j < count($data1); $j++){
						if ($equipo==$data1[$j]['proveedor']){			
							if ($data1[$j]['id_estatus_maestro']=='86'){
								$disponible=$data1[$j]['count'];
								$tdis+=$disponible;				
							}
							if ($data1[$j]['id_estatus_maestro']=='87'){
								$prestamo=$data1[$j]['count'];
								$tpres+=$data1[$j]['count'];				
							}
							if ($data1[$j]['id_estatus_maestro']=='88'){
								$asignado=$data1[$j]['count'];
								$tasig+=$data1[$j]['count'];				
							}
							if ($data1[$j]['id_estatus_maestro']=='89'){
								$danado=$data1[$j]['count'];
								$tdan+=$data1[$j]['count'];				
							}
							if ($data1[$j]['id_estatus_maestro']=='90'){
								$extraviado=$data1[$j]['count'];
								$text+=$data1[$j]['count'];				
							}
							if ($data1[$j]['id_estatus_maestro']=='91'){
								$desincorporado=$data1[$j]['count'];
								$tdes+=$data1[$j]['count'];				
							}
							if ($data1[$j]['id_estatus_maestro']=='618'){
								$robado=$data1[$j]['count'];
								$trob+=$data1[$j]['count'];				
							}
							if ($data1[$j]['id_estatus_maestro']=='619'){
								$reemplazado=$data1[$j]['count'];
								$tree+=$data1[$j]['count'];				
							}
						}else{
							$totalc=$asignado + $danado + $extraviado + $disponible + $prestamo + $desincorporado + $robado + $reemplazado;
							$ttotalc+=$totalc;
							$pdf->Row(array($equipo,$disponible,$asignado,$prestamo,$danado,$extraviado,$desincorporado,$robado, $reemplazado,$totalc));	
							
							
							$asignado=0;
							$danado=0;
							$extraviado=0;
							$disponible=0;
							$prestamo=0;
							$reemplazado=0;
							$robado=0;
							$desincorporado=0;				
							$totalc=0;
							$equipo=$data1[$j]['proveedor'];
							if ($data1[$j]['id_estatus_maestro']=='86'){
								$disponible=$data1[$j]['count'];
								$tdis+=$disponible;				
							}
							if ($data1[$j]['id_estatus_maestro']=='87'){
								$prestamo=$data1[$j]['count'];
								$tpres+=$data1[$j]['count'];				
							}
							if ($data1[$j]['id_estatus_maestro']=='88'){
								$asignado=$data1[$j]['count'];
								$tasig+=$data1[$j]['count'];				
							}
							if ($data1[$j]['id_estatus_maestro']=='89'){
								$danado=$data1[$j]['count'];
								$tdan+=$data1[$j]['count'];				
							}
							if ($data1[$j]['id_estatus_maestro']=='90'){
								$extraviado=$data1[$j]['count'];
								$text+=$data1[$j]['count'];				
							}
							if ($data1[$j]['id_estatus_maestro']=='91'){
								$desincorporado=$data1[$j]['count'];
								$tdes+=$data1[$j]['count'];				
							}
							if ($data1[$j]['id_estatus_maestro']=='618'){
								$robado=$data1[$j]['count'];
								$trob+=$data1[$j]['count'];				
							}
							if ($data1[$j]['id_estatus_maestro']=='619'){
								$reemplazado=$data1[$j]['count'];
								$tree+=$data1[$j]['count'];				
							}
						}
						
					}
					$totalc=$asignado + $danado + $extraviado + $disponible + $prestamo + $desincorporado + $robado + $reemplazado;
					$ttotalc+=$totalc;
					$pdf->Row(array($equipo,$disponible,$asignado,$prestamo,$danado,$extraviado,$desincorporado,$robado, $reemplazado,$totalc));	
					$pdf->Row(array('TOTAL',$tdis,$tasig,$tpres,$tdan,$text,$tdes,$trob,$tree,$ttotalc));
				}
    }
            
    
    
    
    
    
	
    
    
}else{    
    echo "<script>alert('No existen registros para mostrar');window.close(this);</script>";
}


$pdf->Output();		

	
		
?>

