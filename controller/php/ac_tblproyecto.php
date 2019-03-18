<?php

function save($form) {
    $objResponse = new xajaxResponse();
    $objTable = new md_tblproyecto();
    $action = "";
    $objTable->clvcodigo = strval(str_replace(',', '', $form['hdn_id']));
    $objTable->strnombre = $form['txt_nombre'];
    $objTable->memdescripcion = $form['txt_descripcion'];
    $objTable->clvtipo_proyecto = $form['txt_tipo_proyecto'];
    $objTable->clvsector_economico = $form['txt_sector_economico'];
    $objTable->clvestado = $form['txt_estado'];
    $objTable->clvmunicipio = $form['txt_municipio'];
    $objTable->clvparroquia = $form['txt_parroquia'];
    $objTable->memdireccion = $form['txt_direccion'];
    $objTable->clvestatus_proyecto = $form['txt_estatus_proyecto'];
//    $objTable->strcodigo_construpatria = $form['txt_codigo_construpatria'];
    $objTable->clvconstrupatria = $form['txt_construpatria'];
//    $objTable->strcodigo_cemento = $form['txt_codigo_cemento'];
    $objTable->clvcementera = $form['txt_cementera'];
    $objTable->clvcementera_acopio = $form['txt_cementera_acopio'];
//    $objTable->strnacionalidad_tecnico = $form['txt_nacionalidad_tecnico'];
//    $objTable->intcedula_tecnico = $form['txt_cedula_tecnico'];
//    $objTable->strnombre_tecnico = $form['txt_nombre_tecnico'];
//    $objTable->strapellido_tecnico = $form['txt_apellido_tecnico'];
//    $objTable->strtelefonocorp_tecnico = $form['txt_telefono_corptecnico'];
//    $objTable->strtelefonoper_tecnico = $form['txt_telefono_pertecnico'];
//    $objTable->strnacionalidad_inspector = $form['txt_nacionalidad_inspector'];
//    $objTable->intcedula_inspector = $form['txt_cedula_inspector'];
//    $objTable->strnombre_inspector = $form['txt_nombre_inspector'];
//    $objTable->strapellido_inspector = $form['txt_apellido_inspector'];
//    $objTable->strtelefonocorp_inspector = $form['txt_telefono_corpinspector'];
//    $objTable->strtelefonoper_inspector = $form['txt_telefono_perinspector'];
//    $objTable->strnacionalidad_residente = $form['txt_nacionalidad_residente'];
//    $objTable->intcedula_residente = $form['txt_cedula_residente'];
//    $objTable->strnombre_residente = $form['txt_nombre_residente'];
//    $objTable->strapellido_residente = $form['txt_apellido_residente'];
//    $objTable->strtelefonocorp_residente = $form['txt_telefono_corpresidente'];
//    $objTable->strtelefonoper_residente = $form['txt_telefono_perresidente'];
//    $objTable->strnacionalidad_contacto = $form['txt_nacionalidad_contacto'];
//    $objTable->intcedula_contacto = $form['txt_cedula_contacto'];
//    $objTable->strnombre_contacto = $form['txt_nombre_contacto'];
//    $objTable->strapellido_contacto = $form['txt_apellido_contacto'];
//    $objTable->strtelefonocorp_contacto = $form['txt_telefono_corpcontacto'];
//    $objTable->strtelefonoper_contacto = $form['txt_telefono_percontacto'];

    try {
        if ($objTable->clvcodigo == '') {
            $action = "insert";
            $return = $objTable->insert();
            $objResponse->assign('hdn_id', 'value', $return);
            $objResponse->script("$('#tblProyecto').dataTable()._fnAjaxUpdate();");
            $objResponse->script("$('#sideBarFormProyecto').sidebar('hide');");
            $objResponse->alert("Registro completado con exito!");				
				$objResponse->script('location.href="frm_home.php"');
        } else {
            $action = "update";
            $objTable->update();
            $objResponse->script("$('#tblProyecto').dataTable()._fnAjaxUpdate();");
            $objResponse->script("$('#sideBarFormProyecto').sidebar('hide');");
            $objResponse->alert("Registro completado con exito!");				
				$objResponse->script('location.href="frm_home.php"');
        }
    } catch (Exception $e) {
        $objResponse->script("showMessage(6)");
        logError("tblproyecto", $action, $e->getMessage());
    }
    return $objResponse;
}

