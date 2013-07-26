<?php

/**
 * @author Michael Mandt <michael.mandt@logic-works.de>
 * @package agent_linkchecker
 */

namespace AgentLinkChecker\Classes\ErrorCompare\DataHandler;

class QueryHandler
{

    protected $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getPageCommentById($id)
    {
        $this->db->setStatement("SELECT pagecomment FROM t:lw_pages WHERE id = :id ");
        $this->db->bindParameter("id", "i", $id);
        
        $result = $this->db->pselect1();
        
        return $result["pagecomment"];
    }
}