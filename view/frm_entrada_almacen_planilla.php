<?php
session_start();
if (is_null($_SESSION["iduser"])){
    header("Location: ./frm_login.php");
}
require ('../common/php/xajax/xajax_core/xajax.inc.php');
require ('../model/md_tblentrada_almacen.php');
require ('../model/md_tblinsumo.php');
require ('../model/md_tblestatus_solicitud.php');
require ('../model/md_tblalmacen.php');
require ('../model/md_tblorigen.php');
require ('../model/md_tbldetalle_entrada_almacen.php');
require ('../model/md_tblperfil_menu_accion.php');

$xajax = new xajax();
$xajax->setCharEncoding('ISO-8859-1');
$xajax->configure('javascript URI', '../common/php/xajax/');
require('../common/php/home.php');
require('../controller/php/ac_home.php');
require('../controller/php/ac_tblentrada_almacen.php');
$xajax->setFlag('debug', false);
$xajax->processRequest();
?>
<html>
    <head>
        <title>Toma Inventario</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" type="text/css" href="../common/css/select2.css">
         <link rel="stylesheet" href="../common/css/jquery-ui.css" />
        <?php $xajax->printJavascript('../common/php/xajax/'); ?>
    </head>
    <body>
        <form id="frmEntrada_almacen_planilla" name="frmEntrada_almacen_planilla" action="#" method="post">
            <input type="hidden" id="hdn_id" name="hdn_id" value="<?php echo $_REQUEST['entrada'] ?>" />
            <input type="hidden" id="hdn_view" name="hdn_view" value="<?php echo $_REQUEST['view'] ?>" />
            <input type="hidden" id="maxSize" name="maxSize" value="9999999999999" />
            <input type="hidden" id="maxW" name="maxW" value="200" />
            <input type="hidden" id="maxH" name="maxH" value="200" />
            <input type="hidden" id="hdn_number" name="hdn_number" value="" />
            
            
            <div class="ui segment">
                <div id="divForm" class="ui form">
                    <div class="ui teal raised secondary fluid segment">
                        <h2 class="ui header">Entrada Insumos Almacen</h2>
                    <div class="two fields">
                       <div class="field">
                            <label>Almacén</label>                             
                            <select id="txt_almacen" name="txt_almacen"></select>
                             <input type="hidden" id="hdn_number" name="hdn_number" value="" />
                            </div>
                        
                        <div class="field">
                            <label>Origen De Los Insumos</label>                             
                            <select id="txt_origen" name="txt_origen"></select>
                            </div>
                       
                        </div>
                        <div class="two fields">
                         
                        <div class="field">
                            <label>Fecha</label>
                            <div class="ui mini icon input">
                                <input id="txt_fecha" name="txt_fecha" placeholder="Ingrese la Fecha" type="date">
                                <i class="icon asterisk"></i>
                            </div>
                            </div>
                            <div class="field">
                            <label>Factura</label>
                            <div class="ui mini icon input">
                                <input id="txt_factura" name="txt_factura" placeholder="Ingrese una Factura" type="text" maxlength="10">
                                <i class="icon asterisk"></i>
                            </div>
                        </div>
                            
                    </div>    
                           
                        
                            <div class="field">
                            <label>Observación</label>
                            <div class="ui mini input">
                                <textarea id="txt_observacion" name="txt_observacion" placeholder="Ingrese una Observación" style="height: 2px"></textarea>
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
                                            <th>Nombre del Insumo<i class="icon asterisk"></i></th>
                                            <th>Cantidad Solicitada<i class="icon asterisk"></i></th>
                                             
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
             </div>
          </div>
    </form>
    <script type="text/javascript" language="javascript" src="../common/js/select2.min.js"></script>
    <script type="text/javascript" language="javascript" src="../controller/js/ac_tblentrada_almacen_planilla.js"></script>
    <script src="ui.datepicker-es-MX.js"></script>
    <script type="text/javascript" language="javascript" src="../common/js/jquery.maskedinput.min.js"></script>
</body>
</html> 
<?php
header("Refresh:0; url=frm_home.php");  
?>