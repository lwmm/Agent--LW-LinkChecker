<?php

/**
 * ErrorData will be saved as serialized array in lw_master
 * @author Michael Mandt <michael.mandt@logic-works.de>
 * @package agent_linkchecker
 */

namespace AgentLinkChecker\Model;

class ErrorData
{
    protected $commandHandler;
    protected $queryHandler;
    protected $id;
    

    public function __construct($db)
    {
        $this->commandHandler = new \AgentLinkChecker\Model\DataHandler\CommandHandler($db);
        $this->queryHandler = new \AgentLinkChecker\Model\DataHandler\QueryHandler($db);
    }
    
    public function saveErrors($errors)
    {
        if($this->existsErrorLogEntry()){
            $this->updateErrorLogEntry($errors);
        }else{
            $this->createErrorLogEntry($errors);
        }
    }
    
    public function getErrorLog()
    {
        $result = $this->queryHandler->getErrorLog();
        return unserialize($result["opt1clob"]);
    }
    
    private function existsErrorLogEntry()
    {
        $result = $this->queryHandler->existsErrorLogEntry();
        if(!empty($result)){
            $this->id = $result["id"];
            return true;
        }
        return false;
    }
    
    private function createErrorLogEntry($errors)
    {
        $this->commandHandler->createErrorLogEntry($errors);
    }
    
    private function updateErrorLogEntry($errors)
    {
        $this->commandHandler->updateErrorLogEntry($errors, $this->id);
    }
}