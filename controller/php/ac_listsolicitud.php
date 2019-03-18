<?php

require('../../config/configDataTables.php');
require('../../common/php/ssp.class.php');

$table = 'tblsolicitud';

$primaryKey = 'clvcodigo';

$subqueryIdCreacion = "(SELECT CONCAT_WS(' ', tblusuario.strnombre, tblusuario.strapellido) FROM tblusuario WHERE tblusuario.clvcodigo= " . $table . ".clvusuario_creador)";
$subqueryIdProyecto= "(SELECT tblproyecto.memdescripcion FROM tblproyecto WHERE tblproyecto.clvcodigo= " . $table . ".clvproyecto)";
$columns = array(
    array('db' => 'clvcodigo', 'dt' => 0),
    array('db' => $subqueryIdProyecto, 'dt' => 1, 'alias' => 'nombre_proyecto'),
    array('db' => 'memobservacion', 'dt' => 2),
      array('db' => 'clvestatus_solicitud', 'dt' => 3, 
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
    array('db' => $subqueryIdCreacion, 'dt' => 4, 'alias' => 'nombre_usuario_creador'),
    array('db' => 'clvcodigo', 'dt' => 5,
        'formatter' => function($d, $row) {
            return "<div class='6 fluid ui buttons'>
                        <div class='ui tiny button' onclick='viewData(\"" . $d . "\")'>
                            <i class='teal search icon'></i>
                        </div>
                        <div class='ui tiny button' onclick='editData(\"" . $d . "\")'>
                            <i class='orange pencil icon'></i>
                        </div>
                        <div class='ui tiny button' onclick='deleteData(\"" . $d . "\")'>
                            <i class='red trash icon'></i>
                        </div>
                        <div class='ui tiny button' onclick='aproveData(\"" . $d . "\")'>
                            <i class='blue check icon'></i>
                        </div>
                        <div class='ui tiny button' onclick='denegaData(\"" . $d . "\")'>
                            <i class='red remove icon'></i>
                        </div>
                        <div class='ui tiny button' onclick='printData(\"" . $d . "\")'>
                            <i class='blue print icon'></i>
                        </div>
                    </div>";
        }
    )
);

$where = "clvestatus != 1";

echo json_encode(
        SSP::simple($_GET, $sql_details, $table, $primaryKey, $columns, $where)
);
