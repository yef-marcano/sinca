<?php

include_once(dirname(dirname(__FILE__)) . '/config/conexion.php');

class md_tblsolicitud_cementera {

    var $tableName = "tblsolicitud_cementera";
    var $pkField = "clvcodigo";
    var $sequence = 'tblsolicitud_cementera_clvcodigo_seq';
    var $clvcodigo;
    var $clvsolicitud;
    var $clvcementera_acopio;
    var $intpedido;
    var $dtmfecha;
    var $dtmfecha_vencimiento;
    var $clvestatus_solicitud;
    var $clvestatus;
    var $dtmfecha_creacion;
    var $dtmfecha_modificacion;
    var $dtmfecha_eliminacion;
    var $clvusuario_creador;
    var $clvusuario_modificar;
    var $clvusuario_eliminar;

    function __construct() {
        $this->clvcodigo = null;
        $this->clvsolicitud = null;
        $this->clvcementera_acopio = null;
        $this->intpedido = null;
        $this->dtmfecha = null;
        $this->dtmfecha_vencimiento = null;
        $this->clvestatus_solicitud = null;
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

        $fields.="clvsolicitud";
        $fields.=",clvcementera_acopio";
        $fields.=",intpedido";
        $fields.=",dtmfecha";
        $fields.=",dtmfecha_vencimiento";
        $fields.=",clvestatus_solicitud"; 
        $fields.=",clvestatus";

        $values.="'" . $this->clvsolicitud . "'";
        $values.=", '" . $this->clvcementera_acopio . "'";
        $values.=", '" . $this->intpedido . "'";
        $values.=", '" . $this->dtmfecha . "'";
        $values.=", '" . $this->dtmfecha_vencimiento . "'";
        $values.=", 1";
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

        $fields.= "c.clvcodigo";
        $fields.= ", c.clvsolicitud";
        $fields.= ", s.clvproyecto";    
        $fields.= ", c.clvcementera_acopio";
        $fields.= ", c.intpedido";
        $fields.= ", c.dtmfecha";
        $fields.= ", c.dtmfecha_vencimiento";
        $fields.= ", c.clvestatus_solicitud";
        $fields.= ", c.clvestatus";
        $fields.= ", c.dtmfecha_creacion";
        $fields.= ", c.dtmfecha_modificacion";
        $fields.= ", c.dtmfecha_eliminacion";
        $fields.= ", c.clvusuario_creador";
        $fields.= ", (SELECT CONCAT_WS(' ', u.strnombre, u.strapellido) FROM tblusuario u WHERE u.clvcodigo= c.clvusuario_creador) AS nombre_usuario_creador";
        $fields.= ", c.clvusuario_modificar";
        $fields.= ", (SELECT CONCAT_WS(' ', u.strnombre, u.strapellido) FROM tblusuario u WHERE u.clvcodigo= c.clvusuario_modificar) AS nombre_usuario_modificar";
        $fields.= ", c.clvusuario_eliminar";
        $fields.= ", (SELECT CONCAT_WS(' ', u.strnombre, u.strapellido) FROM tblusuario u WHERE u.clvcodigo= c.clvusuario_eliminar) AS nombre_usuario_eliminar";

        if ($condition == "") {
            $query = "SELECT " . $fields . " FROM " . $this->tableName ." c, tblsolicitud s WHERE c.clvsolicitud=s.clvcodigo";
        } else { 
            $query = "SELECT " . $fields . " FROM " . $this->tableName . " c, tblsolicitud s WHERE c.clvsolicitud=s.clvcodigo and " . $condition;
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

        //$fieldsValues.="clvsolicitud= '" . $this->clvsolicitud . "'";
        $fieldsValues.="clvcementera_acopio= '" . $this->clvcementera_acopio . "'";
        //$fieldsValues.=", intpedido= '" . $this->intpedido . "'";
        //$fieldsValues.=", dtmfecha= '" . $this->dtmfecha . "'";
        //$fieldsValues.=", dtmfecha_vencimiento= '" . $this->dtmfecha_vencimiento . "'";
        //$fieldsValues.=", clvestatus_solicitud= '" . $this->clvestatus_solicitud . "'";
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

}

?>