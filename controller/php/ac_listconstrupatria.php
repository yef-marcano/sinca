<?php

require('../../config/configDataTables.php');
require('../../common/php/ssp.class.php');

$table = 'tblconstrupatria';

$primaryKey = 'clvcodigo';

$subqueryIdCreacion = "(SELECT CONCAT_WS(' ', tblusuario.strnombre, tblusuario.strapellido) FROM tblusuario WHERE tblusuario.clvcodigo= " . $table . ".clvusuario_creador)";
$subqueryIdEstado = "(SELECT estado FROM vsw_estado WHERE vsw_estado.cod_estado= " . $table . ".clvestado)";
$columns = array(
    array('db' => 'clvcodigo', 'dt' => 0),
    array('db' => 'strdescripcion', 'dt' => 1),
//    array('db' => 'clvestado', 'dt' => 2),
    array('db' => $subqueryIdEstado, 'dt' => 2, 'alias' => 'nombre_estado'),
    array('db' => 'memdireccion', 'dt' => 3),

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
