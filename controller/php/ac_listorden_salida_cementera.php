<?php

require('../../config/configDataTables.php');
require('../../common/php/ssp.class.php');

$table = 'tblorden_salida_cementera';

$primaryKey = 'clvcodigo';

$subqueryIdCreacion = "(SELECT CONCAT_WS(' ', tblusuario.strnombre, tblusuario.strapellido) FROM tblusuario WHERE tblusuario.clvcodigo= " . $table . ".clvusuario_creador)";
//$subqueryIdAlmacen= "(SELECT tblalmacen.strdescripcion FROM tblalmacen WHERE tblalmacen.clvcodigo= " . $table . ".clvalmacen)";
$subqueryIdConductor= "(SELECT tblconductor.strnombre FROM tblconductor WHERE tblconductor.clvcodigo= " . $table . ".clvconductor)";

$columns = array(
//    array('db' => 'strdescripcion', 'dt' => 1),
    array('db' => 'clvcodigo', 'dt' => 0),
    
    array('db' => $subqueryIdConductor, 'dt' => 1, 'alias' => 'nombre_conductor'),
    array('db' => 'memdireccion_destino', 'dt' => 2),
    array('db' => 'memobservacion', 'dt' => 3),
    array('db' => 'clvestatus', 'dt' => 4, 
        'formatter' => function($d, $row) {
            $status= "";
            if($d == 0){
                $status= "Nuevo";
            }else if($d == 1){
                $status= "Publicada";
            }else if($d == 2){
                $status= "Despublicada";
            }else if($d == 3){
                $status= "Eliminada";
            }
            return $status;
        }),
        array('db' => 'dtmfecha', 'dt' => 5),
    array('db' => 'clvcodigo', 'dt' => 6,
        'formatter' => function($d, $row) {
            return "<div class='2 fluid ui buttons'>
                        <div class='ui tiny button' onclick='viewData(\"" . $d . "\")'>
                            <i class='teal search icon'></i>
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
