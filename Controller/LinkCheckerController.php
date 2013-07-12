<?php
/**
 * @author Michael Mandt <michael.mandt@logic-works.de>
 * @package agent_linkchecker
 */

namespace AgentLinkChecker\Controller;

class LinkCheckerController
{

    protected $config;
    protected $response;
    protected $request;
    protected $db;

    public function __construct($config, $response, $request, $db)
    {
        $this->config = $config;
        $this->response = $response;
        $this->request = $request;
        $this->db = $db;
    }

    public function execute()
    {
        $loggedErrors = new \AgentLinkChecker\Model\ErrorData($this->db);
        
        $view = new \AgentLinkChecker\Views\Main($this->config);
        $tableView = new \AgentLinkChecker\Views\Table();

        $table = false;
        
        if($this->request->getAlnum("cmd") == "check"){
            $scaned_error_urls = \AgentLinkChecker\Classes\LinkCheck\LinkCheck::getInstance($this->db)->check();
            $loggedErrors->saveErrors($scaned_error_urls);
            
            $table = $tableView->render($scaned_error_urls);
        }else{
            $result = $loggedErrors->getErrorLog();
            if($result){
                $table = $tableView->render(unserialize($result["opt1clob"]));
            }
        }
        $this->response->setOutputByKey("AgentLinkChecker", $view->render($table));
    }
        
}