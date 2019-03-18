<?php

function uploadFile($fileName, $maxSize, $fullPath, $relPath) {
    $folder = $relPath;
    $maxlimit = $maxSize;
    $allowed_ext = "tar,gz";

    $filesize = $_FILES[$fileName]['size'];
    $filename = preg_replace('/\s/', '_', strtolower($_FILES[$fileName]['name']));
    $file_ext = split("\.", $filename);

    if ($filesize > 0) {
        if ($filesize > $maxlimit) {
            $errorList[] = "Excede el tama&ntilde;o del archivo";
        }
        if (count($errorList) < 1) {
            $resultExt = strpos($allowed_ext, end($file_ext));
            if ($resultExt !== false) {
                if (file_exists($folder . $filename)) {
                    $errorList[] = "El archivo ya existe";
                } else {
                    $newfilename = $filename;
                    $save = $folder . $filename;
                    if (!move_uploaded_file($_FILES[$fileName]['tmp_name'], $save)) {
                        $errorList[] = "SUBIENDO ARCHIVO";
                    }
                }
            } else {
                $errorList[] = "Tipo de archivo no permitido: " . $filename;
            }
        }
    } else {
        $errorList[] = "No se ha seleccionado el archivo";
        exit;
    }

    if (sizeof($errorList) == 0) {
        return $fullPath . $newfilename;
    } else {
        $eMessage = array();
        for ($x = 0; $x < sizeof($errorList); $x++) {
            $eMessage[] = $errorList[$x];
        }
        return $eMessage;
    }
}

$fullPath = "../backup/nuevos/";
$relPath = "../../backup/nuevos/";
$filename = strip_tags($_REQUEST['filename']);
$maxSize = strip_tags($_REQUEST['maxSize']);
$filesize_image = $_FILES[$filename]['size'];

if ($filesize_image > 0) {
    $upload_image = uploadFile($filename, $maxSize, $fullPath, $relPath);
    if (is_array($upload_image)) {
        foreach ($upload_image as $key => $value) {
            if ($value == "-ERROR-") {
                unset($upload_image[$key]);
            }
        }
        $document = array_values($upload_image);
        for ($x = 0; $x < sizeof($document); $x++) {
            $errorList[] = $document[$x];
        }
        $imgUploaded = false;
    } else {
        $imgUploaded = true;
    }
} else {
    $imgUploaded = false;
    $errorList[] = "El archivo esta vacio";
}


if ($imgUploaded) {
    $html = "<div class='ui green message'>
                <div class='header'>Exito</div>
                El archivo fue subido con exito!
            </div>";
    echo $html;
} else {
    $html = "<div class='ui red message'>
                <div class='header'>Error: </div>
                <ul class='list'>";
    foreach ($errorList as $value) {
        $html.= "   <li>" . $value . "</li>";
    }
    $html.= "   </ul>
            </div>";
    echo $html;
}
?>