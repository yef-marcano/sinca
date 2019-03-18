<?php

function save($form) {
    $objResponse = new xajaxResponse();
    $objTable = new md_tblusuario();
    $action = "";
    $objTable->clvcodigo = strval(str_replace(',', '', $form['hdn_id']));
    $objTable->strnombre = $form['txt_nombre'];
    $objTable->strapellido = $form['txt_apellido'];
    $objTable->strusuario = $form['txt_usuario'];
    $objTable->strpassword = $form['txt_password'];
    $objTable->clvcambiar_clave = $form['txt_cambiar_clave'];
    $objTable->clvperfil = $form['txt_perfil'];
    if($form['txt_estado'] != ""){
        $objTable->clvestado = $form['txt_estado'];
    }else{
        $objTable->clvestado = -1;
    }
//    if($form['txt_base_mision'] != ""){
//        $objTable->id_base_mision = $form['txt_base_mision'];
//    }else{
//        $objTable->id_base_mision = -1;
//    }

    try {
        if ($objTable->clvcodigo == '') {
            $action = "insert";
            $return = $objTable->insert();
            $objResponse->assign('hdn_id', 'value', $return);
            $objResponse->script("$('#tblUsuario').dataTable()._fnAjaxUpdate();");
            $objResponse->script("$('#sideBarFormUsuario').sidebar('hide');");
            $objResponse->script("showMessage(1)");
        } else {
            $action = "update";
            $objTable->update();
            $objResponse->script("$('#tblUsuario').dataTable()._fnAjaxUpdate();");
            $objResponse->script("$('#sideBarFormUsuario').sidebar('hide');");
            $objResponse->script("showMessage(2)");
        }
    } catch (Exception $e) {
        $objResponse->script("showMessage(6)");
        logError("tblusuario", $action, $e->getMessage());
    }
    return $objResponse;
}

function view($id) {
    $objResponse = new xajaxResponse();
    $objTable = new md_tblusuario();
    $resultSet = null;

    try {
        $resultSet = $objTable->find("clvcodigo= " . $id);
        $row = pg_fetch_object($resultSet);
        
        $objResponse->assign('hdn_id', 'value', utf8_decode($row->clvcodigo));
        $objResponse->assign('txt_nombre', 'value', utf8_decode($row->strnombre));
        $objResponse->assign('txt_apellido', 'value', utf8_decode($row->strapellido));
        $objResponse->assign('txt_usuario', 'value', utf8_decode($row->strusuario));
        $objResponse->script("$('#divDropDownCambiarClave').dropdown('set selected', '" . $row->clvcambiar_clave. "');");
        $objResponse->assign('txt_cambiar_clave', 'value', utf8_decode($row->clvcambiar_clave));
        $objResponse->script("$('#divDropDownPerfil').dropdown('set selected', '" . $row->clvperfil. "');");
        $objResponse->assign('txt_perfil', 'value', utf8_decode($row->clvperfil));
        $objResponse->script("$('#divDropDownEstado').dropdown('set selected', '" . $row->clvestado. "');");
        $objResponse->assign('txt_estado', 'value', utf8_decode($row->clvestado));        
        $fechaCreacion = new DateTime($row->dtmfecha_creacion);
        $objResponse->assign('lbl_usuarioCreador', 'innerHTML', utf8_decode($row->nombre_usuario_creador));
        $objResponse->assign('lbl_fechaCreador', 'innerHTML', $fechaCreacion->format("d-m-Y h:i:s A"));
        
        if ($row->dtmfecha_modificacion != "0000-00-00 00:00:00") {
            $fechaModificacion = new DateTime($row->dtmfecha_modificacion);
            $objResponse->assign('lbl_usuarioModificacion', 'innerHTML', utf8_decode($row->nombre_usuario_modificar));
            $objResponse->assign('lbl_fechaModificacion', 'innerHTML', $fechaModificacion->format("d-m-Y h:i:s A"));
        }
        if ($row->dtmfecha_eliminacion != "0000-00-00 00:00:00") {
            $fechaEliminacion = new DateTime($row->dtmfecha_eliminacion);
            $objResponse->assign('lbl_usuarioEliminacion', 'innerHTML', utf8_decode($row->nombre_usuario_eliminar));
            $objResponse->assign('lbl_fechaEliminacion', 'innerHTML', $fechaEliminacion->format("d-m-Y h:i:s A"));
        }
        $objResponse->script("$('#divLoader').removeClass('active').addClass('disabled');");
    } catch (Exception $e) {
        $objResponse->script("showMessage(6)");
        logError("tblusuario", "view", $e->getMessage());
    }
    return $objResponse;
}

