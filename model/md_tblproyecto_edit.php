<?php

include_once(dirname(dirname(__FILE__)) . '/config/conexion.php');

class md_tblproyecto {

    var $tableName = "tblproyecto";
    var $pkField = "clvcodigo";
    var $sequence = 'tblproyecto_clvcodigo_seq';
    var $clvcodigo;
    var $strnombre;
    var $memdescripcion;
    var $clvtipo_proyecto;
    var $clvsector_economico;
    var $clvestado;
    var $clvmunicipio;
    var $clvparroquia;
    var $memdireccion;
    var $clvestatus_proyecto;
    var $strcodigo_construpatria;
    var $clvconstrupatria;
    var $strcodigo_cemento; 
    var $clvcementera;
    var $clvcementera_acopio;
    var $strnacionalidad_tecnico;
    var $intcedula_tecnico;
    var $strnombre_tecnico;
    var $strapellido_tecnico;
    var $strtelefonocorp_tecnico;
    var $strtelefonoper_tecnico;
    var $strnacionalidad_inspector;
    var $intcedula_inspector;
    var $strnombre_inspector;
    var $strapellido_inspector;
    var $strtelefonocorp_inspector;
    var $strtelefonoper_inspector;
    var $strnacionalidad_residente;
    var $intcedula_residente;
    var $strnombre_residente;
    var $strapellido_residente;
    var $strtelefonocorp_residente;
    var $strtelefonoper_residente;
    var $strnacionalidad_contacto;
    var $intcedula_contacto;
    var $strnombre_contacto;
    var $strapellido_contacto;
    var $strtelefonocorp_contacto;
    var $strtelefonoper_contacto;
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
        $this->memdescripcion = null;
        $this->clvtipo_proyecto = null;
        $this->clvsector_economico = null;
        $this->clvestado = null;
        $this->clvmunicipio = null;
        $this->clvparroquia = null;
        $this->memdireccion = null;
        $this->clvestatus_proyecto = null;
        $this->strcodigo_construpatria = null;
        $this->clvconstrupatria = null;
        $this->strcodigo_cemento = null;
        $this->clvcementera = null;
        $this->clvcementera_acopio = null;
        $this->strnacionalidad_tecnico = null;
        $this->intcedula_tecnico = null;
        $this->strnombre_tecnico = null;
        $this->strapellido_tecnico = null;
        $this->strtelefonocorp_tecnico = null;
        $this->strtelefonoper_tecnico = null;
        $this->strnacionalidad_inspector = null;
        $this->intcedula_inspector = null;
        $this->strnombre_inspector = null;
        $this->strapellido_inspector = null;
        $this->strtelefonocorp_inspector= null;
        $this->strtelefonoper_inspector= null;
        $this->strnacionalidad_residente = null;
        $this->intcedula_residente = null;
        $this->strnombre_residente = null;
        $this->strapellido_residente = null;
        $this->strtelefonocorp_residente = null;
        $this->strtelefonoper_residente = null;
        $this->strnacionalidad_contacto = null;
        $this->intcedula_contacto = null;
        $this->strnombre_contacto = null;
        $this->strapellido_contacto = null;
        $this->strtelefonocorp_contacto = null;
        $this->strtelefonoper_contacto = null;
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
        $fields.=",memdescripcion";
        $fields.=",clvtipo_proyecto";
        $fields.=",clvsector_economico";
        $fields.=",clvestado";
        $fields.=",clvmunicipio";
        $fields.=",clvparroquia";
        $fields.=",memdireccion";
        $fields.=",clvestatus_proyecto";
        $fields.=",strcodigo_construpatria";
        $fields.=",clvconstrupatria";
        $fields.=",strcodigo_cemento";
        $fields.=",clvcementera";
        $fields.=",clvcementera_acopio";
        $fields.=",strnacionalidad_tecnico";
        $fields.=",intcedula_tecnico";
        $fields.=",strnombre_tecnico";
        $fields.=",strapellido_tecnico";
        $fields.=",strtelefonocorp_tecnico";
        $fields.=",strtelefonoper_tecnico";
        $fields.=",strnacionalidad_inspector";
        $fields.=",intcedula_inspector";
        $fields.=",strnombre_inspector";
        $fields.=",strapellido_inspector";
        $fields.=",strtelefonocorp_inspector";
        $fields.=",strtelefonoper_inspector";
        $fields.=",strnacionalidad_residente";
        $fields.=",intcedula_residente";
        $fields.=",strnombre_residente";
        $fields.=",strapellido_residente";
        $fields.=",strtelefonocorp_residente";
        $fields.=",strtelefonoper_residente";
        $fields.=",strnacionalidad_contacto";
        $fields.=",intcedula_contacto";
        $fields.=",strnombre_contacto";
        $fields.=",strapellido_contacto";
        $fields.=",strtelefonocorp_contacto";
        $fields.=",strtelefonoper_contacto";     
        $fields.=",clvestatus";      

