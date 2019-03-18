<?php
include_once(dirname(dirname(__FILE__)) . '/config/conexion.php');
include("../common/php/class.excel.writer.php");
	$xls = new ExcelWriter();
	$xls_int = array('type'=>'int','border'=>'000000');
	$xls_date = array('type'=>'date','border'=>'000000');
	$xls_normal = array('border'=>'000000');
	
$var=$_REQUEST['var'];
$campo = explode("|", $var);
$campos=$campo[0];
$filtros=$campo[1];
if($campo[0]==""){
    $parte1="select t.* from ";
    
}else{
    $parte1="select $campos from ";
}

$table="(SELECT es.descripcion as estado, mu.descripcion as municipio, pa.descripcion as parroquia, se.descripcion as sector, u.comunidad, u.descripcion as urbanismo, t.descripcion as torre, 
a.descripcion as apartamento, 
case 
  when a.situacion_legal='0' then 'NO ASIGNADO'
  when a.situacion_legal='1' then 'ASIGNADO'
  when a.situacion_legal='2' then 'VENDIDO'
  when a.situacion_legal='3' then 'PERMUTADO'
  when a.situacion_legal='4' then 'ALQUILADO'
  when a.situacion_legal='5' then 'ADJUDICADO NO RESIDE EN EL APARTAMENTO'
  when a.situacion_legal='6' then 'CEDIDO'
  when a.situacion_legal='7' then 'TRASPASADO'
else
'NO ASIGNADO'
end as situacion_legal,

case 
  when p.nacionalidad='V' then 'VENEZOLANO(A)'
  when p.nacionalidad='E' then 'EXTRANJERO(A)'
  when p.nacionalidad='P' then 'PASAPORTE EXTRANJERO'
  when p.nacionalidad='S' then 'SIN DOCUMENTO'
  when p.nacionalidad='I' then 'NIÑO(A) NO CEDULADO(A)'
end as nacionalidad, p.cedula, p.primer_nombre, p.segundo_nombre, p.primer_apellido, p.segundo_apellido, to_char(p.fecha_nac,'DD/MM/YYYY') as fecha_nac, date_part('year',age( p.fecha_nac)) AS edad,
case
  when p.estado_civil='S' then 'SOLTERO(A)'
  when p.estado_civil='C' then 'CASADO(A)'
  when p.estado_civil='D' then 'DIVORCIADO(A)'
  when p.estado_civil='V' then 'VIUDO(A)'
  when p.estado_civil='X' then 'CONCUBINO(A)'
end as estado_civil, 
case when p.sexo='F' then 'FEMENINO' else 'MASCULINO' end as sexo,
case when p.embarazada='0' then 'NO' else 'SI' end as embarazada, p.semanas_embarazo,
case when p.posee_huella='0' then 'NO' else 'SI' end as posee_huella,
case when p.id_causal is not null then ca.descripcion else 'NINGUNA' end as causal,
case when p.estatus_foto='0' then 'SI' else 'NO' end as estatus_foto,
case when p.id_jefe_familia is not null then j.nacionalidad || '-' || j.cedula || ' ' || j.primer_nombre || ' ' || j.primer_apellido else 'JEFE DE FAMILIA' end as jefe_familia,
case when p.id_parentesco is not null then par.descripcion else 'NINGUNA' end as parentesco,
p.tel_celular, p.tel_local,p.email,
case when p.posee_discapacidad='0' then 'NO' else 'SI' end as posee_discapacidad,
case when p.posee_discapacidad='0' then 'NINGUNA' else dis.descripcion end as discapacidad,
case when p.sufre_enfermedad='0' then 'NO' else 'SI' end as sufre_enfermedad,
case when p.sufre_enfermedad='0' then 'NINGUNA' else en.descripcion end as enfermedad,
case when p.requiere_tratamiento='0' then 'NO' else 'SI' end as requiere_tratamiento,
case 
     when p.tipo_tratamiento='T' then 'TEMPORAL' 
     when p.tipo_tratamiento='F' then 'FIJO'
