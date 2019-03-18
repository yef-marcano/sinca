<?php
include_once(dirname(dirname(__FILE__)) . "/config/configSaime.php");

class ConexionSaime {

    private static $instancia = null;
    var $mysql_connect;
    var $pgs_connect;

    function connect_mysql() {
        $config = ConfigSaime::singleton();
        if (!$this->mysql_connect = @mysql_connect($config->get('mysql_host'), $config->get('mysql_user'), $config->get('mysql_pass'))) {
            throw new Exception("No se puede establecer conexion con base de datos 67775");
        }
        mysql_select_db($config->get('mysql_db_name'), $this->mysql_connect);
    }

    function connect_pgsql() {
        $config = ConfigSaime::singleton();
        if (!$this->pgs_connect = pg_connect("host=" . $config->get('pgsql_host') . " dbname=" . $config->get('pgsql_db_name') . " user=" . $config->get('pgsql_user') . " password=" . $config->get('pgsql_pass') . " port=" . $config->get('pgsql_port'))/* or die('No pudo conectarse: ' . pg_last_error()) */) {
            throw new Exception("No se puede establecer conexion con base de datos");
        }
    }
    
    function mysql_query($sentencia) {
        if (!$result = mysql_query($sentencia, $this->mysql_connect)) {
            throw new Exception("error en mysql ->" . $sentencia."\n".  mysql_error());
        }
        return $result;
    }

    function pgs_query($query) {
        if (!$result = pg_query($query) /* or die ("Error en SQL -->> "." ".$query." <<--<br> -->". pg_last_error()); */) {
            pg_send_query($this->pgs_connect, $query);
            $res = pg_get_result($this->pgs_connect);
            $codigoErrorSql = pg_result_error_field($res, PGSQL_DIAG_SQLSTATE);
            $errorMensaje = "error en pgsql ->" . $query;

            if ($codigoErrorSql == 23503) {
                $errorMensaje = "No se pudo eliminar porque posee relacion con otras tablas";
            }
            if ($codigoErrorSql == 23505) {
                $errorMensaje = "El campo codigo ya existe en la tabla ";
            }
            throw new Exception($errorMensaje, $codigoErrorSql);
        }
        return $result;
    }

    function disconnect_mysql() {
        mysql_close($this->mysql_connect);
    }

    function disconnect_pgsql() {
        pg_close($this->pgs_connect);
    }

    function mysql_lastId(){
        return mysql_insert_id($this->mysql_connect);        
    }
    
    function pgsql_lastId($nombreSeq) {
        $result = "";
        $rows = "";
        $query = "SELECT currval('" . $nombreSeq . "') AS numero_seq;";
        $result = pg_query($this->pgs_connect, $query);
        if (!$result) {
            echo "Error numeroSecuencia()";
            $rows = false;
        } else {
            $rows = pg_fetch_all($result);
            $rows = $rows[0]['numero_seq'];
        }
        return $rows;
    }
    function audit($action) {
        if ($action == "I") {
            $fields = ",dtmfecha_creacion ,clvusuario_creador";
            $values = ", now()," . $_SESSION["iduser"];
            return array($fields, $values);
        }else if ($action == "U") {
            $fieldsValues = ", dtmfecha_modificacion= now(), clvusuario_modificar= ". $_SESSION["iduser"];
            return $fieldsValues;
        }else if ($action == "D") {
            $fieldsValues = ", dtmfecha_eliminacion= now(), clvusuario_eliminar= ". $_SESSION["iduser"];
            return $fieldsValues;
//        }else if ($action == "P") {
//            $fieldsValues = ", fecha_publicacion= current_timestamp, id_usuario_publicar= ". $_SESSION["iduser"];
//            return $fieldsValues;
//        }else if ($action == "DP") {
//            $fieldsValues = ", fecha_despublicacion= current_timestamp, id_usuario_despublicar= ". $_SESSION["iduser"];
//            return $fieldsValues;
         }
    }

    public static function singleton() {
        if (self::$instancia == null) {
            self::$instancia = new self();
        }
        return self::$instancia;
    }

}
?>