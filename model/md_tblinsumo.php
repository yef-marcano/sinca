<?php

include_once(dirname(dirname(__FILE__)) . '/config/conexion.php');

class md_tblinsumo {

    var $tableName = "tblinsumo";
    var $pkField = "clvcodigo";
    var $sequence = 'tblinsumo_clvcodigo_seq';
    var $clvcodigo;
    var $clvcategoria;
    var $clvsubcategoria;
    var $strdescripcion;
    var $clvunidad_medida;
    var $sngprecio;
    var $sngprecio_privada;
    var $intexistencia_minima;
    var $intexistencia_maxima;
    var $clvestatus;
    var $dtmfecha_creacion;
    var $dtmfecha_modificacion;
    var $dtmfecha_eliminacion;
    var $clvusuario_creador;
    var $clvusuario_modificar;
    var $clvusuario_eliminar;

    function __construct() {
        $this->clvcodigo = null;
        $this->clvcategoria = null;
        $this->clvsubcategoria = null;
        $this->strdescripcion = null;
        $this->clvunidad_medida = null;
        $this->sngprecio = null;
        $this->sngprecio_privada = null;
        $this->intexistencia_minima = null;
        $this->intexistencia_maxima = null;
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

        $fields.="clvcategoria";
        $fields.=",clvsubcategoria";
        $fields.=",strdescripcion";
        $fields.=",clvunidad_medida";
        $fields.=",sngprecio";
        $fields.=",sngprecio_privada";
        $fields.=",intecistencia_minima";
        $fields.=",intecistencia_maxima";
        $fields.=",clvestatus";

        $values.="'" . $this->clvcategoria . "'";
        $values.=", '" . $this->clvsubcategoria . "'";
        $values.=", '" . $this->strdescripcion . "'";
        $values.=", '" . $this->clvunidad_medida . "'";
        $values.=", '" . $this->sngprecio . "'";
        $values.=", '" . $this->sngprecio_privada . "'";
        $values.=", '" . $this->intexistencia_minima . "'";
        $values.=", '" . $this->intexistencia_maxima . "'";
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
        $fields.= ", clvcategoria";
        $fields.= ", clvsubcategoria";
        $fields.= ", strdescripcion";
        $fields.= ", clvunidad_medida";
        $fields.= ", sngprecio";
        $fields.= ", sngprecio_privada";
        $fields.= ", intexistencia_minima";
        $fields.= ", intexistencia_maxima";
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
        $fieldsValues.=", clvcategoria= '" . $this->clvcategoria . "'";
        $fieldsValues.=", clvsubcategoria= '" . $this->clvsubcategoria . "'";
        $fieldsValues.=", clvunidad_medida= '" . $this->clvunidad_medida . "'";
        $fieldsValues.=", sngprecio= '" . $this->sngprecio . "'";
        $fieldsValues.=", sngprecio_privada= '" . $this->sngprecio_privada . "'";
        $fieldsValues.=", intexistencia_minima= '" . $this->intexistencia_minima . "'";
        $fieldsValues.=", intexistencia_maxima= '" . $this->intexistencia_maxima . "'";
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