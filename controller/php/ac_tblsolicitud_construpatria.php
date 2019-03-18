<?php

function save($form) {
    $objResponse = new xajaxResponse();
    $objTable = new md_tblsolicitud_construpatria();
    //$objTable = new md_tblsolicitud_costrupatria();

    $action = "";
    $objTable->clvcodigo = strval(str_replace(',', '', $form['hdn_id']));
   // $objTable->clvsolicitud = $form['txt_solicitud'];
    $objTable->clvconstrupatria = $form['txt_construpatria'];
    //$objTable->intpedido = $form['txt_pedido'];
    //$objTable->dtmfecha = $form['txt_fecha'];
    //$objTable->dtmfecha_vencimiento = $form['txt_fecha_vencimiento'];
    //$objTable->clvestatus_solicitud = $form['txt_estatus_solicitud'];
    
    try {
            if ($objTable->clvcodigo == '') {
                $action = "insert";
                $return = $objTable->insert();


//        if ($form['txt_proyecto'] != "") {
//            for ($i = 0; $i < count($form['txt_proyecto']); $i++) {
//                $objTableProyecto->strnombre = $return;
//                $objTableProyecto->memdescripcion = $form['txt_proyecto'][$i];
//                $objTableProyecto->insert();
//            }
//        }


                $objResponse->assign('hdn_id', 'value', $return);
                $objResponse->script("$('#tblSolicitud_construpatria').dataTable()._fnAjaxUpdate();");
                $objResponse->script("$('#sideBarFormSolicitud_construpatria').sidebar('hide');");
                $objResponse->alert("Solicitud Registrada con exito!");				
                                    $objResponse->script('location.href="frm_home.php"');
            } else {
                $action = "update";
                $objTable->update(); 

                $objResponse->script("$('#tblSolicitud_construpatria').dataTable()._fnAjaxUpdate();");
                $objResponse->script("$('#sideBarFormSolicitud_construpatria').sidebar('hide');");
                $objResponse->alert("Solicitud Registrada con exito!");				
                                    $objResponse->script('location.href="frm_home.php"');
            }
        } catch (Exception $e) {
            $objResponse->script("showMessage(6)");
            logError("tblsolicitud", $action, $e->getMessage());
        }
        return $objResponse;
}

//$objResponse->script("alert('entro')");