        $values.="'" . $this->strnombre . "'";
        $values.=", '" . $this->memdescripcion . "'";
        $values.=", '" . $this->clvtipo_proyecto . "'";
        $values.=", '" . $this->clvsector_economico . "'";
        $values.=", '" . $this->clvestado . "'";
        $values.=", '" . $this->clvmunicipio . "'";
        $values.=", '" . $this->clvparroquia . "'";
        $values.=", '" . $this->memdireccion . "'";
        $values.=", '" . $this->clvestatus_proyecto . "'";
        $values.=", '" . $this->strcodigo_construpatria . "'";
        $values.=", '" . $this->clvconstrupatria . "'";
        $values.=", '" . $this->strcodigo_cemento . "'";
        $values.=", '" . $this->clvcementera . "'";
        $values.=", '" . $this->clvcementera_acopio . "'";
        $values.=", '" . $this->strnacionalidad_tecnico . "'";
        $values.=", '" . $this->intcedula_tecnico . "'";
        $values.=", '" . $this->strnombre_tecnico . "'";
        $values.=", '" . $this->strapellido_tecnico . "'";
        $values.=", '" . $this->strtelefonocorp_tecnico . "'";
        $values.=", '" . $this->strtelefonoper_tecnico . "'";
        $values.=", '" . $this->strnacionalidad_inspector . "'";
        $values.=", '" . $this->intcedula_inspector . "'";
        $values.=", '" . $this->strnombre_inspector . "'";
        $values.=", '" . $this->strapellido_inspector . "'";
        $values.=", '" . $this->strtelefonocorp_inspector . "'";
        $values.=", '" . $this->strtelefonoper_inspector . "'";
        $values.=", '" . $this->strnacionalidad_residente . "'";
        $values.=", '" . $this->intcedula_residente . "'";
        $values.=", '" . $this->strnombre_residente . "'";
        $values.=", '" . $this->strapellido_residente . "'";
        $values.=", '" . $this->strtelefonocorp_residente . "'";
        $values.=", '" . $this->strtelefonoper_residente . "'";
        $values.=", '" . $this->strnacionalidad_contacto . "'";
        $values.=", '" . $this->intcedula_contacto . "'";
        $values.=", '" . $this->strnombre_contacto . "'";
        $values.=", '" . $this->strapellido_contacto . "'";
        $values.=", '" . $this->strtelefonocorp_contacto . "'";
        $values.=", '" . $this->strtelefonoper_contacto . "'";
        
        
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
        $fields.= ", strnombre";
        $fields.= ", memdescripcion";
        $fields.= ", clvtipo_proyecto";
        $fields.= ", clvsector_economico";
        $fields.= ", clvestado";
        $fields.= ", clvmunicipio";
        $fields.= ", clvparroquia";
        $fields.= ", memdireccion";
        $fields.= ", clvestatus_proyecto";
        $fields.= ", strcodigo_construpatria";
        $fields.= ", clvconstrupatria";
        $fields.= ", strcodigo_cemento";
        $fields.= ", clvcementera";
        $fields.= ", clvcementera_acopio";
        $fields.= ", strnacionalidad_tecnico";
        $fields.= ", intcedula_tecnico";
        $fields.= ", strnombre_tecnico";
        $fields.= ", strapellido_tecnico";
        $fields.= ", strtelefonocorp_tecnico";
        $fields.= ", strtelefonoper_tecnico";
        $fields.= ", strnacionalidad_inspector";
        $fields.= ", intcedula_inspector";
        $fields.= ", strnombre_inspector";
        $fields.= ", strapellido_inspector";
        $fields.= ", strtelefonocorp_inspector";
        $fields.= ", strtelefonoper_inspector";
        $fields.= ", strnacionalidad_residente";
        $fields.= ", intcedula_residente";
        $fields.= ", strnombre_residente";
        $fields.= ", strapellido_residente";
        $fields.= ", strtelefonocorp_residente";
        $fields.= ", strtelefonoper_residente";
        $fields.= ", strnacionalidad_contacto";
        $fields.= ", intcedula_contacto";
        $fields.= ", strnombre_contacto";
        $fields.= ", strapellido_contacto";
        $fields.= ", strtelefonocorp_contacto";
        $fields.= ", strtelefonoper_contacto";
        $fields.= ", clvestatus";
        $fields.= ", dtmfecha_creacion";
        $fields.= ", dtmfecha_modificacion";
        $fields.= ", dtmfecha_eliminacion";
        $fields.= ", clvusuario_creador";
        $fields.= ", clvusuario_modificar";
        $fields.= ", clvusuario_eliminar";
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

