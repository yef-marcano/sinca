<?php

require('../../config/configDataTables.php');
require('../../common/php/ssp.class.php');

$table = 'tblusuario';

$primaryKey = 'clvcodigo';

$subqueryNombre= "(CONCAT_WS(' ', strnombre, strapellido))";
$subqueryPerfil= "(SELECT p.strdescripcion FROM tblperfil p WHERE p.clvcodigo= ".$table.".clvperfil)";

$columns = array(
    array('db' => 'clvcodigo', 'dt' => 0),
    array('db' => $subqueryNombre, 'dt' => 1, 'alias' => 'nombre_completo'),
    array('db' => 'strusuario', 'dt' => 2),
    array('db' => $subqueryPerfil, 'dt' => 3, 'alias' => 'nombre_perfil'),
    
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