function view($id) {
    $objResponse = new xajaxResponse();
    $objTable = new md_tblproyecto();
    $resultSet = null;

    //$objResponse->alert("Entro!");

    try {
        $resultSet = $objTable->find("clvcodigo= " . $id);

        $row = pg_fetch_object($resultSet);
        
//        $objResponse->assign('hdn_id', 'value', utf8_decode($row->clvcodigo));

        $objResponse->assign('txt_nombre', 'value', utf8_decode($row->strnombre));

        
        
        $objResponse->assign('txt_estado', 'value', utf8_decode($row->clvestado));
        $objResponse->script("$('#divDropDownEstado').dropdown('set selected', '" . $row->clvestado . "');");
        $objResponse->loadCommands(findValuesDropDownMunicipio($row->clvestado));
         
        $objResponse->assign('txt_municipio', 'value', utf8_decode($row->clvmunicipio));
        $objResponse->script("$('#divDropDownMunicipio').dropdown('set selected', '" . $row->clvmunicipio . "');");
        $objResponse->loadCommands(findValuesDropDownParroquia($row->clvmunicipio));
        
        $objResponse->assign('txt_parroquia', 'value', utf8_decode($row->clvparroquia));
        $objResponse->script("$('#divDropDownParroquia').dropdown('set selected', '" . $row->clvparroquia . "');");
        //$objResponse->loadCommands(findSector($row->clvparroquia));
        
        
        
        
        
        $objResponse->assign('txt_descripcion', 'value', utf8_decode($row->memdescripcion));

        $objResponse->script("$('#divDropDownTipoproyecto').dropdown('set selected', '" . $row->clvtipo_proyecto . "');");
        $objResponse->assign('txt_tipo_proyecto', 'value', utf8_decode($row->clvtipo_proyecto));

        $objResponse->script("$('#divDropDownSectoreconomico').dropdown('set selected', '" . $row->clvsector_economico . "');");
        $objResponse->assign('txt_sector_economico', 'value', utf8_decode($row->clvsector_economico));

        $objResponse->assign('txt_direccion', 'value', utf8_decode($row->memdireccion));

        $objResponse->script("$('#divDropDownEstatusproyecto').dropdown('set selected', '" . $row->clvestatus_proyecto . "');");
        $objResponse->assign('txt_estatus_proyecto', 'value', utf8_decode($row->clvestatus_proyecto));

        $objResponse->assign('txt_codigo_construpatria', 'value', utf8_decode($row->strcodigo_construpatria));

        $objResponse->script("$('#divDropDownConstrupatria').dropdown('set selected', '" . $row->clvconstrupatria . "');");
        $objResponse->assign('txt_construpatria', 'value', utf8_decode($row->clvconstrupatria));

        $objResponse->assign('txt_cedula_tecnico', 'value', utf8_decode($row->intcedula_tecnico));

        $objResponse->assign('txt_telefono_corptecnico', 'value', utf8_decode($row->strtelefonocorp_tecnico));

        $objResponse->assign('txt_telefono_pertecnico', 'value', utf8_decode($row->strtelefonoper_tecnico));

        $objResponse->assign('txt_telefono_corpinspector', 'value', utf8_decode($row->strtelefonocorp_inspector));

        $objResponse->assign('txt_telefono_perinspector', 'value', utf8_decode($row->strtelefonoper_inspector));

        $objResponse->assign('txt_telefono_corpresidente', 'value', utf8_decode($row->strtelefonocorp_residente));

        $objResponse->assign('txt_telefono_perresidente', 'value', utf8_decode($row->strtelefonoper_residente));

        $objResponse->assign('txt_telefono_corpcontacto', 'value', utf8_decode($row->strtelefonocorp_contacto));

        $objResponse->assign('txt_telefono_percontacto', 'value', utf8_decode($row->strtelefonoper_contacto));

        $objResponse->script("$('#divDropDownNacionalidad_tecnico').dropdown('set selected', '" . $row->strnacionalidad_tecnico . "');");
        $objResponse->assign('txt_nacionalidad_tecnico', 'value', utf8_decode($row->strnacionalidad_tecnico));

        $objResponse->assign('txt_nombre_tecnico', 'value', utf8_decode($row->strnombre_tecnico));

        $objResponse->assign('txt_apellido_tecnico', 'value', utf8_decode($row->strapellido_tecnico));

        $objResponse->script("$('#divDropDownCementera').dropdown('set selected', '" . $row->clvcementera . "');");
        $objResponse->assign('txt_cementera', 'value', utf8_decode($row->clvcementera));

        $objResponse->script("$('#divDropDownNacionalidad_inspector').dropdown('set selected', '" . $row->strnacionalidad_inspector . "');");
        $objResponse->assign('txt_nacionalidad_inspector', 'value', utf8_decode($row->strnacionalidad_inspector));

        $objResponse->assign('txt_cedula_inspector', 'value', utf8_decode($row->intcedula_inspector));

        $objResponse->assign('txt_nombre_inspector', 'value', utf8_decode($row->strnombre_inspector));

        $objResponse->assign('txt_apellido_inspector', 'value', utf8_decode($row->strapellido_inspector));  

        $objResponse->script("$('#divDropDownNacionalidad_residente').dropdown('set selected', '" . $row->strnacionalidad_residente . "');");
        $objResponse->assign('txt_nacionalidad_residente', 'value', utf8_decode($row->strnacionalidad_residente));

        $objResponse->assign('txt_cedula_residente', 'value', utf8_decode($row->intcedula_residente));

        $objResponse->assign('txt_nombre_residente', 'value', utf8_decode($row->strnombre_residente));

        $objResponse->assign('txt_apellido_residente', 'value', utf8_decode($row->strapellido_residente));  

        $objResponse->script("$('#divDropDownNacionalidad_contacto').dropdown('set selected', '" . $row->strnacionalidad_contacto . "');");
        $objResponse->assign('txt_nacionalidad_contacto', 'value', utf8_decode($row->strnacionalidad_contacto));

        $objResponse->assign('txt_cedula_contacto', 'value', utf8_decode($row->intcedula_contacto));

        $objResponse->assign('txt_nombre_contacto', 'value', utf8_decode($row->strnombre_contacto));

        $objResponse->assign('txt_apellido_contacto', 'value', utf8_decode($row->strapellido_contacto));  

        $objResponse->script("$('#divDropDownCementera_acopio').dropdown('set selected', '" . $row->clvcementera_acopio . "');");
        $objResponse->assign('txt_cementera_acopio', 'value', utf8_decode($row->clvcementera_acopio));

        $objResponse->assign('txt_codigo_cemento', 'value', utf8_decode($row->strcodigo_cemento));

        
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
function delete($id) {
    $objResponse = new xajaxResponse();
    $objTable = new md_tblproyecto();
    $objTable->clvcodigo = $id;

    try {
        $objTable->delete();
        $objResponse->script("$('#tblProyecto').dataTable()._fnAjaxUpdate();");
        $objResponse->script("showMessage(3)");
    } catch (Exception $e) {
        $objResponse->script("showMessage(6)");
        logError("tblproyecto", 'delete', $e->getMessage());
    }
    return $objResponse;
}

//function findValuesDropDownNacionalidad() {
//    $objResponse = new xajaxResponse();
//    $objTable = new md_saime();
//    $resultSet = null;
//    $html = "";
//
//    try {
//        $resultSet = $objTable->findSaime(" clvestatus= 0");
//        $html.= "<div class='item active' data-value=''>Seleccione una Nacionalidad</div>";
//        while ($row = pg_fetch_object($resultSet)) {
//            $html.= "<div class='item' data-value='" . $row->clvcodigo . "'>" . utf8_decode($row->strnacionalidad) . "</div>";
//        }
//        $objResponse->assign('divOptionDropDownNacionalidad', 'innerHTML', $html);
//        $objResponse->script("$('#divDropDownNacionalidad').dropdown();");
//    } catch (Exception $e) {
//        $objResponse->script("showMessage(6)");
//        logError("strnacionalidad", "findValuesDropDownNacionalidad", $e->getMessage());
//    }
//    return $objResponse;
//}

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

function findValuesDropDownConstrupatria() {
    $objResponse = new xajaxResponse();
    $objTable = new md_tblconstrupatria();
    $resultSet = null;
    $html = "";

    try {
        $resultSet = $objTable->find(" clvestatus= 0");
        $html.= "<div class='item active' data-value=''>Seleccione una Cementera</div>";
        while ($row = pg_fetch_object($resultSet)) {
            $html.= "<div class='item' data-value='" . $row->clvcodigo . "'>" . utf8_decode($row->strdescripcion) . "</div>";
        }
        $objResponse->assign('divOptionDropDownConstrupatria', 'innerHTML', $html);
        $objResponse->script("$('#divDropDownConstrupatria').dropdown();");
    } catch (Exception $e) {
        $objResponse->script("showMessage(6)");
        logError("tblconstrupatria", "findValuesDropDownConstrupatria", $e->getMessage());
    }
    return $objResponse;
}

function findValuesDropDownCementera_acopio() {
    $objResponse = new xajaxResponse();
    $objTable = new md_tblcementera_acopio();
    $resultSet = null;
    $html = "";

    try {
        $resultSet = $objTable->find(" clvestatus= 0");
        $html.= "<div class='item active' data-value=''>Seleccione una Cementera</div>";
        while ($row = pg_fetch_object($resultSet)) {
            $html.= "<div class='item' data-value='" . $row->clvcodigo . "'>" . utf8_decode($row->strdescripcion) . "</div>";
        }
        $objResponse->assign('divOptionDropDownCementera_acopio', 'innerHTML', $html);
        $objResponse->script("$('#divDropDownCementera_acopio').dropdown();");
    } catch (Exception $e) {
        $objResponse->script("showMessage(6)");
        logError("tblcemenetera_acopio", "findValuesDropDownCementera_acopio", $e->getMessage());
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

    try {
        $resultSet = $objTable->findParroquia("not borrado and cod_municipio=" . $clvmunicipio . " ORDER BY parroquia");
        $html.= "<div class='item active' data-value=''>Seleccione una Parroquia</div>";
        while ($row = pg_fetch_object($resultSet)) {
            $html.= "<div class='item' data-value='" . $row->cod_parroquia . "'>" . (utf8_decode($row->parroquia)) . "</div>";
        }
        $objResponse->assign('divOptionDropDownParroquia', 'innerHTML', $html);
        $objResponse->script("$('#divDropDownParroquia').dropdown('restore defaults');");
        $objResponse->script("$('#divDropDownParroquia').dropdown();");
    } catch (Exception $e) {
        $objResponse->script("showMessage(6)");
        logError("parroquia", "findValuesDropDownParroquia", $e->getMessage());
    }
    return $objResponse;
}



function findValuesDropDownTipoproyecto() {
    $objResponse = new xajaxResponse();
    $objTable = new md_tbltipo_proyecto();
    $resultSet = null;
    $html = "";

    try {
        $resultSet = $objTable->find(" clvestatus= 0");
        $html.= "<div class='item active' data-value=''>Seleccione un Proyecto</div>";
        while ($row = pg_fetch_object($resultSet)) {
            $html.= "<div class='item' data-value='" . $row->clvcodigo . "'>" . utf8_decode($row->strdescripcion) . "</div>";
        }
        $objResponse->assign('divOptionDropDownTipoproyecto', 'innerHTML', $html);
        $objResponse->script("$('#divDropDownTipoproyecto').dropdown();");
    } catch (Exception $e) {
        $objResponse->script("showMessage(6)");
        logError("tbltipo_proyecto", "findValuesDropDownTipoproyecto", $e->getMessage());
    }
    return $objResponse;
}

function findValuesDropDownSectoreconomico() {
    $objResponse = new xajaxResponse();
    $objTable = new md_tblsector_economico();
    $resultSet = null;
    $html = "";

    try {
        $resultSet = $objTable->find(" clvestatus= 0");
        $html.= "<div class='item active' data-value=''>Seleccione un Proyecto</div>";
        while ($row = pg_fetch_object($resultSet)) {
            $html.= "<div class='item' data-value='" . $row->clvcodigo . "'>" . utf8_decode($row->strdescripcion) . "</div>";
        }
        $objResponse->assign('divOptionDropDownSectoreconomico', 'innerHTML', $html);
        $objResponse->script("$('#divDropDownSectoreconomico').dropdown();");
    } catch (Exception $e) {
        $objResponse->script("showMessage(6)");
        logError("tblsector_economico", "findValuesDropDownSectoreconomico", $e->getMessage());
    }
    return $objResponse;
}

function findValuesDropDownEstatusproyecto() {
    $objResponse = new xajaxResponse();
    $objTable = new md_tblestatus_proyecto();
    $resultSet = null;
    $html = "";

    try {
        $resultSet = $objTable->find(" clvestatus= 0");
        $html.= "<div class='item active' data-value=''>Seleccione un Proyecto</div>";
        while ($row = pg_fetch_object($resultSet)) {
            $html.= "<div class='item' data-value='" . $row->clvcodigo . "'>" . utf8_decode($row->strdescripcion) . "</div>";
        }
        $objResponse->assign('divOptionDropDownEstatusproyecto', 'innerHTML', $html);
        $objResponse->script("$('#divDropDownEstatusproyecto').dropdown();");
    } catch (Exception $e) {
        $objResponse->script("showMessage(6)");
        logError("tblestatus_proyecto", "findValuesDropDownEstatusproyecto", $e->getMessage());
    }
    return $objResponse;
}

function searchSaime($nacionalidad, $cedula, $obj1 = NULL, $obj2 = NULL) {
    $objResponse = new xajaxResponse();
    $objTable = new md_saime();
    //$objTablePersona = new md_tblconductor();
    $resultSet = null;
    $resultSetPersona = null;
    try {

        $resultSet = $objTable->findSaime("strnacionalidad= '" . $nacionalidad . "' AND intcedula= " . $cedula);
        $row = pg_fetch_object($resultSet);
        if (pg_num_rows($resultSet) > 0) {
            if ($obj1) {
                $objResponse->assign($obj1, "value", strtoupper($row->strnombre_primer));
            }

            if ($obj2) {
                $objResponse->assign($obj2, "value", strtoupper($row->strapellido_primer));
            }
        } else {
            $objResponse->assign($obj1, "value", "");
            $objResponse->assign($obj2, "value", "");
        }
    } catch (Exception $e) {
        $objResponse->script("showMessage(6)");
        logError("tbldiex", "select", $e->getMessage());
    }
    return $objResponse;
}


$xajax->registerFunction('save');
$xajax->registerFunction('view');
$xajax->registerFunction('delete');
$xajax->registerFunction('findValuesDropDownCementera');
$xajax->registerFunction('findValuesDropDownConstrupatria');
$xajax->registerFunction('findValuesDropDownCementera_acopio');
$xajax->registerFunction('findValuesDropDownTipoproyecto');
$xajax->registerFunction('findValuesDropDownEstatusproyecto');
$xajax->registerFunction('findValuesDropDownSectoreconomico');
$xajax->registerFunction('findValuesDropDownEstado');
$xajax->registerFunction('findValuesDropDownMunicipio');
$xajax->registerFunction('findValuesDropDownParroquia');
$xajax->registerFunction('searchSaime');

?>