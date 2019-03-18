<?php

function logError($model, $action, $error) {
    $fecha = new DateTime();
    $file = fopen(dirname(dirname(dirname(__FILE__))) . "/log/log_" . $fecha->format("d-m-Y") . ".log", "a+");
    fwrite($file, $fecha->format("d-m-Y H:i:s") . " " . $model . " " . $action . "\n");
    fwrite($file, "\t" . $error . "\n");
    fclose($file);
}

function generarCodigo($longitud) {
    $key = '';
    $pattern = '1234567890abcdefghijklmnopqrstuvwxyz';
    $max = strlen($pattern) - 1;
    for ($i = 0; $i < $longitud; $i++)
        $key .= $pattern{mt_rand(0, $max)};
    return $key;
}

?>