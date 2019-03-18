<?php

function save($form) {
    $objResponse = new xajaxResponse();
    $objTable = new md_tblconductor();
    $action = "";
    $objTable->clvcodigo = strval(str_replace(',', '', $form['hdn_id']));
    $objTable->strnacionalidad = $form['txt_naciondalidad'];
    $objTable->intcedula = $form['txt_cedula'];
    $objTable->strnombre = $form['txt_nombre'];
    $objTable->strapellido = $form['txt_apellido'];
    $objTable->strtelefono = $form['txt_telefono'];

    try {
        if ($objTable->clvcodigo == '') {
            $action = "insert";
            $return = $objTable->insert();
            $objResponse->assign('hdn_id', 'value', $return);
            $objResponse->script("$('#tblConductor').dataTable()._fnAjaxUpdate();");
            $objResponse->script("$('#sideBarFormConductor').sidebar('hide');");
            $objResponse->script("showMessage(1)");
        } else {
            $action = "update";
            $objTable->update();
            $objResponse->script("$('#tblConductor').dataTable()._fnAjaxUpdate();");
            $objResponse->script("$('#sideBarFormConductor').sidebar('hide');");
            $objResponse->script("showMessage(2)");
        }
    } catch (Exception $e) {
        $objResponse->script("showMessage(6)");
        logError("tblconductor", $action, $e->getMessage());
    }
    return $objResponse;
}

function view($id) {
    $objResponse = new xajaxResponse();
    $objTable = new md_tblconductor();
    $resultSet = null;

    try {
        $resultSet = $objTable->find("clvcodigo= " . $id);
        $row = pg_fetch_object($resultSet);
        $objResponse->assign('hdn_id', 'value', utf8_decode($row->clvcodigo));
        $objResponse->script("$('#divDropDownNacionalidad').dropdown('set selected', '" . $row->strnacionalidad . "');");
        $objResponse->assign('txt_nacionalidad', 'value', utf8_decode($row->strnacionalidad));
        $objResponse->assign('txt_cedula', 'value', utf8_decode($row->intcedula));
        $objResponse->assign('txt_nombre', 'value', utf8_decode($row->strnombre));
        $objResponse->assign('txt_apellido', 'value', utf8_decode($row->strapellido));
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
        logError("tblconductor", "view", $e->getMessage());
    }
    return $objResponse;
}

function delete($id) {
    $objResponse = new xajaxResponse();
    $objTable = new md_tblconductor();
    $objTable->clvcodigo =  $id;
    
    try {
        $objTable->delete();
        $objResponse->script("$('#tblConductor').dataTable()._fnAjaxUpdate();");
        $objResponse->script("showMessage(3)");
    } catch (Exception $e) {
        $objResponse->script("showMessage(6)");
        logError("tblconductor", 'delete', $e->getMessage());
    }
    return $objResponse;
}
function findValuesDropDownNacionalidad() {
    $objResponse = new xajaxResponse();
    $objTable = new md_saime();
    $resultSet = null;
    $html = "";

    try {
        $resultSet = $objTable->findSaime(" clvestatus= 0");
        $html.= "<div class='item active' data-value=''>Seleccione una Nacionalidad</div>";
        while ($row = pg_fetch_object($resultSet)) {
            $html.= "<div class='item' data-value='" . $row->clvcodigo . "'>" . utf8_decode($row->strnacionalidad) . "</div>";
        }
        $objResponse->assign('divOptionDropDownNacionalidad', 'innerHTML', $html);
        $objResponse->script("$('#divDropDownNacionalidad').dropdown();");
    } catch (Exception $e) {
        $objResponse->script("showMessage(6)");
        logError("strnacionalidad", "findValuesDropDownNacionalidad", $e->getMessage());
    }
    return $objResponse;
}

$xajax->registerFunction('save');
$xajax->registerFunction('view');
$xajax->registerFunction('delete');
//$xajax->registerFunction('findValuesDropDownNacionalidad');
?>