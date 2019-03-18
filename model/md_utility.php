<?php

include_once(dirname(dirname(__FILE__)) . '/config/conexion.php');

class md_utility {

    
    function findRegion($condition= "") {
        $conex = Conexion::singleton();
        $conex->connect_pgsql();
        $fields = '';

        $fields.= "cod_region";
        $fields.= ", region";
        $fields.= ", borrado";
        

        if ($condition == "") {
            $query = "SELECT " . $fields . " FROM vsw_region";
        } else {
            $query = "SELECT " . $fields . " FROM vsw_region WHERE " . $condition;
        }
        $resultSet = $conex->pgs_query($query);

        $conex->disconnect_pgsql();
        return $resultSet;
    }
    
    function findEstado($condition= "") {
        $conex = Conexion::singleton();
        $conex->connect_pgsql();
        $fields = '';

        $fields.= "cod_estado";
        $fields.= ", cod_region";
        $fields.= ", estado";
        $fields.= ", region";
        $fields.= ", borrado";
       

        if ($condition == "") {
            $query = "SELECT " . $fields . " FROM vsw_estado";
        } else {
            $query = "SELECT " . $fields . " FROM vsw_estado WHERE " . $condition;
        }
        $resultSet = $conex->pgs_query($query);

        $conex->disconnect_pgsql();
        return $resultSet;
    }
    
    function findMunicipio($condition= "") {
        $conex = Conexion::singleton();
        $conex->connect_pgsql();
        $fields = '';

        $fields.= "cod_municipio";
        $fields.= ", cod_estado";
        $fields.= ", estado";
        $fields.= ", municipio";        
        $fields.= ", borrado";
        

        if ($condition == "") {
            $query = "SELECT " . $fields . " FROM vsw_municipio";
        } else {
            $query = "SELECT " . $fields . " FROM vsw_municipio WHERE " . $condition;
        }
        $resultSet = $conex->pgs_query($query);

        $conex->disconnect_pgsql();
        return $resultSet;
    }
    
    function findParroquia($condition= "") {
        $conex = Conexion::singleton();
        $conex->connect_pgsql();
        $fields = '';

        $fields.= "cod_parroquia";
        $fields.= ", cod_municipio";
        $fields.= ", municipio";
        $fields.= ", parroquia";        
        $fields.= ", borrado";
        

        if ($condition == "") {
            $query = "SELECT " . $fields . " FROM vsw_parroquia";
        } else {
            $query = "SELECT " . $fields . " FROM vsw_parroquia WHERE " . $condition;
        }
        $resultSet = $conex->pgs_query($query);

        $conex->disconnect_pgsql();
        return $resultSet;
    }
    
    
    
}

?>
