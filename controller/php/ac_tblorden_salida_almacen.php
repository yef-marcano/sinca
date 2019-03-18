<?php

function save($form) {
    $objResponse = new xajaxResponse();
    $objTable = new md_tblorden_salida_almacen();
    $objTableDetalle = new md_tbldetalle_orden_salida_almacen();
    $action = "";
    $objTable->clvcodigo = strval(str_replace(',', '', $form['hdn_id']));
    $objTable->clvsolicitud = $form['txt_solicitud'];
    $objTable->clvalmacen = $form['txt_almacen'];
    $objTable->dtmfecha = $form['txt_fecha']; 
    $objTable->clvconductor = $form['txt_conductor'];
    $objTable->clvvehiculo = $form['txt_vehiculo'];
    $objTable->memdireccion_destino = $form['txt_direccion'];
    $objTable->memobservacion = $form['txt_observacion'];

    try {
        if ($objTable->clvcodigo == '') {
            $action = "insert";
            $return = $objTable->insert();
            $objTableDetalle->clvorden_salida_almacen = $return;
            if ($form['txt_insumo'] != "") {
                for ($i = 0; $i < count($form['txt_insumo']); $i++) {
                    if ($form['txt_insumo'][$i] != "") {
                        $objTableDetalle->clvinsumo = $form['txt_insumo'][$i];
                        $objTableDetalle->intcantidad = $form['txt_cantidad_despachada'][$i];
                        $returnTorre = $objTableDetalle->insert();
                    }
                }
            }
            
            
            
            
            $objResponse->assign('hdn_id', 'value', $return);
            $objResponse->script("$('#tblOrden_salida_almacen').dataTable()._fnAjaxUpdate();");
            $objResponse->script("$('#sideBarFormOrden_salida_almacen').sidebar('hide');");
            $objResponse->alert("Registro completado con exito!");				
				$objResponse->script('location.href="frm_home.php"');
        } else {
            $action = "update";
            $objTable->update();
            $objResponse->script("$('#tblOrden_salida_almacen').dataTable()._fnAjaxUpdate();");
            $objResponse->script("$('#sideBarFormOrden_salida_almacen').sidebar('hide');");
            $objResponse->alert("Registro completado con exito!");				
				$objResponse->script('location.href="frm_home.php"');
        }
    } catch (Exception $e) {
        $objResponse->script("showMessage(6)");
        logError("tblorden_salida_almacen", $action, $e->getMessage());
    }
    return $objResponse;
}
//
//    try {
//        if ($objTable->clvcodigo == '') {
//            $action = "insert";
//            $return = $objTable->insert();
//            $objResponse->assign('hdn_id', 'value', $return);
//            $objResponse->script("$('#tblProyecto').dataTable()._fnAjaxUpdate();");
//            $objResponse->script("$('#sideBarFormProyecto').sidebar('hide');");
//            $objResponse->alert("Registro completado con exito!");				
//				$objResponse->script('location.href="frm_home.php"');
//        } else {
//            $action = "update";
//            $objTable->update();
//            $objResponse->script("$('#tblProyecto').dataTable()._fnAjaxUpdate();");
//            $objResponse->script("$('#sideBarFormProyecto').sidebar('hide');");
//            $objResponse->alert("Registro completado con exito!");				
//				$objResponse->script('location.href="frm_home.php"');
//        }
//    } catch (Exception $e) {
//        $objResponse->script("showMessage(6)");
//        logError("tblproyecto", $action, $e->getMessage());
//    }
//    return $objResponse;
//}

