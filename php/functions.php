<?php

/**
 * Fichier de fonctions
 */
require_once $_SERVER['DOCUMENT_ROOT'] . '/m152/php/includes/incAll/inc.all.php';

/**
 * upload des medias dans la base de données
 *
 * @param [string] $mediaType
 * @param [string] $mediaName
 * @return [int] l'id du media
 */
function uploadMedias($mediaType, $mediaName)
{
    $todayDate = date("Y-m-d H:i:s");

    EDatabase::beginTransaction();
    // insert dans la table media
    $sql = 'INSERT INTO media (typeMedia, nomMedia, creationDate) VALUES (:mType, :nMedia, :cDate)';
    try {
        $stmt = EDatabase::prepare($sql);
        $stmt->execute(array(
            ':mType' => $mediaType,
            ':nMedia' => $mediaName,
            ':cDate' => $todayDate
        ));
    } catch (PDOException $e) {
        echo "upload error" . $e->getMessage();
        EDatabase::rollBack();
        return false;
    }
    $id = intval(EDatabase::lastInsertId());

    // si tout va bien
    EDatabase::commit();
    return $id;
}

/**
 * upload d'un post qui contient des medias
 *
 * @param [string] $comment
 * @return true si l'insert a fonctionné
 */
function createPost($comment, $idMediaArray)
{
    $todayDate = date("Y-m-d H:i:s");

    EDatabase::beginTransaction();
    // insert dans la table post
    $sql = 'INSERT INTO post (commentaire, creationDate) VALUES(:comment, :cDate)';
    try {
        $stmt = EDatabase::prepare($sql);
        $stmt->execute(array(
            ':comment' => $comment,
            ':cDate' => $todayDate
        ));
    } catch (PDOException $e) {
        echo "upload error" . $e->getMessage();
        EDatabase::rollBack();
        return false;
    }
    $idPost = intval(EDatabase::lastInsertId());

    // insert dans la table intermediaire
    foreach ($idMediaArray as $m) {
        $sql = 'INSERT INTO contenir (media_idmedia, post_idPost) VALUES (:m, :p)';
        try {
            $stmt = EDatabase::prepare($sql);
            $stmt->execute(array(
                ':m' => $m,
                ':p' => $idPost
            ));
        } catch (PDOException $e) {
            echo "upload error" . $e->getMessage();
            EDatabase::rollBack();
            return false;
        }
    }

    // si tout va bien
    EDatabase::commit();
    return true;
}