function view($id) {
    $objResponse = new xajaxResponse();
    $objTable = new md_tblsolicitud_construpatria();
    $objTableDetalle = new md_tbldetalle_solicitud_construpatria();
    $resultSet = null;
    
    try {
            $resultSet = $objTable->find("c.clvcodigo= " . $id);
            $row = pg_fetch_object($resultSet);
            $objResponse->assign('hdn_id', 'value', utf8_decode($row->clvcodigo));
            
                    $objResponse->script("$('#divDropDownConstrupatria').dropdown('set selected', '" . $row->clvconstrupatria . "');");
         $objResponse->assign('txt_construpatria', 'value', utf8_decode($row->clvconstrupatria));
            
             $objResponse->script("$('#divDropDownSolicitud').dropdown('set selected', '" . $row->clvsolicitud . "');");
          $objResponse->assign('txt_solicitud', 'value', utf8_decode($row->clvsolicitud));
          
          $objResponse->script("$('#divDropDownEstatus_solicitud').dropdown('set selected', '" . $row->clvestatus_solicitud . "');");
          $objResponse->assign('txt_estatus_solicitud', 'value', utf8_decode($row->clvestatus_solicitud));
          
            $fecha = new DateTime($row->dtmfecha);
         $objResponse->assign('txt_fecha', 'value', $fecha->format('d-m-Y'));

         $numTr = 1;
        $resultSetDetalle = $objTableDetalle->find("clvsolicitud_construpatria= " . $id);
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
function findValuesDropDownSolicitud() {
    $objResponse = new xajaxResponse();
    $objTable = new md_tblsolicitud_construpatria();
    $resultSet = null;
    $html = "";

    try {
            $resultSet = $objTable->find(" c.clvestatus= 0");
            $html.= "<div class='item active' data-value=''>Seleccione una Solicitud</div>";
            while ($row = pg_fetch_object($resultSet)) {
                $html.= "<div class='item' data-value='" . $row->clvcodigo . "'>" . utf8_decode($row->clvsolicitud) . "</div>";
        }
            $objResponse->assign('divOptionDropDownSolicitud', 'innerHTML', $html);
            $objResponse->script("$('#divDropDownSolicitud').dropdown();");
    } catch (Exception $e) {
            $objResponse->script("showMessage(6)");
            logError("tblestatus_solcitud", "findValuesDropDownEstatus_solicitud", $e->getMessage());
    }
    return $objResponse;
}
function findValuesDropDownConstrupatria() {
    $objResponse = new xajaxResponse();
    $objTable = new md_tblconstrupatria();
    $resultSet = null;
    $html = "";

    try {
            $resultSet = $objTable->find(" clvestatus= 0");
            $html.= "<div class='item active' data-value=''>Seleccione una Solicitud</div>";
            while ($row = pg_fetch_object($resultSet)) {
                $html.= "<div class='item' data-value='" . $row->clvcodigo . "'>" . utf8_decode($row->strdescripcion) . "</div>";
        }
            $objResponse->assign('divOptionDropDownConstrupatria', 'innerHTML', $html);
            $objResponse->script("$('#divDropDownConstrupatria').dropdown();");
    } catch (Exception $e) {
            $objResponse->script("showMessage(6)");
            logError("tblestatus_solcitud", "findValuesDropDownConstrupatria", $e->getMessage());
    }
    return $objResponse;
}
//function findValuesDropDownConstrupatria() {
//    $objResponse = new xajaxResponse();
//    $objTable = new md_tblconstrupatria();
//    $resultSet = null;
//    $html = "";
//
//        try {
//            $resultSet = $objTable->find("clvestatus= 0");
//            $html.= "<option value=''>Seleccione una opci&oacute;n</option>";
//            while ($row = pg_fetch_object($resultSet)) {
//                $html.= "<option value='" . $row->clvcodigo. "'>" . htmlentities($row->strdescripcion) . "</div>";
//            }
//            $objResponse->assign('txt_construpatria', 'innerHTML', $html);
//            $objResponse->script("$('#txt_construpatria').select2({width: '100%',placeholder: 'Selecione una opcion'});");
//        } catch (Exception $e) {
//            $objResponse->script("showMessage(6)");
//            logError("tblconstrupatria", "findValuesDropDownConstrupatria", $e->getMessage());
//        }
//        return $objResponse;
//}

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

function findValuesDropDownEstatus_solicitud() {
    $objResponse = new xajaxResponse();
    $objTable = new md_tblestatus_solicitud();
    $resultSet = null;
    $html = "";

    try {
            $resultSet = $objTable->find(" clvestatus= 0");
            $html.= "<div class='item active' data-value=''>Seleccione un Estatus</div>";
            while ($row = pg_fetch_object($resultSet)) {
                $html.= "<div class='item' data-value='" . $row->clvcodigo . "'>" . utf8_decode($row->strdescripcion) . "</div>";
        }
            $objResponse->assign('divOptionDropDownEstatus_solicitud', 'innerHTML', $html);
            $objResponse->script("$('#divDropDownEstatus_solicitud').dropdown();");
    } catch (Exception $e) {
            $objResponse->script("showMessage(6)");
            logError("tblestatus_solcitud", "findValuesDropDownEstatus_solicitud", $e->getMessage());
    }
    return $objResponse;
}
function delete($id) {
    $objResponse = new xajaxResponse();
    $objTable = new md_tblsolicitud();
    $objTable->clvcodigo =  $id;

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

$xajax->registerFunction('save');
$xajax->registerFunction('view');
$xajax->registerFunction('delete');
$xajax->registerFunction('findValuesDropDownSolicitud');
$xajax->registerFunction('findValuesDropDownEstatus_solicitud');
$xajax->registerFunction('findValuesDropDownInsumo');
$xajax->registerFunction('findValuesDropDownConstrupatria');
?>