<?
set_time_limit(300);
require_once "../modelo/clBienesModelo.php";
require_once "../comunes/writeexcel/class.writeexcel_workbookbig.inc.php";
require_once "../comunes/writeexcel/class.writeexcel_worksheet.inc.php";
$fname = tempnam("/tmp", "equipos.xls");
$libro = &new writeexcel_workbookbig($fname);
$hoja = &$libro->addworksheet('EQUIPOS TELEMATICOS');
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
$hoja->set_column(11, 11, 30);
$hoja->set_column(12, 12, 15);
$hoja->set_column(13, 13, 30);
if ($_REQUEST['titulo']!=""){
    $hoja->write(0,4,utf8_decode($_REQUEST['titulo']),$lo_encabezado);
}else{
    $hoja->write(0,4,utf8_decode('LISTADO EQUIPOS TELEMÁTICOS'),$lo_encabezado);
}
$hoja->write(2,0,utf8_decode('N°'),$lo_titulo);
$hoja->write(2,1,'EQUIPO',$lo_titulo);
$hoja->write(2,2,utf8_decode('CATEGORÍA'),$lo_titulo);
$hoja->write(2,3,'MARCA',$lo_titulo);
$hoja->write(2,4,'MODELO',$lo_titulo);
$hoja->write(2,5,utf8_decode('SERIAL'),$lo_titulo);
$hoja->write(2,6,utf8_decode('GERENCIA'),$lo_titulo);
$hoja->write(2,7,'UNIDAD',$lo_titulo);
$hoja->write(2,8,'ESTATUS',$lo_titulo);
$hoja->write(2,9,'CEDULA',$lo_titulo);
$hoja->write(2,10,'RESPONSABLE',$lo_titulo);
$hoja->write(2,10,'CARGO',$lo_titulo);
$hoja->write(2,12,'PROVEEDOR',$lo_titulo);
$hoja->write(2,13,utf8_decode('UBICACIÓN'),$lo_titulo);
$hoja->write(2,14,utf8_decode('OBSERVACIÓN'),$lo_titulo);