function delete($id) {
    $objResponse = new xajaxResponse();
    $objTable = new md_tblusuario();
    $objTable->clvcodigo =  $id;
    
    try {
        $objTable->delete();
        $objResponse->script("$('#tblUsuario').dataTable()._fnAjaxUpdate();");
        $objResponse->script("showMessage(3)");
    } catch (Exception $e) {
        $objResponse->script("showMessage(6)");
        logError("tblusuario", 'delete', $e->getMessage());
    }
    return $objResponse;
}

function findValuesDropDownPerfil() {
    $objResponse = new xajaxResponse();
    $objTable = new md_tblperfil();
    $resultSet = null;
    $html = "";

    try {
        $resultSet = $objTable->find(" clvestatus= 0");
        $html.= "<div class='item active' data-value=''>Seleccione un perfil</div>";
        while ($row = pg_fetch_object($resultSet)) {
            $html.= "<div class='item' data-value='" . $row->clvcodigo . "'>" . utf8_decode($row->strdescripcion) . "</div>";
        }
        $objResponse->assign('divOptionDropDownPerfil', 'innerHTML', $html);
        $objResponse->script("$('#divDropDownPerfil').dropdown();");
    } catch (Exception $e) {
        $objResponse->script("showMessage(6)");
        logError("tblusuario", "findValuesDropDownPerfil", $e->getMessage());
    }
    return $objResponse;
}

function findValuesDropDownEstado() {
    $objResponse = new xajaxResponse();
    $objTable = new md_utility();
    $resultSet = null;
    $html = "";

    try {
        $resultSet = $objTable->findEstado("not borrado ORDER BY estado");
        $html.= "<div class='item active' data-value=''>Seleccione un estado</div>";
        while ($row = pg_fetch_object($resultSet)) {
            $html.= "<div class='item' data-value='" . $row->cod_estado . "'>" . utf8_decode($row->estado) . "</div>";
        }
        $objResponse->assign('divOptionDropDownEstado', 'innerHTML', $html);
        $objResponse->script("$('#divDropDownEstado').dropdown();");
    } catch (Exception $e) {
        $objResponse->script("showMessage(6)");
        logError("clvestado", "findValuesDropDownEstado", $e->getMessage());
    }
    return $objResponse;
}

//function findValuesDropDownBaseMision() {
//    $objResponse = new xajaxResponse();
//    $objTable = new md_tblbase_misiones();
//    $resultSet = null;
//    $html = "";
//
//    try {
//        $resultSet = $objTable->find("estatus != 3 ORDER BY nombre_base_mision");
//        $html.= "<div class='item active' data-value=''>Seleccione una base de misi&oacute;n</div>";
//        while ($row = pg_fetch_object($resultSet)) {
//            $html.= "<div class='item' data-value='" . $row->id_base_mision . "'>" . htmlentities(utf8_encode($row->nombre_base_mision)) . "</div>";
//        }
//        $objResponse->assign('divOptionDropDownBaseMision', 'innerHTML', $html);
//        $objResponse->script("$('#divDropDownBaseMision').dropdown();");
//    } catch (Exception $e) {
//        $objResponse->script("showMessage(6)");
//        logError("tbl_base_misiones", "findValuesDropDownBaseMision", $e->getMessage());
//    }
//    return $objResponse;
//}

$xajax->registerFunction('save');
$xajax->registerFunction('findValuesDropDownPerfil');
$xajax->registerFunction('findValuesDropDownEstado');
$xajax->registerFunction('view');
$xajax->registerFunction('delete');
//$xajax->registerFunction('findValuesDropDownBaseMision');
?>