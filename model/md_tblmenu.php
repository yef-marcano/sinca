<?php

include_once(dirname(dirname(__FILE__)) . '/config/conexion.php');

class md_tblmenu {

    var $tableName = "tblmenu";
    var $pkField = "clvcodigo";
    var $sequence = 'tblmenu_clvcodigo_seq';
    var $clvcodigo;
    var $clvmodulo;
    var $strdescripcion;
    var $strarchivo;
    var $intorden;
    var $stricono;
    var $clvestatus;
    var $dtmfecha_creacion;
    var $dtmfecha_modificacion;
    var $dtmfecha_eliminacion;
    var $clvusuario_creador;
    var $clvusuario_modificar;
    var $clvusuario_eliminar;

    function __construct() {
        $this->clvcodigo = null;
        $this->clvmodulo = null;
        $this->strdescripcion = null;
        $this->strarchivo = null;
        $this->intorden = null;
        $this->stricono;
        $this->clvestatus = null;
        $this->dtmfecha_creacion = null;
        $this->dtmfecha_modificacion = null;
        $this->dtmfecha_eliminacion = null;
        $this->clvusuario_creador = null;
        $this->clvusuario_modificar = null;
        $this->clvusuario_eliminar = null;
    }

    function insert() {
        $conex = Conexion::singleton();
        $conex->connect_pgsql();
        $fields = '';
        $values = '';
        
        $fields.="clvmodulo";
        $fields.=",strdescripcion";
        $fields.=",strarchivo";
        $fields.=",stricono";
        $fields.=",intorden";
        $fields.=",clvestatus";
        
        if($this->clvmodulo != ""){
            $values.= $this->clvmodulo;
        }else{
            $values.= "null";
        }
        $values.=", '" . $this->strdescripcion . "'";
        $values.=", '" . $this->strarchivo . "'";
        $values.=", '" . $this->stricono . "'";
        $values.=", '" . $this->intorden . "'";
        $values.=", 0";

        list($f, $v) = $conex->audit('I');
        $fields.=$f;
        $values.=$v;

        $query = "INSERT INTO " . $this->tableName . " (" . $fields . ") VALUES (" . $values . ")";
        
        $conex->pgs_query($query);       
        $pk = $conex->pgsql_lastId($this->sequence);        
        $conex->disconnect_pgsql();        
        return $pk;
    }

    function find($condition= "") {
        $conex = Conexion::singleton();
        $conex->connect_pgsql();
        $fields = '';

        $fields.= "clvcodigo";
        $fields.= ", clvmodulo";
        $fields.= ", (SELECT m.strdescripcion FROM tblmodulo m WHERE m.clvcodigo= " . $this->tableName . ".clvmodulo) AS nombre_modulo";
        $fields.= ", strdescripcion";
        $fields.= ", strarchivo";
        $fields.= ", stricono";
        $fields.= ", intorden";
        $fields.= ", clvestatus";
        $fields.= ", dtmfecha_creacion";
        $fields.= ", dtmfecha_modificacion";
        $fields.= ", dtmfecha_eliminacion";
        $fields.= ", clvusuario_creador";
        $fields.= ", (SELECT CONCAT_WS(' ', u.strnombre, u.strapellido) FROM tblusuario u WHERE u.clvcodigo= " . $this->tableName . ".clvusuario_creador) AS nombre_usuario_creador";
        $fields.= ", clvusuario_modificar";
        $fields.= ", (SELECT CONCAT_WS(' ', u.strnombre, u.strapellido) FROM tblusuario u WHERE u.clvcodigo= " . $this->tableName . ".clvusuario_modificar) AS nombre_usuario_modificar";
        $fields.= ", clvusuario_eliminar";
        $fields.= ", (SELECT CONCAT_WS(' ', u.strnombre, u.strapellido) FROM tblusuario u WHERE u.clvcodigo= " . $this->tableName . ".clvusuario_eliminar) AS nombre_usuario_eliminar";

        if ($condition == "") {
            $query = "SELECT " . $fields . " FROM " . $this->tableName;
        } else {
            $query = "SELECT " . $fields . " FROM " . $this->tableName . " WHERE " . $condition;
        }
        $resultSet = $conex->pgs_query($query);

        $conex->disconnect_pgsql();
        return $resultSet;
    }

    function update() {
        $conex = Conexion::singleton();
        $conex->connect_pgsql();
        $fieldsValues = '';
        $condition = "";

        $fieldsValues.="clvmodulo= " . $this->clvmodulo;
        $fieldsValues.=",strdescripcion= '" . $this->strdescripcion . "'";
        $fieldsValues.=",strarchivo= '" . $this->strarchivo . "'";
        $fieldsValues.=",stricono= '" . $this->stricono . "'";
        $fieldsValues.=",intorden= '" . $this->intorden . "'";
        $fieldsValues.= $conex->audit('U');

        $condition = $this->pkField . "= " . $this->clvcodigo;

        $query = "UPDATE " . $this->tableName . " SET " . $fieldsValues . " WHERE " . $condition;
        $conex->pgs_query($query);
        $conex->disconnect_pgsql();
    }
    
    function delete() {
        $conex = Conexion::singleton();
        $conex->connect_pgsql();
        $fieldsValues = '';
        $condition = "";

        $fieldsValues.="clvestatus= 1";
        $fieldsValues.= $conex->audit('D');

        $condition = $this->pkField . "= " . $this->clvcodigo;

        $query = "UPDATE " . $this->tableName . " SET " . $fieldsValues . " WHERE " . $condition;
        $conex->pgs_query($query);
        $conex->disconnect_pgsql();
    }
    
    function findByPerfil($perfil) {
        $conex = Conexion::singleton();
        $conex->connect_pgsql();
        $fields = '';
        $condition = "";
        $join = "";
        $order = "";
        
        $fields.= "m.clvcodigo";
        $fields.= ", m.clvmodulo";
        $fields.= ", mo.strdescripcion AS nombre_modulo";
        $fields.= ", mo.stricono AS icons_modulo";
        $fields.= ", m.strdescripcion";
        $fields.= ", m.strarchivo";
        $fields.= ", m.intorden";
        $fields.= ", mo.intorden";
        $fields.= ", m.clvestatus";
        $fields.= ", m.stricono";

        $join.= " INNER JOIN tblmodulo mo ON mo.clvcodigo= m.clvmodulo";
        $join.= " INNER JOIN tblmenu_accion ma ON  ma.clvmenu=m.clvcodigo";
        $join.= " INNER JOIN tblperfil_menu_accion pma ON  pma.clvperfil=".$perfil;
        $condition.= " m.clvestatus != 1";
        
        $order = " ORDER BY mo.intorden, m.intorden";

        $query = "SELECT DISTINCT " . $fields . " FROM " . $this->tableName . " m " . $join . "  WHERE " . $condition . " " . $order;
        
        //echo $query;

        $resultSet = $conex->pgs_query($query);

        $conex->disconnect_pgsql();
        return $resultSet;
    }

}

?>