$sql="select b.*, g.stritema as gerencia, c.stritema as categoria, 
		case when b.id_unidad_maestro>0 then
		(select '(' ||stritema||')' from tblmaestros where id_maestro=b.id_unidad_maestro and bolborrado=0)
		else
		''
		end as unidad,t.stritema as tipo, m.stritema as marca,
		case when b.id_responsable >0 and b.id_estatus_maestro in (86,87,88)  then
		(select  r.strnombre || ' ' || r.strapellido from tblusuario r where b.id_responsable=r.id_usuario and r.bolborrado=0)
		else case when b.id_responsable=0 and b.id_estatus_maestro in (87,88) and b.strnombres<>'' then
		b.strnombres
		else case  when b.id_responsable=0 and b.id_estatus_maestro in (86) then
		'DISPONIBLE'
		else case  when b.id_estatus_maestro in (89) then
		e.stritemb
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
		(select  c.stritema from  tblmaestros c, tblusuario r where b.id_responsable=r.id_usuario  and r.id_cargo_maestro=c.id_maestro and r.bolborrado=0)
		else case when b.id_responsable=0 and b.id_estatus_maestro in (87,88) and b.strnombres<>'' then
		b.strcargo
		else case  when b.id_responsable=0 and b.id_estatus_maestro in (86) then
		'DISPONIBLE'
		else case  when b.id_estatus_maestro in (89) then
		e.stritemb
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
		end end end end end end end end as cargo, 
		case when b.id_responsable >0 and b.id_estatus_maestro in (86,87,88)  then
		(select  r.strcedula from tblusuario r where b.id_responsable=r.id_usuario and r.bolborrado=0)
		else case when b.id_responsable=0 and b.id_estatus_maestro in (87,88) and b.strnombres<>'' then
		b.strcedula		
		else
		'N/A'
		end end as cedula,
		e.stritema as estatus, mo.stritema as modelo,to_char(b.dtmfechafactura,'DD/MM/YYYY') as dtmfecha,
		case when b.id_proveedor>0 then
		(select strnombre from  tblproveedor where id_proveedor=b.id_proveedor and bolborrado=0)
		else
		'SE DESCONOCE'
		end as proveedor, u.stritema as ubicacion 
		from tblbienes b, tblmaestros g, tblmaestros c, tblmaestros t, 
		tblmaestros m,tblmaestros e, tblmaestros mo, tblmaestros u 
		where b.id_gerencia_maestro=g.id_maestro 
		and b.id_categoria_maestro=c.id_maestro and b.id_tipo_maestro=t.id_maestro and b.id_ubicacion=u.id_maestro 
		and b.id_marca_maestro=m.id_maestro and b.id_estatus_maestro=e.id_maestro 
		and b.id_modelo_maestro=mo.id_maestro and b.bolborrado=0 and g.bolborrado=0 and c.bolborrado=0 
		and t.bolborrado=0 and m.bolborrado=0 and e.bolborrado=0 and mo.bolborrado=0  and u.bolborrado=0  ";
 		if ($_REQUEST['nombre']!=""){
			$sql.="AND b.strnombre LIKE '%".strtoupper($_REQUEST['nombre'])."%' ";
		}
 		if ($_REQUEST['serial']!=""){
			$sql.="AND b.strserial LIKE '%".strtoupper($_REQUEST['serial'])."%' ";
		}		
 		
		if ($_REQUEST['marca']!=""){
			$sql.="AND b.id_marca_maestro='".$_REQUEST['marca']."' ";
		}
 		if ($_REQUEST['modelo']!=""){
			$sql.="AND b.id_modelo_maestro='".$_REQUEST['modelo']."' ";
		}
 		if ($_REQUEST['categoria']!=""){
			$sql.="AND b.id_categoria_maestro='".$_REQUEST['categoria']."' ";
		}
 		if ($_REQUEST['tipo']!=""){
			$sql.="AND b.id_tipo_maestro in (".$_REQUEST['tipo'].") ";
		}
 		if ($_REQUEST['gerencia']!=""){
			$sql.="AND b.id_gerencia_maestro in (".$_REQUEST['gerencia'].") ";
		}
 		if ($_REQUEST['unidad']!=""){
			$sql.="AND b.id_unidad_maestro in (".$_REQUEST['unidad'].") ";
		}
 		if ($_REQUEST['responsable']!=""){
			$sql.="AND b.id_responsable='".strtoupper($_REQUEST['responsable'])."' ";
		}
 		if ($_REQUEST['garantia']!=""){
			$sql.="AND b.lnggarantia='".$_REQUEST['garantia']."' ";
		}
 		if ($_REQUEST['proveedor']!=""){
			$sql.="AND b.id_proveedor='".$_REQUEST['proveedor']."' ";
		}
 		if ($_REQUEST['estatus']!=""){
			$sql.="AND b.id_estatus_maestro in (".$_REQUEST['estatus'].") ";			
		}
 		if ($_REQUEST['ubicacion']!=""){
			$sql.="AND b.id_ubicacion in (".$_REQUEST['ubicacion'].") ";
		}
        $sql.=" ORDER BY t.stritema, e.stritema";
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
        $hoja->write($i+3, 0,$total,$lo_dataleft);
        $hoja->write($i+3, 1,$data[$i]["tipo"],$lo_dataleft);
        $hoja->write($i+3, 2,$data[$i]["categoria"],$lo_dataleft);
        $hoja->write($i+3, 3,$data[$i]["marca"],$lo_dataleft);
        $hoja->write($i+3, 4,$data[$i]["modelo"],$lo_dataleft);
        $hoja->write($i+3, 5,$serial,$lo_dataleft);
        $hoja->write($i+3, 6,$data[$i]["gerencia"],$lo_dataleft);
        $hoja->write($i+3, 7,$data[$i]["unidad"],$lo_dataleft);
        $hoja->write($i+3, 8,$data[$i]["estatus"],$lo_dataleft);
        $hoja->write($i+3, 9,$data[$i]["cedula"],$lo_dataleft);
        $hoja->write($i+3, 10,$data[$i]["responsable"],$lo_dataleft);
        $hoja->write($i+3, 11,$data[$i]["cargo"],$lo_dataleft);
        $hoja->write($i+3, 12,$data[$i]["proveedor"],$lo_dataleft);
        $hoja->write($i+3, 13,$data[$i]["ubicacion"],$lo_dataleft);
        $hoja->write($i+3, 14,$data[$i]["memobservacion"],$lo_dataleft);
       
       

    }
    $libro->close();

    header("Content-Type: application/x-msexcel; name=\"equipos.xls\"");
    header("Content-Disposition: inline; filename=\"equipos.xls\"");
    $fh=fopen($fname, "rb");
    fpassthru($fh);
    unlink($fname);
}else{
    echo "<script>alert('No existen registros para mostrar');window.close(this);</script>";
}

	
		
?>

