<?php

namespace AgentLinkChecker\Classes\LinkCheck\DataHandler;

class CommandHandler
{

    protected $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

}