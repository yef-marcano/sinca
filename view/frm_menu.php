<?php
session_start();
if(is_null($_SESSION["iduser"])){
    header("Location: ./frm_login.php");
}

require ('../common/php/xajax/xajax_core/xajax.inc.php');
require ('../model/md_tblmenu.php');
require ('../model/md_tblmodulo.php');
require ('../model/md_tblaccion.php');
require ('../model/md_tblmenu_accion.php');
require ('../model/md_tblperfil_menu_accion.php');

$xajax = new xajax();
$xajax->setCharEncoding('ISO-8859-1');
$xajax->configure('javascript URI', '../common/php/xajax/');
require('../common/php/home.php');
require('../controller/php/ac_home.php');
require('../controller/php/ac_tblmenu.php');
$xajax->setFlag('debug', false);
$xajax->processRequest();
?>
<html>
    <head>
        <title>Items Menus</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <link rel="stylesheet" type="text/css" href="../common/css/semantic/packaged/css/semantic.css">
        <link rel="stylesheet" type="text/css" href="../common/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="../common/css/dataTables.bootstrap.css">

        <script type="text/javascript" language="javascript" src="../common/js/jquery-2.1.1.min.js"></script>
        <script type="text/javascript" language="javascript" src="../common/css/semantic/packaged/javascript/semantic.js"></script>
        <script type="text/javascript" language="javascript" src="../common/js/jquery.address.js"></script>
        <script type="text/javascript" language="javascript" src="../common/js/jquery.dataTables.js"></script>
        <script type="text/javascript" language="javascript" src="../common/js/dataTables.bootstrap.js"></script>
        <script type="text/javascript" language="javascript" src="../common/js/home.js"></script>
        <script type="text/javascript" language="javascript" src="../controller/js/ac_tblmenu.js"></script>

        <?php $xajax->printJavascript('../common/php/xajax/'); ?>
    </head>
    <body>
        <div class="ui segment">
            <div class="ui left floated header">
                <i class="List Layout icon"></i>
                Items Menus
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
            <table id="tblMenu" class="display" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Descripci&oacute;n</th>
                        <th>Archivo</th>
                        <th>M&oacute;dulo</th>
                        <th>Orden</th>                        
                        <th>Creador</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
            </table>
        </div>
        <div id="sideBarFormMenu" class="ui very wide styled floating sidebar">
            <div id="divLoader" class="ui disabled dimmer">
                <div class="ui large text loader">Cargando...</div>
            </div>
            <br>
                <br>
                <br>
                <br>
                <br>
                <br>
            <form id="frmMenu" name="frmMenu" action="#" method="post">
                <input type="hidden" id="hdn_id" name="hdn_id" value="" />
                <input type="hidden" id="hdn_idaccion" name="hdn_idaccion" value="" />
                <input type="hidden" id="hdn_textaccion" name="hdn_textaccion" value="" />

                <h4 class="ui inverted navy top attached header">
                    <span id="labelFormMenu"></span>
                </h4>
                <div id="divForm1" class="ui form attached segment">
                    <div id="tabsMenu" class="ui top attached tabular menu">
                        <a class="active item" data-tab="first"><i class="grid layout icon"></i>Datos</a>
                        <a class="item" data-tab="second"><i class="browser icon"></i>Auditoria</a>
                    </div>
                    <div class="ui active bottom attached tab segment" data-tab="first">
                        <div class="field">
                            <label>Descripci&oacute;n</label>
                            <div class="ui mini icon input">
                                <input id="txt_descripcion" name="txt_descripcion" placeholder="Ingrese la descripci&oacute;n" type="text">
                                <i class="icon asterisk"></i>
                            </div>
                        </div>
                        <div class="field">
                            <label>Archivo</label>
                            <div class="ui mini icon input">
                                <input id="txt_archivo" name="txt_archivo" placeholder="Ingrese el nombre del archivo" type="text">
                                <i class="icon asterisk"></i>
                            </div>
                        </div>
                        <div class="field">
                            <label>Icono</label>
                            <div class="ui mini icon input">
                                <input id="txt_icono" name="txt_icono" placeholder="Ingrese el nombre del icono" type="text">
                                <i class="icon asterisk"></i>
                            </div>
                        </div>
                        <div class="field">
                            <label>Orden</label>
                            <div class="ui mini icon input">
                                <input id="txt_orden" name="txt_orden" placeholder="Ingrese el orden" type="text">
                                <i class="icon asterisk"></i>
                            </div>
                        </div>
                        <div class="field">
                            <label>Modulo</label>
                            <div id="divDropDownModulo" class="ui fluid selection dropdown">
                                <input type="hidden" id="txt_modulo" name="txt_modulo"/>
                                <div class="default text">Seleccione un modulo</div>
                                <i class="dropdown icon"></i>
                                <div id="divOptionDropDownModulo" class="menu"></div>
                            </div>
                        </div>
                        <div class="ui navy ribbon label">Acciones</div>
                        <table class="ui small table segment">
                            <thead>
                                <tr>
                                    <th colspan="2">
                                        <div class="field">
                                            <div class="ui page grid">
                                                <div class="row">
                                                    <div class="fifteen wide column"> 
                                                        <div id="divDropDownAccion" class="ui fluid selection dropdown">
                                                            <input type="hidden" id="hdn_accion" name="hdn_accion"/>
                                                            <div class="default text">Seleccione una acci&oacute;n</div>
                                                            <i class="dropdown icon"></i>
                                                            <div id="divOptionDropDownAccion" class="menu"></div>
                                                        </div>
                                                    </div>
                                                    <div class="one column">
                                                        <div id="btn_accion" class="ui mini teal icon button">
                                                            <i class="plus icon"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="tblAction"></tbody>
                        </table>
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
    </body>
</html>
