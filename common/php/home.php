<?php

function logError($model, $action, $error) {
    $fecha = new DateTime();
    $file = fopen(dirname(dirname(dirname(__FILE__))) . "/log/log_" . $fecha->format("d-m-Y") . ".log", "a+");
    fwrite($file, $fecha->format("d-m-Y H:i:s") . " " . $model . " " . $action . "\n");
    fwrite($file, "\t" . $error . "\n");
    fclose($file);
}

?>