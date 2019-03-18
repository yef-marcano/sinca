<?php

require('../../config/configDataTables.php');
require('../../common/php/ssp.class.php');

$table = 'tblperfil';

$primaryKey = 'clvcodigo';

$subqueryIdCreacion= "(SELECT CONCAT_WS(' ', tblusuario.strnombre, tblusuario.strapellido) FROM tblusuario WHERE tblusuario.clvcodigo= tblperfil.clvusuario_creador)";
//$subqueryFechaCreacion= "DATE_FORMAT(fecha_creacion, '%d-%m-%Y %h:%i %p')";

$columns = array(
    array('db' => 'clvcodigo', 'dt' => 0),
    array('db' => 'strdescripcion', 'dt' => 1),    
    array('db' => $subqueryIdCreacion, 'dt' => 2, 'alias' => 'nombre_usuario_creador'),
    array('db' => 'clvcodigo', 'dt' => 3,
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