else 'NINGUNO' end as tipo_tratamiento,
case when p.id_grado_instruccion is not null then gr.descripcion else 'NINGUNA' end as grado_instruccion, 
case when p.id_profesion is not null then pr.descripcion else 'NINGUNA' end as profesion,
(SELECT array_to_string(array(SELECT o.descripcion from tbl_personas_oficios po, tbl_oficios o where po.id_oficio=o.id_oficio and po.id_persona=p.id_persona),', ') )as oficios_desempena,
case when p.estudia='0' then 'NO' else 'SI'end as estudia,
case when p.id_institucion_educativa is not null then i.descripcion else 'NINGUNA' end as institucion_educativa, 
case when pc.id_profesion is not null then c.descripcion else 'NINGUNA' end as carrera_universitaria,
case when p.trabaja='0' then 'NO' else 'SI' end as trabaja,
case 
  when p.tipo_trabajo='0' then 'PÚBLICA'
  when p.tipo_trabajo='1' then 'PRIVADA'
  when p.tipo_trabajo='2' then 'INDEPENDIENTE'
else 'NINGUNA'
end as tipo_trabajo,
(SELECT array_to_string(array(SELECT a.descripcion from tbl_personas_areas_laborales pa, tbl_areas_laborales a where pa.id_area_laboral=a.id_area_laboral and pa.id_persona=p.id_persona),', ') )as areas_laborales,
case when p.pertenece_mision='0' then 'NO' else 'SI' end as pertenece_mision,
case when p.pertenece_mision='1' then
(SELECT array_to_string(array(SELECT mi.descripcion from tbl_personas_misiones_sociales m, tbl_misiones_sociales mi where mi.id_mision_social=m.id_mision_social and m.tipo_mision=0 and m.id_persona=p.id_persona),', '))
else
'NINGUNA'
end as misiones_sociales_misionero,
case when p.pertenece_mision='1' then
(SELECT array_to_string(array(SELECT mi.descripcion from tbl_personas_misiones_sociales m, tbl_misiones_sociales mi where mi.id_mision_social=m.id_mision_social and m.tipo_mision=1 and m.id_persona=p.id_persona),', '))
else
'NINGUNA'
end as misiones_sociales_beneficiario,
p.observacion,

to_char(p.fecha_creacion,'DD/MM/YYYY') as fecha_registro,
u.id_estado, u.id_municipio, u.id_parroquia, u.id_sector, u.id_urbanismo, t.id_urbanismo_torre, a.id_urbanismo_apto, pdis.id_discapacidad, pen.id_enfermedad,
p.id_grado_instruccion, p.id_profesion, pc.id_profesion as id_carrera



FROM tbl_personas p
INNER JOIN tbl_urbanismos u on p.id_urbanismo=u.id_urbanismo
INNER JOIN tbl_estado es on u.id_estado=es.id_estado
INNER JOIN tbl_municipio mu on u.id_municipio=mu.id_municipio
INNER JOIN tbl_parroquia pa on u.id_parroquia=pa.id_parroquia
LEFT JOIN tbl_sector se on u.id_sector=se.id_sector
INNER JOIN tbl_urbanismos_torre t on u.id_urbanismo=t.id_urbanismo
INNER JOIN tbl_urbanismos_apto a on p.id_urbanismo_apto=a.id_urbanismo_apto and a.id_urbanismo_torre=t.id_urbanismo_torre
LEFT JOIN tbl_causales ca on p.id_causal=ca.id_causal
LEFT JOIN tbl_personas j on p.id_jefe_familia=j.id_persona
LEFT JOIN tbl_parentescos par on p.id_parentesco=par.id_parentesco
LEFT JOIN tbl_personas_discapacidades pdis on pdis.id_persona=p.id_persona
LEFT JOIN tbl_discapacidades dis on pdis.id_discapacidad=dis.id_discapacidad
LEFT JOIN tbl_personas_enfermedades pen on pen.id_persona=p.id_persona
LEFT JOIN tbl_enfermedades en on pen.id_enfermedad=en.id_enfermedad
LEFT JOIN tbl_grado_instruccion gr on p.id_grado_instruccion=gr.id_grado_instruccion
LEFT JOIN tbl_profesiones pr on p.id_profesion=pr.id_profesion
LEFT JOIN tbl_instituciones_educativas i on p.id_institucion_educativa=i.id_institucion_educativa
LEFT JOIN tbl_personas_carreras pc on pc.id_persona=p.id_persona
LEFT JOIN tbl_profesiones c on pc.id_profesion=c.id_profesion

