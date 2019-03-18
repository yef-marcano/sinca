<?php

function save($form) {
    $objResponse = new xajaxResponse();
    $objTable = new md_tbltoma_inventario();
    $objTableDetalle = new md_tbldetalle_toma_inventario();
    $action = "";
    $objTable->clvcodigo = strval(str_replace(',', '', $form['hdn_id']));
    $objTable->clvalmacen = $form['txt_almacen'];
    
    $fecha = new DateTime($form['txt_fecha']);
    $objTable->dtmfecha = $fecha->format("Y-m-d");
    
    $objTable->memobservacion = $form['txt_observacion'];

//    try {
//        if ($objTable->clvcodigo == '') {
//            $action = "insert";
//            $return = $objTable->insert();
//            $objResponse->assign('hdn_id', 'value', $return);
//            $objResponse->script("$('#tblToma_inventario').dataTable()._fnAjaxUpdate();");
//            $objResponse->script("$('#sideBarFormToma_inventario').sidebar('hide');");
//                $objResponse->alert("Solicitud Registrada con exito!");				
//                                    $objResponse->script('location.href="frm_home.php"');
    try {
        if ($objTable->clvcodigo == '') {
            $action = "insert";
            $return = $objTable->insert();
            $objTableDetalle->clvtoma_inventario = $return;
            if ($form['txt_insumo'] != "") {
                    for ($i = 0; $i < count($form['txt_insumo']); $i++) {
                        if ($form['txt_insumo'][$i] != "") {
                        $objTableDetalle->clvinsumo = $form['txt_insumo'][$i];
                            $objTableDetalle->intcantidad_sistema = $form['txt_cantidad_solicitada'][$i];
                                $objTableDetalle->intcantidad_fisica = $form['txt_cantidad_fisica'][$i];
                                $returnTorre = $objTableDetalle->insert();
                }
            }
        }
        $objResponse->assign('hdn_id', 'value', $return);
        $objResponse->script("$('#tblToma_inventario').dataTable()._fnAjaxUpdate();");
        $objResponse->script("$('#sideBarFormToma_inventario').sidebar('hide');");
        $objResponse->alert("Registro completado con exito!");				
        $objResponse->script('location.href="frm_home.php"');
    //  $objResponse->script("showMessage(1)");
    
        } else {
            $action = "update";
            $objTable->update();
            $objResponse->script("$('#tblToma_inventario').dataTable()._fnAjaxUpdate();");
            $objResponse->script("$('#sideBarFormToma_inventario').sidebar('hide');");
                $objResponse->alert("Solicitud Registrada con exito!");				
                                    $objResponse->script('location.href="frm_home.php"');
        }
    } catch (Exception $e) {
        $objResponse->script("showMessage(6)");
        logError("tbltoma_inventario", $action, $e->getMessage());
    }
    return $objResponse;
}

function view($id) {
    $objResponse = new xajaxResponse();
    $objTable = new md_tbltoma_inventario();
    $objTableDetalle = new md_tbldetalle_toma_inventario();
    
    $resultSet = null;
    
    $resultSetDetalle = null;
    
    $scriptrDetalle = "";

    try {
        $resultSet = $objTable->find("clvcodigo= " . $id);
        $row = pg_fetch_object($resultSet);
        $objResponse->assign('hdn_id', 'value', utf8_decode($row->clvcodigo));
        
                $objResponse->script("$('#txt_almacen').select2('val', '" . $row->clvalmacen . "');");
        $objResponse->script("$('#txt_almacen').select2('enable', true);");
        
        $fecha = new DateTime($row->dtmfecha);
        $objResponse->assign('txt_fecha', 'value', $fecha->format('d-m-Y'));
        
        $objResponse->assign('txt_observacion', 'value', utf8_decode($row->memobservacion));
        
        $fechaCreacion = new DateTime($row->dtmfecha_creacion);
        
        
                $numTr = 1;
        $resultSetDetalle = $objTableDetalle->find("clvtoma_inventario= " . $id);
        while ($rowDetalle = pg_fetch_object($resultSetDetalle)) {
        $scriptrDetalle.= "insertTr();";
         
        $scriptrDetalle.= "$('#txt_insumo_" . $numTr . "').select2('val', '" . $rowDetalle->clvinsumo . "');";
        
        $scriptrDetalle.= "$('#txt_cantidad_solicitada_" . $numTr . "').val('" . $rowDetalle->intcantidad_sistema . "');";
        $scriptrDetalle.= "$('#txt_cantidad_solicitada_" . $numTr . "').blur();";
        
        $scriptrDetalle.= "$('#txt_cantidad_fisica_" . $numTr . "').val('" . $rowDetalle->intcantidad_fisica . "');";
        $scriptrDetalle.= "$('#txt_cantidad_fisica_" . $numTr . "').blur();";
      
        $numTr++;
        }
        $objResponse->script($scriptrDetalle); 
        
        
        
        
        
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
        logError("tbltoma_inventario", "view", $e->getMessage());
    }
    return $objResponse;
}

function delete($id) {
    $objResponse = new xajaxResponse();
    $objTable = new md_tbltoma_inventario();
    $objTable->clvcodigo =  $id;
    
    try {
        $objTable->delete();
        $objResponse->script("$('#tblToma_inventario').dataTable()._fnAjaxUpdate();");
        $objResponse->script("showMessage(3)");
    } catch (Exception $e) {
        $objResponse->script("showMessage(6)");
        logError("tbltoma_inventario", 'delete', $e->getMessage());
    }
    return $objResponse;
}

function findValuesDropDownAlmacen() {
    $objResponse = new xajaxResponse();
    $objTable = new md_tblalmacen();
    $resultSet = null;
    $html = "";

    try {
        $resultSet = $objTable->find("clvestatus= 0");
        $html.= "<option value=''>Seleccione una opci&oacute;n</option>";
        while ($row = pg_fetch_object($resultSet)) {
            $html.= "<option value='" . $row->clvcodigo. "'>" . htmlentities($row->strdescripcion) . "</div>";
        }
        $objResponse->assign('txt_almacen', 'innerHTML', $html);
        $objResponse->script("$('#txt_almacen').select2({width: '100%',placeholder: 'Selecione una opcion'});");
    } catch (Exception $e) {
        $objResponse->script("showMessage(6)");
        logError("tblalmacen", "findValuesDropDownAlmacen", $e->getMessage());
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

$xajax->registerFunction('save');
$xajax->registerFunction('findValuesDropDownAlmacen');
$xajax->registerFunction('findValuesDropDownInsumo');
$xajax->registerFunction('view');
$xajax->registerFunction('delete');
?>