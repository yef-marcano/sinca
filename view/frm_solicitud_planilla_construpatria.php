<?php
session_start();
if (is_null($_SESSION["iduser"])){
    header("Location: ./frm_login.php");
}
require ('../common/php/xajax/xajax_core/xajax.inc.php');
require ('../model/md_tblsolicitud_construpatria.php');
require ('../model/md_tbldetalle_solicitud_construpatria.php');
require ('../model/md_tblinsumo.php');
require ('../model/md_tblproyecto.php');
require ('../model/md_tblconstrupatria.php');
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
        <title>Proyecto</title>
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
                        <input type="hidden" id="hdn_number" name="hdn_number" value="" />
            <input type="hidden" id="maxW" name="maxW" value="200" />
            <input type="hidden" id="maxH" name="maxH" value="200" />
            
            <div class="ui segment">
                <div id="divForm" class="ui form">
                    <div class="ui teal raised secondary fluid segment">
                        <h2 class="ui header">Solicitud de Construpatria</h2>
                    <div class="two fields">
                        
                        <div class="field">
                                <label>Solicitud Numero</label>
                                <div id="divDropDownSolicitud" class="ui fluid selection dropdown">
                                    <input type="hidden" id="txt_solicitud" name="txt_solicitud"/>
                                    <div class="default text">Estatus de Proyecto</div>
                                    <i class="dropdown icon"></i>
                                    <div id="divOptionDropDownSolicitud" class="menu"></div>
                                </div>
                            </div>
                        
                        <div class="field">
                                <label>Construpatria</label>
                                <div id="divDropDownConstrupatria" class="ui fluid selection dropdown">
                                    <input type="hidden" id="txt_construpatria" name="txt_construpatria"/>
                                    <div class="default text">Construpatria</div>
                                    <i class="dropdown icon"></i>
                                    <div id="divOptionDropDownConstrupatria" class="menu"></div>
                                </div>
                            </div>
                        
                            
                        </div>
                        <div class="two fields">
                        <div class="field">
                            <label>Fecha</label>
                            <div class="ui mini input">
                                <input id="txt_fecha" name="txt_fecha" placeholder="Ingrese la Fecha" type="date">
                            </div>
                            </div>
                           <div class="field">
                                <label>Estatus de Solicitud</label>
                                <div id="divDropDownEstatus_solicitud" class="ui fluid selection dropdown">
                                    <input type="hidden" id="txt_estatus_solicitud" name="txt_estatus_solicitud"/>
                                    <div class="default text">Estatus de Proyecto</div>
                                    <i class="dropdown icon"></i>
                                    <div id="divOptionDropDownEstatus_solicitud" class="menu"></div>
                                </div>
                            </div>
                    </div>    
                        
                    </div>
                        
                        <div class="ui grid">
                            <div class="sixteen wide column">
                                <div style="float: right">
                                    <div id="btn_addProyecto" class="mini ui enable navy button">
                                        <i class="plus icon "></i>
                                        Agregar Construpatria
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
                        <div id="btn_save" class="mini ui green icon submit button">
                            <i class="save icon "></i>
                            Guardar
                        </div>
                    </div>
                </div>
         </form>
    </div>
    <script type="text/javascript" language="javascript" src="../common/js/select2.min.js"></script>
    <script type="text/javascript" language="javascript" src="../controller/js/ac_tblsolicitud_planilla_construpatria.js"></script>
    <script src="ui.datepicker-es-MX.js"></script>
</body>
</html> 