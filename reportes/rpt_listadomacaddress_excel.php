<?php
session_start();
require('../comunes/php/fpdf.php');
require_once "../modelo/clBienesModelo.php";
require_once "../modelo/clUsuarioModelo.php";
require_once "../modelo/clMaestroModelo.php";
header("Pragma: ");
header('Cache-control: ');
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Content-type: application/vnd.ms-excel");
header("Content-disposition: attachment; filename=macaddress.xls");

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

$html="<html>
    <head>
        <meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
        <style type='text/css'>
            .style1 {
                font-family:Arial, Helvetica, sans-serif;
                font-size:11px;
                color:#FFFFFF;
                font-weight:bold;
                background-color:red;
            }
            .style2 {
                font-family:Arial, Helvetica, sans-serif;
                font-size:10px;
                text-align:left;
            }
            .style3 {
                font-family:Arial, Helvetica, sans-serif;
                font-size:11px;
            }
            .style4 {
                background-image:url(../comunes/images/carta_horizontal.jpg);
            }
            .style9 {font-size: 14px; font-weight: bold; color: #000000; }
        </style>
    </head>
    <body style='font-size:'11px;'>
            <center class='style9'>";
            if ($_REQUEST['titulo']!=""){
                $html.="<strong>".$_REQUEST['titulo']."</strong>";
            }else{
                $html.="<strong>LISTADO DE MACADDRESS<br /></strong></center>";
            }
$html.="<br />";
$html.="<table width='100%' border='1'>";
$html.="<tr>";
$html.="<th class='style1'>N°</th>";
$html.="<th class='style1'>GERENCIA</th>";
$html.="<th class='style1'>RESPONSABLE</th>";
$html.="<th class='style1'>UBICACIÓN</th>";
$html.="<th class='style1'>EQUIPO</th>";
$html.="<th class='style1'>SERIAL</th>";
$html.="<th class='style1'>MACADDRESS LOCAL</th>";
$html.="<th class='style1'>WI-FI</th>";
$html.="<th class='style1'>SERIAL TARJETA INALÁMBRICA</th>";
$html.="<th class='style1'>MACADDRESS INALÁMBRICA</th>";

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
if ($data){
    for ($i= 0; $i < count($data); $i++){

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
        $total++;
        $html.="<tr>";
        $html.="<td class='style3' >".$total."</td>";
        $html.="<td class='style3' >".$data[$i]['gerencia']."</td>";
        $html.="<td class='style3' >".$data[$i]['responsable']."</td>";
        $html.="<td class='style3' >".$data[$i]['ubicacion']."</td>";
        $html.="<td class='style3' >".$data[$i]['tipo']."</td>";
        $html.="<td class='style3' >".$data[$i]['strserial']."</td>";
        $html.="<td class='style3' >".formato($data[$i]['macaddres'])."</td>";
        $html.="<td class='style3' >".formato($data[$i]['wifi'])."</td>";
        $html.="<td class='style3' >".$data[$i]['serinalambrica']."</td>";
        $html.="<td class='style3' >".formato($macaddress)."</td>";
        $html.="</tr>";
        
    }
    $html.="</table>";
    $html.="<br />";
    if ($_REQUEST['filtro']!=""){
        $html.="<div class='style3' align='justify' >Filtro Aplicado: ".$_REQUEST['filtro']."</div>";
    }

    print $html;
    

}else{
	echo "<script>alert('No existen registros para mostrar');window.close(this);</script>";
}
	
		
?>

