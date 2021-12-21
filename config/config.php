<?php

class Config {

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

$config = Config::singleton();

  $config->set('pgsql_host', 'localhost');
  $config->set('pgsql_user', 'postgres');
  $config->set('pgsql_pass', 'postgres');
  $config->set('pgsql_db_name', 'bd_local');
  $config->set('pgsql_port', '5432'); 


?>
