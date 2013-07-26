<?php

namespace AgentLinkChecker\Classes\ErrorCompare\DataHandler;

class CommandHandler
{

    protected $db;

    public function __construct($db)
    {
        $this->db = $db;
    }
    
    public function savePageCommentById($id, $pageComment)
    {
        $this->db->setStatement("UPDATE t:lw_pages SET pagecomment = :pagecomment WHERE id = :id ");
        $this->db->bindParameter("id", "i", $id);
        $this->db->bindParameter("pagecomment", "s", $pageComment);
        
        return $this->db->pdbquery();
    }

}