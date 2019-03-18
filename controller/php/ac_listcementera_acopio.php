<?php

require('../../config/configDataTables.php');
require('../../common/php/ssp.class.php');

$table = 'tblcementera_acopio';

$primaryKey = 'clvcodigo';

$subqueryIdCreacion = "(SELECT CONCAT_WS(' ', tblusuario.strnombre, tblusuario.strapellido) FROM tblusuario WHERE tblusuario.clvcodigo= " . $table . ".clvusuario_creador)";
$subqueryIdCementera= "(SELECT tblcementera.strdescripcion FROM tblcementera WHERE tblcementera.clvcodigo= " . $table . ".clvcementera)";
//$subqueryIdEstado = "(SELECT tblestado.strdescripcion FROM bd_geografia.tblestado WHERE tblestado.clvcodigo= " . $table . ".id_estado)";
$columns = array(
    array('db' => 'clvcodigo', 'dt' => 0),
    array('db' => $subqueryIdCementera, 'dt' => 1, 'alias' => 'nombre_cementera'),
    array('db' => 'strdescripcion', 'dt' => 2),
//    array('db' => 'clvestado', 'dt' => 3),
    array('db' => 'strtelefono', 'dt' => 3),
//    array('db' => 'clvestatus', 'dt' => 5, 
//        'formatter' => function($d, $row) {
//            $status= "";
//            if($d == 0){
//                $status= "Nuevo";
//            }else if($d == 1){
//                $status= "Publicada";
//            }else if($d == 2){
//                $status= "Despublicada";
//            }else if($d == 3){
//                $status= "Eliminada";
//            }
//            return $status;
//        }),
    array('db' => $subqueryIdCreacion, 'dt' => 4, 'alias' => 'nombre_usuario_creador'),
    array('db' => 'clvcodigo', 'dt' => 5,
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
