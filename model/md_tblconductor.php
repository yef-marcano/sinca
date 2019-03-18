<?php

include_once(dirname(dirname(__FILE__)) . '/config/conexion.php');

class md_tblconductor {

    var $tableName = "tblconductor";
    var $pkField = "clvcodigo";
    var $sequence = 'tblconductor_clvcodigo_seq';
    var $clvcodigo;
    var $strnacionalidad;
    var $intcedula;
    var $strnombre;
    var $strapellido;
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
        $this->strnacionalidad = null;
        $this->intcedula = null;
        $this->strnombre = null;
        $this->strapellido = null;
        $this->strtelefono = null;
        $this->clvestaus = null;
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

        $fields.="strnacionalidad";
        $fields.=",intcedula";
        $fields.=",strnombre";
        $fields.=",strapellido";
        $fields.=",strtelefono";
        $fields.=",clvestatus";

        $values.="'" . $this->strnacionalidad . "'";
        $values.=",'" . $this->intcedula . "'";
        $values.=",'" . $this->strnombre . "'";
        $values.=",'" . $this->strapellido . "'";
        $values.=",'" . $this->strtelefono . "'";
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
        $fields.= ", strnacionalidad";
        $fields.= ", intcedula";
        $fields.= ", strnombre";
        $fields.= ", strapellido";
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

        $fieldsValues.="strnacionalidad= '" . $this->strnacionalidad . "'";
        $fieldsValues.="intcedula= '" . $this->intcedula . "'";
        $fieldsValues.="strnombre= '" . $this->strnombre . "'";
        $fieldsValues.="strapellido= '" . $this->strapellido . "'";
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