function view($id) {
    $objResponse = new xajaxResponse();
    $objTable = new md_tblorden_salida_almacen();   
    $resultSet = null;
    $resultSetDetalle = null;
    $scriptrDetalle = "";
    
    try {
        $resultSet = $objTable->find("c.clvcodigo= " . $id);
        $row = pg_fetch_object($resultSet);
        $objResponse->assign('hdn_id', 'value', utf8_decode($row->clvcodigo));
        
                       $objResponse->script("$('#txt_solicitud').select2('val', '" . $row->clvsolicitud . "');");
        $objResponse->script("$('#txt_solicitud').select2('enable', true);");
        
                       $objResponse->script("$('#txt_almacen').select2('val', '" . $row->clvalmacen . "');");
        $objResponse->script("$('#txt_almacen').select2('enable', true);");
        
                               $objResponse->script("$('#txt_conductor').select2('val', '" . $row->clvconductor . "');");
        $objResponse->script("$('#txt_conductor').select2('enable', true);");
        
                               $objResponse->script("$('#txt_vehiculo').select2('val', '" . $row->clvvehiculo . "');");
        $objResponse->script("$('#txt_vehiculo').select2('enable', true);");
        
        $objResponse->assign('txt_direccion', 'value', utf8_decode($row->memdireccion_destino));
        
        $objResponse->assign('txt_observacion', 'value', utf8_decode($row->memobservacion));
        
                
             $objResponse->script("$('#txt_proyecto').select2('val', '" . $row->clvproyecto . "');");
             $objResponse->loadCommands(findValuesDropDownSolicitud($row->clvproyecto));
             $objResponse->script("$('#txt_solicitud').select2('val', '" . $row->clvsolicitud . "');");
             $objResponse->script("$('#txt_solicitud').select2('enable', true);");
                       $fecha = new DateTime($row->dtmfecha);
        $objResponse->assign('txt_fecha', 'value', $fecha->format('d-m-Y'));
//        
//             
//        
//        
        
        
        
        
        
        
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
        logError("tblorden_salida_almacen", "view", $e->getMessage());
    }
    return $objResponse;
}

