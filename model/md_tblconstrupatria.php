<?php

include_once(dirname(dirname(__FILE__)) . '/config/conexion.php');

class md_tblconstrupatria {

    var $tableName = "tblconstrupatria";
    var $pkField = "clvcodigo";
    var $sequence = 'tblconstrupatria_clvcodigo_seq';
    var $clvcodigo;
    var $strdescripcion;
    var $clvestado;
    var $clvmunicipio;
    var $clvparroquia;
    var $memdireccion;
    var $strtelefono;
    var $clvestatus;
    var $dtmfecha_creacion;
    var $dtmfecha_modificacion;
    var $dtmfecha_eliminacion;
    var $clvusuario_creador;
    var $clvusuario_modificar;
    var $clvusuario_eliminar;

    function __construct() {
        $this->clvcodigo = null;
        $this->strdescripcion = null;
        $this->clvestado = null;
        $this->clvmunicipio = null;
        $this->clvparroquia = null;
        $this->memdireccion = null;
        $this->strtelefono = null;
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

        $fields.="strdescripcion";
        $fields.=",clvestado";
        $fields.=",clvmunicipio";
        $fields.=",clvparroquia";
        $fields.=",memdireccion";
        $fields.=",strtelefono";
        $fields.=",clvestatus";

       $values.="'" . $this->strdescripcion . "'";
        $values.=", '" . $this->clvestado . "'";
        $values.=", '" . $this->clvmunicipio . "'";
        $values.=", '" . $this->clvparroquia . "'";
        $values.=", '" . $this->memdireccion . "'";
        $values.=", '" . $this->strtelefono . "'";
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
        $fields.= ", strdescripcion";
        $fields.= ", clvestado";
        $fields.= ", clvmunicipio";
        $fields.= ", clvparroquia";
        $fields.= ", memdireccion";
        $fields.= ", strtelefono";
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

        $fieldsValues.="strdescripcion= '" . $this->strdescripcion . "'";
        $fieldsValues.=", clvestado= '" . $this->clvestado . "'";
        $fieldsValues.=", clvmunicipio= '" . $this->clvmunicipio . "'";
        $fieldsValues.=", clvparroquia= '" . $this->clvparroquia . "'";
        $fieldsValues.=", memdireccion= '" . $this->memdireccion . "'";
        $fieldsValues.=", strtelefono= '" . $this->strtelefono . "'";
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