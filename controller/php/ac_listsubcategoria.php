<?php

require('../../config/configDataTables.php');
require('../../common/php/ssp.class.php');

$table = 'tblsubcategoria';

$primaryKey = 'clvcodigo';

$subqueryIdCreacion= "(SELECT CONCAT_WS(' ', tblusuario.strnombre, tblusuario.strapellido) FROM tblusuario WHERE tblusuario.clvcodigo= " . $table . ".clvusuario_creador)";
$subqueryIdCategoria= "(SELECT tblcategoria.strdescripcion FROM tblcategoria WHERE tblcategoria.clvcodigo= " . $table . ".clvcategoria)";
$columns = array(
    array('db' => 'clvcodigo', 'dt' => 0),
    array('db' => $subqueryIdCategoria, 'dt' => 1, 'alias' => 'nombre_categoria'),
    array('db' => 'strdescripcion', 'dt' => 2),
//    array('db' => 'clvestatus', 'dt' => 3, 
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
    array('db' => $subqueryIdCreacion, 'dt' => 3, 'alias' => 'nombre_usuario_creador'),
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
