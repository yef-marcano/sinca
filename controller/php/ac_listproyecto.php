<?php

require('../../config/configDataTables.php');
require('../../common/php/ssp.class.php');

$table = 'tblproyecto';

$primaryKey = 'clvcodigo';

$subqueryIdCreacion = "(SELECT CONCAT_WS(' ', tblusuario.strnombre, tblusuario.strapellido) FROM tblusuario WHERE tblusuario.clvcodigo= " . $table . ".clvusuario_creador)";
$subqueryIdTipoproyecto= "(SELECT tbltipo_proyecto.strdescripcion FROM tbltipo_proyecto WHERE tbltipo_proyecto.clvcodigo= " . $table . ".clvtipo_proyecto)";
$subqueryIdEstado = "(SELECT estado FROM vsw_estado WHERE vsw_estado.cod_estado= " . $table . ".clvestado)";
$columns = array(
    array('db' => 'clvcodigo', 'dt' => 0),
    array('db' => 'strnombre', 'dt' => 1),
    array('db' => $subqueryIdTipoproyecto, 'dt' => 2, 'alias' => 'nombre_tipo_proyecto'),
//    array('db' => 'clvtipo_proyecto', 'dt' => 2),
    array('db' => $subqueryIdEstado, 'dt' => 3, 'alias' => 'nombre_estado'),
    array('db' => 'clvestatus_proyecto', 'dt' => 4, 
        'formatter' => function($d, $row) {
            $status= "";
            if($d == 0){
                $status= "REGISTRADO";
            }else if($d == 1){
                $status= "EN EJECUCIÓN";
            }else if($d == 2){
                $status= "PARALIZADO";
            }else if($d == 3){
                $status= "FINALIZADO";
            }
            return $status;
        }),
    array('db' => $subqueryIdCreacion, 'dt' => 5, 'alias' => 'nombre_usuario_creador'),
    array('db' => 'clvcodigo', 'dt' => 6,
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
