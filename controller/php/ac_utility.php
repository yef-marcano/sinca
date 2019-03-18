<?php

function searchSaime($nacionalidad, $cedula, $obj1 = NULL, $obj2 = NULL) {
    $objResponse = new xajaxResponse();
    $objTable = new md_saime();
    $objTablePersona = new md_tblconductor();
    $resultSet = null;
    $resultSetPersona = null;
    try {
        $resultSetPersona = $objTablePersona->find("strnacionalidad= '" . $nacionalidad . "' AND intcedula= " . $cedula);
        if (pg_num_rows($resultSetPersona) == 0) {
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
        } else {
            $rowPersona = pg_fetch_object($resultSetPersona);
            $objResponse->alert("El conductor " . strtoupper($rowPersona->strnombre) . " " . strtoupper($rowPersona->strapellido) . " (" . strtoupper($rowPersona->strnacionalidad) . "-" . $rowPersona->intcedula . "), ya se encuentra registrado en el sistema.");
            $objResponse->script("$('#divDropDownNacionalidad').dropdown('restore defaults');");
            $objResponse->assign("txt_nacionalidad", "value", "");
            $objResponse->assign("txt_cedula", "value", "");
            $objResponse->assign($obj1, "value", "");
            $objResponse->assign($obj2, "value", "");            
        }
    } catch (Exception $e) {
        $objResponse->script("showMessage(6)");
        logError("tbldiex", "select", $e->getMessage());
    }
    return $objResponse;
}

$xajax->registerFunction('searchSaime');
