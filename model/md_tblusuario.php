<?php

include_once(dirname(dirname(__FILE__)) . '/config/conexion.php');

class md_tblusuario {

    var $tableName = "tblusuario";
    var $pkField = "clvcodigo";
    var $sequence = 'tblusuario_clvcodigo_seq';
    var $clvcodigo;
    var $strnombre;
    var $strapellido;
    var $strusuario;
    var $strpassword;
    var $clvperfil;
    var $clvestado;   
    var $clvcambiar_clave;
    var $clvestatus;
    var $dtmfecha_creacion;
    var $dtmfecha_modificacion;
    var $dtmfecha_eliminacion;
    var $clvusuario_creador;
    var $clvusuario_modificar;
    var $clvusuario_eliminar;

    function __construct() {
        $this->clvcodigo = null;
        $this->strnombre = null;
        $this->strapellido = null;
        $this->strusuario = null;
        $this->strpassword = null;
        $this->clvperfil = null;
        $this->clvestado = null;        
        $this->clvcambiar_clave = null;
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

        $fields.="strnombre";
        $fields.=",strapellido";
        $fields.=",strusuario";
        $fields.=",strpassword";
        $fields.=",clvperfil";
        $fields.=",clvestado";
        $fields.=",clvcambiar_clave";
        $fields.=",clvestatus";        

        $values.="'" . $this->strnombre . "'";
        $values.=", '" . $this->strapellido . "'";
        $values.=", '" . $this->strusuario . "'";
        $values.=", MD5('" . $this->strpassword . "')";
        $values.=", " . $this->clvperfil;
        $values.=", " . $this->clvestado;
        $values.=", " . $this->clvcambiar_clave;
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
        $fields.= ", strnombre";
        $fields.= ", strapellido";
        $fields.= ", strusuario";
        $fields.= ", strpassword";
        $fields.= ", clvperfil";
        $fields.= ", (SELECT p.strdescripcion FROM tblperfil p WHERE p.clvcodigo= " . $this->tableName . ".clvperfil) AS strnombre_perfil";
        $fields.= ", clvestado";
        $fields.= ", (SELECT e.estado FROM vsw_estado e WHERE e.cod_estado= " . $this->tableName . ".clvestado) AS strnombre_estado";
        $fields.= ", (SELECT e.cod_region FROM vsw_estado e WHERE e.cod_estado= " . $this->tableName . ".clvestado) AS id_region";       
        $fields.= ", clvcambiar_clave";
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
        //echo $query;die;
        $resultSet = $conex->pgs_query($query);

        $conex->disconnect_pgsql();
        return $resultSet;
    }

    function update() {
        $conex = Conexion::singleton();
        $conex->connect_pgsql();
        $fieldsValues = '';
        $condition = "";

        $fieldsValues.="strnombre= '" . $this->strnombre . "'";
        $fieldsValues.=", strapellido= '" . $this->strapellido . "'";
        $fieldsValues.=", strusuario= '" . $this->strusuario . "'";
        $fieldsValues.=", strpassword= MD5('" . $this->strpassword . "')";
        $fieldsValues.=", clvperfil= " . $this->clvperfil;
        $fieldsValues.=", clvestado= " . $this->clvestado;        
        $fieldsValues.=", clvcambiar_clave= " . $this->clvcambiar_clave;
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

    function updatePassword() {
        $conex = Conexion::singleton();
        $conex->connect_pgsql();
        $fieldsValues = '';
        $condition = "";
        
        $fieldsValues.="strpassword= MD5('" . $this->strpassword . "')";
        $fieldsValues.=", clvcambiar_clave= " . $this->clvcambiar_clave;
        $fieldsValues.= $conex->audit('U');

        $condition = $this->pkField . "= " . $this->clvcodigo;

        $query = "UPDATE " . $this->tableName . " SET " . $fieldsValues . " WHERE " . $condition;
        $conex->pgs_query($query);
        $conex->disconnect_pgsql();
    }
}

?>