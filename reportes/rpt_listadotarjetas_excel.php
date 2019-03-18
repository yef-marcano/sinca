<?
set_time_limit(300);
require_once "../modelo/clBienesModelo.php";
require_once "../comunes/writeexcel/class.writeexcel_workbookbig.inc.php";
require_once "../comunes/writeexcel/class.writeexcel_worksheet.inc.php";
$fname = tempnam("/tmp", "tarjetas.xls");
$libro = &new writeexcel_workbookbig($fname);
$hoja = &$libro->addworksheet('TARJETAS DE INTERNET');
$lo_encabezado= &$libro->addformat();
$lo_encabezado->set_bold();
$lo_encabezado->set_font("Arial");
$lo_encabezado->set_align('center');
$lo_encabezado->set_size('11');
$lo_titulo= &$libro->addformat();
$lo_titulo->set_text_wrap();
$lo_titulo->set_bold();
$lo_titulo->set_font("Arial");
$lo_titulo->set_align('center');
$lo_titulo->set_size('9');
$lo_titulo->set_fg_color('10');
$lo_titulo->set_color('9');
$lo_titulo->set_border('1');
$lo_titulo->set_valign('middle');
$lo_datacenter= &$libro->addformat();
$lo_datacenter->set_font("Arial");
$lo_datacenter->set_align('center');
$lo_datacenter->set_size('9');
$lo_datacenter->set_border('1');
$lo_datacenter->set_text_wrap();
$lo_datacenter->set_num_format('1');
$lo_dataleft= &$libro->addformat();
$lo_dataleft->set_font("Arial");
$lo_dataleft->set_align('left');
$lo_dataleft->set_size('9');
$lo_dataleft->set_border('1');
$lo_dataleft->set_text_wrap();
$lo_dataleft->set_num_format('1');
$lo_dataleft->set_text_justlast('1');


$hoja->set_column(0, 0, 7);
$hoja->set_column(1, 1, 20);
$hoja->set_column(2, 2, 30);
$hoja->set_column(3, 3, 30);
$hoja->set_column(4, 4, 30);
$hoja->set_column(5, 5, 30);
$hoja->set_column(6, 6, 20);
$hoja->set_column(7, 7, 20);
$hoja->set_column(8, 8, 30);
$hoja->set_column(9, 9, 30);
$hoja->set_column(10, 10, 30);
$hoja->set_column(11, 11, 15);
$hoja->set_column(12, 12, 30);
if ($_REQUEST['titulo']!=""){
    $hoja->write(0,4,utf8_decode($_REQUEST['titulo']),$lo_encabezado);
}else{
    $hoja->write(0,4,utf8_decode('LISTADO TARJETAS DE CONEXIÓN A INTERNET'),$lo_encabezado);
}
$hoja->write(2,0,utf8_decode('N°'),$lo_titulo);
$hoja->write(2,1,  utf8_decode('PROVEEDOR'),$lo_titulo);
$hoja->write(2,2,utf8_decode('LÍNEA'),$lo_titulo);
$hoja->write(2,3,utf8_decode('SIM'),$lo_titulo);
$hoja->write(2,4,'MARCA',$lo_titulo);
$hoja->write(2,5,'MODELO',$lo_titulo);
$hoja->write(2,6,utf8_decode('SERIAL'),$lo_titulo);
$hoja->write(2,7,utf8_decode('PLAN'),$lo_titulo);
$hoja->write(2,8,'CARGO',$lo_titulo);
$hoja->write(2,9,'GERENCIA',$lo_titulo);
$hoja->write(2,10,utf8_decode('RESPONSABLE'),$lo_titulo);
$hoja->write(2,11,utf8_decode('ESTATUS'),$lo_titulo);
$hoja->write(2,12,utf8_decode('OBSERVACIÓN'),$lo_titulo);


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
end as sim,g.stritemb as gerencia1
from tblbienes b, tblmaestros g,tblmaestros t,
tblmaestros mo,  tblmaestros m
where b.id_gerencia_maestro=g.id_maestro
and b.id_estatus_maestro=t.id_maestro
and b.id_marca_maestro=m.id_maestro
and b.id_modelo_maestro=mo.id_maestro and b.bolborrado=0 and g.bolborrado=0
and t.bolborrado=0 and mo.bolborrado=0  and m.bolborrado=0  and b.id_tipo_maestro=133 ";



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
if ($data){
     for ($i= 0; $i < count($data); $i++){
        $total++;
        $largo=strlen($data[$i]['strserial']);
        if ($largo>=18 && is_numeric($data[$i]['strserial']) ){
            $serial='#'.$data[$i]['strserial'];
        }else{
            $serial=$data[$i]['strserial'];
        }
        $largo=strlen(trim($data[$i]['sim']));
        if ($largo>=18 && is_numeric($data[$i]['sim']) ){
            $sim='#'.$data[$i]['sim'];
        }else{
            $sim=$data[$i]['sim'];
        }
        $hoja->write($i+3, 0,$total,$lo_dataleft);
        $hoja->write($i+3, 1,utf8_decode($data[$i]["proveedor"]),$lo_dataleft);
        $hoja->write($i+3, 2,utf8_decode($data[$i]["telefono"]),$lo_dataleft);
        $hoja->write($i+3, 3,utf8_decode($sim),$lo_dataleft);
        $hoja->write($i+3, 4,utf8_decode($data[$i]["marca"]),$lo_dataleft);
        $hoja->write($i+3, 5,utf8_decode($data[$i]["modelo"]),$lo_dataleft);
        $hoja->write($i+3, 6,utf8_decode($serial),$lo_dataleft);
        $hoja->write($i+3, 7,utf8_decode($data[$i]["plan"]),$lo_dataleft);
        $hoja->write($i+3, 8,utf8_decode($data[$i]["cargo"]),$lo_dataleft);
        $hoja->write($i+3, 9,utf8_decode($data[$i]["gerencia"]),$lo_dataleft);
        $hoja->write($i+3, 10,utf8_decode($data[$i]["responsable"]),$lo_dataleft);
        $hoja->write($i+3, 11,utf8_decode($data[$i]["estatus"]),$lo_dataleft);
        $hoja->write($i+3, 12,utf8_decode($data[$i]["memobservacion"]),$lo_dataleft);



    }
    $libro->close();

    header("Content-Type: application/x-msexcel; name=\"tarjetas.xls\"");
    header("Content-Disposition: inline; filename=\"tarjetas.xls\"");
    $fh=fopen($fname, "rb");
    fpassthru($fh);
    unlink($fname);
}else{
    echo "<script>alert('No existen registros para mostrar');window.close(this);</script>";
}


?>

