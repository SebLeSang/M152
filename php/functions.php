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

function getAllPosts()
{
    $arr = array();
    $sql = "SELECT p.creationDate AS creaDate, p.modificationDate AS modifDate, p.commentaire AS comment, p.idPost as idP,
    group_concat(m.idmedia ORDER BY m.idmedia) AS idMedias,
    group_concat(m.nomMedia ORDER BY m.idmedia) AS medias,
    group_concat(m.typeMedia ORDER BY m.idmedia) AS types
    FROM post AS p
    JOIN contenir AS c ON p.idPost = c.post_idPost
    JOIN media AS m ON m.idmedia = c.media_idmedia
    GROUP BY p.idPost
    UNION
    SELECT p.creationDate, p.modificationDate, p.commentaire, p.idPost,
    null as idMedias,
    null AS medias,
    null AS types
    FROM post AS p
    WHERE p.idPost NOT IN (
      SELECT contenir.post_idPost
      FROM contenir)
    GROUP BY p.idPost
    ORDER BY creaDate desc";

    try {
        $stmt = EDatabase::prepare($sql, array(PDO::ATTR_CURSOR, PDO::CURSOR_SCROLL));
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
            $re = '/,/m';
            preg_match_all($re, $row["medias"], $matches, PREG_SET_ORDER, 0);
            if (empty($matches[0][0])) {
                $p = new Post($row["creaDate"], $row["modifDate"], $row["comment"], $row["medias"], $row['types'], $row['idP'], $row['idMedias']);
                array_push($arr, $p);
            } else {
                $medias = explode(',', $row["medias"]);
                $types = explode(',', $row['types']);
                $ids = explode(',', $row["idMedias"]);
                $count = 0;
                foreach ($medias as $m) {
                    $p = new Post($row["creaDate"], $row["modifDate"], $row["comment"], $medias[$count], $types[$count], $row['idP'], $ids[$count]);
                    array_push($arr, $p);
                    $count++;
                }
            }
        }
    } catch (PDOException  $e) {
        echo "getAllPosts Error: " . $e->getMessage();
        return false;
    }
    return $arr;
}

function DeletePost($id)
{
    EDatabase::beginTransaction();
    // delete dans la table contenir
    $sql = 'DELETE from contenir WHERE post_idPost = :id';
    try {
        $stmt = EDatabase::prepare($sql);
        $stmt->execute(array(
            ':id' => $id,
        ));
    } catch (PDOException $e) {
        echo "DeletePost error" . $e->getMessage();
        EDatabase::rollBack();
        return false;
    }

    // delete dans la table post
    $sql = 'DELETE from post WHERE idPost = :id';
    try {
        $stmt = EDatabase::prepare($sql);
        $stmt->execute(array(
            ':id' => $id,
        ));
    } catch (PDOException $e) {
        echo "DeletePost error" . $e->getMessage();
        EDatabase::rollBack();
        return false;
    }

    // si tout va bien
    EDatabase::commit();
    return true;
}

function DeleteMedia($id)
{
    EDatabase::beginTransaction();

    $sql = 'DELETE from media WHERE idmedia = :id';
    try {
        $stmt = EDatabase::prepare($sql);
        $stmt->execute(array(
            ':id' => $id,
        ));
    } catch (PDOException $e) {
        echo "DeleteMedia error" . $e->getMessage();
        EDatabase::rollBack();
        return false;
    }
    EDatabase::commit();
    return true;
}
