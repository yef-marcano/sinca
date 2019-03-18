<?php
session_start();
if (is_null($_SESSION["iduser"])) {
    header("Location: ./frm_login.php");
}

require ('../common/php/xajax/xajax_core/xajax.inc.php');
require ('../model/md_tblmenu.php');
require ('../model/md_tblusuario.php');

$xajax = new xajax();
$xajax->setCharEncoding('ISO-8859-1');
$xajax->configure('javascript URI', '../common/php/xajax/');
require('../common/php/home.php');
require('../controller/php/ac_home.php');
$xajax->setFlag('debug', false);
$xajax->processRequest();
?>
<html>
    <head>
        <title>SINCA</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <link rel="stylesheet" type="text/css" href="../common/css/semantic/packaged/css/semantic.css">
        <link rel="stylesheet" type="text/css" href="../common/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="../common/css/dataTables.bootstrap.css">
        <link rel="stylesheet" type="text/css" href="../common/css/home.css">

        <script type="text/javascript" language="javascript" src="../common/js/jquery-2.1.1.min.js"></script>
        <script type="text/javascript" language="javascript" src="../common/css/semantic/packaged/javascript/semantic.js"></script>
        <script type="text/javascript" language="javascript" src="../common/js/jquery.address.js"></script>
        <script type="text/javascript" language="javascript" src="../common/js/jquery.dataTables.js"></script>
        <script type="text/javascript" language="javascript" src="../common/js/dataTables.bootstrap.js"></script>
        <script type="text/javascript" language="javascript" src="../common/js/home.js"></script>
        <script type="text/javascript" language="javascript" src="../controller/js/ac_home.js"></script>
        <script type="text/javascript" language="javascript" src="../common/js/jquery-ui.js"></script>

        <?php $xajax->printJavascript('../common/php/xajax/'); ?>
    </head>
    <body style="min-height: 35rem;">

        <input type="hidden" id="hdn_cambiar_clave" name="hdn_cambiar_clave" value="<?php echo $_SESSION['cambiarClave']; ?>" />

        <div class="navbar navbar-fixed-top" >
            <img src="../common/img/banner_censo.jpg" style="width: 100%; height: 130px;">
        </div>
        <div id="divMenu" class="ui navy large menu" style="margin-top: 140px;"></div>

        <div id="divloader" style="margin-top: -10px"></div>

        <div id="divModal" class="ui small modal">
            <div class=" header">
                <i class="lock icon"></i>
                Actualizar Contrase&ntilde;a
            </div>
            <div class="content">
                <form id="formHome" name="formHome" action="#">
                    <div id="divFormHome" class="ui form attached segment">
                        <div class="two fields">
                            <div class="field">
                                <label>Nueva Contrase&ntilde;a</label>
                                <div class="ui mini icon input">
                                    <input id="txt_password_home" name="txt_password_home" placeholder="Ingrese la contrase&ntilde;a" type="password">
                                    <i class="icon asterisk"></i>
                                </div>
                            </div>
                            <div class="field">
                                <label>Confirmar Contrase&ntilde;a</label>
                                <div class="ui mini icon input">
                                    <input id="txt_password_repeat_home" name="txt_password_repeat_home" placeholder="Confirme la contrase&ntilde;a" type="password">
                                    <i class="icon asterisk"></i>
                                </div>
                            </div>
                        </div>

                    </div>
                    <br />
                    <div style="float: right">
                        <div id="btn_close" class="disabled mini ui red icon button">
                            <i class="delete icon"></i>
                            Cancelar
                        </div>
                        <div id="btn_update" class="mini ui green icon submit button">
                            <i class="save icon "></i>
                            Guardar
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </body>
</html>
