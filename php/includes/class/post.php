<?php

class Post{
    public function __construct($pCreaDate = null, $pModifDate = null, $pComment = "", $pMedias = "", $pType = "")
    {
        $this->creaDate = $pCreaDate;
        $this->modifDate = $pModifDate;
        $this->comment = $pComment;
        $this->medias = $pMedias;
        $this->type = $pType;
    }

    public $creaDate;
    public $modifDate;
    public $comment;
    public $medias;
    public $type;
}