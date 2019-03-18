<?php

function save($form) {
    $objResponse = new xajaxResponse();
    $objTable = new md_tblcementera_acopio();
    $action = "";
    $objTable->clvcodigo = strval(str_replace(',', '', $form['hdn_id']));
    $objTable->clvcementera = $form['txt_cementera'];
    $objTable->strdescripcion = $form['txt_descripcion'];
    $objTable->clvmunicipio = $form['txt_municipio'];
    $objTable->clvparroquia = $form['txt_parroquia'];
    $objTable->memdireccion = $form['txt_direccion'];
    $objTable->strtelefono = $form['txt_telefono'];
    if($form['txt_estado'] != ""){
        $objTable->clvestado = $form['txt_estado'];
    }else{
        $objTable->clvestado = -1;
    }

    try {
        if ($objTable->clvcodigo == '') {
            $action = "insert";
            $return = $objTable->insert();
            $objResponse->assign('hdn_id', 'value', $return);
            $objResponse->script("$('#tblCementera_acopio').dataTable()._fnAjaxUpdate();");
            $objResponse->script("$('#sideBarFormCementera_acopio').sidebar('hide');");
            $objResponse->script("showMessage(1)");
        } else {
            $action = "update";
            $objTable->update();
            $objResponse->script("$('#tblCementera_acopio').dataTable()._fnAjaxUpdate();");
            $objResponse->script("$('#sideBarFormCementera_acopio').sidebar('hide');");
            $objResponse->script("showMessage(2)");
        }
    } catch (Exception $e) {
        $objResponse->script("showMessage(6)");
        logError("tblcementera_acopio", $action, $e->getMessage());
    }
    return $objResponse;
}

function view($id) {
    $objResponse = new xajaxResponse();
    $objTable = new md_tblcementera_acopio();
    $resultSet = null;

    try {
        $resultSet = $objTable->find("clvcodigo= " . $id);
        $row = pg_fetch_object($resultSet);
        
        $objResponse->assign('hdn_id', 'value', utf8_decode($row->clvcodigo));
        $objResponse->script("$('#divDropDownCementera').dropdown('set selected', '" . $row->clvcementera. "');");
        $objResponse->assign('txt_cementera', 'value', utf8_decode($row->clvcementera));
        $objResponse->assign('txt_descripcion', 'value', utf8_decode($row->strdescripcion));
        
        
        
        
        $objResponse->assign('txt_estado', 'value', utf8_decode($row->clvestado));
        $objResponse->script("$('#divDropDownEstado').dropdown('set selected', '" . $row->clvestado . "');");
        $objResponse->loadCommands(findValuesDropDownMunicipio($row->clvestado));
         
        $objResponse->assign('txt_municipio', 'value', utf8_decode($row->clvmunicipio));
        $objResponse->script("$('#divDropDownMunicipio').dropdown('set selected', '" . $row->clvmunicipio . "');");
        $objResponse->loadCommands(findValuesDropDownParroquia($row->clvmunicipio));
        
        $objResponse->assign('txt_parroquia', 'value', utf8_decode($row->clvparroquia));
        $objResponse->script("$('#divDropDownParroquia').dropdown('set selected', '" . $row->clvparroquia . "');");
        //$objResponse->loadCommands(findSector($row->clvparroquia));
        
        
        
        
        
        $objResponse->assign('txt_direccion', 'value', utf8_decode($row->memdireccion));
        $objResponse->assign('txt_telefono', 'value', utf8_decode($row->strtelefono));
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
        logError("tblcementera_acopio", "view", $e->getMessage());
    }
    return $objResponse;
}

function delete($id) {
    $objResponse = new xajaxResponse();
    $objTable = new md_tblcementera_acopio();
    $objTable->clvcodigo =  $id;
    
    try {
        $objTable->delete();
        $objResponse->script("$('#tblCementera_acopio').dataTable()._fnAjaxUpdate();");
        $objResponse->script("showMessage(3)");
    } catch (Exception $e) {
        $objResponse->script("showMessage(6)");
        logError("tblcementera_acopio", 'delete', $e->getMessage());
    }
    return $objResponse;
}