        $fieldsValues.="strnombre= '" . $this->strnombre . "'";
        $fieldsValues.=", memdescripcion= '" . $this->memdescripcion . "'";
        $fieldsValues.=", clvtipo_proyecto= '" . $this->clvtipo_proyecto . "'";
        $fieldsValues.=", clvsector_economico= '" . $this->clvsector_economico . "'";
        $fieldsValues.=", clvestado= '" . $this->clvestado . "'";
        $fieldsValues.=", clvmunicipio= '" . $this->clvmunicipio . "'";
        $fieldsValues.=", clvparroquia= '" . $this->clvparroquia . "'";
        $fieldsValues.=", memdireccion= '" . $this->memdireccion . "'";
        $fieldsValues.=", clvestatus_proyecto= '" . $this->clvestatus_proyecto . "'";
        $fieldsValues.=", clvconstrupatria= '" . $this->clvconstrupatria . "'";
        $fieldsValues.=", clvcementera= '" . $this->clvcementera . "'";
        $fieldsValues.=", clvcementera_acopio= '" . $this->clvcementera_acopio . "'";
        
        $fieldsValues.=", strcodigo_construpatria= '" . $this->strcodigo_construpatria . "'";
        $fieldsValues.=", strcodigo_cemento= '" . $this->strcodigo_cemento . "'";
        $fieldsValues.=", strnacionalidad_tecnico= '" . $this->strnacionalidad_tecnico . "'";
        $fieldsValues.=", intcedula_tecnico= '" . $this->intcedula_tecnico . "'";
        $fieldsValues.=", strnombre_tecnico= '" . $this->strnombre_tecnico . "'";
        $fieldsValues.=", strapellido_tecnico= '" . $this->strapellido_tecnico . "'";
        $fieldsValues.=", strtelefonocorp_tecnico= '" . $this->strtelefonocorp_tecnico . "'";
        $fieldsValues.=", strtelefonoper_tecnico= '" . $this->strtelefonoper_tecnico . "'";
        $fieldsValues.=", strnacionalidad_inspector= '" . $this->strnacionalidad_inspector . "'";
        $fieldsValues.=", intcedula_inspector= '" . $this->intcedula_inspector . "'";
        $fieldsValues.=", strnombre_inspector= '" . $this->strnombre_inspector . "'";
        $fieldsValues.=", strapellido_inspector= '" . $this->strapellido_inspector . "'";
        $fieldsValues.=", strtelefonocorp_inspector= '" . $this->strtelefonocorp_inspector . "'";
        $fieldsValues.=", strtelefonoper_inspector= '" . $this->strtelefonoper_inspector . "'";
        $fieldsValues.=", strnacionalidad_residente= '" . $this->strnacionalidad_residente . "'";
        $fieldsValues.=", intcedula_residente= '" . $this->intcedula_residente . "'";
        $fieldsValues.=", strnombre_residente= '" . $this->strnombre_residente . "'";
        $fieldsValues.=", strapellido_residente= '" . $this->strapellido_residente . "'";
        $fieldsValues.=", strtelefonocorp_residente= '" . $this->strtelefonocorp_residente . "'";
        $fieldsValues.=", strtelefonoper_residente= '" . $this->strtelefonoper_residente . "'";
        $fieldsValues.=", strnacionalidad_contacto= '" . $this->strnacionalidad_contacto . "'";
        $fieldsValues.=", intcedula_contacto= '" . $this->intcedula_contacto . "'";
        $fieldsValues.=", strnombre_contacto= '" . $this->strnombre_contacto . "'";
        $fieldsValues.=", strapellido_contacto= '" . $this->strapellido_contacto . "'";
        $fieldsValues.=", strtelefonocorp_contacto= '" . $this->strtelefonocorp_contacto . "'";
        $fieldsValues.=", strtelefonoper_contacto= '" . $this->strtelefonoper_contacto . "'";        
        
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

function deleteProyecto() {
        $conex = Conexion::singleton();
        $conex->connect_pgsql();
        $condition = "";

        $condition = "strnombre= " . $this->strnombre;

        $query = "DELETE FROM " . $this->tableName . " WHERE " . $condition;
        $conex->pgs_query($query);
        $conex->disconnect_pgsql();
    }
}

?>