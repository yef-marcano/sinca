<?php
session_start();
if (is_null($_SESSION["iduser"])){
    header("Location: ./frm_login.php");
}
require ('../common/php/xajax/xajax_core/xajax.inc.php');
require ('../model/md_tblsolicitud.php');
require ('../model/md_tbldetalle_solicitud.php');
require ('../model/md_tblinsumo.php');
require ('../model/md_tblproyecto.php');
require ('../model/md_tblunidad_despacho.php');
require ('../model/md_tblestatus_solicitud.php');
require ('../model/md_tblperfil_menu_accion.php');

$xajax = new xajax();
$xajax->setCharEncoding('ISO-8859-1');
$xajax->configure('javascript URI', '../common/php/xajax/');
require('../common/php/home.php');
require('../controller/php/ac_home.php');
require('../controller/php/ac_tblsolicitud_aprobe.php');
$xajax->setFlag('debug', false);
$xajax->processRequest();
?>
<html>
    <head>
        <title>Solicitud de Insumos</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="../common/css/select2.css">
         <link rel="stylesheet" href="../common/css/jquery-ui.css" />
        <?php $xajax->printJavascript('../common/php/xajax/'); ?>
    </head>
    <body>
        <form id="frmSolicitud_planilla" name="frmSolicitud_planilla" action="#" method="post">
            
            <input type="hidden" id="hdn_id" name="hdn_id" value="<?php echo $_REQUEST['solicitud'] ?>" />
            <input type="hidden" id="hdn_view" name="hdn_view" value="<?php echo $_REQUEST['view'] ?>" />
            <input type="hidden" id="maxSize" name="maxSize" value="9999999999999" />
            <input type="hidden" id="maxW" name="maxW" value="200" />
            <input type="hidden" id="maxH" name="maxH" value="200" />
            
           
            <div class="ui segment">
                <div id="divForm" class="ui form">
                    <div class="ui teal raised secondary fluid segment">
                        <h2 class="ui header">Solicitud de Insumos</h2>
                    <div class="two fields">
                       <div class="field">
                            <label>Proyecto</label>                             
                            <select id="txt_proyecto" name="txt_proyecto"></select>
                             <input type="hidden" id="hdn_number" name="hdn_number" value="" />
                        </div>
                        <div class="field">
                            <label>Fecha</label>
                            <div class="ui mini icon input">
                                <input id="txt_fecha" name="txt_fecha" placeholder="Ingrese la Fecha" type="date">
                                <i class="icon asterisk"></i>
                            </div>
                            </div>
<!--                            <div class="field">
                                <label>Estatus de Solicitud</label>
                                <div id="divDropDownEstatus_solicitud" class="ui fluid selection dropdown">
                                    <input type="hidden" id="txt_estatus_solicitud" name="txt_estatus_solicitud"/>
                                    <div class="default text">Estatus de Proyecto</div>
                                    <i class="dropdown icon"></i>
                                    <div id="divOptionDropDownEstatus_solicitud" class="menu"></div>
                                </div>
                            </div>-->
                        </div>
                        
                            <div class="field">
                            <label>Observación</label>
                            <div class="ui mini input">
                                <textarea id="txt_observacion" name="txt_observacion" placeholder="Ingrese una Observación" style="height: 2px"></textarea>
                            </div>
                        </div>
                        
                    </div>
                        
                        <div class="ui grid">
                            <div class="sixteen wide column">
                                <div style="float: right">
                                    <div id="btn_addProyecto" class="mini ui enable navy button">
                                        <i class="plus icon "></i>
                                        Agregar Insumo
                                    </div>
                                </div>
                                <br><br>
                                <div class="ui grid">
                                    <div class="five wide column"></div>
                                    <div class="six wide column"><div id="divError"></div></div>
                                    <div class="five wide column"></div>
                                </div>
                                <table id="tableProyectos" class="ui celled small table segment">
                                    <thead>
                                        <tr>
                                            <th>Nombre del Insumo</th>
                                            <th>Cantidad Solicitada</th>
                                            <th></th>
                                            <th>Filtro</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyProyectos"></tbody>
                                </table>
                            </div>
                        </div>
                        
  
                    <br/>
                    <div style="float: right">
                        <div id="btn_cancel" class="mini ui red icon button">
                            <i class="delete icon"></i>
                            Cancelar
                        </div>
<!--                        <div id="btn_save" class="mini ui green icon submit button">
                            <i class="save icon "></i>
                            Guardar
                        </div>-->
                        <div id="btn_save" class="mini ui blue icon submit button">
                            <i class="check icon "></i>
                            Aprobar
                        </div>
                    </div>
                </div>
            </div>
         </form>
    </div>
    <script type="text/javascript" language="javascript" src="../common/js/select2.min.js"></script>
    <script type="text/javascript" language="javascript" src="../controller/js/ac_tblsolicitud_planilla_aprobe.js"></script>
<!--    <script src="ui.datepicker-es-MX.js"></script>-->
</body>
</html> 