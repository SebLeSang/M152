<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/m152/php/includes/incAll/inc.all.php';

header('Content-Type: application/json');

$idPost = intval(filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT));

if(DeletePost($idPost) == true)
    echo '{"ReturnCode": 0, "Message": delete ok}';

else
    echo '{"ReturnCode": 1, "Message": delete not ok}';