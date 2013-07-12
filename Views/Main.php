<?php
/**
 * @author Michael Mandt <michael.mandt@logic-works.de>
 * @package agent_linkchecker
 */

namespace AgentLinkChecker\Views;

class Main
{
    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function render($table)
    {
        $baseUrl = substr(\AgentLinkChecker\Services\Page::getUrl(), 0, strpos(\AgentLinkChecker\Services\Page::getUrl(), "index.php"))."admin.php?obj=linkchecker";

        $view = new \lw_view(dirname(__FILE__) . '/Templates/Main.phtml');

        $view->bootstrapCSS = $this->config["url"]["media"] . "bootstrap/css/bootstrap.min.css";
        $view->bootstrapJS = $this->config["url"]["media"] . "bootstrap/js/bootstrap.min.js";
        
        $view->table = $table;
        
        $view->baseUrl = $baseUrl;
        
        return $view->render();
    }

}