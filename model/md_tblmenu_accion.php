<?php

include_once(dirname(dirname(__FILE__)) . '/config/conexion.php');

class md_tblmenu_accion {

    var $tableName = "tblmenu_accion";
    var $pkField = "clvcodigo";
    var $sequence = 'tblmenu_accion_clvcodigo_seq';
    var $clvcodigo;
    var $clvmenu;
    var $clvaccion;    
    var $clvestatus;
    var $dtmfecha_creacion;
    var $dtmfecha_modificacion;
    var $dtmfecha_eliminacion;
    var $clvusuario_creador;
    var $clvusuario_modificar;
    var $clvusuario_eliminar;

    function __construct() {
        $this->clvcodigo = null;
        $this->clvmenu = null;
        $this->clvaccion = null;
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

        $fields.="clvmenu";
        $fields.=", clvaccion";
        $fields.=", clvestatus";

        $values.= $this->clvmenu;
        $values.= ", " . $this->clvaccion;
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

    function find($condition = "") {
        $conex = Conexion::singleton();
        $conex->connect_pgsql();
        $fields = '';

        $fields.= "clvcodigo";
        $fields.= ", clvmenu";
        $fields.= ", (SELECT m.strdescripcion FROM tblmenu m WHERE m.clvcodigo= " . $this->tableName . ".clvmenu) AS nombre_menu";
        $fields.= ", clvaccion";
        $fields.= ", (SELECT a.strdescripcion FROM tblaccion a WHERE a.clvcodigo= " . $this->tableName . ".clvaccion) AS nombre_accion";
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

        $fieldsValues.="clvmenu= " . $this->clvmenu;
        $fieldsValues.=", clvaccion= " . $this->clvaccion;
        $fieldsValues.= $conex->audit('U');

        $condition = $this->pkField . "= " . $this->clvcodigo;

        $query = "UPDATE " . $this->tableName . " SET " . $fieldsValues . " WHERE " . $condition;
        $conex->pgs_query($query);
        $conex->disconnect_pgsql();
    }

    function delete() {
        $conex = Conexion::singleton();
        $conex->connect_pgsql();
        $condition = "";

        $condition = $this->pkField . "= " . $this->clvcodigo;

        $query = "DELETE FROM " . $this->tableName . " WHERE " . $condition;
        $conex->pgs_query($query);
        $conex->disconnect_pgsql();
    }
    
    function deleteByIdMenu($idMenu) {
        $conex = Conexion::singleton();
        $conex->connect_pgsql();
        $condition = "";

        $condition = "clvmenu= " . $idMenu;

        $query = "DELETE FROM " . $this->tableName . " WHERE " . $condition;
        
        //echo $query;die;
        $conex->pgs_query($query);
        $conex->disconnect_pgsql();
    }

}

?>