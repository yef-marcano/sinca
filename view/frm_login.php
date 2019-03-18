<?php
session_start();

require ('../common/php/xajax/xajax_core/xajax.inc.php');
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
        <link rel="stylesheet" type="text/css" href="../common/css/home.css">

        <script type="text/javascript" language="javascript" src="../common/js/jquery-2.1.1.min.js"></script>
        <script type="text/javascript" language="javascript" src="../common/css/semantic/packaged/javascript/semantic.js"></script>
        <script type="text/javascript" language="javascript" src="../common/js/home.js"></script>
        <script type="text/javascript" language="javascript" src="../controller/js/ac_login.js"></script>

        <?php $xajax->printJavascript('../common/php/xajax/'); ?>
    </head>
    <body>
        <div class="navbar navbar-fixed-top" >
            <img src="../common/img/banner_censo.jpg" style="width: 100%; height: 130px;">
        </div>
        <!--<div class="bannerLogin">
            <div class="banner-up"></div>
            <a href="#" target="_BLANK" class="img-logo">
                <img src="../common/img/log_saber_trabajo.png" height="100px">
            </a>            
        </div>-->
        <div style="min-height: 18rem;">&nbsp;</div>
        <div class="ui five column grid">
            <div class="five column row">
                <div class="column"></div>
                <div class="column"></div>
                <div id='divMessageColumn' class="column"></div>
                <div class="column"></div>
                <div class="column"></div>
            </div>
        </div>
        <div class="ui grid">
            <div class="five wide column">
            </div>
            <div class="six wide column">
                <form id="frmLogin" name="frmLogin" action="#" method="post">
                    <div id="divForm1" class="ui red raised secondary fluid form segment">
                        <h3 class="ui header">Iniciar Sesi&oacute;n</h3>
                        <div class="field">
                            <label>Usuario</label>
                            <div class="ui left icon input">
                                <input id="txt_user" name="txt_user" placeholder="Ingrese su usuario" type="text">
                                <i class="user icon"></i>
                            </div>
                        </div>
                        <div class="field">
                            <label>Contrase&ntilde;a</label>
                            <div class="ui left icon input">
                                <input id="txt_pass" name="txt_pass" type="password" placeholder="Ingrese su contrase&ntilde;a">
                                <i class="lock icon"></i>
                            </div>                        
                        </div>
                        <div class="ui green submit button" style="float: right;">Acceder</div>
                    </div>
                </form>
            </div>
            <div class="four five column">
            </div>
        </div>
    </body>
</html>
