<?php

/**
 * @author Michael Mandt <michael.mandt@logic-works.de>
 * @package agent_linkchecker
 */

namespace AgentLinkChecker\Model\DataHandler;

class CommandHandler
{

    protected $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    
    public function createErrorLogEntry($errors)
    {
        $this->db->setStatement("INSERT INTO t:lw_master (lw_object, opt1number) VALUES (:lw_object, :opt1number) ");
        $this->db->bindParameter("lw_object", "s", "agent_linkchecker");
        $this->db->bindParameter("opt1number", "i", $errors["scan_date"]);
        
        $id = $this->db->pdbinsert($this->db->gt("lw_master"));

        if ($id > 0) {
            return $this->db->saveClob($this->db->gt("lw_master"), "opt1clob", $this->db->quote(serialize($errors)), $id);
        }
        else {
            return false;
        }
    }
    
    public function updateErrorLogEntry($errors, $id)
    {
        $this->db->setStatement("UPDATE t:lw_master SET opt1number = :opt1number WHERE id = :id ");
        $this->db->bindParameter("id", "i", $id);
        $this->db->bindParameter("opt1number", "i", $errors["scan_date"]);
        $this->db->pdbquery();

        if ($id > 0) {
            return $this->db->saveClob($this->db->gt("lw_master"), "opt1clob", $this->db->quote(serialize($errors)), $id);
        }
        else {
            return false;
        }
    }
}