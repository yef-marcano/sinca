<?php

function save($form) {
    $objResponse = new xajaxResponse();
    $objTable = new md_tblsubcategoria();
    $action = "";
    $objTable->clvcodigo = strval(str_replace(',', '', $form['hdn_id']));
    $objTable->strdescripcion = $form['txt_descripcion'];
    $objTable->clvcategoria = $form['txt_categoria'];

    try {
        if ($objTable->clvcodigo == '') {
            $action = "insert";
            $return = $objTable->insert();
            $objResponse->assign('hdn_id', 'value', $return);
            $objResponse->script("$('#tblsubCategoria').dataTable()._fnAjaxUpdate();");
            $objResponse->script("$('#sideBarFormsubCategoria').sidebar('hide');");
            $objResponse->script("showMessage(1)");
        } else {
            $action = "update";
            $objTable->update();
            $objResponse->script("$('#tblsubCategoria').dataTable()._fnAjaxUpdate();");
            $objResponse->script("$('#sideBarFormsubCategoria').sidebar('hide');");
            $objResponse->script("showMessage(2)");
        }
    } catch (Exception $e) {
        $objResponse->script("showMessage(6)");
        logError("tblsubcategoria", $action, $e->getMessage());
    }
    return $objResponse;
}

function view($id) {
    $objResponse = new xajaxResponse();
    $objTable = new md_tblsubcategoria();
    $resultSet = null;

    try {
        $resultSet = $objTable->find("clvcodigo= " . $id);
        $row = pg_fetch_object($resultSet);
        $objResponse->assign('hdn_id', 'value', utf8_decode($row->clvcodigo));
        $objResponse->script("$('#divDropDownCategoria').dropdown('set selected', '" . $row->clvcategoria . "');");
        $objResponse->assign('txt_categoria', 'value', utf8_decode($row->clvcategoria));
        $objResponse->assign('txt_descripcion', 'value', utf8_decode($row->strdescripcion));
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
        logError("tblsubcategoria", "view", $e->getMessage());
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

function delete($id) {
    $objResponse = new xajaxResponse();
    $objTable = new md_tblsubcategoria();
    $objTable->clvcodigo =  $id;
    
    try {
        $objTable->delete();
        $objResponse->script("$('#tblsubCategoria').dataTable()._fnAjaxUpdate();");
        $objResponse->script("showMessage(3)");
    } catch (Exception $e) {
        $objResponse->script("showMessage(6)");
        logError("tblsubcategoria", 'delete', $e->getMessage());
    }
    return $objResponse;
}

$xajax->registerFunction('save');
$xajax->registerFunction('view');
$xajax->registerFunction('delete');
$xajax->registerFunction('findValuesDropDownCategoria');
?>