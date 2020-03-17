<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/m152/php/includes/incAll/inc.all.php';

$msg = filter_input(INPUT_POST, 'commentaire', FILTER_SANITIZE_STRING);

$total_size = 0;
$idArr = array();
if ($msg != "") {
    if (isset($_FILES) && is_array($_FILES) && count($_FILES) > 0) {
        $files = $_FILES['myFiles'];
        for ($i = 0; $i < count($files['name']); $i++) {
            $total_size += $files['size'][$i];
            if ($total_size < 70000000 || $files['size'][$i] < 3000000) {
                $type = explode("/", $files["type"][0]);
                if ($type[0] == "image" || $type[0] == "video" || $type[0] == "audio") {
                    $fileName = $files['name'][$i];
                    $splitName = explode(".", $fileName);
                    $newName = uniqid($splitName[0]);
                    $finalName = $newName . "." . $splitName[1];
                    $id = uploadMedias($files["type"][0], $finalName);
                    array_push($idArr, $id);
                    move_uploaded_file($files['tmp_name'][$i], '../img_temp/' . $finalName);
                } else
                    echo "le type de fichier n'est pas correct";
            } else
                echo "la taille totale des fichier dépasse 70M ou la taille d'un fichier est supérieur a 3M";
        }
        if (createPost($msg, $idArr) == true) {
            header('Location: ../index.php');
        }
    }
}
echo "Veuillez rentrez un commentaire";
