<?php
session_start();
if(is_null($_SESSION["iduser"])){
    header("Location: ./frm_login.php");
}

require ('../common/php/xajax/xajax_core/xajax.inc.php');
require ('../model/md_tblsolicitud_construpatria.php');
require ('../model/md_tblproyecto.php');
require ('../model/md_tblestatus_solicitud.php');
require ('../model/md_tblperfil_menu_accion.php');

$xajax = new xajax();
$xajax->setCharEncoding('ISO-8859-1');
$xajax->configure('javascript URI', '../common/php/xajax/');
require('../common/php/home.php');
require('../controller/php/ac_home.php');
require('../controller/php/ac_tblsolicitud_construpatria.php');
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
                <i class="shopping cart icon "></i>
                Solicitud Construpatria
            </div>
            <div class="ui clearing divider"></div>
            <table id="tblSolicitud_construpatria" class="display" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Proyecto</th>
                        <th>Estatus</th>
                        <th>Solicitud #</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
            </table>
        </div>
        
    
        <script type="text/javascript" language="javascript" src="../common/js/jquery.maskedinput.min.js"></script>
        <script type="text/javascript" language="javascript" src="../controller/js/ac_tblsolicitud_construpatria.js"></script>
        
        </body>
</html>
