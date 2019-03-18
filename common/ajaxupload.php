<?php

function uploadImage2($fileName, $maxSize, $maxW, $fullPath, $relPath, $colorR, $colorG, $colorB, $maxH = null) {
    $folder = $relPath;
    $maxlimit = $maxSize;
    $allowed_ext = "jpg,jpeg,gif,png";

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
                    $num = time();
                    $front_name = substr($file_ext[0], 0, 15);
                    $newfilename = $front_name . "_" . $num . "." . end($file_ext);
                    $filetype = end($file_ext);
                    $save = $folder . $newfilename;
                } else {
                    $filetype = end($file_ext);
                    $newfilename = $filename;
                    $save = $folder . $filename;
                }
                if (!file_exists($save)) {
                    list($width_orig, $height_orig) = getimagesize($_FILES[$fileName]['tmp_name']);
                    if ($maxH == null) {
                        $fwidth = $maxW;
                        $fheight = $maxH;
                    } else {
                        $fwidth = $maxW;
                        $fheight = $maxH;
                        if ($fheight == 0 || $fwidth == 0 || $height_orig == 0 || $width_orig == 0) {
                            die("Error Fatal [add-pic-line-67-orig]");
                        }
                        if ($fheight < 45) {
                            $blank_height = 45;
                            $top_offset = round(($blank_height - $fheight) / 2);
                        } else {
                            $blank_height = $fheight;
                        }
                    }

                    $image_p = @imagecreatetruecolor($fwidth, $blank_height);
                    $white = @imagecolorallocate($image_p, $colorR, $colorG, $colorB);
                    @imagefill($image_p, 0, 0, $white);

                    switch ($filetype) {
                        case "gif":
                            $image = @imagecreatefromgif($_FILES[$fileName]['tmp_name']);
                            break;
                        case "jpg":
                            $image = @imagecreatefromjpeg($_FILES[$fileName]['tmp_name']);
                            break;
                        case "jpeg":
                            $image = @imagecreatefromjpeg($_FILES[$fileName]['tmp_name']);
                            break;
                        case "png":
                            $image = @imagecreatefrompng($_FILES[$fileName]['tmp_name']);
                            break;
                    }
                    @imagecopyresampled($image_p, $image, 0, $top_offset, 0, 0, $fwidth, $fheight, $width_orig, $height_orig);
                    switch ($filetype) {
                        case "gif":
                            if (!@imagegif($image_p, $save)) {
                                $errorList[] = "Permiso denegado [GIF]";
                            }
                            break;
                        case "jpg":
                            if (!@imagejpeg($image_p, $save, 100)) {
                                $errorList[] = "Permiso denegado [JPG]";
                            }
                            break;
                        case "jpeg":
                            if (!@imagejpeg($image_p, $save, 100)) {
                                $errorList[] = "Permiso denegado [JPEG]";
                            }
                            break;
                        case "png":
                            if (!@imagepng($image_p, $save, 0)) {
                                $errorList[] = "Permiso denegado [PNG]";
                            }
                            break;
                    }
                    @imagedestroy($filename);
                } else {
                    $errorList[] = "La imagen ya existe";
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

$fullPath = "../common/upload/";
$relPath = "../upload/";
$colorR = 255;
$colorG = 255;
$colorB = 255;
$filename = strip_tags($_REQUEST['filename']);
$maxSize = strip_tags($_REQUEST['maxSize']);
$maxW = strip_tags($_REQUEST['maxW']);
$maxH = strip_tags($_REQUEST['maxH']);
$filesize_image = $_FILES[$filename]['size'];
//echo "-------------".$filename."------------";
//print_r($_REQUEST);

if ($filesize_image > 0) {
    $upload_image = uploadImage2($filename, $maxSize, $maxW, $fullPath, $relPath, $colorR, $colorG, $colorB, $maxH);
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
    $errorList[] = "El archivo esta vacio 1";
}


if ($imgUploaded) {
    $archivo = split("/", $upload_image);
    
    echo '<center><img class="rounded ui image" src="' . $upload_image . '" border="0" width="100%" height="100%"/></center>';
    echo "<input type='hidden' id='" . str_replace("txt", "hdn", $filename) . "' name='" . str_replace("txt", "hdn", $filename) . "' value='" . $archivo[3] . "' />";
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