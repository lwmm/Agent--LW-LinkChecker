<?php

/**
 * @author Michael Mandt <michael.mandt@logic-works.de>
 * @package agent_linkchecker
 */

namespace AgentLinkChecker\Classes\LinkCheck\DataHandler;

class QueryHandler
{

    protected $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getAllUrls()
    {
        $this->db->setStatement("SELECT id, page_id, description, url, itemtype FROM t:lw_items WHERE url != '' AND url IS NOT NULL ORDER BY description ");
        return $this->db->pselect();
    }
    
    public function getPageById($id)
    {
        $this->db->setStatement("SELECT id FROM t:lw_pages WHERE id = :id ");
        $this->db->bindParameter("id", "i", $id);
        
        return $this->db->pselect1();
    }
}