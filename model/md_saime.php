<?php

include_once(dirname(dirname(__FILE__)) . '/config/conexionSaime.php');

class md_saime {

    function findSaime($condition= "") {
        $conex = ConexionSaime::singleton();
        $conex->connect_pgsql();
        $fields = '';

        $fields.= "strnacionalidad";
        $fields.= ", intcedula";
        $fields.= ", strnombre_primer";
        $fields.= ", strnombre_segundo";
        $fields.= ", strapellido_primer";
        $fields.= ", strapellido_segundo";
        $fields.= ", dtmnacimiento";
        $fields.= ", clvobjecion";
        $fields.= ", blnborrado";
        $fields.= ", dtmregistro";
        $fields.= ", clvusuario";
        $fields.= ", date_part('year',age( dtmnacimiento )) AS stredad";

        if ($condition == "") {
            $query = "SELECT " . $fields . " FROM tbldiex";
        } else {
            $query = "SELECT " . $fields . " FROM tbldiex WHERE " . $condition;
        }
        $resultSet = $conex->pgs_query($query);

        $conex->disconnect_pgsql();
        return $resultSet;
    }
    
    
    
    
}

?>
