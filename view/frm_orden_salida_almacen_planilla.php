<?php
session_start();
if (is_null($_SESSION["iduser"])){
    header("Location: ./frm_login.php");
}
require ('../common/php/xajax/xajax_core/xajax.inc.php');
require ('../model/md_tblorden_salida_almacen.php');
require ('../model/md_tbldetalle_orden_salida_almacen.php');
require ('../model/md_tblsolicitud.php');
require ('../model/md_tbldetalle_solicitud.php');
require ('../model/md_tblproyecto.php');
require ('../model/md_tblalmacen.php');
require ('../model/md_tblconductor.php');
require ('../model/md_tblestatus_solicitud.php');
require ('../model/md_tblvehiculo.php');
require ('../model/md_tblinsumo.php');
require ('../model/md_tblperfil_menu_accion.php');



$xajax = new xajax();
$xajax->setCharEncoding('ISO-8859-1');
$xajax->configure('javascript URI', '../common/php/xajax/');
require('../common/php/home.php');
require('../controller/php/ac_home.php');
require('../controller/php/ac_tblorden_salida_almacen.php');
$xajax->setFlag('debug', false);
$xajax->processRequest();
?>
<html>
    <head>
        <title>Orden Salida Almacèn</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="../common/css/select2.css">
         <link rel="stylesheet" href="../common/css/jquery-ui.css" />
        <?php $xajax->printJavascript('../common/php/xajax/'); ?>
    </head>
    <body>
        <!--<form id="frmSolicitud_planilla" name="frmToma_inventario_planilla" action="#" method="post">-->
        <form id="frmOrden_salida_almacen_planilla" name="frmOrden_salida_almacen_planilla" action="#" method="post">
            <input type="hidden" id="hdn_id" name="hdn_id" value="<?php echo $_REQUEST['orden'] ?>" />
            <input type="hidden" id="hdn_view" name="hdn_view" value="<?php echo $_REQUEST['view'] ?>" />
            <input type="hidden" id="maxSize" name="maxSize" value="9999999999999" />
            <input type="hidden" id="maxW" name="maxW" value="200" />
            <input type="hidden" id="maxH" name="maxH" value="200" />
            <input type="hidden" id="hdn_number" name="hdn_number" value="" />
            
            <div class="ui segment">
            <div id="divForm" class="ui form">
            <div class="ui teal raised secondary fluid segment">
            <h2 class="ui header"> Orden Salida Almacèn</h2>
                    <div class="four fields">
                       <div class="field">
                           <label>Proyecto</label>                             
                            <select id="txt_proyecto" name="txt_proyecto"></select>
                            </div>
                        <div class="field">
                           <label>Solicitud</label>                             
                            <select id="txt_solicitud" name="txt_solicitud"></select>
                            </div>
                       <div class="field">
                            <label>Almacen</label>                             
                            <select id="txt_almacen" name="txt_almacen"></select>
                            </div> 
                        <div class="field">
                            <label>Fecha</label>
                            <div class="ui mini input">
                                <input id="txt_fecha" name="txt_fecha" placeholder="Ingrese la Fecha" type="date">
                            </div>
                            </div>
                        </div>
                        <div class="two fields">
                            
                        <div class="field">
                            <label>Conductor</label>                             
                            <select id="txt_conductor" name="txt_conductor"></select>
                            </div>
                            
                            <div class="field">
                            <label>Vehiculo</label>                             
                            <select id="txt_vehiculo" name="txt_vehiculo"></select>
                            </div>
                        </div>    
                          <div class="two fields">
                            <div class="field">
                            <label>Direccion</label>
                            <div class="ui mini input">
                                <textarea id="txt_direccion" name="txt_direccion" placeholder="Ingrese una Direccion/Destino" style="height: 2px"></textarea>
                            </div>
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
                                
                                <div class="ui grid">
                                    <div class="five wide column"></div>
                                    <div class="six wide column"><div id="divError"></div></div>
                                    <div class="five wide column"></div>
                                </div>
                                <div id="Detalle">
                                    <table id="tableProyectos" class="ui celled small table segment">
                                        <thead>
                                            <tr>
                                                <th>Nombre del Insumo</th>
                                                <th>Cantidad Solicitada</th>
                                                <th>Cantidad a Despachar</th>                                            
                                            </tr>
                                        </thead>
                                        <tbody id="tbodyProyectos"></tbody>
                                    </table>
                                </div>
                            </div>
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
                </div>
         </form>
    
    <script type="text/javascript" language="javascript" src="../common/js/select2.min.js"></script>
    <script type="text/javascript" language="javascript" src="../controller/js/ac_tblorden_salida_almacen_planilla.js"></script>
    <script src="ui.datepicker-es-MX.js"></script>
</body>
</html> 