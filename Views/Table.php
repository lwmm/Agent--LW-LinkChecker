<?php
/**
 * @author Michael Mandt <michael.mandt@logic-works.de>
 * @package agent_linkchecker
 */

namespace AgentLinkChecker\Views;

class Table
{
    public function render($error_url_array)
    {
        $baseUrl = substr(\AgentLinkChecker\Services\Page::getUrl(), 0, strpos(\AgentLinkChecker\Services\Page::getUrl(), "index.php"))."admin.php";

        $view = new \lw_view(dirname(__FILE__) . '/Templates/Table.phtml');
        $view->baseUrl = $baseUrl;
        
        $view->errorUrls = $error_url_array["errors"];
        
        $year = substr($error_url_array["scan_date"], 0, 4);
        $month = substr($error_url_array["scan_date"], 4, 2);
        $day = substr($error_url_array["scan_date"], 6, 2);
        $hour = substr($error_url_array["scan_date"], 8, 2);
        $min = substr($error_url_array["scan_date"], 10, 2);
        $sek = substr($error_url_array["scan_date"], 12, 2);
        
        $view->scan_date = $day . "." . $month . "." . $year . " - " . $hour . ":" . $min . ":" . $sek;
        
        return $view->render();
    }
}