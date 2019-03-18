<?php

function save($form) {
    $objResponse = new xajaxResponse();
    $objTable = new md_tblsolicitud();
    $objTableDetalle = new md_tbldetalle_solicitud();
    $action = "";
    $objTable->clvcodigo = strval(str_replace(',', '', $form['hdn_id']));
    $fecha = new DateTime($form['txt_fecha']);
    $objTable->dtmfecha = $fecha->format("Y-m-d");
    $objTable->memobservacion = $form['txt_observacion'];
    $objTable->clvestatus_solicitud = '2';
    $objTable->clvproyecto = $form['txt_proyecto'];
    try {

        if ($form['txt_insumo'] != "") {
            for ($i = 0; $i < count($form['txt_insumo']); $i++) {
                if ($form['txt_insumo'][$i] != "") {
                     
                    $resultSet = $objTableDetalle->find("clvsolicitud=" . $objTable->clvcodigo . " and clvinsumo=" . $form['txt_insumo'][$i]);
                    $row = pg_fetch_object($resultSet);
                    $objTableDetalle->clvcodigo = $row->clvcodigo;
                    $objTableDetalle->clvunidad_despacho = $form['txt_unidad_despachada'][$i];
                    $returnTorre = $objTableDetalle->updateUnidadDespacho();
                }
            }
        }
        $objResponse->assign('hdn_id', 'value', $return);
        $objResponse->script("$('#tblSolicitud').dataTable()._fnAjaxUpdate();");
        $objResponse->script("$('#sideBarFormSolicitud').sidebar('hide');");
        $objResponse->alert("Solicitud Aprobada con exito!");
        $objResponse->script('location.href="frm_home.php"');
    } catch (Exception $e) {
        $objResponse->script("showMessage(6)");
        logError("tblsolicitud", $action, $e->getMessage());
    }
    return $objResponse;
}

function findValuesDropDownProyecto() {
    $objResponse = new xajaxResponse();
    $objTable = new md_tblproyecto();
    $resultSet = null;
    $html = "";

    try {
        $resultSet = $objTable->find("clvestatus= 0");
        $html.= "<option value=''>Seleccione una opci&oacute;n</option>";
        while ($row = pg_fetch_object($resultSet)) {
            $html.= "<option value='" . $row->clvcodigo . "'>" . htmlentities($row->strnombre) . "</div>";
        }
        $objResponse->assign('txt_proyecto', 'innerHTML', $html);
        $objResponse->script("$('#txt_proyecto').select2({width: '100%',placeholder: 'Selecione una opcion'});");
    } catch (Exception $e) {
        $objResponse->script("showMessage(6)");
        logError("tblproyecto", "findValuesDropDownProyecto", $e->getMessage());
    }
    return $objResponse;
}

function findValuesDropDownInsumo($object) {
    $objResponse = new xajaxResponse();
    $objTable = new md_tblinsumo();
    $resultSet = null;
    $html = "";

    try {
        $resultSet = $objTable->find(" clvestatus= 0");
        $html.= "<option value=''>Seleccione un Insumo</div>";
        while ($row = pg_fetch_object($resultSet)) {
            $html.= "<option value='" . $row->clvcodigo . "'>" . utf8_decode($row->strdescripcion) . "</div>";
        }
        $objResponse->assign($object, "innerHTML", $html);
        $objResponse->script("$('#" . $object . "').select2({width: '100%',placeholder: 'Selecione una opcion'});");
    } catch (Exception $e) {
        $objResponse->script("showMessage(6)");
        logError("tblinsumo", "findValuesDropDownInsumo", $e->getMessage());
    }
    return $objResponse;
}

