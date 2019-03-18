<?php

function save($form) {
    $objResponse = new xajaxResponse();
    $objTable = new md_tblmenu();
    $objTableRelation = new md_tblmenu_accion();
    $action = "";
    
    $objTable->clvcodigo = strval(str_replace(',', '', $form['hdn_id']));
    $objTable->clvmodulo = strval(str_replace(',', '', $form['txt_modulo']));
    $objTable->strdescripcion = $form['txt_descripcion'];
    $objTable->strarchivo = $form['txt_archivo'];
    $objTable->stricono = $form['txt_icono'];
    $objTable->intorden = $form['txt_orden'];
    try {
        if ($objTable->clvcodigo == '') {
            $action = "insert";
            $return = $objTable->insert();
            $menuActions = split(",", $form['hdn_idaccion']);
            for ($i = 0; $i < count($menuActions); $i++) {
                if ($menuActions[$i] != "") {
                    $objTableRelation->clvmenu = $return;
                    $objTableRelation->clvaccion = $menuActions[$i];
                    $objTableRelation->insert();
                }
            }
            $objResponse->assign('hdn_id', 'value', $return);
            $objResponse->script("$('#tblMenu').dataTable()._fnAjaxUpdate();");
            $objResponse->script("$('#sideBarFormMenu').sidebar('hide');");
            $objResponse->script("showMessage(1)");
        } else {
            $action = "update";
            $objTable->update();
            $objTableRelation->deleteByIdMenu($objTable->clvcodigo);
            $menuActions = split(",", $form['hdn_idaccion']);
            for ($i = 0; $i < count($menuActions); $i++) {
                if ($menuActions[$i] != "") {
                    $objTableRelation->clvmenu = $objTable->clvcodigo;
                    $objTableRelation->clvaccion = $menuActions[$i];
                    $objTableRelation->insert();
                }
            }
            $objResponse->script("$('#tblMenu').dataTable()._fnAjaxUpdate();");
            $objResponse->script("$('#sideBarFormMenu').sidebar('hide');");
            $objResponse->script("showMessage(2)");
        }
    } catch (Exception $e) {
        $objResponse->script("showMessage(6)");
        logError("tblmenu | tblmenu_accion", $action, $e->getMessage());
    }
    return $objResponse;
}

function view($id) {
    $objResponse = new xajaxResponse();
    $objTable = new md_tblmenu();
    $objTableRelation = new md_tblmenu_accion();
    $resultSet = null;
    $resultSetRelation = null;

    try {
        $resultSet = $objTable->find("clvcodigo= " . $id);
        $row = pg_fetch_object($resultSet);
        
        $objResponse->assign('hdn_id', 'value', utf8_decode($row->clvcodigo));
        $objResponse->assign('txt_descripcion', 'value', utf8_decode($row->strdescripcion));
        $objResponse->assign('txt_archivo', 'value', utf8_decode($row->strarchivo));
        $objResponse->assign('txt_icono', 'value', utf8_decode($row->stricono));
        $objResponse->assign('txt_orden', 'value', utf8_decode($row->intorden));
        $objResponse->script("$('#divDropDownCategoria').dropdown('set selected', '" . $row->clvcategoria . "');");
        $objResponse->assign('txt_modulo', 'value', utf8_decode($row->clvmodulo));
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
        
        $resultSetRelation = $objTableRelation->find("clvmenu= " . $id);
        while ($rowRelation = pg_fetch_object($resultSetRelation)) {
            $objResponse->script("insertActionTable('" . $rowRelation->nombre_accion . "', '" . $rowRelation->clvaccion . "');");
        }
        
        $objResponse->script("$('#divLoader').removeClass('active').addClass('disabled');");
    } catch (Exception $e) {
        $objResponse->script("showMessage(6)");
        logError("tblmenu | tblmenu_accion", "view", $e->getMessage());
    }
    return $objResponse;
}

function delete($id) {
    $objResponse = new xajaxResponse();
    $objTable = new md_tblmenu();
    $objTableRelation = new md_tblmenu_accion();
    $objTable->clvcodigo = $id;

    try {
        $objTableRelation->deleteByIdMenu($id);
        $objTable->delete();
        $objResponse->script("$('#tblMenu').dataTable()._fnAjaxUpdate();");
        $objResponse->script("showMessage(3)");
    } catch (Exception $e) {
        $objResponse->script("showMessage(6)");
        logError("tblmenu | tblmenu_accion", 'delete', $e->getMessage());
    }
    return $objResponse;
}

function findValuesDropDownModulo() {
    $objResponse = new xajaxResponse();
    $objTable = new md_tblmodulo();
    $resultSet = null;
    $html = "";

    try {
        $resultSet = $objTable->find("clvestatus != 1");
        $html.= "<div class='item active' data-value=''>Seleccione un modulo</div>";
        while ($row = pg_fetch_object($resultSet)) {
            $html.= "<div class='item' data-value='" . $row->clvcodigo . "'>" . utf8_decode($row->strdescripcion) . "</div>";
        }
        $objResponse->assign('divOptionDropDownModulo', 'innerHTML', $html);
        $objResponse->script("$('#divDropDownModulo').dropdown();");
    } catch (Exception $e) {
        $objResponse->script("showMessage(6)");
        logError("tblmodulo", "findValuesDropDownModulos", $e->getMessage());
    }
    return $objResponse;
}

function findValuesDropDownAccion() {
    $objResponse = new xajaxResponse();
    $objTable = new md_tblaccion();
    $resultSet = null;
    $html = "";

    try {
        $resultSet = $objTable->find('clvestatus!=1');
        $html.= "<div class='item active' data-value=''>Seleccione una acci&oacute;n</div>";
        while ($row = pg_fetch_object($resultSet)) {
            $html.= "<div class='item' data-value='" . $row->clvcodigo . "'>" . utf8_decode($row->strdescripcion) . "</div>";
        }
        $objResponse->assign('divOptionDropDownAccion', 'innerHTML', $html);
        $objResponse->script("$('#divDropDownAccion').dropdown();");
    } catch (Exception $e) {
        $objResponse->script("showMessage(6)");
        logError("tblaccion", "findValuesDropDown", $e->getMessage());
    }
    return $objResponse;
}

$xajax->registerFunction('save');
$xajax->registerFunction('view');
$xajax->registerFunction('delete');
$xajax->registerFunction('findValuesDropDownModulo');
$xajax->registerFunction('findValuesDropDownAccion');
?>