WHERE p.estatus!=2
order by p.id_persona, p.id_jefe_familia) t";

if($campo[1]==''){
    $filtros="where 1=1 ";
}else{
    $filtros=" where 1=1 ".$campo[1];
    $filtros=  str_replace("\'", "'", $filtros);
//    $filtros=  str_replace("='", " like '%", $filtros);
//    $filtros=  str_replace("' and", "%' and", $filtros);
    
}
$parte1.=$table.' '.$filtros;

$conex = Conexion::singleton();
$conex->connect_pgsql();

$colores = array('F17C0E','0012FF','06FF00','FF0000','AE00FF','BCA8E6','7E8D00','9D9D96','00789B','FF4E00');
if ($campo[0]==""){
    $campo[0]="ESTADO,MUNCIPIO,PARROQUIA,SECTOR,COMUNIDAD,URBANISMO,TORRE,APARTAMENTO,SITUACION_LEGAL,NACIONALIDAD,CEDULA,PRIMER_NOMBRE,SEGUNDO_NOMBRE,PRIMER_APELLIDO,SEGUNDO_APELLIDO,FECHA_NAC,EDAD,ESTADO_CIVIL,SEXO,EMBARAZADA,SEMANAS_EMBARAZO,POSEE_HUELLA,CAUSAL,ESTATUS_FOTO,JEFE_FAMILIA,PARENTESCO,TEL_CELULAR,TEL_LOCAL,EMAIL,POSEE_DISCAPACIDAD,DISCAPACIDAD,SUFRE_ENFERMEDAD,ENFERMEDAD,REQUIERE_TRATAMIENTO,TIPO_TRATAMIENTO,GRADO_INSTRUCCION,PROFESION,OFICIOS_DESEMPENA,ESTUDIA,INSTITUCION_EDUCATIVA,CARRERA_UNIVERSITARIA,TRABAJA,TIPO_TRABAJO,AREAS_LABORALES,PERTENECE_MISION,MISIONES_SOCIALES_MISIONERO,MISIONES_SOCIALES_BENEFICIARIO,OBSERVACION,FECHA_REGISTRO";    
}

$cabecera = explode(",", $campo[0]);

$const=sizeof($cabecera);

$arr = $cabecera;
	$xls->OpenRow();
	foreach($arr as $cod=>$val)	
        $xls->NewCell(strtoupper($val),false,array('align'=>'center','background'=>'C00000','color'=>'FFFFFF','bold'=>true,'border'=>'000000'));
	$xls->CloseRow();	
//echo $parte1;die;            
$resultado = $conex->pgs_query($parte1);
$registros = pg_num_rows ($resultado); 
if ($registros>0){
    while($row = pg_fetch_array($resultado)){
        $xls->OpenRow();
        for($e=0;$e<=$const-1;$e++){
           $xls->NewCell($row[$e],true,$xls_normal); //Auto alineado
        }
            $xls->CloseRow();
    }
    
    $xls->GetXLS(); 
    
    
    
}else{
    echo "<script>alert('No existen registros para mostrar');window.close(this);</script>";
}
