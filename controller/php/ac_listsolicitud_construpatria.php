<?php

require('../../config/configDataTables.php');
require('../../common/php/ssp.class.php');

$table = 'tblsolicitud_construpatria';

$primaryKey = 'clvcodigo';

$subqueryIdCreacion = "(SELECT CONCAT_WS(' ', tblusuario.strnombre, tblusuario.strapellido) FROM tblusuario WHERE tblusuario.clvcodigo= " . $table . ".clvusuario_creador)";
$subqueryIdProyecto= "(SELECT tblproyecto.memdescripcion FROM tblproyecto, tblsolicitud WHERE tblproyecto.clvcodigo= tblsolicitud.clvproyecto and tblsolicitud.clvcodigo=".$table.".clvsolicitud)";
$columns = array(
    array('db' => 'clvcodigo', 'dt' => 0),
    array('db' => $subqueryIdProyecto, 'dt' => 1, 'alias' => 'nombre_proyecto'),
//    array('db' => 'clvproyecto', 'dt' => 1),
  array('db' => 'clvestatus_solicitud', 'dt' => 2, 
    'formatter' => function($d, $row) {
        $status= "";
        if($d == 0){
            $status= "ELIMINADA";
        }else if($d == 1){
            $status= "REGISTRADA";
        }else if($d == 2){
            $status= "APROBADA";
        }else if($d == 3){
            $status= "DESPACHADA";
        }else if($d == 4){
            $status= "NEGADA";
        }
            return $status;
        }),
    array('db' => 'clvsolicitud', 'dt' => 3),
    //array('db' => $subqueryIdCreacion, 'dt' => 3, 'alias' => 'nombre_usuario_creador'),
    array('db' => 'clvcodigo', 'dt' => 4,
        'formatter' => function($d, $row) {
            return "<div class='3 fluid ui buttons'>
                        <div class='ui tiny button' onclick='viewData(\"" . $d . "\")'>
                            <i class='teal search icon'></i>
                        </div>
                        <div class='ui tiny button' onclick='editData(\"" . $d . "\")'>
                            <i class='orange pencil icon'></i>
                        </div>
                        <div class='ui tiny button' onclick='deleteData(\"" . $d . "\")'>
                            <i class='red trash icon'></i>
                        </div>
                    </div>";
        }
    )
);

$where = "clvestatus != 1";

echo json_encode(
        SSP::simple($_GET, $sql_details, $table, $primaryKey, $columns, $where)
);