function delete($id) {
    $objResponse = new xajaxResponse();
    $objTable = new md_tblorden_salida_almacen();
    $objTable->clvcodigo =  $id;
    
    try {
        $objTable->delete();
        $objResponse->script("$('#tblOrden_salida_almacen').dataTable()._fnAjaxUpdate();");
        $objResponse->script("showMessage(3)");
    } catch (Exception $e) {
        $objResponse->script("showMessage(6)");
        logError("tblorden_salida_almacen", 'delete', $e->getMessage());
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

function findValuesDropDownProyecto() {
    $objResponse = new xajaxResponse();
    $objTable = new md_tblproyecto();
    $resultSet = null;
    $html = "";

    try {
        $resultSet = $objTable->find("clvestatus= 0");
        $html.= "<option value=''>Seleccione una opci&oacute;n</option>";
        while ($row = pg_fetch_object($resultSet)) {
            $html.= "<option value='" . $row->clvcodigo. "'>" . htmlentities($row->strnombre) . "</div>";
        }
        $objResponse->assign('txt_proyecto', 'innerHTML', $html);
        $objResponse->script("$('#txt_proyecto').select2({width: '100%',placeholder: 'Selecione una opcion'});");
    } catch (Exception $e) {
        $objResponse->script("showMessage(6)");
        logError("tblproyecto", "findValuesDropDownProyecto", $e->getMessage());
    }
    return $objResponse;
}




function findValuesDropDownSolicitud($clvproyecto) {
    $objResponse = new xajaxResponse();
    $objTable = new md_tblsolicitud();
    
    $resultSet = null;
    $html = "";
    if ($clvproyecto == "") {
       $clvproyecto = 0;
    }
    try {
        $resultSet = $objTable->find("clvestatus= 0 and clvproyecto=".$clvproyecto);
        $html.= "<option value=''>Seleccione una opci&oacute;n</option>";
        while ($row = pg_fetch_object($resultSet)) {
            $html.= "<option value='" . $row->clvcodigo. "'>Solicitud #" . htmlentities($row->clvcodigo) . "</div>";
        }
        $objResponse->assign('txt_solicitud', 'innerHTML', $html);
        $objResponse->script("$('#txt_solicitud').select2({width: '100%',placeholder: 'Selecione una opcion'});");
    } catch (Exception $e) {
        $objResponse->script("showMessage(6)");
        logError("tblsolicitud", "findValuesDropDownSolicitud", $e->getMessage());
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

function findValuesDropDownConductor() {
    $objResponse = new xajaxResponse();
    $objTable = new md_tblconductor();
    $resultSet = null;
    $html = "";

    try {
        $resultSet = $objTable->find("clvestatus= 0");
        $html.= "<option value=''>Seleccione una opci&oacute;n</option>";
        while ($row = pg_fetch_object($resultSet)) {
            $html.= "<option value='" . $row->clvcodigo. "'>" . htmlentities($row->strnombre) . "</div>";
        }
        $objResponse->assign('txt_conductor', 'innerHTML', $html);
        $objResponse->script("$('#txt_conductor').select2({width: '100%',placeholder: 'Selecione una opcion'});");
    } catch (Exception $e) {
        $objResponse->script("showMessage(6)");
        logError("tblconductor", "findValuesDropDownConductor", $e->getMessage());
    }
    return $objResponse;
}
function findValuesDropDownVehiculo() {
    $objResponse = new xajaxResponse();
    $objTable = new md_tblvehiculo();
    $resultSet = null;
    $html = "";

    try {
        $resultSet = $objTable->find("clvestatus= 0");
        $html.= "<option value=''>Seleccione una opci&oacute;n</option>";
        while ($row = pg_fetch_object($resultSet)) {
            $html.= "<option value='" . $row->clvcodigo. "'>Placa #" . htmlentities($row->strplaca) . "</div>";
        }
        $objResponse->assign('txt_vehiculo', 'innerHTML', $html);
        $objResponse->script("$('#txt_vehiculo').select2({width: '100%',placeholder: 'Selecione una opcion'});");
    } catch (Exception $e) {
        $objResponse->script("showMessage(6)");
        logError("tblvehiculo", "findValuesDropDownVehiculo", $e->getMessage());
    }
    return $objResponse;
}

function cargarSolicitud($id) {
    $objResponse = new xajaxResponse();
    
    $objTableDetalle = new md_tbldetalle_solicitud();
    
    $resultSet = null;
    
    $resultSetDetalle = null;
    
    $scriptrDetalle = "";
    
    try { 
        
        $numTr = 1;
        $resultSetDetalle = $objTableDetalle->find("clvsolicitud= " . $id);
        $html="<table id='tableProyectos' class='ui celled small table segment'>
                                        <thead>
                                            <tr>
                                                <th>Nombre del Insumo</th>
                                                <th>Cantidad Solicitada</th>
                                                <th>Cantidad a Despachar</th>                                            
                                            </tr>
                                        </thead>
                                        <tbody id='tbodyProyectos'></tbody>
                                    </table>";
        $scriptrDetalle.= "$('#hdn_number').val('1');";
        while ($rowDetalle = pg_fetch_object($resultSetDetalle)) {
            $scriptrDetalle.= "insertTr();";
            
            //$scriptrDetalle.= "$('#txt_insumo_" . $numTr . "').val('" . $rowDetalle->clvisumo . "');";

            $scriptrDetalle.= "$('#txt_insumo_" . $numTr . "').val('" . $rowDetalle->clvinsumo . "');";
            $scriptrDetalle.= "$('#txt_cantidad_solicitada_" . $numTr . "').val('" . $rowDetalle->intcantidad_solicitada . "');";
            $scriptrDetalle.= "$('#txt_cantidad_despachada_" . $numTr . "').blur();";
            $numTr++;
        }
        $objResponse->assign('Detalle', 'innerHTML', $html);
        $objResponse->script($scriptrDetalle);          
        
    } catch (Exception $e) {
        $objResponse->script("showMessage(6)");
        logError("tbldetalle_solicitud", "view", $e->getMessage());
    }
    return $objResponse;
}

$xajax->registerFunction('findValuesDropDownInsumo');
$xajax->registerFunction('findValuesDropDownProyecto');
$xajax->registerFunction('findValuesDropDownSolicitud');
$xajax->registerFunction('findValuesDropDownAlmacen');
$xajax->registerFunction('findValuesDropDownConductor');
$xajax->registerFunction('findValuesDropDownVehiculo');
$xajax->registerFunction('cargarSolicitud');
$xajax->registerFunction('view');
$xajax->registerFunction('save');
$xajax->registerFunction('delete');
?>