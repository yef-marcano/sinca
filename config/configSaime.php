<?php

class ConfigSaime {

    private $vars;
    private static $instancia;

    private function __construct() {
        $this->vars = array();
    }

    //Con set vamos guardando nuestras variables.
    public function set($nombre, $valor) {
        if (!isset($this->vars[$nombre])) {
            $this->vars[$nombre] = $valor;
        }
    }

    //Con get('nombre_de_la_variable') recuperamos un valor.
    public function get($nombre) {
        if (isset($this->vars[$nombre])) {
            return $this->vars[$nombre];
        }
    }

    public static function singleton() {
        if (!isset(self::$instancia)) {
            $c = __CLASS__;
            self::$instancia = new $c;
        }

        return self::$instancia;
    }

}

$config = ConfigSaime::singleton();
/*
$config->set('mysql_host', '192.168.0.91:3306');
$config->set('mysql_user', 'root');
$config->set('mysql_pass', 'root');
$config->set('mysql_db_name', 'bd_saber_trabajo');
*/

 $config->set('pgsql_host', '192.168.0.91');
  $config->set('pgsql_user', 'sametsis');
  $config->set('pgsql_pass', '+s4b1dur14+');
  $config->set('pgsql_db_name', 'bd_saime');
  $config->set('pgsql_port', '5432'); 
?>