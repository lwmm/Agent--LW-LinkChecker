<?php

/**
 * @author Michael Mandt <michael.mandt@logic-works.de>
 * @package agent_linkchecker
 */

namespace AgentLinkChecker\Model\DataHandler;

class QueryHandler
{

    protected $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function existsErrorLogEntry()
    {
        $this->db->setStatement("SELECT * FROM t:lw_master WHERE lw_object = :lw_object ");
        $this->db->bindParameter("lw_object" , "s", "agent_linkchecker");
        
        return $this->db->pselect1();
    }
    
    public function getErrorLog()
    {
        return $this->existsErrorLogEntry();
    }
}