<?php

class Post{
    public function __construct($pCreaDate = null, $pModifDate = null, $pComment = "", $pMedias = "", $pType = "", $pIdPost, $pIdMedias)
    {
        $this->creaDate = $pCreaDate;
        $this->modifDate = $pModifDate;
        $this->comment = $pComment;
        $this->medias = $pMedias;
        $this->type = $pType;
        $this->idPost = $pIdPost;
        $this->idMedias = $pIdMedias;
    }

    public $creaDate;
    public $modifDate;
    public $comment;
    public $medias;
    public $type;
    public $idPost;
    public $pIdMedias;
}