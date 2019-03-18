<?php

function save($form) {
    $objResponse = new xajaxResponse();
    $objTable = new md_tblinsumo();
    $action = "";
    $objTable->clvcodigo = strval(str_replace(',', '', $form['hdn_id']));
    $objTable->strdescripcion = $form['txt_descripcion'];
    $objTable->sngprecio = $form['txt_precio'];
    $objTable->sngprecio_privada = $form['txt_precioprivada'];
    $objTable->intexistencia_minima = $form['txt_existencia_minima'];
    $objTable->intexistencia_maxima = $form['txt_existencia_maxima'];
    $objTable->clvcategoria = $form['txt_categoria'];
    $objTable->clvsubcategoria = $form['txt_subcategoria'];
    $objTable->clvunidad_medida = $form['txt_unidadmedida'];

    try {
        if ($objTable->clvcodigo == '') {
            $action = "insert";
            $return = $objTable->insert();
            $objResponse->assign('hdn_id', 'value', $return);
            $objResponse->script("$('#tblInsumo').dataTable()._fnAjaxUpdate();");
            $objResponse->script("$('#sideBarFormInsumo').sidebar('hide');");
            $objResponse->script("showMessage(1)");
        } else {
            $action = "update";
            $objTable->update();
            $objResponse->script("$('#tblInsumo').dataTable()._fnAjaxUpdate();");
            $objResponse->script("$('#sideBarFormInsumo').sidebar('hide');");
            $objResponse->script("showMessage(2)");
        }
    } catch (Exception $e) {
        $objResponse->script("showMessage(6)");
        logError("tblinsumo", $action, $e->getMessage());
    }
    return $objResponse;
}

function view($id) {
    $objResponse = new xajaxResponse();
    $objTable = new md_tblinsumo();
    $resultSet = null;

    try {
        $resultSet = $objTable->find("clvcodigo= " . $id);
        $row = pg_fetch_object($resultSet);
        $objResponse->assign('hdn_id', 'value', utf8_decode($row->clvcodigo));
        $objResponse->script("$('#divDropDownCategoria').dropdown('set selected', '" . $row->clvcategoria . "');");
        $objResponse->assign('txt_categoria', 'value', utf8_decode($row->clvcategoria));
        $objResponse->script("$('#divDropDownsubCategoria').dropdown('set selected', '" . $row->clvsubcategoria . "');");
        $objResponse->assign('txt_subcategoria', 'value', utf8_decode($row->clvsubcategoria));
        $objResponse->script("$('#divDropDownUnidadmedida').dropdown('set selected', '" . $row->clvunidad_medida . "');");
        $objResponse->assign('txt_unidadmedida', 'value', utf8_decode($row->clvunidad_medida));
        $objResponse->assign('txt_descripcion', 'value', utf8_decode($row->strdescripcion));
        $objResponse->assign('txt_precio', 'value', utf8_decode($row->sngprecio));
        $objResponse->assign('txt_precioprivada', 'value', utf8_decode($row->sngprecio_privada));
        $objResponse->assign('txt_existencia_minima', 'value', utf8_decode($row->intexistencia_minima));
        $objResponse->assign('txt_existencia_maxima', 'value', utf8_decode($row->intexistencia_maxima));
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
        logError("tblinsumo", "view", $e->getMessage());
    }
    return $objResponse;
}
function findValuesDropDownCategoria() {
    $objResponse = new xajaxResponse();
    $objTable = new md_tblcategoria();
    $resultSet = null;
    $html = "";

    try {
        $resultSet = $objTable->find(" clvestatus= 0");
        $html.= "<div class='item active' data-value=''>Seleccione una Categoria</div>";
        while ($row = pg_fetch_object($resultSet)) {
            $html.= "<div class='item' data-value='" . $row->clvcodigo . "'>" . utf8_decode($row->strdescripcion) . "</div>";
        }
        $objResponse->assign('divOptionDropDownCategoria', 'innerHTML', $html);
        $objResponse->script("$('#divDropDownCategoria').dropdown();");
    } catch (Exception $e) {
        $objResponse->script("showMessage(6)");
        logError("tblcemenetera", "findValuesDropDownCategoria", $e->getMessage());
    }
    return $objResponse;
}

function findValuesDropDownsubCategoria() {
    $objResponse = new xajaxResponse();
    $objTable = new md_tblsubcategoria();
    $resultSet = null;
    $html = "";

    try {
        $resultSet = $objTable->find(" clvestatus= 0");
        $html.= "<div class='item active' data-value=''>Seleccione una Sub-Categoria</div>";
        while ($row = pg_fetch_object($resultSet)) {
            $html.= "<div class='item' data-value='" . $row->clvcodigo . "'>" . utf8_decode($row->strdescripcion) . "</div>";
        }
        $objResponse->assign('divOptionDropDownsubCategoria', 'innerHTML', $html);
        $objResponse->script("$('#divDropDownsubCategoria').dropdown();");
    } catch (Exception $e) {
        $objResponse->script("showMessage(6)");
        logError("tblcemenetera", "findValuesDropDownsubCategoria", $e->getMessage());
    }
    return $objResponse;
}
function findValuesDropDownUnidadmedida() {
    $objResponse = new xajaxResponse();
    $objTable = new md_tblunidad_medida();
    $resultSet = null;
    $html = "";

    try {
        $resultSet = $objTable->find(" clvestatus= 0");
        $html.= "<div class='item active' data-value=''>Seleccione una Unidad de Medida</div>";
        while ($row = pg_fetch_object($resultSet)) {
            $html.= "<div class='item' data-value='" . $row->clvcodigo . "'>" . utf8_decode($row->strdescripcion) . "</div>";
        }
        $objResponse->assign('divOptionDropDownUnidadmedida', 'innerHTML', $html);
        $objResponse->script("$('#divDropDownUnidadmedida').dropdown();");
    } catch (Exception $e) {
        $objResponse->script("showMessage(6)");
        logError("tblcemenetera", "findValuesDropDownUnidadmedida", $e->getMessage());
    }
    return $objResponse;
}

function delete($id) {
    $objResponse = new xajaxResponse();
    $objTable = new md_tblinsumo();
    $objTable->clvcodigo =  $id;
    
    try {
        $objTable->delete();
        $objResponse->script("$('#tblInsumo').dataTable()._fnAjaxUpdate();");
        $objResponse->script("showMessage(3)");
    } catch (Exception $e) {
        $objResponse->script("showMessage(6)");
        logError("tblinsumo", 'delete', $e->getMessage());
    }
    return $objResponse;
}

$xajax->registerFunction('save');
$xajax->registerFunction('view');
$xajax->registerFunction('delete');
$xajax->registerFunction('findValuesDropDownCategoria');
$xajax->registerFunction('findValuesDropDownsubCategoria');
$xajax->registerFunction('findValuesDropDownUnidadmedida');
?>