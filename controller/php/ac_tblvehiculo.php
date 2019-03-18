<?php

function save($form) {
    $objResponse = new xajaxResponse();
    $objTable = new md_tblvehiculo();
    $action = "";
    $objTable->clvcodigo = strval(str_replace(',', '', $form['hdn_id']));
    $objTable->strplaca = $form['txt_placa'];
    $objTable->strplaca_batea = $form['txt_placa_batea'];
    $objTable->strmarca = $form['txt_marca'];
    $objTable->strcolor = $form['txt_color'];

    try {
        if ($objTable->clvcodigo == '') {
            $action = "insert";
            $return = $objTable->insert();
            $objResponse->assign('hdn_id', 'value', $return);
            $objResponse->script("$('#tblVehiculo').dataTable()._fnAjaxUpdate();");
            $objResponse->script("$('#sideBarFormVehiculo').sidebar('hide');");
            $objResponse->script("showMessage(1)");
        } else {
            $action = "update";
            $objTable->update();
            $objResponse->script("$('#tblVehiculo').dataTable()._fnAjaxUpdate();");
            $objResponse->script("$('#sideBarFormVehiculo').sidebar('hide');");
            $objResponse->script("showMessage(2)");
        }
    } catch (Exception $e) {
        $objResponse->script("showMessage(6)");
        logError("tblvehiculo", $action, $e->getMessage());
    }
    return $objResponse;
}

function view($id) {
    $objResponse = new xajaxResponse();
    $objTable = new md_tblvehiculo();
    $resultSet = null;

    try {
        $resultSet = $objTable->find("clvcodigo= " . $id);
        $row = pg_fetch_object($resultSet);
        $objResponse->assign('hdn_id', 'value', utf8_decode($row->clvcodigo));
        $objResponse->assign('txt_placa', 'value', utf8_decode($row->strplaca));
        $objResponse->assign('txt_placa_batea', 'value', utf8_decode($row->strplaca_batea));
        $objResponse->assign('txt_marca', 'value', utf8_decode($row->strmarca));
        $objResponse->assign('txt_color', 'value', utf8_decode($row->strcolor));
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
        logError("tblvehiculo", "view", $e->getMessage());
    }
    return $objResponse;
}

function delete($id) {
    $objResponse = new xajaxResponse();
    $objTable = new md_tblvehiculo();
    $objTable->clvcodigo =  $id;
    
    try {
        $objTable->delete();
        $objResponse->script("$('#tblVehiculo').dataTable()._fnAjaxUpdate();");
        $objResponse->script("showMessage(3)");
    } catch (Exception $e) {
        $objResponse->script("showMessage(6)");
        logError("tblvehiculo", 'delete', $e->getMessage());
    }
    return $objResponse;
}

$xajax->registerFunction('save');
$xajax->registerFunction('view');
$xajax->registerFunction('delete');
?>