function findValuesDropDownCementera() {
    $objResponse = new xajaxResponse();
    $objTable = new md_tblcementera();
    $resultSet = null;
    $html = "";

    try {
        $resultSet = $objTable->find(" clvestatus= 0");
        $html.= "<div class='item active' data-value=''>Seleccione una Cementera</div>";
        while ($row = pg_fetch_object($resultSet)) {
            $html.= "<div class='item' data-value='" . $row->clvcodigo . "'>" . utf8_decode($row->strdescripcion) . "</div>";
        }
        $objResponse->assign('divOptionDropDownCementera', 'innerHTML', $html);
        $objResponse->script("$('#divDropDownCementera').dropdown();");
    } catch (Exception $e) {
        $objResponse->script("showMessage(6)");
        logError("tblcemenetera", "findValuesDropDownCementera", $e->getMessage());
    }
    return $objResponse;
}

function findValuesDropDownEstado() {
    $objResponse = new xajaxResponse();
    $objTable = new md_utility();
    $resultSet = null;
    $html = "";

    try {
        $resultSet = $objTable->findEstado("not borrado ORDER BY estado");
        $html.= "<div class='item active' data-value=''>Seleccione un estado</div>";
        while ($row = pg_fetch_object($resultSet)) {
            $html.= "<div class='item' data-value='" . $row->cod_estado . "'>" . utf8_decode($row->estado) . "</div>";
        }
        $objResponse->assign('divOptionDropDownEstado', 'innerHTML', $html);
        $objResponse->script("$('#divDropDownEstado').dropdown();");
    } catch (Exception $e) {
        $objResponse->script("showMessage(6)");
        logError("clvestado", "findValuesDropDownEstado", $e->getMessage());
    }
    return $objResponse;
}

function findValuesDropDownMunicipio($clvestado) {
    $objResponse = new xajaxResponse();
    $objTable = new md_utility();
    $resultSet = null;
    $html = "";
     if ($clvestado == null){
        $clvestado=0;
    }    
    try {
        $resultSet = $objTable->findMunicipio("not borrado and cod_estado=" . $clvestado . " ORDER BY estado");
        $html.= "<div class='item active' data-value=''>Seleccione un municipio</div>";
        while ($row = pg_fetch_object($resultSet)) {
            $html.= "<div class='item' data-value='" . $row->cod_municipio . "'>" . utf8_decode($row->municipio) . "</div>";
        }
        $objResponse->assign('divOptionDropDownMunicipio', 'innerHTML', $html);
        $objResponse->script("$('#divDropDownMunicipio').dropdown('restore defaults');");
        $objResponse->script("$('#divDropDownMunicipio').dropdown();");
    } catch (Exception $e) {
        $objResponse->script("showMessage(6)");
        logError("clvestado", "findValuesDropDownMunicipio", $e->getMessage());
    }
    return $objResponse;
}
function findValuesDropDownParroquia($clvmunicipio) {
    $objResponse = new xajaxResponse();
    $objTable = new md_utility();
    $resultSet = null;
    $html = "";
    if ($clvmunicipio == null){
        $clvmunicipio=0;
    }
    try {
        $resultSet = $objTable->findParroquia("not borrado and cod_municipio=" . $clvmunicipio . " ORDER BY parroquia");
        $html.= "<div class='item active' data-value=''>Seleccione una Parroquia</div>";
        while ($row = pg_fetch_object($resultSet)) {
            $html.= "<div class='item' data-value='" . $row->cod_parroquia . "'>" . (utf8_decode($row->parroquia)) . "</div>";
        }
        $objResponse->assign('divOptionDropDownParroquia', 'innerHTML', $html);
       // $objResponse->script("$('#divDropDownParroquia').dropdown('restore defaults');");
        $objResponse->script("$('#divDropDownParroquia').dropdown();");
    } catch (Exception $e) {
        $objResponse->script("showMessage(6)");
        logError("parroquia", "findValuesDropDownParroquia", $e->getMessage());
    }
    return $objResponse;
}

$xajax->registerFunction('save');
$xajax->registerFunction('view');
$xajax->registerFunction('delete');
$xajax->registerFunction('findValuesDropDownCementera');
$xajax->registerFunction('findValuesDropDownEstado');
$xajax->registerFunction('findValuesDropDownMunicipio');
$xajax->registerFunction('findValuesDropDownParroquia');
?>