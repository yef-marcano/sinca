<?php
session_start();
if (is_null($_SESSION["iduser"])) {
    header("Location: ./frm_login.php");
}

require ('../common/php/xajax/xajax_core/xajax.inc.php');
require ('../model/md_tblusuario.php');
require ('../model/md_tblperfil.php');
require ('../model/md_utility.php');
require ('../model/md_tblperfil_menu_accion.php');

$xajax = new xajax();
$xajax->setCharEncoding('ISO-8859-1');
$xajax->configure('javascript URI', '../common/php/xajax/');
require('../common/php/home.php');
require('../controller/php/ac_home.php');
require('../controller/php/ac_tblusuario.php');
$xajax->setFlag('debug', false);
$xajax->processRequest();
?>
<html>
    <head>
        <title>Usuarios</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <?php $xajax->printJavascript('../common/php/xajax/'); ?>
    </head>
    <body>
        <div class="ui segment">
            <div class="ui left floated header">
                <i class="user icon"></i>
                Usuarios
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
            <table id="tblUsuario" class="display" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Usuario</th>
                        <th>Perfil</th>                        
                        <th>&nbsp;</th>
                    </tr>
                </thead>
            </table>
        </div>
        <div id="sideBarFormUsuario" class="ui very wide styled floating sidebar">
            <div id="divLoader" class="ui disabled dimmer">
                <div class="ui large text loader">Cargando...</div>
            </div>
            <br>
                <br>
                <br>
                <br>
                <br>
                <br>
            <form id="frmUsuario" name="frmUsuario" action="#" method="post">
                <input type="hidden" id="hdn_id" name="hdn_id" value="" />

                <h4 class="ui inverted navy top attached header">
                    <span id="labelFormUsuario"></span>
                </h4>
                <div id="divForm1" class="ui form attached segment">
                    <div id="tabsUsuario" class="ui top attached tabular menu">
                        <a class="active item" data-tab="first"><i class="grid layout icon"></i>Datos</a>
                        <a class="item" data-tab="second"><i class="browser icon"></i>Auditoria</a>
                    </div>
                    <div class="ui active bottom attached tab segment" data-tab="first">
                        <div class="two fields">
                            <div class="field">
                                <label>Nombre</label>
                                <div class="ui mini icon input">
                                    <input id="txt_nombre" name="txt_nombre" placeholder="Ingrese el nombre" type="text">
                                    <i class="icon asterisk"></i>
                                </div>
                            </div>
                            <div class="field">
                                <label>Apellido</label>
                                <div class="ui mini icon input">
                                    <input id="txt_apellido" name="txt_apellido" placeholder="Ingrese el apellido" type="text">
                                    <i class="icon asterisk"></i>
                                </div>
                            </div>
                        </div>
                        <div class="field">
                            <label>Usuario</label>
                            <div class="ui mini icon input">
                                <input id="txt_usuario" name="txt_usuario" placeholder="Ingrese el usuario" type="text">
                                <i class="icon asterisk"></i>
                            </div>
                        </div>
                        <div class="two fields">
                            <div class="field">
                                <label>Contrase&ntilde;a</label>
                                <div class="ui mini icon input">
                                    <input id="txt_password" name="txt_password" placeholder="Ingrese la contrase&ntilde;a" type="password">
                                    <i class="icon asterisk"></i>
                                </div>
                            </div>
                            <div class="field">
                                <label>Confirmar Contrase&ntilde;a</label>
                                <div class="ui mini icon input">
                                    <input id="txt_password_repeat" name="txt_password_repeat" placeholder="Confirme la contrase&ntilde;a" type="password">
                                    <i class="icon asterisk"></i>
                                </div>
                            </div>
                        </div>
                        <div class="field">
                            <label>Cambiar clave al iniciar sesi&oacute;n?</label>
                            <div id="divDropDownCambiarClave" class="ui fluid selection dropdown">
                                <input type="hidden" id="txt_cambiar_clave" name="txt_cambiar_clave"/>
                                <div class="default text">Seleccione una opci&oacute;n</div>
                                <i class="dropdown icon"></i>
                                <div id="divOptionDropDownCambiarClave" class="menu">
                                    <div class='item active' data-value=''>Seleccione una opci&oacute;n</div>
                                    <div class='item' data-value='0'>No</div>
                                    <div class='item' data-value='1'>Si</div>
                                </div>
                            </div>
                        </div>
                        <div class="field">
                            <label>Perfil</label>
                            <div id="divDropDownPerfil" class="ui fluid selection dropdown">
                                <input type="hidden" id="txt_perfil" name="txt_perfil"/>
                                <div class="default text">Seleccione un perfil</div>
                                <i class="dropdown icon"></i>
                                <div id="divOptionDropDownPerfil" class="menu"></div>
                            </div>
                        </div>
                        <div class="field">
                            <label>Estado</label>
                            <div id="divDropDownEstado" class="ui fluid selection dropdown">
                                <input type="hidden" id="txt_estado" name="txt_estado"/>
                                <div class="default text">Seleccione un estado</div>
                                <i class="dropdown icon"></i>
                                <div id="divOptionDropDownEstado" class="menu"></div>
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
            <script type="text/javascript" language="javascript" src="../controller/js/ac_tblusuario.js"></script>
        </div>
    </body>
</html>
