<?php
session_start();
if(is_null($_SESSION["iduser"])){
    header("Location: ./frm_login.php");
}

require ('../common/php/xajax/xajax_core/xajax.inc.php');
require ('../model/md_tblconductor.php');
require ('../model/md_utility.php');
require ('../model/md_saime.php');
require ('../model/md_tblperfil_menu_accion.php');

$xajax = new xajax();
$xajax->setCharEncoding('ISO-8859-1');
$xajax->configure('javascript URI', '../common/php/xajax/');
require('../common/php/home.php');
require('../controller/php/ac_home.php');
require('../controller/php/ac_tblconductor.php');
require('../controller/php/ac_utility.php');
$xajax->setFlag('debug', false);
$xajax->processRequest();
?>
<html>
    <head>
        <title>Conductor</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <?php $xajax->printJavascript('../common/php/xajax/'); ?>
    </head>
    <body>
        <div class="ui segment">
            <div class="ui left floated header">
                <i class="truck icon"></i>
                Conductor
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
            <table id="tblConductor" class="display" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Cedula</th>
                        <th>Estatus</th>
                        <th>Creador</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
            </table>
        </div>
        <div id="sideBarFormConductor" class="ui very wide styled floating sidebar">
            <div id="divLoader" class="ui disabled dimmer">
                <div class="ui large text loader">Cargando...</div>
            </div>
            <form id="frmConductor" name="frmConductor" action="#" method="post">
                <input type="hidden" id="hdn_id" name="hdn_id" value="" />
                 <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <h4 class="ui inverted navy top attached header">
                    <span id="labelFormConductor"></span>
                </h4>
                <div id="divForm1" class="ui form attached segment">
                    <div id="tabsConductor" class="ui top attached tabular menu">
                        <a class="active item" data-tab="first"><i class="grid layout icon"></i>Datos</a>
                        <a class="item" data-tab="second"><i class="browser icon"></i>Auditor&iacute;a</a>
                    </div>
                    <div class="ui active bottom attached tab segment" data-tab="first">
                        <div class="field">
                                <label>Nacionalidad</label>
                                <div id="divDropDownNacionalidad" class="ui fluid selection dropdown">
                                    <input type="hidden" id="txt_nacionalidad" name="txt_nacionalidad"/>
                                    <div class="default text">Seleccione una nacionalidad</div>
                                    <i class="dropdown icon"></i>
                                    <div id="divOptionDropDownNacionalidad" class="menu">
                                        <div class='item active' data-value=''>Seleccione una nacionalidad</div>
                                        <div class='item' data-value='V'>Venezolano(a)</div>
                                        <div class='item' data-value='E'>Extranjero(a)</div>
                                    </div>
                                </div>                           
                               
                            </div>
                        <div class="field">
                            <label>Cedula</label>
                            <div class="ui mini icon input">
                                <input id="txt_cedula" name="txt_cedula" placeholder="Ingrese la c&eacute;dula de identidad" type="text" maxlength="10">
                                <i class="icon asterisk"></i>
                            </div>
                        </div>
                        <div class="field">
                            <label>Nombre</label>
                            <div class="ui mini icon input">
                                <input id="txt_nombre" name="txt_nombre" placeholder="Ingrese Nombre" type="text">
                                <i class="icon asterisk"></i>
                            </div>
                        </div>
                        <div class="field">
                            <label>Apellido</label>
                            <div class="ui mini icon input">
                                <input id="txt_apellido" name="txt_apellido" placeholder="Ingrese Apellido" type="text">
                                <i class="icon asterisk"></i>
                            </div>
                        </div>
                        <div class="field">
                            <label>Telefono</label>
                            <div class="ui mini icon input">
                                <input id="txt_telefono" name="txt_telefono" placeholder="Ingrese Telefono" type="text">
                                <i class="icon asterisk"></i>
                            </div>
                        </div>
                    </div>
                    <div class="ui bottom attached tab segment" data-tab="second">
                        <div class="ui vertically divided grid">
                            <div class="one column row">
                                <div class="column">
                                    <div class="ui small label">
                                        <i class="user icon"></i>&nbsp;&nbsp;Usuario Creador
                                        &nbsp;&nbsp;/&nbsp;&nbsp;
                                        <i class="calendar icon"></i>&nbsp;&nbsp;Fecha Creaci&oacute;n
                                    </div>
                                    <div class="ui blue small message">
                                        <ul class="list">
                                            <li id="lbl_usuarioCreador">&nbsp;</li>
                                            <li id="lbl_fechaCreador">&nbsp;</li>
                                        </ul>
                                    </div>

                                    <div class="ui small label">
                                        <i class="user icon"></i>&nbsp;&nbsp;Usuario Modificaci&oacute;n
                                        &nbsp;&nbsp;/&nbsp;&nbsp;
                                        <i class="calendar icon"></i>&nbsp;&nbsp;Fecha Modificaci&oacute;n
                                    </div>
                                    <div class="ui yellow small message">
                                        <ul class="list">
                                            <li id="lbl_usuarioModificacion">&nbsp;</li>
                                            <li id="lbl_fechaModificacion">&nbsp;</li>
                                        </ul>
                                    </div>


                                    <div class="ui small label">
                                        <i class="user icon"></i>&nbsp;&nbsp;Usuario Eliminaci&oacute;n
                                        &nbsp;&nbsp;/&nbsp;&nbsp;
                                        <i class="calendar icon"></i>&nbsp;&nbsp;Fecha Eliminaci&oacute;n
                                    </div>
                                    <div class="ui red small message">
                                        <ul class="list">
                                            <li id="lbl_usuarioEliminacion">&nbsp;</li>
                                            <li id="lbl_fechaEliminacion">&nbsp;</li>
                                        </ul>
                                    </div>
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
            </form>
        </div>        
        <script type="text/javascript" language="javascript" src="../common/js/jquery.maskedinput.min.js"></script>
        <script type="text/javascript" language="javascript" src="../controller/js/ac_tblconductor.js"></script>
        
    </body>
</html>