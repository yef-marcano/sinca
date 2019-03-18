<?php

include_once(dirname(dirname(__FILE__)) . '/config/conexion.php');

class md_tblorden_salida_almacen {

    var $tableName = "tblorden_salida_almacen";
    var $pkField = "clvcodigo";
    var $sequence = 'tblorden_salida_almacen_clvcodigo_seq';
    var $clvcodigo;
    var $clvsolicitud;
    var $clvalmacen;
    var $dtmfecha;
    var $clvconductor;
    var $clvvehiculo;
    var $memdireccion_destino;
    var $memobservacion;
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
        $this->clvalmacen = null;
        $this->dtmfecha = null;
        $this->clvconductor = null;
        $this->clvvehiculo = null;
        $this->memdireccion_destino = null;
        $this->memobservacion = null;
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
        $fields.=",clvalmacen";
        $fields.=",dtmfecha";
        $fields.=",clvconductor";
        $fields.=",clvvehiculo";
        $fields.=",memdireccion_destino";
        $fields.=",memobservacion";
        $fields.=",clvestatus";

        $values.="'" . $this->clvsolicitud . "'";
        $values.=", '" . $this->clvalmacen . "'";
        $values.=", '" . $this->dtmfecha . "'";
        $values.=", '" . $this->clvconductor . "'";
        $values.=", '" . $this->clvvehiculo . "'";
        $values.=", '" . $this->memdireccion_destino . "'";
        $values.=", '" . $this->memobservacion . "'";
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
        $fields.= ", c.clvalmacen";
        $fields.= ", c.dtmfecha";
        $fields.= ", c.clvconductor";
        $fields.= ", c.clvvehiculo";
        $fields.= ", c.memdireccion_destino";
        $fields.= ", c.memobservacion";
        $fields.= ", c.dtmfecha_creacion";
        $fields.= ", c.dtmfecha_modificacion";
        $fields.= ", c.dtmfecha_eliminacion";
        $fields.= ", c.clvusuario_creador";
        $fields.= ", c.clvusuario_modificar";
        $fields.= ", c.clvusuario_eliminar";
        $fields.= ", (SELECT CONCAT_WS(' ', u.strnombre, u.strapellido) FROM tblusuario u WHERE u.clvcodigo=c.clvusuario_creador) AS nombre_usuario_creador";
        $fields.= ", c.clvusuario_modificar";
        $fields.= ", (SELECT CONCAT_WS(' ', u.strnombre, u.strapellido) FROM tblusuario u WHERE u.clvcodigo=c.clvusuario_modificar) AS nombre_usuario_modificar";
        $fields.= ", c.clvusuario_eliminar";
        $fields.= ", (SELECT CONCAT_WS(' ', u.strnombre, u.strapellido) FROM tblusuario u WHERE u.clvcodigo=c.clvusuario_eliminar) AS nombre_usuario_eliminar";

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

        $fieldsValues.="clvsolicitud= '" . $this->clvsolicitud . "'";
        $fieldsValues.=", clvalmacen= '" . $this->clvalmacen . "'";
        $fieldsValues.=", dtmfecha= '" . $this->dtmfecha . "'";
        $fieldsValues.=", clvconductor= '" . $this->clvconductor . "'";
        $fieldsValues.=", clvvehiculo= '" . $this->clvvehiculo . "'";
        $fieldsValues.=", memdireccion_destino= '" . $this->memdireccion_destino . "'";
        $fieldsValues.=", memobservacion= '" . $this->memobservacion . "'";
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