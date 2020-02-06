<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/m152/php/includes/incAll/inc.all.php';

$msg = filter_input(INPUT_POST, 'commentaire', FILTER_SANITIZE_STRING);


$total_size = 0;
if (isset($_FILES) && is_array($_FILES) && count($_FILES) > 0) {
    $files = $_FILES['myFiles'];
    for($i=0; $i < count($files['name']); $i++){
        $total_size += $files['size'][$i];
        if ($total_size < 70000 || $files['size'][$i] < 3000) {
            if($files["type"][0] == "image/png" || $files["type"][0] == "image/gif" || $files["type"][0] == "image/jpg"){
                $fileName = $files['name'][$i];
                $newName = uniqid($fileName);
                move_uploaded_file($files['tmp_name'][$i], '../img_temp/' . $newName);
            }
            else
                echo "le type de fichier n'est pas correct";
        }
        else
            echo "la taille totale des fichier dépasse 70M ou la taille d'un fichier est supérieur a 3M";
    }
}