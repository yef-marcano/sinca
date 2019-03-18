<?php

function uploadImage($fileName, $maxSize, $maxW, $fullPath, $relPath, $colorR, $colorG, $colorB, $maxH = null) {
    $filesNames = "";
    $folder = $relPath;
    $maxlimit = $maxSize;
    $allowed_ext = "jpg,jpeg,gif,png";

    for ($i = 0; $i < count($_FILES[$fileName]['name']); $i++) {
        $filesize = $_FILES[$fileName]['size'][$i];
        $filename = preg_replace('/\s/', '_', strtolower($_FILES[$fileName]['name'][$i]));
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
                        list($width_orig, $height_orig) = getimagesize($_FILES[$fileName]['tmp_name'][$i]);
                        if ($maxH == null) {
                            if ($width_orig < $maxW) {
                                $fwidth = $width_orig;
                            } else {
                                $fwidth = $maxW;
                            }
                            $ratio_orig = $width_orig / $height_orig;
                            $fheight = $fwidth / $ratio_orig;

                            $blank_height = $fheight;
                            $top_offset = 0;
                        } else {
                            if ($width_orig <= $maxW && $height_orig <= $maxH) {
                                $fheight = $height_orig;
                                $fwidth = $width_orig;
                            } else {
                                if ($width_orig > $maxW) {
                                    $ratio = ($width_orig / $maxW);
                                    $fwidth = $maxW;
                                    $fheight = ($height_orig / $ratio);
                                    if ($fheight > $maxH) {
                                        $ratio = ($fheight / $maxH);
                                        $fheight = $maxH;
                                        $fwidth = ($fwidth / $ratio);
                                    }
                                }
                                if ($height_orig > $maxH) {
                                    $ratio = ($height_orig / $maxH);
                                    $fheight = $maxH;
                                    $fwidth = ($width_orig / $ratio);
                                    if ($fwidth > $maxW) {
                                        $ratio = ($fwidth / $maxW);
                                        $fwidth = $maxW;
                                        $fheight = ($fheight / $ratio);
                                    }
                                }
                            }

                                if ($fheight == 0 || $fwidth == 0 || $height_orig == 0 || $width_orig == 0) {
                                    die("FATAL ERROR REPORT ERROR CODE [add-pic-line-67-orig]");
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
                                    $image = @imagecreatefromgif($_FILES[$fileName]['tmp_name'][$i]);
                                    break;
                                case "jpg":
                                    $image = @imagecreatefromjpeg($_FILES[$fileName]['tmp_name'][$i]);
                                    break;
                                case "jpeg":
                                    $image = @imagecreatefromjpeg($_FILES[$fileName]['tmp_name'][$i]);
                                    break;
                                case "png":
                                    $image = @imagecreatefrompng($_FILES[$fileName]['tmp_name'][$i]);
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
                            ($filesNames == "") ? $filesNames = $newfilename : $filesNames.= "," . $newfilename;
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
        }
        if (sizeof($errorList) == 0) {
            return $filesNames;
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
    $filename = strip_tags($_REQUEST['filenameMultiple']);
    $maxSize = strip_tags($_REQUEST['maxSizeMultiple']);
    $maxW = strip_tags($_REQUEST['maxWMultiple']);
    $maxH = strip_tags($_REQUEST['maxHMultiple']);
    $filesize_image = $_FILES[$filename]['size'];

    $upload_image = uploadImage($filename, $maxSize, $maxW, $fullPath, $relPath, $colorR, $colorG, $colorB, $maxH);


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


    if ($imgUploaded) {
        echo "<script>parent.viewImg(null, '" . $upload_image . "', false);</script>";
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