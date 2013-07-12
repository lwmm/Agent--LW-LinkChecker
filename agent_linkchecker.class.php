<?php
/**
 * @author Michael Mandt <michael.mandt@logic-works.de>
 * @package agent_linkchecker
 */

class agent_linkchecker extends lw_agent
{

    protected $config;
    protected $request;
    protected $response;

    public function __construct()
    {
        parent::__construct();
        $this->config = $this->conf;
        $this->className = "agent_linkchecker";
        $this->adminSurfacePath = $this->config['path']['agents'] . "adminSurface/templates/";

        $usage = new lw_usage($this->className, "0");
        $this->secondaryUser = $usage->executeUsage();

        include_once(dirname(__FILE__) . '/Services/Autoloader.php');
        $autoloader = new \AgentLinkChecker\Services\Autoloader();
    }

    protected function showEdit()
    {
        $response = new \AgentLinkChecker\Services\Response();
        $controller = new \AgentLinkChecker\Controller\LinkCheckerController($this->config, $response, $this->request, $this->db);
        $controller->execute();
        return $response->getOutputByKey("AgentLinkChecker");
    }

    protected function buildNav()
    {
        $view = new \AgentLinkChecker\Views\Navigation();
        return $view->render();
    }

    protected function deleteAllowed()
    {
        return true;
    }

}