<?php

include_once(dirname(dirname(__FILE__)) . '/config/conexion.php');

class md_tblperfil_menu_accion {

    var $tableName = "tblperfil_menu_accion";
    var $pkField = "clvcodigo";
    var $sequence = 'tblperfil_menu_accion_clvcodigo_seq';
    var $clvcodigo;
    var $clvperfil;
    var $clvmenu_accion;
    var $clvestatus;
    var $dtmfecha_creacion;
    var $dtmfecha_modificacion;
    var $dtmfecha_eliminacion;
    var $clvusuario_creador;
    var $clvusuario_modificar;
    var $clvusuario_eliminar;

    function __construct() {
        $this->clvcodigo = null;
        $this->clvperfil = null;
        $this->clvmenu_accion = null;
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

        $fields.="clvperfil";
        $fields.=", clvmenu_accion";
        $fields.=", clvestatus";

        $values.= $this->clvperfil;
        $values.= ", " . $this->clvmenu_accion;
        $values.=", 0";

        list($f, $v) = $conex->audit('I');
        $fields.=$f;
        $values.=$v;

        $query = "INSERT INTO " . $this->tableName . " (" . $fields . ") VALUES (" . $values . ")";
        //echo $query;
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
        $fields.= ", clvperfil";
        $fields.= ", (SELECT p.strdescripcion FROM tblperfil p WHERE p.clvcodigo= " . $this->tableName . ".clvperfil) AS nombre_perfil";
        $fields.= ", clvmenu_accion";
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
        //echo $query;
        $resultSet = $conex->pgs_query($query);

        $conex->disconnect_pgsql();
        return $resultSet;
    }

    function update() {
        $conex = Conexion::singleton();
        $conex->connect_pgsql();
        $fieldsValues = '';
        $condition = "";

        $fieldsValues.="clvperfil= " . $this->clvperfil;
        $fieldsValues.=", clvmenu_accion= " . $this->clvmenu_accion;
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
   function denegaData() {
        $conex = Conexion::singleton();
        $conex->connect_pgsql();
        $fieldsValues = '';
        $condition = "";

        $fieldsValues.="clvestatus= 6";
        $fieldsValues.= $conex->audit('D');

        $condition = $this->pkField . "= " . $this->clvcodigo;

        $query = "UPDATE " . $this->tableName . " SET " . $fieldsValues . " WHERE " . $condition;
        $conex->pgs_query($query);
        $conex->disconnect_pgsql();
    }
    
    function deleteByIdPerfil($idPerfil) {
        $conex = Conexion::singleton();
        $conex->connect_pgsql();
        $condition = "";

        $condition = "clvperfil= " . $idPerfil;

        $query = "DELETE FROM " . $this->tableName . " WHERE " . $condition;
        $conex->pgs_query($query);
        $conex->disconnect_pgsql();
    }

    function deleteByIdMenu($idMenu) {
        $conex = Conexion::singleton();
        $conex->connect_pgsql();
        $condition = "";

        $condition = $this->tableName . ".clvmenu_accion IN (SELECT ma.clvmenu_accion FROM tblmenu_accion ma WHERE ma.clvcodigo= " . $idMenu . ")";

        $query = "DELETE FROM " . $this->tableName . " WHERE " . $condition;
        $conex->pgs_query($query);
        $conex->disconnect_pgsql();
    }

    function checkAction($perfil, $menu, $action) {
        $conex = Conexion::singleton();
        $conex->connect_pgsql();
        $fields = '';

        $fields.= "count(*) AS cant";

        $tabla = "tblperfil_menu_accion pma";
        $join = "tblmenu_accion ma ON ma.clvcodigo= pma.clvmenu_accion";

        $condition = "pma.clvperfil= " . $perfil . " AND ma.clvmenu= " . $menu . " AND ma.clvaccion= " . $action;

        $query = "SELECT " . $fields . " FROM " . $tabla . " INNER JOIN " . $join . " WHERE " . $condition;
        
        //echo $query;
        
        $resultSet = $conex->pgs_query($query);
        $row= pg_fetch_object($resultSet);
        
        $conex->disconnect_pgsql();
        return $row->cant;
    }

}

?>