function findValuesDropDownUnidad_despachada($object) {
    $objResponse = new xajaxResponse();
    $objTable = new md_tblunidad_despacho();
    $resultSet = null;
    $html = "";

    try {
        $resultSet = $objTable->find(" clvestatus= 0");
        $html.= "<option value=''>Seleccione un Despacho</div>";
        while ($row = pg_fetch_object($resultSet)) {
            $html.= "<option value='" . $row->clvcodigo . "'>" . utf8_decode($row->strdescripcion) . "</div>";
        }
        $objResponse->assign($object, "innerHTML", $html);
        $objResponse->script("$('#" . $object . "').select2({width: '100%',placeholder: 'Selecione una opcion'});");
    } catch (Exception $e) {
        $objResponse->script("showMessage(6)");
        logError("tblunidad_despacho", "findValuesDropDownUnidad_despacho", $e->getMessage());
    }
    return $objResponse;
}

function delete($id) {
    $objResponse = new xajaxResponse();
    $objTable = new md_tblsolicitud();
    $objTable->clvcodigo = $id;

    try {
        $objTable->delete();
        $objResponse->script("$('#tblSolicitud').dataTable()._fnAjaxUpdate();");
        $objResponse->script("showMessage(3)");
    } catch (Exception $e) {
        $objResponse->script("showMessage(6)");
        logError("tblsolicitud", 'delete', $e->getMessage());
    }
    return $objResponse;
}

function denegaData($id) {
    $objResponse = new xajaxResponse();
    $objTable = new md_tblsolicitud();
    $objTable->clvcodigo = $id;
//    $objTable->clvestatus_solicitud =  $es;
    //$objTable2->clvestatus_solicitud =  $id;

    try {
        $objTable->update("clvestatus_solicitud= ");
        $objResponse->script("$('#tblSolicitud').dataTable()._fnAjaxUpdate();");
        $objResponse->script("showMessage(3)");
    } catch (Exception $e) {
        $objResponse->script("showMessage(6)");
        logError("tblsolicitud", 'Update', $e->getMessage());
    }
    return $objResponse;
}

function view($id) {
    $objResponse = new xajaxResponse();
    $objTable = new md_tblsolicitud();
    $objTableDetalle = new md_tbldetalle_solicitud();

    $resultSet = null;

    $resultSetDetalle = null;

    $scriptrDetalle = "";

    try {
        $resultSet = $objTable->find("clvcodigo= " . $id);
        $row = pg_fetch_object($resultSet);
        $objResponse->assign('hdn_id', 'value', utf8_decode($row->clvcodigo));

        $objResponse->script("$('#txt_proyecto').select2('val', '" . $row->clvproyecto . "');");

        $fecha = new DateTime($row->dtmfecha);
        $objResponse->assign('txt_fecha', 'value', $fecha->format('d-m-Y'));
        $objResponse->assign('txt_observacion', 'value', utf8_decode($row->memobservacion));



        $numTr = 1;
        $resultSetDetalle = $objTableDetalle->find("clvsolicitud= " . $id);
        while ($rowDetalle = pg_fetch_object($resultSetDetalle)) {
            $scriptrDetalle.= "insertTr();";

            $scriptrDetalle.= "$('#txt_insumo_" . $numTr . "').select2('val', '" . $rowDetalle->clvinsumo . "');";

            $scriptrDetalle.= "$('#txt_cantidad_solicitada_" . $numTr . "').val('" . $rowDetalle->intcantidad_solicitada . "');";
            $scriptrDetalle.= "$('#txt_cantidad_solicitada_" . $numTr . "').blur();";
            $numTr++;
        }
        $objResponse->script($scriptrDetalle);

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
        logError("tblproyecto", "view", $e->getMessage());
    }
    return $objResponse;
}

$xajax->registerFunction('save');
$xajax->registerFunction('findValuesDropDownEstatus_solicitud');
$xajax->registerFunction('findValuesDropDownInsumo');
$xajax->registerFunction('findValuesDropDownProyecto');
$xajax->registerFunction('findValuesDropDownUnidad_despachada');
$xajax->registerFunction('view');
$xajax->registerFunction('delete');
$xajax->registerFunction('denegaData');
?>