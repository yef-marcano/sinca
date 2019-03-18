<?php
session_start();
if(is_null($_SESSION["iduser"])){
    header("Location: ./frm_login.php");
}

require ('../common/php/xajax/xajax_core/xajax.inc.php');
require ('../model/md_tblorden_salida_construpatria.php');
require ('../model/md_tblperfil_menu_accion.php');

$xajax = new xajax();
$xajax->setCharEncoding('ISO-8859-1');
$xajax->configure('javascript URI', '../common/php/xajax/');
require('../common/php/home.php');
require('../controller/php/ac_home.php');
require('../controller/php/ac_tblorden_salida_construpatria.php');
$xajax->setFlag('debug', false);
$xajax->processRequest();
?>
<html>
    <head>
        <title>Solicitud</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <?php $xajax->printJavascript('../common/php/xajax/'); ?>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    </head>
    <body>
        <div class="ui segment">
            <div class="ui left floated header">
                <i class="th list icon"></i>
                 Orden Salida Construpatria
            </div>
            <div class="ui clearing divider"></div>
            <div class="ui five column grid">
                <div class="five column row">
                    <div class="column"></div>
                    <div class="column"></div>
                    <div id='divMessageColumn' class="column"></div>
                    <div class="column"></div>
                    <div class="column">
                        <div style="float: right;height: 41px">
                            <div id="btn_add" class="mini ui green labeled icon button left">
                                <i class="add icon"></i>
                                Agregar                            
                            </div>                            
                        </div>
                    </div>
                </div>
            </div>
            <table id="tblOrden_salida_construpatria" class="display" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Conductor</th>
                        <th>Dirección</th>
                        <th>Observacion</th>
                        <th>Estatus</th>
                        <th>Fecha</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
            </table>
        </div>
        
    
        <script type="text/javascript" language="javascript" src="../common/js/jquery.maskedinput.min.js"></script>
       <!-- <script type="text/javascript" language="javascript" src="../controller/js/ac_tblorden_salida_cementera.js"></script>-->
        <script type="text/javascript" language="javascript" src="../controller/js/ac_tblorden_salida_construpatria.js"></script>
        
        </body>
</html>
