<?php

function save($form) {
    $objResponse = new xajaxResponse();
    $objTable = new md_tblperfil();
    $objTableRelation = new md_tblperfil_menu_accion();
    $action = "";
    $objTable->clvcodigo = strval(str_replace(',', '', $form['hdn_id']));
    $objTable->strdescripcion = $form['txt_descripcion'];

    try {
        if ($objTable->clvcodigo == '') {
            $action = "insert";
            $return = $objTable->insert();
            if (isset($form['menuAccion'])) {
                for ($i = 0; $i < count($form['menuAccion']); $i++) {
                    $objTableRelation->clvperfil = $return;
                    $objTableRelation->clvmenu_accion = $form['menuAccion'][$i];
                    $objTableRelation->insert();
                }
            }
            $objResponse->assign('hdn_id', 'value', $return);
            $objResponse->script("$('#tblPerfil').dataTable()._fnAjaxUpdate();");
            $objResponse->script("$('#sideBarFormPerfil').sidebar('hide');");
            $objResponse->script("showMessage(1)");
        } else {
            $action = "update";
            $objTable->update();
            if (isset($form['menuAccion'])) {
                $objTableRelation->deleteByIdPerfil($objTable->clvcodigo);
                for ($i = 0; $i < count($form['menuAccion']); $i++) {
                    $objTableRelation->clvperfil = $objTable->clvcodigo;
                    $objTableRelation->clvmenu_accion = $form['menuAccion'][$i];
                    $objTableRelation->insert();
                }
            }
            $objResponse->script("$('#tblPerfil').dataTable()._fnAjaxUpdate();");
            $objResponse->script("$('#sideBarFormPerfil').sidebar('hide');");
            $objResponse->script("showMessage(2)");
        }
    } catch (Exception $e) {
        $objResponse->script("showMessage(6)");
        logError("tblperfil | tblperfil_menu_accion", $action, $e->getMessage());
    }
    return $objResponse;
}

function view($id) {
    $objResponse = new xajaxResponse();
    $objTable = new md_tblperfil();
    $objTableRelation = new md_tblperfil_menu_accion();
    $resultSet = null;
    $resultSetRelation = null;
    $script = "";

    try {
        $resultSet = $objTable->find("clvcodigo= " . $id);
        $row = pg_fetch_object($resultSet);
        $objResponse->assign('hdn_id', 'value', utf8_decode($row->clvcodigo));
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
            $objResponse->assign('lbl_usuarioEliminacion', 'innerHTML', utf8_decode($row->nombre_usuario_eliminacion));
            $objResponse->assign('lbl_fechaEliminacion', 'innerHTML', $fechaEliminacion->format("d-m-Y h:i:s A"));
        }
        $resultSetRelation = $objTableRelation->find("clvperfil= " . $id);
        while ($rowRelation = pg_fetch_object($resultSetRelation)) {
            $script.= "$('#menuAccion_" . $rowRelation->clvmenu_accion . "').checkbox('enable');";
        }
        $objResponse->script($script);
        $objResponse->script("$('#divLoader').removeClass('active').addClass('disabled');");
    } catch (Exception $e) {
        $objResponse->script("showMessage(6)");
        logError("tblperfil | tblperfil_menu_accion", "view", $e->getMessage());
    }
    return $objResponse;
}

function delete($id) {
    $objResponse = new xajaxResponse();
    $objTable = new md_tblperfil();
    $objTableRelation = new md_tblperfil_menu_accion();
    $objTable->clvcodigo = $id;

    try {
        $objTableRelation->deleteByIdPerfil($id);
        $objTable->delete();
        $objResponse->script("$('#tblPerfil').dataTable()._fnAjaxUpdate();");
        $objResponse->script("showMessage(3)");
    } catch (Exception $e) {
        $objResponse->script("showMessage(6)");
        logError("tblperfil", 'delete', $e->getMessage());
    }
    return $objResponse;
}

function findMenuAction($idPerfil) {
    $objResponse = new xajaxResponse();
    $objTable = new md_tblmenu_accion();
    $resultSet = null;
    $condition = "";
    $html = "";
    $idMenu = null;
    $cantAccion = 0;
    try {
        $condition = "clvestatus != 1 ORDER BY 3,5";
        $resultSet = $objTable->find($condition);
        while ($row = pg_fetch_object($resultSet)) {
            if ($idMenu == null) {
                $idMenu = $row->clvmenu;
                $cantAccion = 1;
                $html.= "<div class='title'><i class='dropdown icon'></i>" . utf8_decode($row->nombre_menu) . "</div><div id='content_" . $row->clvmenu . "' class='content'><div id='btn_all' style='float: right' class='ui mini button green' onclick='checkAll(this, \"" . $row->clvmenu . "\")'><i class='check icon'></i></div><div class='ui two column vertically divided grid'>";
            } else if ($idMenu != $row->clvmenu) {
                $idMenu = $row->clvmenu;

                if (($cantAccion % 2) == 1) {
                    $html.= "</div>";
                }

                $cantAccion = 1;
                $html.= "</div></div><div class='title'><i class='dropdown icon'></i>" . utf8_decode($row->nombre_menu) . "</div><div id='content_" . $row->clvmenu . "' class='content'><div id='btn_all' style='float: right' class='ui mini button green' onclick='checkAll(this, \"" . $row->clvmenu . "\")'><i class='check icon'></i></div><div class='ui two column vertically divided grid'>";
            } else {
                $cantAccion++;
            }


            if (($cantAccion % 2) == 1) {
                $html.= "<div class='row'>";
            }

            $html.= $cantAccion . "<div class='column'><div class='field'><div id='menuAccion_" . $row->clvcodigo . "' class='ui slider checkbox'><input type='checkbox' name='menuAccion[]' value='" . $row->clvcodigo . "'><label>" . utf8_decode($row->nombre_accion) . "</label></div></div></div>";

            if (($cantAccion % 2) == 0) {
                $html.= "</div>";
            }
        }
        if (($cantAccion % 2) == 1) {
            $html.= "</div>";
        }
        $html.= "</div></div>";

        $objResponse->assign("divAccordionMenus", "innerHTML", $html);
        $objResponse->script("$('#divAccordionMenus').accordion();");
        $objResponse->script("$('.ui.checkbox').checkbox();");
    } catch (Exception $e) {
        $objResponse->script("showMessage(6)");
        logError("r_menus_acciones", "findMenuAction", $e->getMessage());
    }
    return $objResponse;
}

$xajax->registerFunction('save');
$xajax->registerFunction('view');
$xajax->registerFunction('delete');
$xajax->registerFunction('findMenuAction');
?>