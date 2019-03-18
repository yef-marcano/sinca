<?php

function buildMenu() {
    $objResponse = new xajaxResponse();
    $objTable = new md_tblmenu();
    $resultSet = null;
    $condition = "";
    $html = "";
    $idModulo = null;

    try {
        $resultSet = $objTable->findByPerfil($_SESSION['perfil']);
        while ($row = pg_fetch_object($resultSet)) {
            if ($idModulo == null) {
                $idModulo = $row->clvmodulo;
                $html.= "<a class='ui dropdown item'>
                            <i class='" . $row->icons_modulo . "'></i>
                            " . utf8_decode($row->nombre_modulo) . "
                            <i class='dropdown icon'></i>
                            <div class='menu ui transition hidden'>";
            } else if ($idModulo != $row->clvmodulo) {
                $idModulo = $row->clvmodulo;
                $html.= "   </div>
                        </a>
                        <a class='ui dropdown item'>
                            <i class='" . $row->icons_modulo . "'></i>
                            " . utf8_decode($row->nombre_modulo) . "
                            <i class='dropdown icon'></i>
                            <div class='menu ui transition hidden'>";
            }
            $html.= "           <div class='item' onclick='cargar(\"" . utf8_decode($row->strarchivo) . "\", \"" . utf8_decode($row->nombre_modulo) . "\", \"" . $row->stricono . "\")'><i class='" . $row->stricono . "'></i>" . utf8_decode($row->strdescripcion) . "</div>";
        }
        $html.= "</div></a><a class='item' href='../uploads/manual.pdf' target='_blank'><i class='question icon'></i>Ayuda</a>";
        $html.= "<div class='right menu'>
                    <div class='item'>
                        <i class='user medium icon'></i>
                        <span onclick='abrirModal(true)' style='cursor:pointer;'>" . $_SESSION['nombreUsuario'] . "</span>
                    </div>
                    <div class='item'>
                        <div class='ui mini red button' onclick='cerrarSession();'>Cerrar Sesi&oacute;n</div>
                    </div>
                  </div>";
        $objResponse->assign("divMenu", "innerHTML", $html);
    } catch (Exception $e) {
        $objResponse->script("showMessage(6)");
        logError("tblmenu", "view", $e->getMessage());
    }
    return $objResponse;
}

function validUser($form) {
    $objResponse = new xajaxResponse();
    $objTable = new md_tblusuario();
    $resultSet = null;
    $condition = "";

    try {
        $condition = "UPPER(strusuario)= UPPER('" . $form['txt_user'] . "') AND strpassword = MD5('" . $form['txt_pass'] . "') AND clvestatus = 0";
        $resultSet = $objTable->find($condition);

        if (pg_num_rows($resultSet) > 0) {
            $row = pg_fetch_object($resultSet);
            $_SESSION['nombreUsuario'] = $row->strnombre . " " . $row->strapellido;
            $_SESSION['iduser'] = $row->clvcodigo;
            $_SESSION['user'] = $row->strusuario;
            $_SESSION['perfil'] = $row->clvperfil;
            $_SESSION['region'] = $row->id_region;
            $_SESSION['estado'] = $row->clvestado;
            $_SESSION['proyecto'] = $row->clvproyecto;
            $_SESSION['cambiarClave'] = $row->clvcambiar_clave;
            $objResponse->script('location.href="frm_home.php"');
        } else {
            $row = pg_fetch_object($resultSet);
            $objResponse->script("showMessage2('divMessageColumn', 2, 'Usuario o Contrase&ntilde;a no coinciden');");
        }
    } catch (Exception $e) {
        $objResponse->script("showMessage(6)");
        logError("tblusuario", "view", $e->getMessage());
    }
    return $objResponse;
}

function validAction($menu, $accion) {
    $objResponse = new xajaxResponse();
    $objTable = new md_tblperfil_menu_accion();

    $result = $objTable->checkAction($_SESSION['perfil'], $menu, $accion);

    $objResponse->setReturnValue($result);

    return $objResponse;
}

function updatePassword($form) {
    $objResponse = new xajaxResponse();
    $objTable = new md_tblusuario();

    try {
        $objTable->clvcodigo= $_SESSION['iduser'];
        $objTable->strpassword= $form['txt_password_home'];
        $objTable->clvcambiar_clave= 0;
        $objTable->updatePassword();
        
        $_SESSION['cambiarClave']= 0;
        $objResponse->alert("Contrase&ntilde;a Actualizada con exito!");
        $objResponse->script("$('#divModal').modal('hide');");
        $objResponse->assign("hdn_cambiar_clave", "value", 0);        
    } catch (Exception $e) {
        $objResponse->script("showMessage(6)");
        logError("tblusuario", "view", $e->getMessage());
    }
    return $objResponse;
}
//$xajax->register(XAJAX_FUNCTION,'buildMenu'); //--
//$xajax->register(XAJAX_FUNCTION,'validUser'); //--
//$xajax->register(XAJAX_FUNCTION,'validAction'); //--
//$xajax->register(XAJAX_FUNCTION,'updatePassword'); //--
$xajax->registerFunction('buildMenu');
$xajax->registerFunction('validUser');
$xajax->registerFunction('validAction');
$xajax->registerFunction('updatePassword');
?>