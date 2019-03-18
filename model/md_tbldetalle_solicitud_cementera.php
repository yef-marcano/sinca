<?php

include_once(dirname(dirname(__FILE__)) . '/config/conexion.php');

class md_tbldetalle_solicitud_cementera {

    var $tableName = "tbldetalle_solicitud_cementera";
    var $pkField = "clvcodigo";
    var $sequence = 'tbldetalle_solicitud_cementera_clvcodigo_seq';
    var $clvcodigo;
    var $clvsolicitud_cementera;
    var $clvinsumo;
    var $intcantidad_solicitada;
    var $intcantidad_despachada;
    var $clvestatus;
    var $dtmfecha_creacion;
    var $dtmfecha_modificacion;
    var $dtmfecha_eliminacion;
    var $clvusuario_creador;
    var $clvusuario_modificar;
    var $clvusuario_eliminar;

    function __construct() {
        $this->clvcodigo = null;
        $this->clvsolicitud_cementera = null;
        $this->clvinsumo = null;
        $this->intcantidad_solicitada = null;
        $this->intcantidad_despachada = null;
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

        $fields.="clvsolicitud_cementera";
        $fields.=",clvinsumo";
        $fields.=",intcantidad_solicitada";
        $fields.=",intcantidad_despachada";
        $fields.=",clvestatus";

        $values.="'" . $this->clvsolicitud_cementera . "'";
        $values.=",'" . $this->clvinsumo . "'";
        $values.=",'" . $this->intcantidad_solicitada . "";
        $values.=",'" . $this->intcantidad_despachada . "";
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
        $fields.= ", clvsolicitud_cementera";
        $fields.= ", clvinsumo";
        $fields.= ", (SELECT u.strdescripcion FROM tblinsumo u WHERE u.clvcodigo= " . $this->tableName . ".clvinsumo) AS nombre_insumo";
        $fields.= ", intcantidad_solicitada";
        $fields.= ", intcantidad_despachada";
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

        $fieldsValues.="clvsolicitud_cementera= '" . $this->clvsolicitud_cementera . "'";
        $fieldsValues.=", clvinsumo= '" . $this->clvinsumo . "'";
        $fieldsValues.=", intcantidad_solicitada= '" . $this->intcantidad_solicitada . "'";
        $fieldsValues.=", intcantidad_despachada= '" . $this->intcantidad_despachada . "'";
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